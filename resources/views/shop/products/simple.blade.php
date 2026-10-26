<!DOCTYPE html>
<html>
<head>
    <title>Products Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .product { border: 1px solid #ccc; margin: 10px; padding: 15px; display: inline-block; width: 300px; }
        .product img { width: 100%; height: 200px; object-fit: cover; }
        .product h3 { margin: 10px 0; }
        .product p { color: #666; }
        .price { font-size: 18px; font-weight: bold; color: #007bff; }
    </style>
</head>
<body>
    <h1>Products Test Page</h1>
    <p>Total products: {{ $products->count() }}</p>

    @if($products->count() > 0)
        @foreach($products as $product)
            <div class="product">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" onerror="this.src='/images/no-image.png'">
                <h3>{{ $product->name }}</h3>
                <p>{{ Str::limit($product->description, 100) }}</p>
                <div class="price">${{ number_format($product->price, 2) }}</div>
                <p><strong>Stock:</strong> {{ $product->stock }}</p>
                <p><strong>Category:</strong> {{ $product->category->name ?? 'No Category' }}</p>
                <p><strong>Image Path:</strong> {{ $product->image }}</p>
                <p><strong>Image URL:</strong> {{ $product->image_url }}</p>
                <p><strong>Sizes:</strong> {{ is_array($product->sizes) ? implode(', ', $product->sizes) : 'No sizes' }}</p>
                <p><strong>Colors:</strong>
                    @if(is_array($product->colors))
                        @foreach($product->colors as $color)
                            <span style="background: {{ $color['code'] ?? '#ccc' }}; padding: 2px 8px; margin: 2px; color: white;">
                                {{ $color['name'] ?? 'Unknown' }}
                            </span>
                        @endforeach
                    @else
                        No colors
                    @endif
                </p>
            </div>
        @endforeach
    @else
        <p style="color: red; font-size: 18px;">No products found!</p>
        <p>Debug info:</p>
        <ul>
            <li>Total products in database: {{ App\Models\Product::count() }}</li>
            <li>Active products: {{ App\Models\Product::where('is_active', true)->count() }}</li>
            <li>Categories: {{ App\Models\Category::count() }}</li>
        </ul>
    @endif
</body>
</html>
