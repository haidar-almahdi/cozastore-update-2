<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Women\'s Fashion',
                'slug' => 'womens-fashion',
                'description' => 'Latest fashion trends for women',
                'is_active' => true,
            ],
            [
                'name' => 'Men\'s Fashion',
                'slug' => 'mens-fashion',
                'description' => 'Stylish clothing for men',
                'is_active' => true,
            ],
            [
                'name' => 'Accessories',
                'slug' => 'accessories',
                'description' => 'Fashion accessories and jewelry',
                'is_active' => true,
            ],
            [
                'name' => 'Shoes',
                'slug' => 'shoes',
                'description' => 'Comfortable and stylish footwear',
                'is_active' => true,
            ],
            [
                'name' => 'Bags',
                'slug' => 'bags',
                'description' => 'Handbags, backpacks and more',
                'is_active' => true,
            ]
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
