@extends('layouts.admin')

@section('title', 'Add New Product')

@section('styles')
<style>
/* Admin Size Section Styling */
.size-section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.1rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f8f9fa;
}

.size-section-title i {
    color: #667eea;
    font-size: 1rem;
}

.admin-size-options-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 20px;
    margin-top: 15px;
}

.admin-size-option-card-with-stock {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.admin-size-option-card {
    position: relative;
    cursor: pointer;
    margin: 0;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.admin-size-option-card input {
    display: none;
}

.admin-size-card-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 15px 10px;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
    border: 2px solid #e9ecef;
    border-radius: 12px;
    transition: all 0.3s ease;
    position: relative;
    min-height: 70px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.admin-size-name {
    font-size: 1rem;
    font-weight: 700;
    color: #2c3e50;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.admin-size-selection-indicator {
    position: absolute;
    top: 5px;
    right: 5px;
    background: #28a745;
    color: white;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    opacity: 0;
    transform: scale(0);
    transition: all 0.3s ease;
}

/* Hover Effects for Admin Sizes */
.admin-size-option-card:hover .admin-size-card-content {
    transform: translateY(-3px);
    border-color: #667eea;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2);
    background: linear-gradient(135deg, #ffffff 0%, #e8f2ff 100%);
}

.admin-size-option-card:hover .admin-size-name {
    color: #667eea;
}

/* Selected State for Admin Sizes */
.admin-size-option-card input:checked + .admin-size-card-content {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #667eea;
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
}

.admin-size-option-card input:checked + .admin-size-card-content .admin-size-name {
    color: white;
}

.admin-size-option-card input:checked + .admin-size-card-content .admin-size-selection-indicator {
    opacity: 1;
    transform: scale(1);
}

/* Size Stock Input Styling */
.size-stock-input-container {
    background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 15px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.size-stock-input-container:hover {
    border-color: #667eea;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.15);
}

.stock-input-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 10px;
}

.stock-input-label i {
    color: #667eea;
    font-size: 0.85rem;
}

.size-stock-input {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 10px 12px;
    font-size: 0.9rem;
    font-weight: 600;
    text-align: center;
    transition: all 0.3s ease;
    background: white;
}

.size-stock-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    outline: none;
}

.size-stock-input:hover {
    border-color: #667eea;
}

/* Size Type Selector Styling */
.size-type-selector {
    background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
    border: 2px solid #e9ecef;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.size-type-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 1rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 15px;
}

.size-type-title i {
    color: #667eea;
    font-size: 0.9rem;
}

.size-type-options {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.size-type-option {
    cursor: pointer;
    margin: 0;
    flex: 1;
    min-width: 120px;
}

.size-type-option input {
    display: none;
}

.size-type-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 15px;
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    transition: all 0.3s ease;
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 600;
    color: #2c3e50;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.size-type-label i {
    font-size: 1.5rem;
    color: #667eea;
    transition: all 0.3s ease;
}

.size-type-option:hover .size-type-label {
    transform: translateY(-2px);
    border-color: #667eea;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
}

.size-type-option:hover .size-type-label i {
    transform: scale(1.1);
    color: #5a6fd8;
}

.size-type-option input:checked + .size-type-label {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #667eea;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
}

.size-type-option input:checked + .size-type-label i {
    color: white;
    transform: scale(1.1);
}

/* Admin Color Section Styling */
.color-section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.1rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f8f9fa;
}

.color-section-title i {
    color: #667eea;
    font-size: 1rem;
}

.admin-color-picker-container {
    background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
    border: 2px solid #e9ecef;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.predefined-colors-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 15px;
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f0f0f0;
}

.admin-color-option {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 15px;
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.admin-color-option:hover {
    transform: translateY(-3px);
    border-color: #667eea;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2);
}

.admin-color-preview {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    border: 3px solid #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15), inset 0 1px 3px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.admin-color-option:hover .admin-color-preview {
    transform: scale(1.1);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2), inset 0 1px 3px rgba(0,0,0,0.1);
}

.admin-color-name {
    font-size: 0.85rem;
    font-weight: 600;
    color: #2c3e50;
    text-align: center;
    text-transform: capitalize;
}

.admin-color-option:hover .admin-color-name {
    color: #667eea;
}

/* Selected Colors Section */
.selected-colors-section {
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f0f0f0;
}

.selected-colors-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 1rem;
    font-weight: 600;
    color: #28a745;
    margin-bottom: 15px;
}

