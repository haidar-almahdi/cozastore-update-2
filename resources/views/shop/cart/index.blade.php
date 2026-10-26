@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fa fa-shopping-cart"></i> Shopping Cart
                @if($items->count() > 0)
                    <span class="badge badge-secondary">{{ $items->sum('quantity') }} items</span>
                @endif
            </h2>

            @if($items->count() > 0)
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                @foreach($items as $item)
                                    <div class="row align-items-center border-bottom py-3" id="cart-item-{{ $item->id }}" data-size-stocks='@json($item->product->size_stocks ?? [])' data-product-stock="{{ $item->product->stock }}">
                                        <div class="col-md-2">
                                            <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="img-fluid rounded" style="height: 80px; object-fit: contain; background: #f8f9fa;">
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="mb-1">{{ $item->product->name }}</h6>
                                            <small class="text-muted">{{ $item->product->category->name ?? '' }}</small>
                                            @if($item->size)
                                                <br><small><strong>Size:</strong> {{ $item->size }}</small>
                                            @endif
                                            @if($item->color)
                                                <br><small><strong>Color:</strong> {{ $item->color }}</small>
                                            @endif
                                        </div>
                                        <div class="col-md-4">
                                            @php
                                                $sizes = is_array($item->product->sizes) ? $item->product->sizes : [];
                                                $rawColors = is_array($item->product->colors) ? $item->product->colors : [];
                                                $colors = [];
                                                foreach ($rawColors as $c) {
                                                    if (is_array($c) && !empty($c['name'])) {
                                                        $colors[] = $c['name'];
                                                    } elseif (is_string($c)) {
                                                        $colors[] = $c;
                                                    }
                                                }
                                                $colors = array_values(array_unique($colors));
                                            @endphp
                                            <div class="mb-2">
                                                <small class="font-weight-bold d-block mb-1">Size</small>
                                                <select class="form-control form-control-sm" id="size-{{ $item->id }}" onchange="updateStockHint({{ $item->id }})">
                                                    <option value="">No size</option>
                                                    @foreach($sizes as $size)
                                                        <option value="{{ $size }}" {{ (string)$item->size === (string)$size ? 'selected' : '' }}>{{ $size }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-2">
                                                <small class="font-weight-bold d-block mb-1">Color</small>
                                                <select class="form-control form-control-sm" id="color-{{ $item->id }}">
                                                    <option value="">No color</option>
                                                    @foreach($colors as $color)
                                                        <option value="{{ $color }}" {{ (string)$item->color === (string)$color ? 'selected' : '' }}>{{ $color }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="stepQuantity({{ $item->id }}, -1)">-</button>
                                                </div>
                                                <input type="number" id="qty-{{ $item->id }}" class="form-control text-center" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="stepQuantity({{ $item->id }}, 1)">+</button>
                                                </div>
                                            </div>
                                            <small class="text-muted d-block mt-1" id="stock-hint-{{ $item->id }}">Max: {{ $item->product->stock }}</small>
                                            <button class="btn btn-sm btn-primary mt-2" type="button" onclick="saveItem({{ $item->id }})">
                                                <i class="fa fa-save"></i> Update
                                            </button>
                                        </div>
                                        <div class="col-md-1 text-center">
                                            <strong>${{ number_format($item->price * $item->quantity, 2) }}</strong>
                                        </div>
                                        <div class="col-md-1 text-center">
                                            <button class="btn btn-sm btn-outline-danger" onclick="removeItem({{ $item->id }})">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Order Summary</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <strong>${{ number_format($cart->total, 2) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Shipping:</span>
                                    <span>Free</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-3">
                                    <strong>Total:</strong>
                                    <strong class="text-primary">${{ number_format($cart->total, 2) }}</strong>
                                </div>

                                @auth
                                    <a href="{{ route('shop.checkout') }}" class="btn btn-primary btn-block">
                                        <i class="fa fa-credit-card"></i> Proceed to Checkout
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-primary btn-block">
                                        <i class="fa fa-sign-in-alt"></i> Login to Checkout
                                    </a>
                                @endauth

                                <a href="{{ route('shop.products.index') }}" class="btn btn-outline-secondary btn-block mt-2">
                                    <i class="fa fa-shopping-bag"></i> Continue Shopping
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fa fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <h4>Your cart is empty</h4>
                    <p class="text-muted">Add some products to your cart to see them here.</p>
                    <a href="{{ route('shop.products.index') }}" class="btn btn-primary">
                        <i class="fa fa-shopping-bag"></i> Start Shopping
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function getAvailableStock(itemId) {
    const row = document.getElementById(`cart-item-${itemId}`);
    const sizeSelect = document.getElementById(`size-${itemId}`);
    const size = sizeSelect ? sizeSelect.value : '';
    const productStock = parseInt(row?.dataset.productStock || '0', 10);
    let available = productStock;

    try {
        const sizeStocks = JSON.parse(row?.dataset.sizeStocks || '{}');
        if (size && sizeStocks && Object.prototype.hasOwnProperty.call(sizeStocks, size)) {
            available = parseInt(sizeStocks[size], 10);
        }
    } catch (e) {
        available = productStock;
    }

    return Number.isNaN(available) ? productStock : available;
}

function updateStockHint(itemId) {
    const available = getAvailableStock(itemId);
    const hint = document.getElementById(`stock-hint-${itemId}`);
    const qtyInput = document.getElementById(`qty-${itemId}`);
    const size = document.getElementById(`size-${itemId}`)?.value || '';
    if (qtyInput) {
        qtyInput.max = available;
        if (parseInt(qtyInput.value, 10) > available) {
            qtyInput.value = available > 0 ? available : 1;
        }
    }
    if (hint) {
        hint.textContent = size ? `Max for size ${size}: ${available}` : `Max: ${available}`;
    }
}

function stepQuantity(itemId, delta) {
    const input = document.getElementById(`qty-${itemId}`);
    if (!input) return;
    const min = parseInt(input.min || '1', 10);
    const max = getAvailableStock(itemId);
    const current = parseInt(input.value || '1', 10);
    const next = Math.min(max, Math.max(min, current + delta));
    input.value = next;
}

function saveItem(itemId) {
    const quantity = parseInt(document.getElementById(`qty-${itemId}`)?.value || '1', 10);
    const size = document.getElementById(`size-${itemId}`)?.value || '';
    const color = document.getElementById(`color-${itemId}`)?.value || '';
    const max = getAvailableStock(itemId);

    if (quantity < 1) return;
    if (quantity > max) {
        alert(`Only ${max} item(s) available for this selection.`);
        return;
    }

    fetch(`/shop/cart/${itemId}`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            quantity: quantity,
            size: size,
            color: color
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Error updating cart');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating cart');
    });
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[id^="cart-item-"]').forEach((row) => {
        const itemId = row.id.replace('cart-item-', '');
        updateStockHint(itemId);
    });
});

function removeItem(itemId) {
    if (confirm('Are you sure you want to remove this item from your cart?')) {
        fetch(`/shop/cart/${itemId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error removing item');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error removing item');
        });
    }
}
</script>
@endsection
