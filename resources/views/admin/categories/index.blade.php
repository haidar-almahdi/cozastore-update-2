@extends('layouts.admin')

@section('title', 'Categories Management')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="fa fa-tags text-primary"></i> Categories Management
                </h1>
                <p class="page-subtitle">Organize your products into categories</p>
            </div>
            <div>
                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                    <i class="fa fa-plus"></i> Add New Category
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="stat-card blue">
                <i class="fa fa-tags stat-icon"></i>
                <div class="stat-number">{{ $totalCategories }}</div>
                <div class="stat-label">Total Categories</div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="stat-card green">
                <i class="fa fa-cube stat-icon"></i>
                <div class="stat-number">{{ $totalProducts }}</div>
                <div class="stat-label">Total Products</div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="stat-card orange">
                <i class="fa fa-star stat-icon"></i>
                <div class="stat-number">{{ $mostProductsCount }}</div>
                <div class="stat-label">Most Products</div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="stat-card purple">
                <i class="fa fa-eye stat-icon"></i>
                <div class="stat-number">{{ $activeCategories }}</div>
                <div class="stat-label">Active Categories</div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="stat-card red">
                <i class="fa fa-eye-slash stat-icon"></i>
                <div class="stat-number">{{ $inactiveCategories }}</div>
                <div class="stat-label">Inactive Categories</div>
            </div>
        </div>
    </div>

    <!-- Categories Grid -->
    <div class="row">
        @foreach($categories as $category)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 font-weight-bold">{{ $category->name }}</h6>
                        <span class="badge badge-{{ $category->is_active ? 'success' : 'secondary' }}">
                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="card-body">
                        @if($category->image)
                            <div class="text-center mb-3">
                                <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}"
                                     class="img-thumbnail" style="max-width: 100px; max-height: 100px; object-fit: cover;">
                            </div>
                        @endif

                        @if($category->description)
                            <p class="text-muted small mb-3">{{ Str::limit($category->description, 100) }}</p>
                        @endif

                        <div class="row text-center mb-3">
                            <div class="col-6">
                                <div class="border-right">
                                    <h4 class="text-primary mb-0">{{ $category->products->count() }}</h4>
                                    <small class="text-muted">Products</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="text-success mb-0">{{ $category->products->sum('stock') }}</h4>
                                <small class="text-muted">Total Stock</small>
                            </div>
                        </div>

                        @if($category->products->count() > 0)
                            <div class="mb-3">
                                <small class="text-muted">Recent Products:</small>
                                <div class="mt-1">
                                    @foreach($category->products->take(3) as $product)
                                        <span class="badge badge-light mr-1 mb-1">{{ $product->name }}</span>
                                    @endforeach
                                    @if($category->products->count() > 3)
                                        <span class="badge badge-info">+{{ $category->products->count() - 3 }} more</span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="text-center">
                            <small class="text-muted">Created: {{ $category->created_at->format('M d, Y') }}</small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="btn-group w-100" role="group">
                            <a href="{{ route('admin.products.index', ['category_id' => $category->id]) }}"
                               class="btn btn-info btn-sm" title="View Products">
                                <i class="fa fa-cube"></i> Products
                            </a>
                            <a href="{{ route('admin.categories.edit', $category) }}"
                               class="btn btn-warning btn-sm" title="Edit Category">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                  class="d-inline" onclick="return confirmDelete('Are you sure you want to delete this category?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" title="Delete Category">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        @if($categories->count() == 0)
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fa fa-tags fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Categories Found</h5>
                        <p class="text-muted">Start organizing your products by creating categories.</p>
                        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Create First Category
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Quick Stats Table -->
    @if($categories->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fa fa-chart-bar"></i> Categories Overview</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Products</th>
                                <th>Total Stock</th>
                                <th>Avg Price</th>
                                <th>Status</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories->sortByDesc(function($cat) { return $cat->products->count(); }) as $category)
                                <tr>
                                    <td>
                                        <strong>{{ $category->name }}</strong>
                                        @if($category->description)
                                            <br><small class="text-muted">{{ Str::limit($category->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">{{ $category->products->count() }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $category->products->sum('stock') }}</span>
                                    </td>
                                    <td class="font-weight-bold text-success">
                                        ${{ number_format($category->products->avg('price') ?? 0, 2) }}
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $category->is_active ? 'success' : 'secondary' }}">
                                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ $category->created_at->format('M d, Y') }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
<script>
    // Confirm delete actions
    function confirmDelete(message) {
        return confirm(message || 'Are you sure you want to delete this item?');
    }
</script>
@endsection