.admin-selected-colors-display {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    min-height: 60px;
    padding: 15px;
    background: white;
    border: 2px dashed #dee2e6;
    border-radius: 12px;
    align-items: center;
}

.admin-selected-color-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
    padding: 10px;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
    border: 2px solid #e9ecef;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}

.admin-selected-color-item:hover {
    transform: translateY(-2px);
    border-color: #dc3545;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.2);
}

.admin-selected-color-preview {
    width: 35px;
    height: 35px;
    border-radius: 8px;
    border: 2px solid #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

.admin-selected-color-name {
    font-size: 0.75rem;
    font-weight: 600;
    color: #2c3e50;
    text-align: center;
}

.admin-selected-color-item:hover .admin-selected-color-name {
    color: #dc3545;
}

.admin-selected-color-item::after {
    content: '×';
    position: absolute;
    top: -5px;
    right: -5px;
    background: #dc3545;
    color: white;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
    opacity: 0;
    transform: scale(0);
    transition: all 0.3s ease;
}

.admin-selected-color-item:hover::after {
    opacity: 1;
    transform: scale(1);
}

/* Custom Color Section */
.custom-color-section {
    margin-top: 20px;
}

.custom-color-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 1rem;
    font-weight: 600;
    color: #667eea;
    margin-bottom: 15px;
}

.custom-color-controls {
    display: flex;
    gap: 12px;
    align-items: center;
}

