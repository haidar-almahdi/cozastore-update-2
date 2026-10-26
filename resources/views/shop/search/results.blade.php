@extends('layouts.app')

@section('title', 'Search Results for "' . $query . '"')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Search Results for "{{ $query }}"</h2>

            @if($products->count() > 0 || $categories->count() > 0)
                <!-- Categories Results -->
                @if($categories->count() > 0)
                    <div class="mb-5">
                        <h4 class="mb-3">Categories ({{ $categories->count() }})</h4>
                        <div class="row">
                            @foreach($categories as $category)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $category->name }}</h5>
                                            <p class="card-text">{{ Str::limit($category->description, 100) }}</p>
                                            <p class="text-muted">{{ $category->products_count }} products</p>
                                            <a href="{{ route('shop.products.index', ['category' => $category->slug]) }}" class="btn btn-primary">View Products</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Products Results -->
                @if($products->count() > 0)
                    <div class="mb-5">
                        <h4 class="mb-3">Products ({{ $products->total() }})</h4>
                        <div class="row">
                            @foreach($products as $product)
                                <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                                    <div class="card h-100">
                                        <img src="{{ $product->image_url }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                                        <div class="card-body d-flex flex-column">
                                            <h6 class="card-title">{{ $product->name }}</h6>
                                            <p class="card-text text-muted small">{{ Str::limit($product->description, 60) }}</p>
                                            <div class="mt-auto">
                                                <p class="h5 text-primary mb-1">${{ number_format($product->current_price, 2) }}</p>
                                                @if($product->has_active_discount)
                                                    <small class="text-muted" style="text-decoration: line-through;">${{ number_format($product->price, 2) }}</small>
                                                @endif
                                                <a href="{{ route('shop.products.show', $product) }}" class="btn btn-outline-primary btn-sm">View Details</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($products->hasPages())
                            <div class="d-flex justify-content-center">
                                {{ $products->appends(request()->query())->links() }}
                            </div>
                        @endif
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fa fa-search fa-3x text-muted mb-3"></i>
                    <h4>No results found</h4>
                    <p class="text-muted">Try searching with different keywords or browse our categories.</p>
                    <a href="{{ route('shop.products.index') }}" class="btn btn-primary">Browse All Products</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
