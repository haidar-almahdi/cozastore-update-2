<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateTestProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:test-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test products';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $category = \App\Models\Category::first();
        if (!$category) {
            $this->error('No categories found. Please create a category first.');
            return 1;
        }

        $products = [
            [
                'name' => 'Test T-Shirt',
                'slug' => 'test-t-shirt',
                'description' => 'A comfortable test t-shirt',
                'price' => 25.99,
                'stock' => 50,
                'sizes' => ['S', 'M', 'L', 'XL'],
                'colors' => ['Red', 'Blue', 'Green'],
                'category_id' => $category->id,
                'is_active' => true,
                'is_featured' => false
            ],
            [
                'name' => 'Test Jeans',
                'slug' => 'test-jeans',
                'description' => 'Comfortable test jeans',
                'price' => 45.99,
                'stock' => 30,
                'sizes' => ['28', '30', '32', '34'],
                'colors' => ['Blue', 'Black'],
                'category_id' => $category->id,
                'is_active' => true,
                'is_featured' => false
            ]
        ];

        foreach ($products as $productData) {
            \App\Models\Product::firstOrCreate(
                ['slug' => $productData['slug']],
                $productData
            );
        }

        $this->info('Test products created successfully!');
        $this->info('Total products: ' . \App\Models\Product::count());

        return 0;
    }
}
