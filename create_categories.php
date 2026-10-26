<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Category;

// Check if categories exist
$categoriesCount = Category::count();
echo "Current categories count: " . $categoriesCount . "\n";

if ($categoriesCount == 0) {
    echo "Creating default categories...\n";

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
        Category::create($category);
        echo "Created category: " . $category['name'] . "\n";
    }

    echo "Categories created successfully!\n";
} else {
    echo "Categories already exist:\n";
    Category::all()->each(function($cat) {
        echo "- " . $cat->name . " (Active: " . ($cat->is_active ? 'Yes' : 'No') . ")\n";
    });
}
