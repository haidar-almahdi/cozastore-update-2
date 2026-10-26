<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\LoyaltyService;

class OrderController extends Controller
{
    /**
     * Get or create cart for current user/session
     */
    protected function getCart()
    {
        if (Auth::check()) {
            $cart = Cart::firstOrCreate(
                ['user_id' => Auth::id(), 'status' => 'active'],
                ['session_id' => session()->getId()]
            );
        } else {
            $cart = Cart::firstOrCreate(
                ['session_id' => session()->getId(), 'status' => 'active']
            );
        }

        return $cart;
    }

    /**
     * Display a listing of user orders
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = $user->orders()->with(['items.product'])->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by amount range
        if ($request->filled('min_amount')) {
            $query->where('total_amount', '>=', $request->min_amount);
        }

        if ($request->filled('max_amount')) {
            $query->where('total_amount', '<=', $request->max_amount);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Search by order number
        if ($request->filled('search')) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }

        $orders = $query->paginate(10)->withQueryString();

        // User statistics
        $stats = [
            'total_orders' => $user->orders()->count(),
            'total_spent' => $user->orders()->sum('total_amount'),
            'pending_orders' => $user->orders()->where('status', 'pending')->count(),
            'completed_orders' => $user->orders()->where('status', 'completed')->count(),
        ];

        return view('shop.orders.index', compact('orders', 'stats'));
    }

    /**
     * Show the checkout form
     */
    public function checkout()
    {
        $cart = $this->getCart();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('shop.cart.index')->with('error', 'Your cart is empty.');
        }

        // Load cart items with their relationships
        $cart->load(['items.product']);

        return view('shop.checkout', compact('cart'));
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request)
    {
        $request->validate([
            'shipping_name' => 'required|string|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        $cart = $this->getCart();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('shop.cart.index')->with('error', 'Your cart is empty.');
        }

        // Load cart items with their relationships
        $cart->load(['items.product']);

        try {
            DB::beginTransaction();

            // Check stock availability for all items first
            foreach ($cart->items as $item) {
                $product = $item->product;
                if (!$product) {
                    throw new \Exception("One of the products in your cart is no longer available.");
                }
                $item->price = $product->current_price;
                $item->save();
                $availableStock = (int) $product->stock;
                if ($item->size && is_array($product->size_stocks) && !empty($product->size_stocks)) {
                    $availableStock = (int) ($product->size_stocks[$item->size] ?? 0);
                }

                if ($availableStock < $item->quantity) {
                    throw new \Exception("Sorry, we only have {$availableStock} units of {$product->name} in stock. You requested {$item->quantity} units.");
                }
            }

            // Calculate totals
            $subtotal = $cart->items->sum(function($item) {
                return $item->price * $item->quantity;
            });
            $shipping = $subtotal * 0.02; // 2% shipping
            $total = $subtotal + $shipping;

            // Loyalty redemption (optional)
            $redeemBlocks = (int) $request->input('redeem_blocks', 0);
            $discount = 0.0;
            if (Auth::check() && $redeemBlocks > 0) {
                /** @var LoyaltyService $loyalty */
                $loyalty = app(LoyaltyService::class);
                $maxBlocks = $loyalty->maxRedeemableBlocks(Auth::user());
                $redeemBlocks = max(0, min($redeemBlocks, $maxBlocks));
                if ($redeemBlocks > 0) {
                    $discount = $loyalty->discountForBlocks($redeemBlocks);
                    $total = max(0, $total - $discount);
                }
            }

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'total_amount' => $total,
                'shipping_name' => $request->shipping_name,
                'shipping_phone' => $request->shipping_phone,
                'shipping_address' => $request->shipping_address,
                'notes' => $request->notes,
                'status' => 'pending'
            ]);

            // Create order items and update stock
            foreach ($cart->items as $item) {
                $product = $item->product;

                // Create order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'size' => $item->size,
                    'color' => $item->color
                ]);

                // Update product stock
                if ($item->size && is_array($product->size_stocks) && !empty($product->size_stocks)) {
                    $sizeStocks = $product->size_stocks;
                    $currentSizeStock = (int) ($sizeStocks[$item->size] ?? 0);
                    $sizeStocks[$item->size] = max(0, $currentSizeStock - (int) $item->quantity);
                    $product->size_stocks = $sizeStocks;
                    $product->save();
                }

                $product->decrement('stock', $item->quantity);
            }

            // Clear the cart
            $cart->items()->delete();
            $cart->delete();

            // Loyalty redeem transaction after order creation (so we can reference the order)
            if (Auth::check() && isset($loyalty) && $redeemBlocks > 0) {
                $loyalty->redeem(Auth::user(), $loyalty->pointsForBlocks($redeemBlocks), $order, 'Redeemed at checkout for order ' . $order->order_number);
            }

            DB::commit();

            // Redirect to the order show page
            return redirect()->route('shop.orders.show', $order)
                ->with('success', 'Your order has been placed successfully! Order number: ' . $order->order_number);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['items.product']);

        return view('shop.orders.show', compact('order'));
    }

    /**
     * Cancel the specified order
     */
    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending orders can be cancelled.');
        }

        // Restore stock for cancelled items
        foreach ($order->items as $item) {
            if ($item->product) {
                if ($item->size && is_array($item->product->size_stocks) && !empty($item->product->size_stocks)) {
                    $sizeStocks = $item->product->size_stocks;
                    $sizeStocks[$item->size] = ((int) ($sizeStocks[$item->size] ?? 0)) + (int) $item->quantity;
                    $item->product->size_stocks = $sizeStocks;
                    $item->product->save();
                }
                $item->product->increment('stock', $item->quantity);
            }
        }

        $order->update(['status' => 'cancelled']);

        return redirect()->back()->with('success', 'Order has been cancelled successfully.');
    }
}
