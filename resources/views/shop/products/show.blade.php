@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-6">
            <!-- Product Images Gallery -->
            <div class="product-images-gallery">
                <!-- Main Image Container -->
                <div class="main-image-container position-relative mb-3">
                    <div class="main-image-wrapper">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                             class="main-image" id="mainImage">
                    </div>

                    <!-- Navigation Arrows -->
                    @php
                        $allImages = [$product->image_url];
                        if($product->additional_images_urls) {
                            $allImages = array_merge($allImages, $product->additional_images_urls->toArray());
                        }
                    @endphp

                    @if(count($allImages) > 1)
                        <button class="image-nav-btn prev-btn" onclick="previousImage()">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <button class="image-nav-btn next-btn" onclick="nextImage()">
                            <i class="fa fa-chevron-right"></i>
                        </button>

                        <!-- Image Counter -->
                        <div class="image-counter">
                            <span id="currentImageIndex">1</span> / {{ count($allImages) }}
                        </div>
                    @endif
                </div>

                <!-- Thumbnail Images -->
                @if(count($allImages) > 1)
                    <div class="thumbnail-container">
                        <div class="thumbnail-wrapper" id="thumbnailWrapper">
                            @foreach($allImages as $index => $image)
                                <div class="thumbnail-item {{ $index === 0 ? 'active' : '' }}"
                                     onclick="changeMainImage('{{ $image }}', {{ $index }})">
                                    <img src="{{ $image }}" alt="{{ $product->name }}" class="thumbnail-image">
                                </div>
                            @endforeach
                        </div>

                        <!-- Thumbnail Navigation -->
                        <button class="thumbnail-nav-btn thumbnail-prev" onclick="scrollThumbnails('left')">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <button class="thumbnail-nav-btn thumbnail-next" onclick="scrollThumbnails('right')">
                            <i class="fa fa-chevron-right"></i>
                        </button>
                    </div>
                @endif
            </div>
            <!-- Ratings Under Gallery -->
            <div class="ratings-under-gallery mt-3">
                <h3 class="mb-3">Ratings</h3>
                @auth
                    <form method="POST" action="{{ route('shop.products.ratings.store', $product) }}">
                        @csrf
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="stars selectable-stars">
                                @for ($i = 1; $i <= 5; $i++)
                                    <label style="cursor:pointer;margin-right:6px;">
                                        <input type="radio" name="stars" value="{{ $i }}" class="d-none" {{ (int)($userRating ?? 0) === $i ? 'checked' : '' }}>
                                        <i class="fa {{ (int)($userRating ?? 0) >= $i ? 'fa-star' : 'fa-star-o' }} star-input" data-value="{{ $i }}"></i>
                                    </label>
                                @endfor
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">Save rating</button>
                        </div>
                        @error('stars')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </form>
                @else
                    <a class="btn btn-outline-primary btn-sm" href="{{ route('login') }}">Login to rate</a>
                @endauth
            </div>
        </div>

        <div class="col-lg-6">
            <!-- Product Details -->
            <div class="product-details">
                <div class="product-header mb-4">
                    <h1 class="product-title">{{ $product->name }}</h1>
                    <div class="product-meta">
                        <span class="category-badge">
                            <i class="fa fa-tag"></i> {{ $product->category->name ?? 'Uncategorized' }}
                        </span>
                        <div class="product-rating">
                            <div class="stars">
                                @php
                                    $avg = $averageRating ?? 0;
                                    $full = floor($avg);
                                    $half = ($avg - $full) >= 0.5 ? 1 : 0;
                                    $empty = 5 - $full - $half;
                                @endphp
                                @for ($i = 0; $i < $full; $i++)
                                    <i class="fa fa-star"></i>
                                @endfor
                                @if ($half)
                                    <i class="fa fa-star-half-o"></i>
                                @endif
                                @for ($i = 0; $i < $empty; $i++)
                                    <i class="fa fa-star-o"></i>
                                @endfor
                            </div>
                            <span class="rating-text">({{ number_format($avg, 1) }}) • {{ $ratingsCount ?? 0 }} reviews</span>
                        </div>
                    </div>
                </div>

                <div class="price-section mb-4">
                    <div class="price-container">
                        <div>
                            <span class="current-price">${{ number_format($product->current_price, 2) }}</span>
                            @if($product->has_active_discount)
                                <div>
                                    <small class="text-muted" style="text-decoration: line-through;">
                                        ${{ number_format($product->price, 2) }}
                                    </small>
                                    <span class="badge badge-danger">-{{ $product->effective_discount_percentage }}%</span>
                                    @if($product->isBlackFridayActive())
                                        <span class="badge badge-dark">Black Friday</span>
                                    @elseif($product->isQuickOfferActive())
                                        <span class="badge badge-info">Quick Offer</span>
                                    @else
                                        <span class="badge badge-secondary">Manual Discount</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="price-details">
                            @if($product->stock > 0)
                                <span class="stock-status in-stock">
                                    <i class="fa fa-check-circle"></i> {{ $product->stock }} in stock
                                </span>
                            @else
                                <span class="stock-status out-of-stock">
                                    <i class="fa fa-times-circle"></i> Out of stock
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="product-description mb-4">
                    <p>{{ $product->description }}</p>
                </div>

                <!-- Add to Cart Form -->
                <form id="addToCartForm" class="mb-4">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    @if($product->sizes && count($product->sizes) > 0)
                        <div class="form-group mb-4">
                            @php
                                // تحديد نوع الأحجام (ملابس أم أحذية أم إكسسوارات)
                                $clothingSizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
                                $shoeSizes = ['35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46'];
                                $accessorySizes = ['One Size', 'Small', 'Medium', 'Large', 'Adjustable'];

                                $hasClothingSizes = !empty(array_intersect($product->sizes, $clothingSizes));
                                $hasShoeSizes = !empty(array_intersect($product->sizes, $shoeSizes));
                                $hasAccessorySizes = !empty(array_intersect($product->sizes, $accessorySizes));

                                if ($hasShoeSizes) {
                                    $sizeType = 'shoes';
                                } elseif ($hasAccessorySizes) {
                                    $sizeType = 'accessories';
                                } else {
                                    $sizeType = 'clothing';
                                }
                            @endphp

                            <div class="d-flex align-items-center justify-content-between">
                            <label class="size-section-title mb-0">
                                @if($sizeType === 'shoes')
                                    <i class="fa fa-shoe-prints"></i>
                                    Available Shoe Sizes
                                @elseif($sizeType === 'accessories')
                                    <i class="fa fa-gem"></i>
                                    Available Sizes
                                @else
                                    <i class="fa fa-expand-arrows-alt"></i>
                                    Available Sizes
                                @endif
                            </label>
                            <a href="{{ route('shop.products.size-guide', $product) }}" class="btn btn-outline-info btn-sm">
                                <i class="fa fa-ruler"></i> Size Guide
                            </a>
                            </div>
                            <div class="size-options-container">
                                @foreach($product->sizes as $index => $size)
                                    @php
                                        $hasPerSizeStocks = is_array($product->size_stocks) && count($product->size_stocks) > 0;
                                        $sizeStock = $hasPerSizeStocks
                                            ? (int) ($product->size_stocks[$size] ?? 0)
                                            : (int) $product->stock;
                                        $isAvailable = $sizeStock > 0;
                                    @endphp
                                    <label class="size-option-card {{ !$isAvailable ? 'out-of-stock' : '' }}">
                                        <input type="radio" name="size" value="{{ $size }}" {{ !$isAvailable ? 'disabled' : '' }} {{ count($product->sizes) > 0 ? 'required' : '' }}>
                                        <div class="size-card-content">
                                            <span class="size-name">
                                                @if($sizeType === 'shoes')
                                                    Size {{ $size }}
                                                @elseif($sizeType === 'accessories')
                                                    {{ $size }}
                                                @else
                                                    {{ $size }}
                                                @endif
                                            </span>
                                            <span class="size-stock {{ $isAvailable ? 'in-stock' : 'no-stock' }}">
                                                @if($isAvailable)
                                                    {{ $sizeStock }} pcs
                                                @else
                                                    Out of stock
                                                @endif
                                            </span>
                                            @if(!$isAvailable)
                                                <div class="unavailable-overlay">
                                                    <i class="fa fa-times"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            <div id="size-info-panel" class="mt-3" style="display:none;">
                                <div class="card border-info">
                                    <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between">
                                        <strong><i class="fa fa-ruler"></i> Size details</strong>
                                        <span class="badge badge-light" id="size-info-unit"></span>
                                    </div>
                                    <div class="card-body py-2 px-3" id="size-info-body">
                                        <div class="text-muted small">Select a size to view its measurements.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($product->colors && count($product->colors) > 0)
                        <div class="form-group mb-4">
                            <label class="color-section-title">
                                <i class="fa fa-palette"></i>
                                Available Colors
                            </label>
                            <div class="color-options-container">
                                @foreach($product->colors as $color)
                                    <label class="color-option-card">
                                        <input type="radio" name="color" value="{{ $color['code'] }}" required>
                                        <div class="color-card-content">
                                            <div class="color-preview" style="background-color: {{ $color['code'] }};"></div>
                                            <span class="color-name">{{ $color['name'] }}</span>
                                            <div class="color-selection-indicator">
                                                <i class="fa fa-check"></i>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="form-group mb-4">
                        <label class="quantity-section-title">
                            <i class="fa fa-sort-numeric-up"></i>
                            Quantity
                        </label>
                        <div class="quantity-container">
                            <div class="quantity-selector">
                                <button class="quantity-btn minus-btn" type="button" onclick="changeQuantity(-1)">
                                    <i class="fa fa-minus"></i>
                                </button>
                                <input type="number" name="quantity" id="quantity" class="quantity-input" value="1" min="1" max="{{ $product->stock }}" required>
                                <button class="quantity-btn plus-btn" type="button" onclick="changeQuantity(1)">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                            <div class="stock-info">
                                <span class="stock-text">
                                    <i class="fa fa-box"></i>
                                    <span id="current-stock">{{ $product->stock }}</span> available in stock
                                    <span id="size-stock-info" style="display: none;">
                                        (<span id="size-stock-count"></span> for selected size)
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="action-buttons-container">
                        @if($product->stock > 0)
                            <button type="submit" class="add-to-cart-btn">
                                <i class="fa fa-shopping-cart"></i>
                                <span>Add to Cart</span>
                                <div class="btn-ripple"></div>
                            </button>
                        @else
                            <button type="button" class="add-to-cart-btn out-of-stock" disabled>
                                <i class="fa fa-times-circle"></i>
                                <span>Out of Stock</span>
                            </button>
                        @endif

                        @auth
                            <button type="button" class="favorite-btn {{ auth()->user()->hasFavorited($product) ? 'active' : '' }}" onclick="toggleFavorite({{ $product->id }}, this)">
                                <i class="fa fa-heart{{ auth()->user()->hasFavorited($product) ? '' : '-o' }}"></i>
                                <span class="favorite-tooltip">{{ auth()->user()->hasFavorited($product) ? 'Remove from favorites' : 'Add to favorites' }}</span>
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="favorite-btn">
                                <i class="fa fa-heart-o"></i>
                                <span class="favorite-tooltip">Login to add to favorites</span>
                            </a>
                        @endauth
                    </div>
                </form>

                <!-- Product Info -->
                <div class="product-info">
                    <div class="row">
                        <div class="col-6">
                            <strong>Stock:</strong> {{ $product->stock }} available in stock
                        </div>
                        <div class="col-6">
                            <strong>Category:</strong> {{ $product->category->name ?? 'Uncategorized' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container pb-5">
    <div class="row">
        <div class="col-lg-12 mb-4">
            <h3 class="mb-3">Comments</h3>
            @auth
                <form method="POST" action="{{ route('shop.products.comments.store', $product) }}" class="mb-3 comments-form">
                    @csrf
                    <div class="mb-2">
                        <textarea name="content" rows="3" class="form-control" placeholder="Write your comment..."></textarea>
                        @error('content')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-comment-gradient btn-sm">Add comment</button>
                </form>
            @else
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('login') }}">Login to comment</a>
            @endauth

            <div class="list-group comments-list">
                @forelse(($product->comments ?? collect())->sortByDesc('created_at') as $comment)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong>{{ $comment->user->name ?? 'User' }}</strong>
                            <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                        </div>
                        <div>{{ $comment->content }}</div>
                    </div>
                @empty
                    <div class="text-muted">No comments yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
