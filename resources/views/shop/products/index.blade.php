@extends('layouts.app')

@section('title', 'Shop')

@section('styles')
<style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
            text-decoration: none;
            list-style: none;
        }

        body {
            background: linear-gradient(135deg, rgba(102,126,234,0.10) 0%, rgba(118,75,162,0.10) 100%);
            background-attachment: fixed;
            min-height: 100vh;
        }

        /* Page entrance animation */
        .page-content {
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Neutralize container styling to match light pages */
        .container { padding: 30px; }

        .btn-primary {
            background: #7494ec;
            border: none;
            color: #fff;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: #5a7de8;
        }

        /* Use default text colors like the rest of pages */
        h1, h2, h3, h4, h5, h6 { color: inherit !important; text-shadow: none; }
        .text-muted { color: #6c757d !important; }

        /* Pagination light theme tweaks */
        .pagination .page-link { color: #495057; }
        .pagination .page-link:hover { color: #0d6efd; }
        .pagination .page-item.active .page-link { background-color: #0d6efd; border-color: #0d6efd; color: #fff; }

        /* Footer: revert to default light theme */
        footer.bg3 { background-color: #222 !important; }

        .card {
            border: none;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
            transition: transform 0.3s ease;
            border-radius: 15px;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 35px rgba(0,0,0,0.4);
        }

        .card-img-top {
            height: 250px;
            object-fit: cover;
        }

        .block2-pic {
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .block2-pic img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .block2-pic:hover img {
            transform: scale(1.05);
        }

        .block2 {
            margin-bottom: 30px;
            transition: transform 0.3s ease;
        }

        .block2:hover {
            transform: translateY(-5px);
        }

        .product-card { position: relative; overflow: hidden; }
        .quick-view-btn {
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 2;
        }
        .product-card:hover .quick-view-btn { display: block; }
        .product-card::after {
            content: '';
            display: block;
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.2);
            opacity: 0;
            transition: opacity 0.2s;
            z-index: 1;
        }
        .product-card:hover::after { opacity: 1; }
        .modal-backdrop { z-index: 1040 !important; }
        .modal-content { z-index: 1100 !important; }
        .block2-btn-favorite {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 2;
        }

        .block2-btn-favorite {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 10;
        }

        .favorite-btn-top {
            background: rgba(255, 255, 255, 0.95);
            border: none;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            text-decoration: none;
            backdrop-filter: blur(10px);
        }

        .favorite-btn-top i {
            color: #ccc;
            font-size: 18px;
            transition: all 0.3s ease;
        }

        .favorite-btn-top:hover {
            background: white;
            transform: scale(1.15);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.3);
            text-decoration: none;
        }

        .favorite-btn-top:hover i {
            color: #dc3545;
            transform: scale(1.1);
        }

        .favorite-btn-top.active {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
        }

        .favorite-btn-top.active i {
            color: white;
            animation: heartBeat 0.6s ease-in-out;
        }

        .favorite-btn-top.active:hover {
            background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
        }

        .block2-pic {
            position: relative;
        }

        /* Filter buttons hover effects */
        .filter-btn:hover {
            background: linear-gradient(45deg, #667eea, #764ba2) !important;
            color: white !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        .filter-btn.active {
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        /* Product grid animations */
        .isotope-item {
            animation: slideInUp 0.6s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Enhanced product cards */
        .block2 {
            margin-bottom: 30px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border-radius: 20px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .block2:hover {
            transform: translateY(-12px) scale(1.03);
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
        }

        /* Product Info Card */
        .product-info-card {
            padding: 20px;
            background: linear-gradient(135deg, #fff 0%, #f8f9ff 100%);
        }

        .product-name {
            display: block;
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
            text-decoration: none;
            margin-bottom: 8px;
            line-height: 1.3;
            transition: all 0.3s ease;
        }

        .product-name:hover {
            color: #667eea;
            text-decoration: none;
            transform: translateX(2px);
        }

        .product-category {
            font-size: 0.85rem;
            color: #7f8c8d;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 500;
        }

        .product-price-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .product-price {
            font-size: 1.4rem;
            font-weight: 700;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stock-badge {
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .stock-badge.in-stock {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.2);
        }

        .stock-badge.out-of-stock {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.2);
        }

        .product-rating {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .stars {
            color: #ffc107;
            font-size: 0.9rem;
        }

        .stars i {
            margin-right: 2px;
        }

        .rating-text {
            font-size: 0.85rem;
            color: #6c757d;
            font-weight: 500;
        }

        /* Image hover effects */
        .block2-pic img {
            transition: all 0.4s ease;
            border-radius: 15px 15px 0 0;
        }

        .block2:hover .block2-pic img {
            transform: scale(1.08);
            filter: brightness(1.1);
        }

        /* Product Actions Styling */
        .product-actions {
            display: flex;
            gap: 8px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #f0f0f0;
        }

        .product-action-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px 12px;
            border: none;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }

        .add-to-cart-btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }

        .add-to-cart-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
            background: linear-gradient(135deg, #218838 0%, #1ea085 100%);
            color: white;
            text-decoration: none;
        }

        .favorite-action-btn {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
            color: #dc3545;
            border: 2px solid #e9ecef;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .favorite-action-btn:hover {
            transform: translateY(-2px);
            border-color: #dc3545;
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
            text-decoration: none;
        }

        .favorite-action-btn.active {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            border-color: #dc3545;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }

        .favorite-action-btn.active:hover {
            background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
        }

        .out-of-stock-btn {
            background: #f8f9fa;
            color: #6c757d;
            border: 2px solid #e9ecef;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .product-action-btn i {
            font-size: 0.9rem;
            transition: transform 0.3s ease;
        }

        .product-action-btn:hover i {
            transform: scale(1.1);
        }

        .favorite-action-btn:hover i {
            animation: heartBeat 0.6s ease-in-out;
        }

        @keyframes heartBeat {
            0% { transform: scale(1); }
            25% { transform: scale(1.2); }
            50% { transform: scale(1); }
            75% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .product-name {
                font-size: 1rem;
            }

            .product-price {
                font-size: 1.2rem;
            }

            .block2 {
                margin-bottom: 20px;
            }

            .product-actions {
                flex-direction: column;
                gap: 6px;
            }

            .product-action-btn {
                padding: 8px 10px;
                font-size: 0.8rem;
            }

            .product-action-btn span {
                display: none;
            }

            .product-action-btn i {
                font-size: 1rem;
            }
        }

        .assistant-product-card {
            border: 1px solid #e7edf8;
            border-radius: 10px;
            padding: 8px;
            display: flex;
            gap: 8px;
            margin-top: 6px;
            background: #fff;
        }

        .assistant-product-card img {
            width: 44px;
            height: 44px;
            object-fit: cover;
            border-radius: 6px;
            background: #f5f7fb;
        }

        .assistant-actions button {
            border: 1px solid #d8e1f3;
            background: #f7faff;
            color: #35507f;
            font-size: .82rem;
            border-radius: 999px;
            padding: 4px 10px;
            margin: 4px 6px 0 0;
            cursor: pointer;
        }
    </style>
@endsection

@section('content')

    <section class="container page-content pt-4">
        <div class="card" style="border-radius:16px; box-shadow:0 10px 25px rgba(102,126,234,.16);">
            <div class="card-body" style="padding:16px;">
                <div class="d-flex align-items-center justify-content-between flex-wrap mb-2">
                    <div>
                        <strong><i class="fa fa-robot text-primary"></i> Smart Shopping Assistant</strong>
                        <div class="text-muted" style="font-size:.9rem;">اكتب مثل: بدي فستان أسود تحت 60 دولار</div>
                    </div>
                    <button id="assistant-clear" type="button" class="btn btn-sm btn-outline-secondary">
                        <i class="fa fa-trash"></i> Clear
                    </button>
                </div>
                <div id="assistant-messages" style="max-height:210px; overflow:auto; border:1px solid #e9edf5; border-radius:10px; padding:10px; background:#fafcff; margin-bottom:10px;"></div>
                <form id="assistant-form" class="d-flex" style="gap:8px;">
                    @csrf
                    <input id="assistant-input" type="text" class="form-control" placeholder="اكتب طلبك..." required>
                    <button class="btn btn-primary" type="submit"><i class="fa fa-paper-plane"></i></button>
                </form>
            </div>
        </div>
    </section>

    <!-- Filter Bar -->
    <section class="container py-4 page-content">
        <div class="filter-section" style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-radius: 20px; padding: 30px; margin-bottom: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
            <h3 class="text-center mb-4" style="font-weight: 700; color: #333; font-size: 2rem;">Browse Our Products</h3>
            <div class="d-flex flex-wrap align-items-center justify-content-center mb-4">
                <a href="{{ route('shop.products.index') }}"
                   class="filter-btn m-2 {{ !request('category') ? 'active' : '' }}" style="padding: 12px 25px; border-radius: 25px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; border: 2px solid #667eea; {{ !request('category') ? 'background: linear-gradient(45deg, #667eea, #764ba2); color: white;' : 'background: transparent; color: #667eea;' }}">
                    <i class="fa fa-th-large" style="margin-right: 8px;"></i>All Products
                </a>
                @foreach($categories as $category)
                    <a href="{{ route('shop.products.index', ['category' => $category->slug]) }}"
                       class="filter-btn m-2 {{ request('category') == $category->slug ? 'active' : '' }}" style="padding: 12px 25px; border-radius: 25px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; border: 2px solid #667eea; {{ request('category') == $category->slug ? 'background: linear-gradient(45deg, #667eea, #764ba2); color: white;' : 'background: transparent; color: #667eea;' }}">
                        {{ $category->name }}
                        <span class="badge" style="background: {{ request('category') == $category->slug ? 'rgba(255,255,255,0.3)' : '#667eea' }}; color: white; margin-left: 8px; padding: 4px 8px; border-radius: 12px; font-size: 0.7rem;">{{ $category->products_count ?? 0 }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        @if(request('category'))
            <div class="alert alert-info mb-4">
                Showing products in category: <strong>{{ $categories->firstWhere('slug', request('category'))->name }}</strong>
                <a href="{{ route('shop.products.index') }}" class="float-right">Clear filter</a>
            </div>
        @endif

        <div class="row">
            @foreach($products as $product)
                <div class="col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item {{ $product->category->slug }}">
                    <div class="block2">
                        <div class="block2-pic hov-img0">
                            <a href="{{ route('shop.products.show', $product) }}" class="block2-img dis-block hov-img-zoom">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                            </a>
                            <div class="block2-btn-favorite">
                                @auth
                                    <button class="favorite-btn-top {{ auth()->user()->hasFavorited($product) ? 'active' : '' }}"
                                            data-product-id="{{ $product->id }}"
                                            onclick="toggleFavorite(this)"
                                            title="{{ auth()->user()->hasFavorited($product) ? 'Remove from favorites' : 'Add to favorites' }}">
                                        <i class="fa fa-heart"></i>
                                    </button>
                                @else
                                    <a href="{{ route('login') }}" class="favorite-btn-top" title="Login to add to favorites">
                                        <i class="fa fa-heart"></i>
                                    </a>
                                @endauth
                            </div>
                        </div>
                        <div class="product-info-card">
                            <div class="product-details">
                                <a href="{{ route('shop.products.show', $product->slug) }}" class="product-name">
                                    {{ $product->name }}
                                </a>
                                <div class="product-category">
                                    {{ $product->category->name ?? 'Uncategorized' }}
                                </div>
                                <div class="product-price-container">
                                    <span class="product-price">
                                        ${{ number_format($product->current_price, 2) }}
                                    </span>
                                    @if($product->has_active_discount)
                                        <small class="text-muted" style="text-decoration: line-through;">
                                            ${{ number_format($product->price, 2) }}
                                        </small>
                                    @endif
                                    @if($product->stock > 0)
                                        <span class="stock-badge in-stock">
                                            <i class="fa fa-check-circle"></i> In Stock
                                        </span>
                                    @else
                                        <span class="stock-badge out-of-stock">
                                            <i class="fa fa-times-circle"></i> Out of Stock
                                        </span>
                                    @endif
                                </div>
                                @if($product->has_active_discount)
                                    <div class="mb-2">
                                        <span class="badge badge-danger">-{{ $product->effective_discount_percentage }}% OFF</span>
                                        @if($product->isBlackFridayActive())
                                            <span class="badge badge-dark">Black Friday</span>
                                        @elseif($product->isQuickOfferActive())
                                            <span class="badge badge-info">Quick Offer</span>
                                        @else
                                            <span class="badge badge-secondary">Manual Discount</span>
                                        @endif
                                    </div>
                                @endif

                                <!-- Product Rating (placeholder) -->
                                <div class="product-rating">
                                    <div class="stars">
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star-o"></i>
                                    </div>
                                    <span class="rating-text">(4.0)</span>
                                </div>

                                <!-- Product Action Buttons -->
                                <div class="product-actions">
                                    @if($product->stock > 0)
                                        <button class="product-action-btn add-to-cart-btn"
                                                onclick="quickAddToCart({{ $product->id }}, '{{ $product->name }}')"
                                                title="Add to Cart">
                                            <i class="fa fa-shopping-cart"></i>
                                            <span>Add to Cart</span>
                                        </button>
                                    @else
                                        <button class="product-action-btn out-of-stock-btn" disabled>
                                            <i class="fa fa-times-circle"></i>
                                            <span>Out of Stock</span>
                                        </button>
                                    @endif

                                    @auth
                                        <button class="product-action-btn favorite-action-btn {{ auth()->user()->hasFavorited($product) ? 'active' : '' }}"
                                                data-product-id="{{ $product->id }}"
                                                onclick="toggleFavorite(this)"
                                                title="{{ auth()->user()->hasFavorited($product) ? 'Remove from favorites' : 'Add to favorites' }}">
                                            <i class="fa fa-heart"></i>
                                            <span>{{ auth()->user()->hasFavorited($product) ? 'Favorited' : 'Favorite' }}</span>
                                        </button>
                                    @else
                                        <a href="{{ route('login') }}" class="product-action-btn favorite-action-btn" title="Login to add to favorites">
                                            <i class="fa fa-heart"></i>
                                            <span>Favorite</span>
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    <!-- Quick View Modal -->
    <div class="modal fade" id="quickViewModal" tabindex="-1" role="dialog" aria-labelledby="quickViewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quickViewModalLabel">Product Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="quickViewContent">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="product-image-container">
                                <img id="modalProductImage" src="" alt="" class="img-fluid">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h3 id="modalProductName"></h3>
                            <p id="modalProductPrice" class="text-primary h4"></p>
                            <p id="modalProductDescription" class="text-muted"></p>

                            <form id="addToCartForm" class="mt-4">
                                @csrf
                                <input type="hidden" id="modalProductId" name="product_id">

                                <div class="form-group">
                                    <label for="modalSize">Size</label>
                                    <select class="form-control" id="modalSize" name="size" required>
                                        <option value="">Select Size</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="modalColor">Color</label>
                                    <select class="form-control" id="modalColor" name="color" required>
                                        <option value="">Select Color</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="modalQuantity">Quantity</label>
                                    <input type="number" class="form-control" id="modalQuantity" name="quantity" value="1" min="1" required>
                                </div>

                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fa fa-shopping-cart"></i> Add to Cart
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        (function(){
            const form = document.getElementById('assistant-form');
            const input = document.getElementById('assistant-input');
            const box = document.getElementById('assistant-messages');
            const clearBtn = document.getElementById('assistant-clear');
            const storageKey = 'assistant_chat_v1';
            if (!form || !input || !box) return;

            function persist() {
                sessionStorage.setItem(storageKey, box.innerHTML);
            }

            function restore() {
                const html = sessionStorage.getItem(storageKey);
                if (html) {
                    box.innerHTML = html;
                    box.scrollTop = box.scrollHeight;
                } else {
                    msg('أهلاً! أنا مساعد التسوق. كيف بقدر ساعدك اليوم؟');
                }
            }

            function msg(text, me = false) {
                const div = document.createElement('div');
                div.style.marginBottom = '8px';
                div.innerHTML = me
                    ? `<div class="text-right"><span class="badge badge-primary">${text}</span></div>`
                    : `<div class="text-left"><span class="badge badge-light" style="border:1px solid #dfe6f3;">${text}</span></div>`;
                box.appendChild(div);
                box.scrollTop = box.scrollHeight;
                persist();
            }

            function renderProducts(products = []) {
                if (!products.length) return;
                const wrap = document.createElement('div');
                wrap.style.marginBottom = '8px';
                wrap.innerHTML = `<div class="text-left">${products.map(p => `
                    <a href="/shop/products/${p.slug}" class="assistant-product-card">
                        <img src="${p.image_url}" alt="${p.name}">
                        <div>
                            <div style="font-weight:600;color:#2f3b52;">${p.name}</div>
                            <div style="font-size:.82rem;color:#6e7a92;">$${p.price}</div>
                        </div>
                    </a>
                `).join('')}</div>`;
                box.appendChild(wrap);
                box.scrollTop = box.scrollHeight;
                persist();
            }

            function renderActions(actions = []) {
                if (!actions.length) return;
                const div = document.createElement('div');
                div.className = 'assistant-actions';
                div.innerHTML = actions.map(a => `<button type="button" data-action="${a}">${a}</button>`).join('');
                div.addEventListener('click', function(e){
                    const btn = e.target.closest('button[data-action]');
                    if (!btn) return;
                    input.value = btn.getAttribute('data-action');
                    input.focus();
                });
                box.appendChild(div);
                box.scrollTop = box.scrollHeight;
                persist();
            }

            form.addEventListener('submit', function(e){
                e.preventDefault();
                const message = input.value.trim();
                if (!message) return;
                msg(message, true);
                input.value = '';

                fetch('{{ route('shop.assistant.chat') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ message })
                })
                .then(r => r.json())
                .then(data => {
                    const fallbackReply = data?.message || data?.error || 'ما وصلتني نتيجة واضحة. جرّب كلمة ثانية.';
                    msg(data.reply || fallbackReply);
                    renderProducts(Array.isArray(data.products) ? data.products : []);
                    renderActions(Array.isArray(data.follow_ups) ? data.follow_ups : []);
                })
                .catch(() => msg('صار خطأ، حاول مرة ثانية.'));
            });

            if (clearBtn) {
                clearBtn.addEventListener('click', function(){
                    box.innerHTML = '';
                    sessionStorage.removeItem(storageKey);
                    msg('تم مسح المحادثة. كيف بقدر ساعدك؟');
                });
            }

            restore();
        })();

        $(document).ready(function() {
            // Quick View Modal
            $('.quick-view-btn').click(function(e) {
                e.preventDefault();
                var productId = $(this).data('product-id');

                // Show loading spinner
                $('#quickViewContent').html('<div class="text-center py-5"><span class="spinner-border"></span></div>');
                $('#quickViewModal').modal('show');

                // Fetch product details
                $.ajax({
                    url: '/shop/products/' + productId,
                    type: 'GET',
                    success: function(response) {
                        // Update modal content
                        $('#modalProductId').val(response.id);
                        $('#modalProductName').text(response.name);
                        $('#modalProductPrice').text('$' + response.price);
                        $('#modalProductDescription').text(response.description);
                        $('#modalProductImage').attr('src', response.image_url);

                        // Update size options
                        var sizeSelect = $('#modalSize');
                        sizeSelect.empty().append('<option value="">Select Size</option>');
                        response.sizes.forEach(function(size) {
                            sizeSelect.append('<option value="' + size + '">' + size + '</option>');
                        });

                        // Update color options
                        var colorSelect = $('#modalColor');
                        colorSelect.empty().append('<option value="">Select Color</option>');
                        response.colors.forEach(function(color) {
                            colorSelect.append('<option value="' + color + '">' + color + '</option>');
                        });
                    },
                    error: function() {
                        $('#quickViewContent').html('<div class="alert alert-danger">Error loading product details</div>');
                    }
                });
            });

            // Add to Cart Form Submission
            $('#addToCartForm').submit(function(e) {
                e.preventDefault();

                $.ajax({
                    url: '{{ route("shop.cart.add") }}',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        // Show success message
                        alert('Product added to cart successfully!');
                        // Close modal
                        $('#quickViewModal').modal('hide');
                        // Update cart count if you have one
                        if (response.cart_count) {
                            updateCartCount(response.cart_count);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            var errorHtml = '<div class="alert alert-danger"><ul>';
                            $.each(errors, function(key, value) {
                                errorHtml += '<li>' + value[0] + '</li>';
                            });
                            errorHtml += '</ul></div>';
                            $('#addToCartForm').prepend(errorHtml);
                        } else {
                            alert('Error adding product to cart');
                        }
                    }
                });
            });

            // Favorite functionality
            window.toggleFavorite = function(button) {
                const productId = button.dataset.productId;
                const icon = button.querySelector('i');

                console.log('Toggling favorite for product ID:', productId);
                console.log('User authenticated:', @auth 'true' @else 'false' @endauth);

                // Check if user is logged in
                @guest
                    console.log('User not authenticated, redirecting to login');
                    showNotification('Please login first to add favorites.', 'error');
                    setTimeout(() => window.location.href = '/login', 1500);
                    return;
                @endguest

                @auth
                    console.log('User is authenticated: {{ auth()->user()->email }}');
                @endauth

                // Show loading state
                button.style.pointerEvents = 'none';
                icon.className = 'fa fa-spinner fa-spin';

                const url = '/shop/favorites/' + productId + '/toggle';
                const token = $('meta[name="csrf-token"]').attr('content');

                console.log('Making request to:', url);
                console.log('CSRF Token:', token);

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);

                    if (!response.ok) {
                        return response.text().then(text => {
                            console.log('Error response text:', text);
                            throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    // Reset loading state
                    button.style.pointerEvents = 'auto';
                    icon.className = 'fa fa-heart';

                    if (data.status === 'added') {
                        button.classList.add('active');
                        showNotification('Product added to favorites!', 'success');
                    } else {
                        button.classList.remove('active');
                        showNotification('Product removed from favorites!', 'info');
                    }

                    // Update favorite count in navbar if exists
                    updateFavoriteCount(data.favorites_count);
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Reset loading state
                    button.style.pointerEvents = 'auto';
                    icon.className = 'fa fa-heart';

                    // Show detailed error
                    if (error.message.includes('401')) {
                        showNotification('Please login first to add favorites.', 'error');
                        window.location.href = '/login';
                    } else if (error.message.includes('419')) {
                        showNotification('Session expired. Please refresh the page.', 'error');
                        setTimeout(() => window.location.reload(), 2000);
                    } else {
                        showNotification('Something went wrong: ' + error.message, 'error');
                    }
                });
            };

            function updateFavoriteCount(count) {
                // Update text counters
                const counters = document.querySelectorAll('.favorite-count');
                counters.forEach(counter => {
                    counter.textContent = count;
                });

                // Update navbar icon counter
                const favoriteIcon = document.querySelector('a[href="{{ route('shop.favorites') }}"]');
                if (favoriteIcon) {
                    favoriteIcon.setAttribute('data-notify', count > 0 ? count : '0');
                }
            }

            function updateCartCount(count) {
                // Update text counters
                const counters = document.querySelectorAll('.cart-count');
                counters.forEach(counter => {
                    counter.textContent = count;
                });

                // Update navbar icon counter
                const cartIcon = document.querySelector('a[href="{{ route('shop.cart') }}"]');
                if (cartIcon) {
                    cartIcon.setAttribute('data-notify', count > 0 ? count : '0');
                }
            }

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
                `;
                notification.innerHTML = `
                    <div class="d-flex align-items-center">
                        <i class="fa fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} mr-2"></i>
                        ${message}
                    </div>
                `;

                // Add to page
                document.body.appendChild(notification);

                // Remove after 3 seconds
                setTimeout(() => {
                    notification.style.animation = 'slideOut 0.3s ease-in';
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 300);
                }, 3000);
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
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    border: none;
                    border-radius: 8px;
                }
            `;
            document.head.appendChild(style);

            // Initial favorite count update
            $.get('/shop/favorites/count')
                .done(function(data) {
                    $('.favorite-count').text(data.count);
                });
        });

        // Quick Add to Cart function
        function quickAddToCart(productId, productName) {
            // Show loading state
            const button = event.target.closest('.add-to-cart-btn');
            const originalContent = button.innerHTML;
            button.innerHTML = '<i class="fa fa-spinner fa-spin"></i><span>Adding...</span>';
            button.disabled = true;

            // Create form data for basic add to cart (default size/color if available)
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            formData.append('product_id', productId);
            formData.append('quantity', 1);

            fetch('{{ route("shop.cart.add") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Reset button state
                button.innerHTML = originalContent;
                button.disabled = false;

                if (data.message) {
                    showNotification(`${productName} added to cart!`, 'success');
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
                button.innerHTML = originalContent;
                button.disabled = false;

                // If product requires size/color selection, redirect to product page
                if (error.message.includes('size') || error.message.includes('color')) {
                    showNotification('Please select size and color on product page', 'info');
                    setTimeout(() => {
                        window.location.href = `/shop/products/${productId}`;
                    }, 1500);
                } else {
                    showNotification('Error adding product to cart', 'error');
                }
            });
        }
    </script>
@endsection
