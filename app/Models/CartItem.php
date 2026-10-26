<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'price',
        'size',
        'color'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the cart that owns the item.
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Get the product associated with the cart item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the subtotal for this item.
     */
    public function getSubtotalAttribute()
    {
        return $this->price * $this->quantity;
    }

    /**
     * Check if the item is available (in stock and active).
     */
    public function isAvailable()
    {
        return $this->product->is_active && $this->product->stock >= $this->quantity;
    }

    /**
     * Get the maximum quantity that can be ordered for this item.
     */
    public function getMaxOrderableQuantityAttribute()
    {
        return $this->product->stock;
    }

    /**
     * Check if the item has custom options (size or color).
     */
    public function hasOptions()
    {
        return !is_null($this->size) || !is_null($this->color);
    }

    /**
     * Get formatted options string.
     */
    public function getOptionsAttribute()
    {
        $options = [];
        if ($this->size) {
            $options[] = "Size: {$this->size}";
        }
        if ($this->color) {
            $options[] = "Color: {$this->color}";
        }
        return implode(', ', $options);
    }
} 