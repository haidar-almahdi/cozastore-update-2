<?php
// Debug script for product update issues
echo "<h1>Product Update Debug Script</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;} .success{color:green;} .error{color:red;} .info{color:blue;}</style>";

try {
    // Load Laravel
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';

    echo "<p class='success'>✅ Laravel loaded successfully</p>";

    // Get a product to test with
    $product = \App\Models\Product::first();

    if (!$product) {
        echo "<p class='error'>❌ No products found in database</p>";
        exit;
    }

    echo "<p class='success'>✅ Found product: {$product->name} (ID: {$product->id})</p>";

    // Test the update method
    echo "<h2>Testing Product Update</h2>";

    // Simulate request data
    $testData = [
        'name' => $product->name . ' (Updated)',
        'description' => 'Updated description',
        'price' => 99.99,
        'stock' => 50,
        'category_id' => $product->category_id,
        'sizes' => ['S', 'M', 'L'],
        'colors' => [['name' => 'Red', 'code' => '#ff0000']],
        'color_codes' => ['#ff0000'],
        'status' => 'active'
    ];

    echo "<p class='info'>Test data prepared:</p>";
    echo "<pre>" . json_encode($testData, JSON_PRETTY_PRINT) . "</pre>";

    // Try to update the product directly
    $originalName = $product->name;
    $product->update([
        'name' => $testData['name'],
        'description' => $testData['description'],
        'price' => $testData['price'],
        'stock' => $testData['stock'],
        'sizes' => $testData['sizes'],
        'colors' => $testData['colors'],
        'is_active' => true
    ]);

    // Refresh the product from database
    $product->refresh();

    if ($product->name !== $originalName) {
        echo "<p class='success'>✅ Product updated successfully!</p>";
        echo "<p>New name: {$product->name}</p>";
        echo "<p>New price: {$product->price}</p>";
        echo "<p>New stock: {$product->stock}</p>";

        // Revert the changes
        $product->update(['name' => $originalName]);
        echo "<p class='info'>Changes reverted for testing purposes</p>";
    } else {
        echo "<p class='error'>❌ Product update failed</p>";
    }

    // Check validation rules
    echo "<h2>Validation Test</h2>";

    $validator = \Illuminate\Support\Facades\Validator::make($testData, [
        'category_id' => 'required|exists:categories,id',
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
        'sizes' => 'nullable|array',
        'colors' => 'nullable|array',
        'color_codes' => 'nullable|array',
        'status' => 'nullable|in:active,inactive',
    ]);

    if ($validator->passes()) {
        echo "<p class='success'>✅ Validation passed</p>";
    } else {
        echo "<p class='error'>❌ Validation failed:</p>";
        foreach ($validator->errors()->all() as $error) {
            echo "<p class='error'>- {$error}</p>";
        }
    }

    // Check routes
    echo "<h2>Route Test</h2>";
    $route = route('admin.products.update', $product);
    echo "<p class='info'>Update route: {$route}</p>";

    // Check if user is authenticated (if session is available)
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    echo "<h2>Session Information</h2>";
    if (isset($_SESSION)) {
        echo "<p class='info'>Session is available</p>";
    } else {
        echo "<p class='error'>No session found</p>";
    }

} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p class='error'>Stack trace:</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
