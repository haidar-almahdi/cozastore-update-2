<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserWalletConvertedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public float $amount,
        public int $blocks
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Wallet credited',
            'message' => "Congrats! {$this->blocks} block(s) of points were converted and \${$this->amount} was added to your wallet.",
            'amount' => $this->amount,
            'blocks' => $this->blocks,
            'type' => 'wallet_credit',
        ];
    }
}
