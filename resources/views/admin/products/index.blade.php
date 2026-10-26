@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fa fa-cubes text-primary"></i> Products Management
                        </h1>
                        <p class="page-subtitle">Manage and organize your store products</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Add New Product
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td>
                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                                 class="rounded" style="width: 50px; height: 50px; object-fit: contain; background: #f8f9fa;">
                                        </td>
                                        <td>
                                            <strong>{{ $product->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $product->category->name ?? 'No Category' }}</span>
                                        </td>
                                        <td>
                                            <strong>${{ number_format($product->price, 2) }}</strong>
                                        </td>
                                        <td>
                                            @if($product->stock > 0)
                                                <span class="badge bg-success">{{ $product->stock }} in stock</span>
                                            @else
                                                <span class="badge bg-danger">Out of stock</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($product->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.products.edit', $product) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                                                      class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fa fa-box fa-3x mb-3"></i>
                                                <h5>No products found</h5>
                                                <p>Start by adding your first product.</p>
                                                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                                                    <i class="fa fa-plus"></i> Add Product
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($products->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $products->onEachSide(1)->links('pagination::bootstrap-4') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
