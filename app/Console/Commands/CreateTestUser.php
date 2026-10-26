<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Product;
use Illuminate\Console\Command;

class CreateTestUser extends Command
{
    protected $signature = 'create:test-user {--favorites=3 : Number of favorite products to add}';
    protected $description = 'Create a test user with sample data';

    public function handle(): int
    {
        $email = 'test@example.com';

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Test User',
                'password' => 'password123',
                'phone' => '+963987654321',
                'address' => 'Test Address',
                'gender' => 'female',
                'role' => 'customer'
            ]
        );

        $this->info('✅ Test user created successfully!');

        // Add favorites if products exist
        $favoritesCount = (int) $this->option('favorites');
        if ($favoritesCount > 0) {
            $this->addFavorites($user, $favoritesCount);
        }

        $this->displayUserInfo($user);
        return 0;
    }

    private function addFavorites(User $user, int $count): void
    {
        $products = Product::take($count)->get();

        if ($products->isEmpty()) {
            $this->warn('No products found to add as favorites');
            return;
        }

        $user->favorites()->delete(); // Clear existing favorites

        foreach ($products as $product) {
            $user->favorites()->create(['product_id' => $product->id]);
        }

        $this->info("Added {$products->count()} favorite products");
    }

    private function displayUserInfo(User $user): void
    {
        $this->table(['Field', 'Value'], [
            ['Email', $user->email],
            ['Name', $user->name],
            ['Role', $user->role],
            ['Favorites', $user->favorites()->count()]
        ]);
    }
}
