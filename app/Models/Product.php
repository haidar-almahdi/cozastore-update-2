<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use App\Models\SiteSetting;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'sizes',
        'size_stocks',
        'colors',
        'image',
        'additional_images',
        'is_featured',
        'is_active',
        'category_id',
        'ai_keywords',
        'discount_percentage',
        'discount_starts_at',
        'discount_ends_at',
        'black_friday_enabled',
        'quick_offer_enabled',
        'quick_offer_percentage',
        'quick_offer_starts_at',
        'quick_offer_ends_at',
    ];

    protected $casts = [
        'sizes' => 'array',
        'size_stocks' => 'array',
        'colors' => 'array',
        'additional_images' => 'array',
        'ai_keywords' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'black_friday_enabled' => 'boolean',
        'quick_offer_enabled' => 'boolean',
        'price' => 'decimal:2',
        'discount_starts_at' => 'datetime',
        'discount_ends_at' => 'datetime',
        'quick_offer_starts_at' => 'datetime',
        'quick_offer_ends_at' => 'datetime',
    ];

    public function isManualDiscountActive(?Carbon $at = null): bool
    {
        $at = $at ?: now();
        if (!$this->discount_percentage || $this->discount_percentage <= 0) {
            return false;
        }

        if ($this->discount_starts_at && $at->lt($this->discount_starts_at)) {
            return false;
        }

        if ($this->discount_ends_at && $at->gt($this->discount_ends_at)) {
            return false;
        }

        return true;
    }

    public function isBlackFridayActive(?Carbon $at = null): bool
    {
        if (!$this->black_friday_enabled) {
            return false;
        }

        $settings = static::getBlackFridaySettings();
        if (!empty($settings['force_active'])) {
            return true;
        }

        $at = $at ?: now();
        $month = (int) $settings['month'];
        $startDay = (int) $settings['start_day'];
        $endDay = (int) $settings['end_day'];

        return ((int) $at->month === $month) && ((int) $at->day >= $startDay) && ((int) $at->day <= $endDay);
    }

    public function getBlackFridayPercentageAttribute(): int
    {
        $settings = static::getBlackFridaySettings();
        return (int) $settings['discount_percentage'];
    }

    public function getEffectiveDiscountPercentageAttribute(): int
    {
        if ($this->isBlackFridayActive()) {
            return max(0, min(90, (int) $this->black_friday_percentage));
        }

        if ($this->isQuickOfferActive()) {
            return max(0, min(90, (int) $this->quick_offer_percentage));
        }

        $manual = $this->isManualDiscountActive() ? (int) $this->discount_percentage : 0;
        return max(0, min(90, $manual));
    }

    public function isQuickOfferActive(?Carbon $at = null): bool
    {
        if (!$this->quick_offer_enabled || (int) $this->quick_offer_percentage <= 0) {
            return false;
        }

        $at = $at ?: now();
        if ($this->quick_offer_starts_at && $at->lt($this->quick_offer_starts_at)) {
            return false;
        }
        if ($this->quick_offer_ends_at && $at->gt($this->quick_offer_ends_at)) {
            return false;
        }

        return true;
    }

    public function getCurrentPriceAttribute(): float
    {
        $discount = $this->effective_discount_percentage;
        if ($discount <= 0) {
            return (float) $this->price;
        }

        return round(((float) $this->price) * (1 - ($discount / 100)), 2);
    }

    public function getHasActiveDiscountAttribute(): bool
    {
        return $this->effective_discount_percentage > 0;
    }

    public static function getBlackFridaySettings(): array
    {
        $defaults = [
            'discount_percentage' => (int) config('discounts.black_friday.discount_percentage', 30),
            'month' => (int) config('discounts.black_friday.month', 11),
            'start_day' => (int) config('discounts.black_friday.start_day', 25),
            'end_day' => (int) config('discounts.black_friday.end_day', 30),
        ];

        if (!Schema::hasTable('site_settings')) {
            return $defaults;
        }

        return [
            'discount_percentage' => (int) SiteSetting::getValue('black_friday.discount_percentage', $defaults['discount_percentage']),
            'month' => (int) SiteSetting::getValue('black_friday.month', $defaults['month']),
            'start_day' => (int) SiteSetting::getValue('black_friday.start_day', $defaults['start_day']),
            'end_day' => (int) SiteSetting::getValue('black_friday.end_day', $defaults['end_day']),
            'force_active' => (int) SiteSetting::getValue('black_friday.force_active', 0),
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function comments()
    {
        return $this->hasMany(ProductComment::class);
    }

    public function ratings()
    {
        return $this->hasMany(ProductRating::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the cart items for this product.
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the carts that contain this product.
     */
    public function carts()
    {
        return $this->belongsToMany(Cart::class, 'cart_items')
            ->withPivot(['quantity', 'price', 'size', 'color'])
            ->withTimestamps();
    }

    /**
     * Get the favorites for this product.
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Get the users who have favorited this product.
     */
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')
            ->withTimestamps();
    }

    // Accessor for image URL
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/no-image.png');
    }

    // Accessor for additional images URLs
    public function getAdditionalImagesUrlsAttribute()
    {
        if ($this->additional_images) {
            return collect($this->additional_images)->map(function ($image) {
                return asset('storage/' . $image);
            });
        }
        return collect();
    }

    // Accessor to ensure sizes always returns an array
    public function getSizesAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    // Accessor to ensure colors always returns an array
    public function getColorsAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    // Accessor to ensure additional_images always returns an array
    public function getAdditionalImagesAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * Check if the product is favorited by a user.
     *
     * @param \App\Models\User|null $user
     * @return bool
     */
    public function isFavoritedBy($user)
    {
        if (!$user) {
            return false;
        }
        return $user->hasFavorited($this);
    }
}
