<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
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
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $cart = $this->getCart();
            $items = $cart->items()
                ->with('product')
                ->get();

            // Filter out items with null products
            $items = $items->filter(function($item) {
                return $item->product !== null;
            });

            return view('shop.cart.index', compact('cart', 'items'));
        } catch (\Exception $e) {
            return redirect()->route('shop.home')
                ->with('error', 'Error loading cart: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    // public function create()
    // {
    //     return redirect()->route('shop.products.index');
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'size' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = Product::findOrFail($request->product_id);

        if (!$product->is_active) {
            return response()->json(['error' => 'This product is currently not available.'], 422);
        }

        $availableStock = (int) $product->stock;
        if ($request->filled('size') && is_array($product->size_stocks) && !empty($product->size_stocks)) {
            $availableStock = (int) ($product->size_stocks[$request->size] ?? 0);
        }

        if ($availableStock < $request->quantity) {
            return response()->json(['error' => 'The requested quantity is not available. Maximum available: ' . $availableStock], 422);
        }

        $cart = $this->getCart();

        // Check if product already exists in cart
        $cartItem = $cart->items()
            ->where('product_id', $product->id)
            ->where('size', $request->size)
            ->where('color', $request->color)
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $request->quantity;

            if ($newQuantity > $availableStock) {
                return response()->json(['error' => 'Cannot add more of this item. Maximum available: ' . $availableStock], 422);
            }

            $cartItem->update([
                'quantity' => $newQuantity
            ]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $product->current_price,
                'size' => $request->size,
                'color' => $request->color
            ]);
        }

        return response()->json([
            'message' => 'Product added to cart successfully',
            'cart_count' => $cart->item_count
        ]);
    }

    /**
     * Display the specified resource.
     */
    // public function show(string $id)
    // {
    //     return redirect()->route('shop.cart.index');
    // }

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit(string $id)
    // {
    //     return redirect()->route('shop.cart.index');
    // }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CartItem $item)
    {
        try {
            $request->validate([
                'quantity' => 'required|integer|min:1',
                'size' => 'nullable|string|max:50',
                'color' => 'nullable|string|max:50'
            ]);

            $cart = $this->getCart();
            $item->load('product');

            if ((int) $item->cart_id !== (int) $cart->id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Cart item not found.'
                ], 404);
            }

            $product = $item->product;
            $selectedSize = $request->filled('size') ? $request->size : null;
            $selectedColor = $request->filled('color') ? $request->color : null;

            // Validate selected size against product sizes (if configured)
            $availableSizes = is_array($product->sizes) ? $product->sizes : [];
            if (!empty($availableSizes) && $selectedSize && !in_array($selectedSize, $availableSizes, true)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Selected size is not available for this product.'
                ], 422);
            }

            // Validate selected color against product colors (supports string or object colors)
            $availableColors = [];
            if (is_array($product->colors)) {
                foreach ($product->colors as $c) {
                    if (is_array($c)) {
                        if (!empty($c['name'])) {
                            $availableColors[] = (string) $c['name'];
                        }
                    } elseif (is_string($c)) {
                        $availableColors[] = $c;
                    }
                }
            }
            if (!empty($availableColors) && $selectedColor && !in_array($selectedColor, $availableColors, true)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Selected color is not available for this product.'
                ], 422);
            }

            $availableStock = (int) $product->stock;
            if ($selectedSize && is_array($product->size_stocks) && !empty($product->size_stocks)) {
                $availableStock = (int) ($product->size_stocks[$selectedSize] ?? 0);
            }

            if ((int) $request->quantity > $availableStock) {
                return response()->json([
                    'success' => false,
                    'error' => 'Requested quantity not available in stock. Maximum available: ' . $availableStock
                ], 422);
            }

            $existingItem = $cart->items()
                ->where('product_id', $item->product_id)
                ->where('size', $selectedSize)
                ->where('color', $selectedColor)
                ->where('id', '!=', $item->id)
                ->first();

            if ($existingItem) {
                $newQuantity = $existingItem->quantity + $request->quantity;

                if ($newQuantity > $availableStock) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Cannot update. Total quantity would exceed available stock: ' . $availableStock
                    ], 422);
                }

                $existingItem->update(['quantity' => $newQuantity]);
                $item->delete();
                $updatedItem = $existingItem;
            } else {
                $item->update([
                    'quantity' => $request->quantity,
                    'size' => $selectedSize,
                    'color' => $selectedColor
                ]);
                $updatedItem = $item;
            }

            $cart->refresh(); // Refresh cart to get updated totals

            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully',
                'new_quantity' => $updatedItem->quantity,
                'new_total' => number_format($updatedItem->price * $updatedItem->quantity, 2),
                'new_size' => $updatedItem->size,
                'new_color' => $updatedItem->color,
                'cart_count' => $cart->item_count,
                'cart_total' => number_format($cart->total, 2),
                'item_merged' => isset($existingItem) && $existingItem->wasRecentlyCreated === false
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to update cart: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function remove($id)
    {
        try {
        $cart = $this->getCart();
            $cartItem = $cart->items()->findOrFail($id);
            $cartItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart successfully',
                'cart_count' => $cart->item_count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove item from cart'
            ], 500);
        }
    }

    /**
     * Clear all items from cart.
     */
    public function clear()
    {
        $cart = $this->getCart();
        $cart->items()->delete();
        return back()->with('success', 'Cart cleared successfully.');
    }

    /**
     * Process the checkout.
     */
    public function checkout(Request $request)
    {
        $cart = $this->getCart();

        if ($cart->items->isEmpty()) {
            return redirect()->route('shop.cart')->with('error', 'Your cart is empty.');
        }

        $validator = Validator::make($request->all(), [
            'shipping_name' => 'required|string|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Validate all products before processing
            foreach ($cart->items as $item) {
                $product = $item->product;
                if (!$product->is_active) {
                    throw new \Exception("Product {$product->name} is no longer available.");
                }

                // Check stock availability based on size
                $availableStock = $product->stock;
                if ($item->size && $product->size_stocks && isset($product->size_stocks[$item->size])) {
                    $availableStock = $product->size_stocks[$item->size];
                }

                if ($availableStock < $item->quantity) {
                    $sizeText = $item->size ? " (Size: {$item->size})" : "";
                    throw new \Exception("Insufficient stock for {$product->name}{$sizeText}. Available: {$availableStock}");
                }
            }

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => 'ORD-' . date('Ymd') . '-' . Str::random(6),
                'total_amount' => $cart->total,
                'status' => 'pending',
                'shipping_name' => $request->shipping_name,
                'shipping_phone' => $request->shipping_phone,
                'shipping_address' => $request->shipping_address,
                'notes' => $request->notes,
                'status_history' => [[
                    'status' => 'pending',
                    'notes' => 'Order placed by customer',
                    'changed_at' => now()
                ]]
            ]);

            // Create order items and update stock
            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'size' => $item->size,
                    'color' => $item->color,
                ]);

                // Reduce stock from specific size if available, otherwise from general stock
                $product = $item->product;
                if ($item->size && $product->size_stocks && isset($product->size_stocks[$item->size])) {
                    // Update size-specific stock
                    $sizeStocks = $product->size_stocks;
                    $sizeStocks[$item->size] -= $item->quantity;
                    $product->update(['size_stocks' => $sizeStocks]);
                }

                // Always reduce from general stock as well
                $product->decrement('stock', $item->quantity);
            }

            // Clear cart
            $cart->items()->delete();
            $cart->update(['status' => 'completed']);

            DB::commit();

            return redirect()->route('shop.orders.show', $order)
                ->with('success', 'Order placed successfully. Your order number is ' . $order->order_number);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Get cart count for header display
     */
    public function getCartCount()
    {
        $cart = $this->getCart();
        return response()->json(['count' => $cart->item_count]);
    }


}
