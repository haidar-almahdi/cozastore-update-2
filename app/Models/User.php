<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'gender',
        'role',
        'wallet_balance',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'wallet_balance' => 'decimal:2',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user is a customer.
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function isDriver(): bool
    {
        return $this->role === 'driver';
    }

    /**
     * Get the user's active cart.
     */
    public function activeCart()
    {
        return $this->hasOne(Cart::class)->where('status', 'active');
    }

    /**
     * Get or create the user's active cart.
     */
    public function getActiveCartAttribute()
    {
        return $this->carts()->where('status', 'active')->first()
            ?: $this->carts()->create(['status' => 'active']);
    }

    /**
     * Get all carts belonging to the user.
     */
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Get the user's favorites.
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Get the products that the user has favorited.
     */
    public function favoriteProducts()
    {
        return $this->belongsToMany(Product::class, 'favorites')
            ->withTimestamps();
    }

    public function loyaltyTransactions()
    {
        return $this->hasMany(LoyaltyPointTransaction::class);
    }

    public function assignedDeliveries()
    {
        return $this->hasMany(Order::class, 'driver_id');
    }

    /**
     * Check if a product is favorited by the user.
     */
    public function hasFavorited(Product $product): bool
    {
        return $this->favorites()->where('product_id', $product->id)->exists();
    }

    /**
     * Add a product to favorites.
     */
    public function addToFavorites(Product $product): void
    {
        $this->favorites()->firstOrCreate(['product_id' => $product->id]);
    }

    /**
     * Remove a product from favorites.
     */
    public function removeFromFavorites(Product $product): void
    {
        $this->favorites()->where('product_id', $product->id)->delete();
    }
}
