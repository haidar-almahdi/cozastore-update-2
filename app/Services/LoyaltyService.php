<?php

namespace App\Services;

use App\Models\LoyaltyPointTransaction;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\AdminWalletConversionNotification;
use App\Notifications\UserWalletConvertedNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LoyaltyService
{
    // Config: how many currency units per 100 points
    public const POINTS_PER_BLOCK = 100;
    public const DISCOUNT_PER_BLOCK = 10.0; // $10 per 100 points

    public function awardIfNotExists(User $user, int $points, string $type, $reference = null, ?string $description = null): ?LoyaltyPointTransaction
    {
        $tx = DB::transaction(function () use ($user, $points, $type, $reference, $description) {
            // Build where for unique
            $attrs = [
                'user_id' => $user->id,
                'type' => $type,
            ];

            if ($reference instanceof Model) {
                $attrs['reference_type'] = $reference->getMorphClass();
                $attrs['reference_id'] = $reference->getKey();
            } elseif (is_array($reference)) {
                $attrs = array_merge($attrs, $reference); // allow passing ['reference_type'=>..,'reference_id'=>..]
            }

            // Check existing
            $exists = LoyaltyPointTransaction::where($attrs)->first();
            if ($exists) {
                return null; // already awarded
            }

            $tx = LoyaltyPointTransaction::create(array_merge($attrs, [
                'points' => $points,
                'description' => $description,
            ]));

            // Update balance
            $user->increment('loyalty_points_balance', $points);

            return $tx;
        });

        if ($tx) {
            $this->processAutomaticWalletConversion($user->fresh());
        }

        return $tx;
    }

    public function redeem(User $user, int $points, ?Model $reference = null, ?string $description = null): LoyaltyPointTransaction
    {
        return DB::transaction(function () use ($user, $points, $reference, $description) {
            if ($points <= 0) {
                throw new \InvalidArgumentException('Points must be positive for redeem.');
            }
            if ($user->loyalty_points_balance < $points) {
                throw new \RuntimeException('Insufficient loyalty points.');
            }

            $data = [
                'user_id' => $user->id,
                'points' => -$points,
                'type' => 'redeem',
                'description' => $description,
            ];

            if ($reference) {
                $data['reference_type'] = $reference->getMorphClass();
                $data['reference_id'] = $reference->getKey();
            }

            $tx = LoyaltyPointTransaction::create($data);
            $user->decrement('loyalty_points_balance', $points);

            return $tx;
        });
    }

    public function maxRedeemableBlocks(User $user): int
    {
        return intdiv($user->loyalty_points_balance, self::POINTS_PER_BLOCK);
    }

    public function discountForBlocks(int $blocks): float
    {
        return $blocks * self::DISCOUNT_PER_BLOCK;
    }

    public function pointsForBlocks(int $blocks): int
    {
        return $blocks * self::POINTS_PER_BLOCK;
    }

    public function awardDeliveredOrderPoints(User $user, Order $order): ?LoyaltyPointTransaction
    {
        return $this->awardIfNotExists(
            $user,
            3,
            'order_delivered_award',
            $order,
            'Delivered order reward: ' . $order->order_number
        );
    }

    public function awardCommentPointIfEligible(User $user, Product $product): array
    {
        $hasPurchased = Order::where('user_id', $user->id)
            ->whereIn('status', ['processing', 'completed'])
            ->whereHas('items', function ($q) use ($product) {
                $q->where('product_id', $product->id);
            })
            ->exists();

        if (!$hasPurchased) {
            return ['awarded' => false, 'reason' => 'purchase_required'];
        }

        $tx = $this->awardIfNotExists(
            $user,
            1,
            'comment_award',
            $product,
            'Comment reward on product: ' . $product->name
        );

        return ['awarded' => (bool) $tx, 'reason' => $tx ? 'awarded' : 'already_awarded'];
    }

    protected function processAutomaticWalletConversion(User $user): void
    {
        $result = DB::transaction(function () use ($user) {
            $locked = User::query()->whereKey($user->id)->lockForUpdate()->first();
            if (!$locked) {
                return ['blocks' => 0, 'amount' => 0.0];
            }

            $blocks = intdiv((int) $locked->loyalty_points_balance, self::POINTS_PER_BLOCK);
            if ($blocks <= 0) {
                return ['blocks' => 0, 'amount' => 0.0];
            }

            $pointsToConvert = $this->pointsForBlocks($blocks);
            $amount = $this->discountForBlocks($blocks);

            $locked->decrement('loyalty_points_balance', $pointsToConvert);
            $locked->increment('wallet_balance', $amount);

            LoyaltyPointTransaction::create([
                'user_id' => $locked->id,
                'points' => -$pointsToConvert,
                'type' => 'wallet_conversion',
                'description' => "Auto conversion: {$pointsToConvert} points to \${$amount} wallet credit",
            ]);

            return ['blocks' => $blocks, 'amount' => $amount];
        });

        if (($result['blocks'] ?? 0) > 0) {
            $freshUser = $user->fresh();
            $amount = (float) $result['amount'];
            $blocks = (int) $result['blocks'];

            $freshUser->notify(new UserWalletConvertedNotification($amount, $blocks));

            User::where('role', 'admin')->get()->each(function (User $admin) use ($freshUser, $amount, $blocks) {
                $admin->notify(new AdminWalletConversionNotification($freshUser, $amount, $blocks));
            });
        }
    }
}