/* Product Images Gallery */
.product-images-gallery {
    max-width: 100%;
}

/* Product Details Styling */
.product-header {
    border-bottom: 2px solid #f8f9fa;
    padding-bottom: 20px;
}

.product-title {
    font-size: 2.2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 15px;
    line-height: 1.2;
}

.product-meta {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.category-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: linear-gradient(45deg, #667eea, #764ba2);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
    width: fit-content;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.product-rating {
    display: flex;
    align-items: center;
    gap: 10px;
}

.stars {
    color: #ffc107;
    font-size: 1.1rem;
}

.stars i {
    margin-right: 3px;
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.rating-text {
    color: #6c757d;
    font-size: 0.95rem;
    font-weight: 500;
}

/* Price Section */
.price-section {
    background: linear-gradient(135deg, #f8f9ff 0%, #e8f2ff 100%);
    padding: 25px;
    border-radius: 15px;
    border: 1px solid rgba(102, 126, 234, 0.1);
}

.price-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.current-price {
    font-size: 2.5rem;
    font-weight: 800;
    background: linear-gradient(45deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.price-details {
    text-align: right;
}

.stock-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stock-status.in-stock {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
    border: 2px solid rgba(40, 167, 69, 0.2);
}

.stock-status.out-of-stock {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
    border: 2px solid rgba(220, 53, 69, 0.2);
}

/* Responsive Design for Product Details */
@media (max-width: 768px) {
    .product-title {
        font-size: 1.8rem;
    }

    .current-price {
        font-size: 2rem;
    }

    .price-container {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }

    .price-details {
        text-align: left;
    }
}

/* Interactive Rating Stars */
.selectable-stars {
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.selectable-stars .star-input {
    font-size: 1.6rem;
    color: #d1d5db; /* gray-300 */
    transition: transform 0.15s ease, color 0.2s ease, text-shadow 0.2s ease;
    filter: drop-shadow(0 1px 1px rgba(0,0,0,0.08));
}

.selectable-stars .star-input.active,
.selectable-stars .star-input.hovered {
    color: #ffc107; /* Bootstrap warning */
    text-shadow: 0 2px 6px rgba(255, 193, 7, 0.5);
}

.selectable-stars .star-input:active {
    transform: scale(0.92);
}

.selectable-stars .star-input:focus {
    outline: none;
}

.rating-text {
    font-weight: 600;
}

/* Comments section styling */
.comments-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.comments-list .list-group-item {
    border: 1px solid #eef0f3;
    border-radius: 14px !important;
    margin-bottom: 12px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.06);
    padding: 14px 16px;
}

.comments-list .list-group-item strong {
    color: #2c3e50;
}

.comments-list .list-group-item small {
    color: #8692a6 !important;
}

.comments-form textarea.form-control {
    border-radius: 12px;
    border: 2px solid #eef0f3;
}

.comments-form textarea.form-control:focus {
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
    border-color: #667eea;
}

.main-image-container {
    background: #f8f9fa;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.main-image-wrapper {
    position: relative;
    width: 100%;
    height: 500px;
    overflow: hidden;
}

.main-image {
    width: 100%;
    height: 100%;
    object-fit: contain;
    transition: transform 0.3s ease;
}

.main-image:hover {
    transform: scale(1.02);
}

/* Navigation Arrows */
.image-nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0,0,0,0.7);
    color: white;
    border: none;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.3s ease;
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.image-nav-btn:hover {
    background: rgba(0,0,0,0.9);
    transform: translateY(-50%) scale(1.1);
}

.prev-btn {
    left: 15px;
}

.next-btn {
    right: 15px;
}

/* Image Counter */
.image-counter {
    position: absolute;
    bottom: 15px;
    right: 15px;
    background: rgba(0,0,0,0.7);
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 500;
}

/* Thumbnail Container */
.thumbnail-container {
    position: relative;
    margin-top: 15px;
}

.thumbnail-wrapper {
    display: flex;
    gap: 10px;
    overflow-x: auto;
    scroll-behavior: smooth;
    padding: 10px 0;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.thumbnail-wrapper::-webkit-scrollbar {
    display: none;
}

.thumbnail-item {
    flex-shrink: 0;
    width: 80px;
    height: 80px;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    border: 3px solid transparent;
    transition: all 0.3s ease;
    position: relative;
}

.thumbnail-item:hover {
    border-color: #007bff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,123,255,0.3);
}

.thumbnail-item.active {
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.2);
}

.thumbnail-image {
    width: 100%;
    height: 100%;
    object-fit: contain;
    transition: transform 0.3s ease;
    background: #f8f9fa;
}

.thumbnail-item:hover .thumbnail-image {
    transform: scale(1.1);
}

/* Thumbnail Navigation */
.thumbnail-nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255,255,255,0.9);
    border: 1px solid #ddd;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.3s ease;
    z-index: 5;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    color: #333;
}

.thumbnail-nav-btn:hover {
    background: white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    transform: translateY(-50%) scale(1.1);
}

.thumbnail-prev {
    left: -15px;
}

.thumbnail-next {
    right: -15px;
}

/* Size Section Styling */
.size-section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f8f9fa;
}

.size-section-title i {
    color: #667eea;
    font-size: 1.1rem;
}

.size-options-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.size-option-card {
    position: relative;
    cursor: pointer;
    margin: 0;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.size-option-card input {
    display: none;
}

.size-card-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 20px 15px;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
    border: 2px solid #e9ecef;
    border-radius: 15px;
    transition: all 0.3s ease;
    position: relative;
    min-height: 90px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.size-name {
    font-size: 1.1rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.size-stock {
    font-size: 0.85rem;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 12px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.size-stock.in-stock {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
    border: 1px solid rgba(40, 167, 69, 0.2);
}

.size-stock.no-stock {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
    border: 1px solid rgba(220, 53, 69, 0.2);
}

/* Hover Effects */
.size-option-card:not(.out-of-stock):hover .size-card-content {
    transform: translateY(-3px);
    border-color: #667eea;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2);
    background: linear-gradient(135deg, #ffffff 0%, #e8f2ff 100%);
}

.size-option-card:not(.out-of-stock):hover .size-name {
    color: #667eea;
}

/* Selected State */
.size-option-card input:checked + .size-card-content {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #667eea;
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
}

.size-option-card input:checked + .size-card-content .size-name {
    color: white;
}

.size-option-card input:checked + .size-card-content .size-stock.in-stock {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border-color: rgba(255, 255, 255, 0.3);
}

/* Out of Stock State */
.size-option-card.out-of-stock {
    cursor: not-allowed;
}

.size-option-card.out-of-stock .size-card-content {
    background: #f8f9fa;
    border-color: #dee2e6;
    opacity: 0.6;
}

.size-option-card.out-of-stock .size-name {
    color: #6c757d;
}

.unavailable-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(220, 53, 69, 0.9);
    color: white;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: bold;
}

/* Responsive Design for Sizes */
@media (max-width: 768px) {
    .size-options-container {
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 12px;
    }

    .size-card-content {
        padding: 15px 10px;
        min-height: 80px;
    }

    .size-name {
        font-size: 1rem;
    }

    .size-stock {
        font-size: 0.75rem;
    }
}

@media (max-width: 480px) {
    .size-options-container {
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }

    .size-card-content {
        padding: 12px 8px;
        min-height: 70px;
    }

    .size-name {
        font-size: 0.9rem;
    }
}

/* Color Section Styling */
.color-section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f8f9fa;
}

.color-section-title i {
    color: #667eea;
    font-size: 1.1rem;
}

.color-options-container {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 15px;
}

.color-option-card {
    cursor: pointer;
    margin: 0;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.color-option-card input {
    display: none;
}

.color-card-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 15px;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
    border: 2px solid #e9ecef;
    border-radius: 15px;
    transition: all 0.3s ease;
    position: relative;
    min-width: 100px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.color-preview {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    border: 3px solid #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15), inset 0 1px 3px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    position: relative;
}

.color-name {
    font-size: 0.85rem;
    font-weight: 600;
    color: #2c3e50;
    text-align: center;
    text-transform: capitalize;
}

.color-selection-indicator {
    position: absolute;
    top: 8px;
    right: 8px;
    background: #28a745;
    color: white;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    opacity: 0;
    transform: scale(0);
    transition: all 0.3s ease;
}

/* Hover Effects for Colors */
.color-option-card:hover .color-card-content {
    transform: translateY(-3px);
    border-color: #667eea;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2);
    background: linear-gradient(135deg, #ffffff 0%, #e8f2ff 100%);
}

.color-option-card:hover .color-preview {
    transform: scale(1.1);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2), inset 0 1px 3px rgba(0,0,0,0.1);
}

.color-option-card:hover .color-name {
    color: #667eea;
}

/* Selected State for Colors */
.color-option-card input:checked + .color-card-content {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #667eea;
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
}

.color-option-card input:checked + .color-card-content .color-name {
    color: white;
}

.color-option-card input:checked + .color-card-content .color-preview {
    border-color: white;
    transform: scale(1.05);
}

.color-option-card input:checked + .color-card-content .color-selection-indicator {
    opacity: 1;
    transform: scale(1);
}

/* Responsive Design for Colors */
@media (max-width: 768px) {
    .color-options-container {
        gap: 12px;
    }

    .color-card-content {
        padding: 12px;
        min-width: 85px;
    }

    .color-preview {
        width: 40px;
        height: 40px;
    }

    .color-name {
        font-size: 0.75rem;
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .main-image-wrapper {
        height: 350px;
    }

    .image-nav-btn {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }

    .prev-btn {
        left: 10px;
    }

    .next-btn {
        right: 10px;
    }

    .thumbnail-item {
        width: 60px;
        height: 60px;
    }
}

.thumbnail-image {
    transition: all 0.3s ease;
}

.thumbnail-image:hover {
    border-color: #007bff !important;
}

/* Image Loading Animation */
.main-image {
    opacity: 1;
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.main-image.loading {
    opacity: 0.7;
}

/* Zoom Effect on Hover */
.main-image-wrapper:hover .main-image {
    transform: scale(1.02);
}

/* Loading Spinner */
.image-loading {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 5;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #007bff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Quantity Section Styling */
.quantity-section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f8f9fa;
}

.quantity-section-title i {
    color: #667eea;
    font-size: 1.1rem;
}

.quantity-container {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.quantity-selector {
    display: flex;
    align-items: center;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
    border: 2px solid #e9ecef;
    border-radius: 15px;
    padding: 8px;
    width: fit-content;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.quantity-selector:hover {
    border-color: #667eea;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.15);
}

.quantity-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    width: 45px;
    height: 45px;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
}

.quantity-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
}

.quantity-btn:active {
    transform: translateY(0);
}

.quantity-input {
    border: none;
    background: transparent;
    text-align: center;
    font-size: 1.2rem;
    font-weight: 700;
    color: #2c3e50;
    width: 80px;
    padding: 10px;
    outline: none;
}

.quantity-input::-webkit-outer-spin-button,
.quantity-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.quantity-input[type=number] {
    -moz-appearance: textfield;
}

.stock-info {
    display: flex;
    align-items: center;
}

.stock-text {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    color: #28a745;
    background: rgba(40, 167, 69, 0.1);
    padding: 8px 16px;
    border-radius: 20px;
    border: 1px solid rgba(40, 167, 69, 0.2);
}

.stock-text i {
    font-size: 0.85rem;
}

/* Responsive Design for Quantity */
@media (max-width: 768px) {
    .quantity-selector {
        padding: 6px;
    }

    .quantity-btn {
        width: 40px;
        height: 40px;
        font-size: 12px;
    }

    .quantity-input {
        width: 70px;
        font-size: 1.1rem;
    }

    .stock-text {
        font-size: 0.8rem;
        padding: 6px 12px;
    }
}

/* Smooth Transitions */
* {
    box-sizing: border-box;
}

.product-images-gallery * {
    transition: all 0.3s ease;
}

/* Action Buttons Styling */
.action-buttons-container {
    display: flex;
    gap: 15px;
    margin-bottom: 30px;
    align-items: center;
}

.add-to-cart-btn {
    flex: 1;
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    border: none;
    padding: 18px 30px;
    border-radius: 15px;
    font-size: 1.1rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
}

.add-to-cart-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(40, 167, 69, 0.4);
    background: linear-gradient(135deg, #218838 0%, #1ea085 100%);
}

.add-to-cart-btn:active {
    transform: translateY(-1px);
}

.add-to-cart-btn i {
    font-size: 1.2rem;
    transition: transform 0.3s ease;
}

.add-to-cart-btn:hover i {
    transform: scale(1.1);
}

.add-to-cart-btn.out-of-stock {
    background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
    cursor: not-allowed;
    opacity: 0.7;
    box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
}

.add-to-cart-btn.out-of-stock:hover {
    transform: none;
    background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
    box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
}

.btn-ripple {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.add-to-cart-btn:active .btn-ripple {
    width: 300px;
    height: 300px;
}

.favorite-btn {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
    color: #dc3545;
    border: 2px solid #e9ecef;
    padding: 18px 20px;
    border-radius: 15px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    min-width: 70px;
    height: 60px;
    font-weight: 600;
}

.favorite-btn:hover {
    transform: translateY(-3px);
    border-color: #dc3545;
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
    box-shadow: 0 8px 25px rgba(220, 53, 69, 0.3);
    text-decoration: none;
}

.favorite-btn.active {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
    border-color: #dc3545;
    box-shadow: 0 8px 25px rgba(220, 53, 69, 0.3);
}

.favorite-btn.active:hover {
    background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
}

.favorite-btn i {
    font-size: 1.3rem;
    transition: all 0.3s ease;
}

.favorite-btn:hover i {
    transform: scale(1.2);
    animation: heartBeat 0.6s ease-in-out;
}

.favorite-tooltip {
    position: absolute;
    bottom: -45px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 0.8rem;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: all 0.3s ease;
    z-index: 10;
}

.favorite-btn:hover .favorite-tooltip {
    opacity: 1;
    bottom: -40px;
}

@keyframes heartBeat {
    0% { transform: scale(1); }
    25% { transform: scale(1.3); }
    50% { transform: scale(1); }
    75% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

/* Responsive Design for Action Buttons */
@media (max-width: 768px) {
    .action-buttons-container {
        gap: 12px;
    }

    .add-to-cart-btn {
        padding: 15px 25px;
        font-size: 1rem;
    }

    .favorite-btn {
        padding: 15px;
        min-width: 55px;
        height: 55px;
    }

    .favorite-btn i {
        font-size: 1.1rem;
    }

    .favorite-tooltip {
        font-size: 0.7rem;
        padding: 6px 10px;
    }
}

/* Gradient button for Add comment */
.btn-comment-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    border: none;
    padding: 8px 14px;
    border-radius: 10px;
    font-weight: 700;
    letter-spacing: 0.3px;
    box-shadow: 0 6px 16px rgba(102, 126, 234, 0.35);
}

.btn-comment-gradient:hover {
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 10px 24px rgba(118, 75, 162, 0.45);
}

.btn-comment-gradient:active {
    transform: translateY(0);
}

.btn-comment-gradient:focus {
    box-shadow: 0 0 0 0.2rem rgba(118, 75, 162, 0.25);
}

/* Ratings block under gallery */
.ratings-under-gallery {
    background: #fff;
    border: 1px solid #eef0f3;
    border-radius: 14px;
    padding: 14px 16px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.06);
}
</style>
@endsection

@section('scripts')
<script>
// Image Gallery Variables
let currentImageIndex = 0;
const allImages = [
    '{{ $product->image_url }}',
    @if($product->additional_images_urls)
        @foreach($product->additional_images_urls as $image)
            '{{ $image }}',
        @endforeach
    @endif
];

// Change Main Image Function
function changeMainImage(src, index = null) {
    document.getElementById('mainImage').src = src;

    if (index !== null) {
        currentImageIndex = index;
        updateImageCounter();
    }

    // Update active thumbnail
    document.querySelectorAll('.thumbnail-item').forEach((item, i) => {
        item.classList.remove('active');
        if (i === currentImageIndex) {
            item.classList.add('active');
        }
    });
}

// Navigate to Previous Image
function previousImage() {
    currentImageIndex = currentImageIndex > 0 ? currentImageIndex - 1 : allImages.length - 1;
    changeMainImage(allImages[currentImageIndex], currentImageIndex);
}

// Navigate to Next Image
function nextImage() {
    currentImageIndex = currentImageIndex < allImages.length - 1 ? currentImageIndex + 1 : 0;
    changeMainImage(allImages[currentImageIndex], currentImageIndex);
}

// Update Image Counter
function updateImageCounter() {
    const counter = document.getElementById('currentImageIndex');
    if (counter) {
        counter.textContent = currentImageIndex + 1;
    }
}

// Scroll Thumbnails
function scrollThumbnails(direction) {
    const wrapper = document.getElementById('thumbnailWrapper');
    const scrollAmount = 100;

    if (direction === 'left') {
        wrapper.scrollLeft -= scrollAmount;
    } else {
        wrapper.scrollLeft += scrollAmount;
    }
}

// Keyboard Navigation
document.addEventListener('keydown', function(e) {
    if (e.key === 'ArrowLeft') {
        previousImage();
    } else if (e.key === 'ArrowRight') {
        nextImage();
    }
});

// Touch/Swipe Support for Mobile
let startX = 0;
let endX = 0;

document.getElementById('mainImage').addEventListener('touchstart', function(e) {
    startX = e.touches[0].clientX;
});

document.getElementById('mainImage').addEventListener('touchend', function(e) {
    endX = e.changedTouches[0].clientX;
    handleSwipe();
});

function handleSwipe() {
    const threshold = 50;
    const diff = startX - endX;

    if (Math.abs(diff) > threshold) {
        if (diff > 0) {
            nextImage(); // Swipe left - next image
        } else {
            previousImage(); // Swipe right - previous image
        }
    }
}

function changeQuantity(change) {
    const quantityInput = document.getElementById('quantity');
    const currentValue = parseInt(quantityInput.value);
    const newValue = currentValue + change;
    const max = parseInt(quantityInput.max);

    if (newValue >= 1 && newValue <= max) {
        quantityInput.value = newValue;
    }
}

// Update quantity max based on selected size
function updateQuantityMax() {
    const selectedSize = document.querySelector('input[name="size"]:checked');
    const quantityInput = document.getElementById('quantity');

    if (selectedSize) {
        const sizeStock = sizeStocks[selectedSize.value] || {{ $product->stock }};
        quantityInput.max = sizeStock;

        // Reset quantity to 1 if current value exceeds new max
        if (parseInt(quantityInput.value) > sizeStock) {
            quantityInput.value = Math.min(1, sizeStock);
        }

        // Update stock display
        updateStockDisplay(sizeStock);
    }
}

// Update stock display
function updateStockDisplay(sizeStock) {
    const stockElement = document.getElementById('current-stock');
    const sizeStockInfo = document.getElementById('size-stock-info');
    const sizeStockCount = document.getElementById('size-stock-count');

    if (stockElement && sizeStockInfo && sizeStockCount) {
        stockElement.textContent = {{ $product->stock }};

        if (sizeStock !== {{ $product->stock }}) {
            sizeStockCount.textContent = sizeStock;
            sizeStockInfo.style.display = 'inline';
        } else {
            sizeStockInfo.style.display = 'none';
        }
    }
}

// Size stocks data
const sizeStocks = {
    @if($product->size_stocks)
        @foreach($product->size_stocks as $size => $stock)
            '{{ $size }}': {{ $stock }},
        @endforeach
    @endif
};

// Add event listeners to size options
document.addEventListener('DOMContentLoaded', function() {
    const sizeInputs = document.querySelectorAll('input[name="size"]');
    sizeInputs.forEach(input => {
        input.addEventListener('change', () => {
            updateQuantityMax();
            fetchAndRenderSizeInfo(input.value);
        });
    });

    // Initialize with first available size if any
    const firstAvailableSize = document.querySelector('input[name="size"]:not(:disabled)');
    if (firstAvailableSize) {
        firstAvailableSize.checked = true;
        updateQuantityMax();
        fetchAndRenderSizeInfo(firstAvailableSize.value);
    }
});

// Interactive Rating Stars
document.addEventListener('DOMContentLoaded', function() {
    const container = document.querySelector('.selectable-stars');
    if (!container) return;
    const stars = Array.from(container.querySelectorAll('.star-input'));
    const radios = Array.from(container.querySelectorAll('input[type="radio"][name="stars"]'));

    const getCheckedValue = () => {
        const r = radios.find(x => x.checked);
        return r ? parseInt(r.value) : 0;
    };

    const setActive = (value) => {
        stars.forEach((icon, idx) => {
            if (idx < value) {
                icon.classList.add('active');
                icon.classList.remove('fa-star-o');
                icon.classList.add('fa-star');
            } else {
                icon.classList.remove('active');
                icon.classList.remove('fa-star');
                icon.classList.add('fa-star-o');
            }
        });
    };

    const setHovered = (value) => {
        stars.forEach((icon, idx) => {
            if (idx < value) {
                icon.classList.add('hovered');
            } else {
                icon.classList.remove('hovered');
            }
        });
    };

    const clearHovered = () => {
        stars.forEach(icon => icon.classList.remove('hovered'));
    };

    container.addEventListener('mousemove', (e) => {
        const icon = e.target.closest('.star-input');
        if (!icon) return;
        const value = parseInt(icon.getAttribute('data-value'));
        setHovered(value);
    });

    container.addEventListener('mouseleave', () => {
        clearHovered();
        setActive(getCheckedValue());
    });

    stars.forEach(icon => {
        icon.addEventListener('click', () => {
            const value = parseInt(icon.getAttribute('data-value'));
            const radio = radios.find(r => parseInt(r.value) === value);
            if (radio) {
                radio.checked = true;
            }
            setActive(value);
        });
    });

    setActive(getCheckedValue());
});

// Add to Cart Form
document.getElementById('addToCartForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const submitButton = this.querySelector('.add-to-cart-btn');
    const originalContent = submitButton.innerHTML;

    // Show loading state
    submitButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i><span>Adding to Cart...</span>';
    submitButton.disabled = true;

    const formData = new FormData(this);

    fetch('{{ route("shop.cart.add") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Reset button state
        submitButton.innerHTML = originalContent;
        submitButton.disabled = false;

        if (data.message) {
            showNotification(data.message, 'success');
            // Update cart count if available
            if (data.cart_count && window.updateCartCount) {
                window.updateCartCount(data.cart_count);
            }
        } else if (data.error) {
            showNotification(data.error, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Reset button state
        submitButton.innerHTML = originalContent;
        submitButton.disabled = false;
        showNotification('Error adding product to cart', 'error');
    });
});

// Toggle Favorite
function toggleFavorite(productId, button) {
    const icon = button.querySelector('i');
    const originalClass = icon.className;

    console.log('Toggle favorite for product ID:', productId);

    // Show loading
    icon.className = 'fa fa-spinner fa-spin';
    button.disabled = true;

    // Create form data
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

    const url = `/shop/favorites/${productId}/toggle`;
    console.log('Request URL:', url);

    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response ok:', response.ok);

        if (!response.ok) {
            return response.text().then(text => {
                console.log('Error response body:', text);
                throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Success response data:', data);

        if (data.error) {
            console.log('Server returned error:', data.error);
            showNotification('Error: ' + data.error, 'error');
            icon.className = originalClass;
        } else if (data.status === 'added') {
            console.log('Product added to favorites');
            icon.className = 'fa fa-heart';
            button.classList.add('active');
            showNotification('Product added to favorites!', 'success');
        } else if (data.status === 'removed') {
            console.log('Product removed from favorites');
            icon.className = 'fa fa-heart-o';
            button.classList.remove('active');
            showNotification('Product removed from favorites!', 'info');
        } else {
            console.log('Unexpected response:', data);
            icon.className = originalClass;
        }

        // Update favorite count if available
        if (data.favorites_count !== undefined && window.updateFavoriteCount) {
            window.updateFavoriteCount(data.favorites_count);
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        icon.className = originalClass;
        showNotification('Error updating favorites: ' + error.message, 'error');
    })
    .finally(() => {
        button.disabled = false;
    });
}

// Show notification function
function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} notification-toast`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        animation: slideIn 0.3s ease-out;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        border: none;
        font-weight: 600;
    `;
    notification.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fa fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} mr-2"></i>
            ${message}
        </div>
    `;

    // Add to page
    document.body.appendChild(notification);

    // Remove after 4 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-in';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 4000);
}

// Add CSS for notifications
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    .notification-toast {
        backdrop-filter: blur(10px);
    }
`;
document.head.appendChild(style);

// Fetch and render size-specific info
function fetchAndRenderSizeInfo(size) {
    const panel = document.getElementById('size-info-panel');
    const body = document.getElementById('size-info-body');
    const unitEl = document.getElementById('size-info-unit');
    if (!panel || !body) return;

    panel.style.display = 'block';
    body.innerHTML = '<div class="text-muted small"><i class="fa fa-spinner fa-spin"></i> Loading size details...</div>';
    unitEl.textContent = '';

    const url = new URL("{{ route('shop.products.size-info', $product) }}", window.location.origin);
    url.searchParams.set('size', size);

    fetch(url.toString(), {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (!data.found) {
            body.innerHTML = '<div class="text-muted small">No measurements available for this size.</div>';
            unitEl.textContent = '';
            return;
        }

        const measurements = data.measurements || {};
        const skipKeys = new Set(['size','us','uk','eu','id','created_at','updated_at']);
        const rows = Object.keys(measurements)
            .filter(k => !skipKeys.has(k) && measurements[k] !== null && measurements[k] !== '')
            .map(k => {
                const label = k.replace(/_/g,' ').replace(/\b\w/g, c => c.toUpperCase());
                const val = measurements[k];
                return `<div class="d-flex justify-content-between py-1 border-bottom"><span class="text-muted">${label}</span><strong>${val}</strong></div>`;
            });

        if (rows.length === 0) {
            body.innerHTML = '<div class="text-muted small">No measurements available for this size.</div>';
        } else {
            body.innerHTML = rows.join('');
        }

        unitEl.textContent = (data.unit || '').toUpperCase();
    })
    .catch(() => {
        body.innerHTML = '<div class="text-danger small">Failed to load size details.</div>';
        unitEl.textContent = '';
    });
}
</script>
@endsection
