@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fa fa-credit-card"></i> Checkout
            </h2>
        </div>
    </div>

    <form method="POST" action="{{ route('shop.orders.store') }}">
        @csrf
        <div class="row">
            <!-- Order Summary -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        @if($cart && $cart->items && $cart->items->count() > 0)
                            @foreach($cart->items as $item)
                                <div class="row align-items-center mb-3 pb-3 border-bottom">
                                    <div class="col-2">
                                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}"
                                             class="img-fluid rounded" style="height: 60px; object-fit: contain;">
                                    </div>
                                    <div class="col-6">
                                        <h6 class="mb-1">{{ $item->product->name }}</h6>
                                        <small class="text-muted">
                                            @if($item->size) Size: {{ $item->size }} @endif
                                            @if($item->color) | Color: {{ $item->color }} @endif
                                        </small>
                                        @php
                                            $availableStock = $item->product->stock;
                                            if ($item->size && $item->product->size_stocks && isset($item->product->size_stocks[$item->size])) {
                                                $availableStock = $item->product->size_stocks[$item->size];
                                            }
                                        @endphp
                                        @if($availableStock < $item->quantity)
                                            <br><small class="text-danger">
                                                <i class="fa fa-exclamation-triangle"></i>
                                                Only {{ $availableStock }} available in stock!
                                            </small>
                                        @endif
                                    </div>
                                    <div class="col-2 text-center">
                                        <span class="badge bg-secondary">{{ $item->quantity }}</span>
                                    </div>
                                    <div class="col-2 text-end">
                                        <strong>${{ number_format($item->price * $item->quantity, 2) }}</strong>
                                    </div>
                                </div>
                            @endforeach

                            @php
                                $subtotal = $cart->items->sum(function($item) {
                                    return $item->price * $item->quantity;
                                });
                                $shipping = $subtotal * 0.02; // 2% shipping
                                $total = $subtotal + $shipping;
                            @endphp

                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Subtotal:</span>
                                        <span>${{ number_format($subtotal, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Shipping (2%):</span>
                                        <span>${{ number_format($shipping, 2) }}</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <strong>Total:</strong>
                                        <strong class="text-primary">${{ number_format($total, 2) }}</strong>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fa fa-shopping-cart fa-3x text-muted mb-3"></i>
                                <h5>Your cart is empty</h5>
                                <a href="{{ route('shop.products.index') }}" class="btn btn-primary">Continue Shopping</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Shipping Information -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Shipping Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="shipping_name" class="form-control @error('shipping_name') is-invalid @enderror"
                                   value="{{ old('shipping_name', auth()->user()->name) }}" required>
                            @error('shipping_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Phone Number *</label>
                            <input type="tel" name="shipping_phone" class="form-control @error('shipping_phone') is-invalid @enderror"
                                   value="{{ old('shipping_phone', auth()->user()->phone) }}" required>
                            @error('shipping_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Shipping Address *</label>
                            <textarea name="shipping_address" class="form-control @error('shipping_address') is-invalid @enderror"
                                      rows="3" required>{{ old('shipping_address', auth()->user()->address) }}</textarea>
                            @error('shipping_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label">Order Notes (Optional)</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror"
                                      rows="2" placeholder="Any special instructions...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @auth
                        @php
                            $balance = auth()->user()->loyalty_points_balance ?? 0;
                            $blocks = intdiv($balance, 100);
                        @endphp
                        <div class="card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Loyalty Points</h6>
                                <span class="badge bg-primary">Balance: {{ $balance }} pts</span>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-2">Every 100 points = $10 discount</p>
                                @if($blocks > 0)
                                    <div class="mb-3">
                                        <label class="form-label">Redeem now</label>
                                        <select name="redeem_blocks" class="form-control">
                                            <option value="0">Do not redeem</option>
                                            @for($i=1; $i <= $blocks; $i++)
                                                <option value="{{ $i }}">Redeem {{ $i*100 }} pts (save ${{ $i*10 }})</option>
                                            @endfor
                                        </select>
                                    </div>
                                @else
                                    <div class="alert alert-light mb-0">
                                        You need {{ 100 - ($balance % 100) }} more points to unlock $10 discount
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endauth

                        @if($cart && $cart->items && $cart->items->count() > 0)
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fa fa-credit-card"></i> Place Order
                                </button>
                                <a href="{{ route('shop.cart.index') }}" class="btn btn-outline-secondary">
                                    <i class="fa fa-arrow-left"></i> Back to Cart
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Payment Info -->
                <div class="card mt-3">
                    <div class="card-body text-center">
                        <h6 class="mb-3">Payment Method</h6>
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            Cash on Delivery (COD)
                        </div>
                        <small class="text-muted">
                            You will pay when your order is delivered to your address.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
