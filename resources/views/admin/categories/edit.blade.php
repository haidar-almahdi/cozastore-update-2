@extends('layouts.admin')

@section('title', 'Edit Category - ' . $category->name)

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="fa fa-edit text-primary"></i> Edit Category
                </h1>
                <p class="page-subtitle">Update category information</p>
            </div>
            <div>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left"></i> Back to Categories
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-info-circle"></i> Category Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="font-weight-bold">Category Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $category->name) }}" placeholder="Enter category name" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3"
                                placeholder="Enter category description">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Category Image</label>

                            @if ($category->image)
                                <div class="mb-3">
                                    <label class="form-label">Current Image:</label>
                                    <div>
                                        <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}"
                                            class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                    </div>
                                </div>
                            @endif

                            <input type="file" name="image"
                                class="form-control-file @error('image') is-invalid @enderror" accept="image/*"
                                onchange="previewImage(this)">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Upload a new image to replace the current one (JPG, PNG,
                                GIF, WEBP - Max: 2MB)</small>

                            <!-- Image Preview -->
                            <div id="imagePreview" class="mt-3" style="display: none;">
                                <label class="form-label">New Image Preview:</label>
                                <div>
                                    <img id="preview" src="" alt="Preview" class="img-thumbnail"
                                        style="max-width: 200px; max-height: 200px;">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" id="is_active" class="form-check-input"
                                    value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                <label for="is_active" class="form-check-label font-weight-bold">Active Category</label>
                            </div>
                            <small class="form-text text-muted">Check to make this category visible to customers</small>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                                <i class="fa fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Update Category
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Category Stats -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa fa-chart-bar"></i> Category Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Products:</span>
                        <strong>{{ $category->products->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Stock:</span>
                        <strong>{{ $category->products->sum('stock') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Average Price:</span>
                        <strong>${{ number_format($category->products->avg('price') ?? 0, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Created:</span>
                        <strong>{{ $category->created_at->format('M d, Y') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    document.getElementById('preview').src = e.target.result;
                    document.getElementById('imagePreview').style.display = 'block';
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
