<?php

// Simple script to create categories
$host = 'localhost';
$dbname = 'cozastore';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if categories exist
    $stmt = $pdo->query("SELECT COUNT(*) FROM categories");
    $count = $stmt->fetchColumn();

    echo "Current categories count: $count\n";

    if ($count == 0) {
        echo "Creating default categories...\n";

        $categories = [
            ['Women\'s Fashion', 'womens-fashion', 'Latest fashion trends for women'],
            ['Men\'s Fashion', 'mens-fashion', 'Stylish clothing for men'],
            ['Accessories', 'accessories', 'Fashion accessories and jewelry'],
            ['Shoes', 'shoes', 'Comfortable and stylish footwear'],
            ['Bags', 'bags', 'Handbags, backpacks and more']
        ];

        $sql = "INSERT INTO categories (name, slug, description, is_active, created_at, updated_at) VALUES (?, ?, ?, 1, NOW(), NOW())";
        $stmt = $pdo->prepare($sql);

        foreach ($categories as $category) {
            $stmt->execute($category);
            echo "Created category: {$category[0]}\n";
        }

        echo "Categories created successfully!\n";
    } else {
        echo "Categories already exist:\n";
        $stmt = $pdo->query("SELECT name, is_active FROM categories");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $active = $row['is_active'] ? 'Yes' : 'No';
            echo "- {$row['name']} (Active: $active)\n";
        }
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