.custom-color-input {
    width: 60px;
    height: 45px;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.custom-color-input:hover {
    border-color: #667eea;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.2);
}

/* Responsive Design */
@media (max-width: 768px) {
    .admin-size-options-container {
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }

    .admin-size-card-content {
        padding: 12px 8px;
        min-height: 60px;
    }

    .admin-size-name {
        font-size: 0.9rem;
    }

    .predefined-colors-grid {
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 12px;
    }

    .admin-color-option {
        padding: 12px;
    }

    .admin-color-preview {
        width: 40px;
        height: 40px;
    }

    .custom-color-controls {
        flex-direction: column;
        align-items: stretch;
    }
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Unified Dashboard Header -->
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fa fa-cube text-primary"></i> Add New Product
                        </h1>
                        <p class="page-subtitle">Create a new product and manage its details</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back to Products
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <!-- معلومات المنتج -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Product Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label class="form-label">Product Name *</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                              rows="4">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label class="form-label">AI Keywords (Arabic)</label>
                                    <textarea name="ai_keywords" class="form-control @error('ai_keywords') is-invalid @enderror"
                                              rows="3" placeholder="مثال: فستان سهرة, رسمي, طويل, اسود">{{ old('ai_keywords') }}</textarea>
                                    <small class="text-muted">افصل الكلمات بفاصلة او سطر جديد. هذا يساعد المساعد الذكي يفهم طلب المستخدم بسرعة.</small>
                                    @error('ai_keywords')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Price *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="price" class="form-control @error('price') is-invalid @enderror"
                                               value="{{ old('price') }}" step="0.01" min="0" required>
                                    </div>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Discount %</label>
                                    <input type="number" name="discount_percentage" class="form-control @error('discount_percentage') is-invalid @enderror"
                                           value="{{ old('discount_percentage', 0) }}" min="0" max="90">
                                    @error('discount_percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Stock Quantity *</label>
                                    <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror"
                                           value="{{ old('stock') }}" min="0" required>
                                    @error('stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Discount Starts At</label>
                                    <input type="datetime-local" name="discount_starts_at" class="form-control @error('discount_starts_at') is-invalid @enderror"
                                           value="{{ old('discount_starts_at') }}">
                                    @error('discount_starts_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Discount Ends At</label>
                                    <input type="datetime-local" name="discount_ends_at" class="form-control @error('discount_ends_at') is-invalid @enderror"
                                           value="{{ old('discount_ends_at') }}">
                                    @error('discount_ends_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3 mt-2">
                                    <label class="form-check">
                                        <input type="hidden" name="black_friday_enabled" value="0">
                                        <input type="checkbox" name="black_friday_enabled" value="1" class="form-check-input"
                                               {{ old('black_friday_enabled', 1) ? 'checked' : '' }}>
                                        <span class="form-check-label">Enable Black Friday discount for this product</span>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label class="form-label">Category *</label>
                                    <select name="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الأحجام والألوان -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Sizes & Colors</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-4">
                                    <label class="size-section-title">
                                        <i class="fa fa-expand-arrows-alt"></i>
                                        Available Sizes
                                    </label>

                                    <!-- Size Type Selector -->
                                    <div class="size-type-selector mb-3">
                                        <label class="size-type-title">
                                            <i class="fa fa-tags"></i>
                                            Size Type
                                        </label>
                                        <div class="size-type-options">
                                            <label class="size-type-option">
                                                <input type="radio" name="size_type" value="none" checked onchange="changeSizeType('none')">
                                                <span class="size-type-label">
                                                    <i class="fa fa-ban"></i>
                                                    No Size
                                                </span>
                                            </label>
                                            <label class="size-type-option">
                                                <input type="radio" name="size_type" value="clothing" onchange="changeSizeType('clothing')">
                                                <span class="size-type-label">
                                                    <i class="fa fa-tshirt"></i>
                                                    Clothing Sizes
                                                </span>
                                            </label>
                                            <label class="size-type-option">
                                                <input type="radio" name="size_type" value="shoes" onchange="changeSizeType('shoes')">
                                                <span class="size-type-label">
                                                    <i class="fa fa-shoe-prints"></i>
                                                    Shoe Sizes
                                                </span>
                                            </label>
                                            <label class="size-type-option">
                                                <input type="radio" name="size_type" value="accessories" onchange="changeSizeType('accessories')">
                                                <span class="size-type-label">
                                                    <i class="fa fa-gem"></i>
                                                    Accessories
                                                </span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Clothing Sizes -->
                                    <div id="clothing-sizes" class="admin-size-options-container">
                                        @php
                                            $clothingSizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
                                            $oldSizes = old('sizes', []);
                                            $oldSizeStocks = old('size_stocks', []);
                                        @endphp
                                        @foreach($clothingSizes as $size)
                                            <div class="admin-size-option-card-with-stock">
                                                <label class="admin-size-option-card">
                                                    <input type="checkbox" name="sizes[]" value="{{ $size }}"
                                                           {{ in_array($size, $oldSizes) ? 'checked' : '' }}
                                                           onchange="toggleSizeStock('{{ $size }}')">
                                                    <div class="admin-size-card-content">
                                                        <span class="admin-size-name">{{ $size }}</span>
                                                        <div class="admin-size-selection-indicator">
                                                            <i class="fa fa-check"></i>
                                                        </div>
                                                    </div>
                                                </label>
                                                <div class="size-stock-input-container" id="stock-container-{{ $size }}"
                                                     style="display: {{ in_array($size, $oldSizes) ? 'block' : 'none' }};">
                                                    <label class="stock-input-label">
                                                        <i class="fa fa-box"></i>
                                                        Stock for {{ $size }}
                                                    </label>
                                                    <input type="number"
                                                           name="size_stocks[{{ $size }}]"
                                                           class="form-control size-stock-input"
                                                           placeholder="Enter stock quantity"
                                                           value="{{ $oldSizeStocks[$size] ?? '' }}"
                                                           min="0">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Shoe Sizes -->
                                    <div id="shoe-sizes" class="admin-size-options-container" style="display: none;">
                                        @php
                                            $shoeSizes = ['35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46'];
                                        @endphp
                                        @foreach($shoeSizes as $size)
                                            <div class="admin-size-option-card-with-stock">
                                                <label class="admin-size-option-card">
                                                    <input type="checkbox" name="sizes[]" value="{{ $size }}"
                                                           {{ in_array($size, $oldSizes) ? 'checked' : '' }}
                                                           onchange="toggleSizeStock('{{ $size }}')">
                                                    <div class="admin-size-card-content">
                                                        <span class="admin-size-name">{{ $size }}</span>
                                                        <div class="admin-size-selection-indicator">
                                                            <i class="fa fa-check"></i>
                                                        </div>
                                                    </div>
                                                </label>
                                                <div class="size-stock-input-container" id="stock-container-{{ $size }}"
                                                     style="display: {{ in_array($size, $oldSizes) ? 'block' : 'none' }};">
                                                    <label class="stock-input-label">
                                                        <i class="fa fa-box"></i>
                                                        Stock for Size {{ $size }}
                                                    </label>
                                                    <input type="number"
                                                           name="size_stocks[{{ $size }}]"
                                                           class="form-control size-stock-input"
                                                           placeholder="Enter stock quantity"
                                                           value="{{ $oldSizeStocks[$size] ?? '' }}"
                                                           min="0">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Accessories Sizes -->
                                    <div id="accessories-sizes" class="admin-size-options-container" style="display: none;">
                                        @php
                                            $accessorySizes = ['One Size', 'Small', 'Medium', 'Large', 'Adjustable'];
                                        @endphp
                                        @foreach($accessorySizes as $size)
                                            <div class="admin-size-option-card-with-stock">
                                                <label class="admin-size-option-card">
                                                    <input type="checkbox" name="sizes[]" value="{{ $size }}"
                                                           {{ in_array($size, $oldSizes) ? 'checked' : '' }}
                                                           onchange="toggleSizeStock('{{ $size }}')">
                                                    <div class="admin-size-card-content">
                                                        <span class="admin-size-name">{{ $size }}</span>
                                                        <div class="admin-size-selection-indicator">
                                                            <i class="fa fa-check"></i>
                                                        </div>
                                                    </div>
                                                </label>
                                                <div class="size-stock-input-container" id="stock-container-{{ $size }}"
                                                     style="display: {{ in_array($size, $oldSizes) ? 'block' : 'none' }};">
                                                    <label class="stock-input-label">
                                                        <i class="fa fa-box"></i>
                                                        Stock for {{ $size }}
                                                    </label>
                                                    <input type="number"
                                                           name="size_stocks[{{ $size }}]"
                                                           class="form-control size-stock-input"
                                                           placeholder="Enter stock quantity"
                                                           value="{{ $oldSizeStocks[$size] ?? '' }}"
                                                           min="0">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- No Size Option -->
                                    <div id="no-sizes" style="display: block;">
                                        <div class="alert alert-info">
                                            <i class="fa fa-info-circle"></i>
                                            This product doesn't require size selection.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group mb-4">
                                    <label class="color-section-title">
                                        <i class="fa fa-palette"></i>
                                        Available Colors
                                    </label>
                                    <div class="admin-color-picker-container">
                                        <!-- خيار بلا لون -->
                                        <div class="mb-3">
                                            <label class="form-check">
                                                <input type="checkbox" class="form-check-input" id="no-color-option" onchange="toggleColorSection()">
                                                <span class="form-check-label">
                                                    <i class="fa fa-ban text-muted"></i>
                                                    This product doesn't have color options
                                                </span>
                                            </label>
                                        </div>

                                        <div id="color-selection-section">
                                        <!-- مربعات الألوان المحددة مسبقاً -->
                                        <div class="predefined-colors-grid">
                                            @php
                                                $predefinedColors = [
                                                    ['code' => '#000000', 'name' => 'Black'],
                                                    ['code' => '#FFFFFF', 'name' => 'White'],
                                                    ['code' => '#FF0000', 'name' => 'Red'],
                                                    ['code' => '#00FF00', 'name' => 'Green'],
                                                    ['code' => '#0000FF', 'name' => 'Blue'],
                                                    ['code' => '#FFFF00', 'name' => 'Yellow'],
                                                    ['code' => '#FF00FF', 'name' => 'Magenta'],
                                                    ['code' => '#00FFFF', 'name' => 'Cyan'],
                                                    ['code' => '#FFA500', 'name' => 'Orange'],
                                                    ['code' => '#800080', 'name' => 'Purple'],
                                                    ['code' => '#FFC0CB', 'name' => 'Pink'],
                                                    ['code' => '#A52A2A', 'name' => 'Brown'],
                                                    ['code' => '#808080', 'name' => 'Gray'],
                                                    ['code' => '#000080', 'name' => 'Navy'],
                                                    ['code' => '#008000', 'name' => 'Dark Green'],
                                                    ['code' => '#800000', 'name' => 'Maroon']
                                                ];
                                            @endphp
                                            @foreach($predefinedColors as $color)
                                                <div class="admin-color-option" onclick="selectColor('{{ $color['code'] }}', '{{ $color['name'] }}')">
                                                    <div class="admin-color-preview" style="background-color: {{ $color['code'] }};"></div>
                                                    <span class="admin-color-name">{{ $color['name'] }}</span>
                                                </div>
                                            @endforeach
                                        </div>

                                        <!-- منطقة الألوان المختارة -->
                                        <div class="selected-colors-section">
                                            <label class="selected-colors-title">
                                                <i class="fa fa-check-circle"></i>
                                                Selected Colors
                                            </label>
                                            <div id="selected-colors-display" class="admin-selected-colors-display"></div>
                                        </div>

                                        <!-- Color picker مخصص -->
                                        <div class="custom-color-section">
                                            <label class="custom-color-title">
                                                <i class="fa fa-eyedropper"></i>
                                                Custom Color
                                            </label>
                                            <div class="custom-color-controls">
                                                <input type="color" id="customColorPicker" class="custom-color-input" value="#000000">
                                                <input type="text" id="customColorName" class="form-control" placeholder="Color name..." style="flex: 1;">
                                                <button type="button" class="btn btn-primary" onclick="addCustomColor()">
                                                    <i class="fa fa-plus"></i> Add
                                                </button>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الصور -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Product Images</h5>
                    </div>
                    <div class="card-body">
                        <!-- الصورة الرئيسية -->
                        <div class="form-group mb-3">
                            <label class="form-label">Main Image *</label>
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror"
                                   accept="image/*" onchange="previewMainImage(this)" required>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="main-image-preview" class="mt-2"></div>
                        </div>

                        <!-- الصور الإضافية -->
                        <div class="form-group mb-3">
                            <label class="form-label">Additional Images</label>
                            <input type="file" name="additional_images[]" class="form-control @error('additional_images.*') is-invalid @enderror"
                                   accept="image/*" multiple onchange="previewAdditionalImages(this)">
                            @error('additional_images.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">You can select multiple images</small>
                            <div id="additional-images-preview" class="mt-2"></div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Create Product
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function previewMainImage(input) {
    const preview = document.getElementById('main-image-preview');
    preview.innerHTML = '';

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded" style="max-height: 200px;">`;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function previewAdditionalImages(input) {
    const preview = document.getElementById('additional-images-preview');
    preview.innerHTML = '';

    if (input.files) {
        Array.from(input.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'mb-2';
                div.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded" style="max-height: 100px;">`;
                preview.appendChild(div);
            }
            reader.readAsDataURL(file);
        });
    }
}

let selectedColors = [];

function selectColor(colorCode, colorName) {
    const existingColor = selectedColors.find(c => c.code === colorCode);
    if (!existingColor) {
        selectedColors.push({
            code: colorCode,
            name: colorName
        });
        updateSelectedColorsDisplay();
    }
}

function addCustomColor() {
    const customColorCode = document.getElementById('customColorPicker').value;
    const customColorName = document.getElementById('customColorName').value.trim() || 'Custom Color';

    const existingColor = selectedColors.find(c => c.code === customColorCode);
    if (!existingColor) {
        selectedColors.push({
            code: customColorCode,
            name: customColorName
        });
        updateSelectedColorsDisplay();

        // Clear the custom color name input
        document.getElementById('customColorName').value = '';
    }
}

function removeColor(colorCode) {
    selectedColors = selectedColors.filter(c => c.code !== colorCode);
    updateSelectedColorsDisplay();
}

function updateSelectedColorsDisplay() {
    const container = document.getElementById('selected-colors-display');
    container.innerHTML = '';

    if (selectedColors.length === 0) {
        container.innerHTML = '<div style="color: #6c757d; font-style: italic; text-align: center; width: 100%; padding: 20px;">No colors selected yet. Click on colors above to add them.</div>';
        return;
    }

    selectedColors.forEach(color => {
        // Create color item container
        const colorItem = document.createElement('div');
        colorItem.className = 'admin-selected-color-item';
        colorItem.onclick = () => removeColor(color.code);
        colorItem.title = `Click to remove ${color.name}`;

        // Create color preview
        const colorPreview = document.createElement('div');
        colorPreview.className = 'admin-selected-color-preview';
        colorPreview.style.backgroundColor = color.code;

        // Create color name
        const colorNameSpan = document.createElement('span');
        colorNameSpan.className = 'admin-selected-color-name';
        colorNameSpan.textContent = color.name;

        // Add elements to item
        colorItem.appendChild(colorPreview);
        colorItem.appendChild(colorNameSpan);

        // Create hidden inputs for form submission
        const hiddenCodeInput = document.createElement('input');
        hiddenCodeInput.type = 'hidden';
        hiddenCodeInput.name = 'color_codes[]';
        hiddenCodeInput.value = color.code;

        const hiddenNameInput = document.createElement('input');
        hiddenNameInput.type = 'hidden';
        hiddenNameInput.name = 'color_names[]';
        hiddenNameInput.value = color.name;

        // Add to container
        container.appendChild(colorItem);
        container.appendChild(hiddenCodeInput);
        container.appendChild(hiddenNameInput);
    });
}

// Toggle size stock input visibility
function toggleSizeStock(size) {
    const checkbox = document.querySelector(`input[name="sizes[]"][value="${size}"]`);
    const stockContainer = document.getElementById(`stock-container-${size}`);
    const stockInput = stockContainer.querySelector('input');

    if (checkbox.checked) {
        stockContainer.style.display = 'block';
        stockInput.required = true;
        // Add animation
        setTimeout(() => {
            stockContainer.style.opacity = '1';
            stockContainer.style.transform = 'translateY(0)';
        }, 10);
    } else {
        stockContainer.style.display = 'none';
        stockInput.required = false;
        stockInput.value = '';
        stockContainer.style.opacity = '0';
        stockContainer.style.transform = 'translateY(-10px)';
    }
}

// Change size type (clothing vs shoes vs accessories vs none)
function changeSizeType(type) {
    const clothingSizes = document.getElementById('clothing-sizes');
    const shoeSizes = document.getElementById('shoe-sizes');
    const accessoriesSizes = document.getElementById('accessories-sizes');
    const noSizes = document.getElementById('no-sizes');

    // Clear all selected sizes when switching
    const allSizeCheckboxes = document.querySelectorAll('input[name="sizes[]"]');
    allSizeCheckboxes.forEach(checkbox => {
        checkbox.checked = false;
        const stockContainer = document.getElementById(`stock-container-${checkbox.value}`);
        if (stockContainer) {
            stockContainer.style.display = 'none';
            const stockInput = stockContainer.querySelector('input');
            if (stockInput) {
                stockInput.value = '';
                stockInput.required = false;
            }
        }
    });

    // Hide all size sections first
    clothingSizes.style.display = 'none';
    shoeSizes.style.display = 'none';
    accessoriesSizes.style.display = 'none';
    noSizes.style.display = 'none';

    // Show the selected type
    if (type === 'clothing') {
        clothingSizes.style.display = 'grid';
    } else if (type === 'shoes') {
        shoeSizes.style.display = 'grid';
    } else if (type === 'accessories') {
        accessoriesSizes.style.display = 'grid';
    } else if (type === 'none') {
        noSizes.style.display = 'block';
    }
}

// Toggle color section
function toggleColorSection() {
    const noColorCheckbox = document.getElementById('no-color-option');
    const colorSection = document.getElementById('color-selection-section');

    if (noColorCheckbox.checked) {
        colorSection.style.display = 'none';
        // Clear selected colors
        selectedColors = [];
        updateSelectedColorsDisplay();
    } else {
        colorSection.style.display = 'block';
    }
}

// Form submission handler
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Handle no color option
            const noColorCheckbox = document.getElementById('no-color-option');
            if (noColorCheckbox && noColorCheckbox.checked) {
                // Remove all color inputs
                const colorInputs = form.querySelectorAll('input[name="color_codes[]"], input[name="color_names[]"]');
                colorInputs.forEach(input => input.remove());
            }

            // Handle no size option
            const noSizeRadio = document.querySelector('input[name="size_type"][value="none"]');
            if (noSizeRadio && noSizeRadio.checked) {
                // Uncheck all size checkboxes
                const sizeCheckboxes = form.querySelectorAll('input[name="sizes[]"]');
                sizeCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                    checkbox.disabled = true;
                });

                // Clear size stocks
                const sizeStockInputs = form.querySelectorAll('input[name^="size_stocks"]');
                sizeStockInputs.forEach(input => {
                    input.value = '';
                    input.disabled = true;
                });
            }
        });
    }
});
</script>
@endsection
