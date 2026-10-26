<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'status'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the cart.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items in the cart.
     */
    public function items()
    {
        return $this->hasMany(CartItem::class); //one to many
    }

    /**
     * Get the products in the cart.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'cart_items') //Many to many
            ->withPivot(['quantity', 'price', 'size', 'color'])
            ->withTimestamps();
    }

    /**
     * Calculate the total price of the cart.
     */
    public function getTotalAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }

    /**
     * Get the number of items in the cart.
     */
    public function getItemCountAttribute()
    {
        return $this->items->sum('quantity');
    }

    /**
     * Scope a query to only include active carts.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Check if the cart is empty.
     */
    public function isEmpty()
    {
        return $this->items->isEmpty();
    }

    /**
     * Check if the cart has any out of stock items.
     */
    public function hasOutOfStockItems()
    {
        return $this->items->contains(function ($item) {
            return !$item->product->is_active || $item->product->stock < $item->quantity;
        });
    }

    /**
     * Get out of stock items.
     */
    public function getOutOfStockItems()
    {
        return $this->items->filter(function ($item) {
            return !$item->product->is_active || $item->product->stock < $item->quantity;
        })->map(function ($item) {
            return $item->product->name;
        });
    }

    /**
     * Clear all items from the cart.
     */
    public function clear()
    {
        return $this->items()->delete();
    }

    /**
     * Add an item to the cart.
     */
    public function addItem($product, $quantity, $size = null, $color = null)
    {
        $existingItem = $this->items()
            ->where('product_id', $product->id)
            ->where('size', $size)
            ->where('color', $color)
            ->first();

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $quantity;
            if ($newQuantity > $product->stock) {
                throw new \Exception("Cannot add more of this item. Maximum available: {$product->stock}");
            }
            return $existingItem->update(['quantity' => $newQuantity]);
        }

        return $this->items()->create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'price' => $product->price,
            'size' => $size,
            'color' => $color
        ]);
    }

    /**
     * Update an item in the cart.
     */
    public function updateItem($product, $quantity, $size = null, $color = null)
    {
        return $this->items()
            ->where('product_id', $product->id)
            ->where('size', $size)
            ->where('color', $color)
            ->update([
                'quantity' => $quantity,
                'price' => $product->price
            ]);
    }

    /**
     * Remove an item from the cart.
     */
    public function removeItem($product, $size = null, $color = null)
    {
        return $this->items()
            ->where('product_id', $product->id)
            ->where('size', $size)
            ->where('color', $color)
            ->delete();
    }

    protected static function boot()
    {
        parent::boot();

        static::retrieved(function ($cart) {
            $cart->load(['items.product']);
        });
    }
}
