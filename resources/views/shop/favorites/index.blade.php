@extends('layouts.app')

@section('title', 'My Favorites')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fa fa-heart text-danger"></i> My Favorites
                <span class="badge badge-secondary">{{ $favorites->count() }}</span>
            </h2>

            @if($favorites->count() > 0)
                <div class="row">
                    @foreach($favorites as $favorite)
                        <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                            <div class="card h-100">
                                <div class="position-relative">
                                    <img src="{{ $favorite->product->image_url }}" class="card-img-top" alt="{{ $favorite->product->name }}" style="height: 200px; object-fit: contain; background: #f8f9fa;">
                                    <button class="btn btn-sm btn-danger position-absolute" style="top: 10px; right: 10px;" onclick="removeFavorite({{ $favorite->product->id }}, this)">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title">{{ $favorite->product->name }}</h6>
                                    <p class="card-text text-muted small">{{ Str::limit($favorite->product->description, 60) }}</p>
                                    <div class="mt-auto">
                                        <p class="h5 text-primary mb-1">${{ number_format($favorite->product->current_price, 2) }}</p>
                                        @if($favorite->product->has_active_discount)
                                            <small class="text-muted" style="text-decoration: line-through;">${{ number_format($favorite->product->price, 2) }}</small>
                                        @endif
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('shop.products.show', $favorite->product) }}" class="btn btn-outline-primary btn-sm flex-fill">View Details</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fa fa-heart-o fa-3x text-muted mb-3"></i>
                    <h4>No favorites yet</h4>
                    <p class="text-muted">Start adding products to your favorites to see them here.</p>
                    <a href="{{ route('shop.products.index') }}" class="btn btn-primary">Browse Products</a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function removeFavorite(productId, button) {
    if (confirm('Are you sure you want to remove this product from your favorites?')) {
        const originalIcon = button.innerHTML;
        button.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
        button.disabled = true;

        // Create form data
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        fetch(`/shop/favorites/${productId}/toggle`, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                alert('Error: ' + data.error);
                button.innerHTML = originalIcon;
                button.disabled = false;
            } else if (data.status === 'removed') {
                // Remove the card from the page
                const card = button.closest('.col-sm-6');
                card.remove();

                // Update the count in the header
                const badge = document.querySelector('.badge-secondary');
                if (badge) {
                    const currentCount = parseInt(badge.textContent);
                    badge.textContent = currentCount - 1;
                }

                // Check if no favorites left
                const remainingCards = document.querySelectorAll('.col-sm-6').length;
                if (remainingCards === 0) {
                    location.reload(); // Reload to show "no favorites" message
                }
            } else {
                alert('Unexpected response');
                button.innerHTML = originalIcon;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error removing favorite: ' + error.message);
            button.innerHTML = originalIcon;
            button.disabled = false;
        });
    }
}
</script>
@endsection
