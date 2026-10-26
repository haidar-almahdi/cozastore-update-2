@extends('layouts.app')

@section('title', 'Order Details')

@section('styles')
    <style>
        .orders-hero-title { display:flex; align-items:center; gap:10px; font-weight:700; }
        .orders-hero-title i { color:#6c63ff; }
        .card { border-radius: 12px; box-shadow: 0 6px 18px rgba(0,0,0,.04); }
        .card-header { background:#f8f9fb; border-bottom:1px solid #eee; border-top-left-radius:12px; border-top-right-radius:12px; }
        .badge.rounded-pill { padding:.45em .75em; font-weight:600; }
        .order-item-row { transition: box-shadow .2s ease, transform .08s ease; border-bottom:1px solid #f1f1f1; }
        .order-item-row:hover { box-shadow:0 8px 24px rgba(0,0,0,.06); transform:translateY(-1px); border-radius:10px; }
        .order-thumb { width:60px; height:60px; object-fit:contain; background:#fff; border:1px solid #eee; border-radius:10px; }
        .totals-line { display:flex; justify-content:flex-end; gap:16px; margin-bottom:8px; }
        .totals-line .label { color:#6c757d; }
    </style>
@endsection

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="orders-hero-title mb-0">
                    <i class="fa fa-shopping-bag"></i>
                    <span>Order Details</span>
                </h2>
                <a href="{{ route('shop.orders.index') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left"></i> Back to Orders
                </a>
            </div>

            <!-- Order Info -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Order Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-4"><strong>Order Number:</strong></div>
                                <div class="col-8">{{ $order->order_number }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4"><strong>Order Date:</strong></div>
                                <div class="col-8">{{ $order->created_at->format('M d, Y h:i A') }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4"><strong>Status:</strong></div>
                                <div class="col-8">
                                    @php
                                        $statusColors = [
                                            'pending' => 'warning',
                                            'processing' => 'info',
                                            'shipped' => 'primary',
                                            'delivered' => 'success',
                                            'cancelled' => 'danger'
                                        ];
                                    @endphp
                                    <span class="badge rounded-pill bg-{{ $statusColors[$order->status] ?? 'secondary' }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4"><strong>Total Amount:</strong></div>
                                <div class="col-8"><strong class="text-primary">${{ number_format($order->total_amount, 2) }}</strong></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Shipping Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-4"><strong>Name:</strong></div>
                                <div class="col-8">{{ $order->shipping_name }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4"><strong>Phone:</strong></div>
                                <div class="col-8">{{ $order->shipping_phone }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4"><strong>Address:</strong></div>
                                <div class="col-8">{{ $order->shipping_address }}</div>
                            </div>
                            @if($order->notes)
                                <div class="row">
                                    <div class="col-4"><strong>Notes:</strong></div>
                                    <div class="col-8">{{ $order->notes }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Order Items</h5>
                </div>
                <div class="card-body">
                    @if($order->items && $order->items->count() > 0)
                        @foreach($order->items as $item)
                            <div class="row align-items-center mb-3 pb-3 order-item-row">
                                <div class="col-2">
                                    @if($item->product)
                                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="order-thumb">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 60px; width: 60px;">
                                            <i class="fa fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-6">
                                    <h6 class="mb-1">{{ $item->product->name ?? 'Product not available' }}</h6>
                                    <small class="text-muted">
                                        @if($item->size) Size: {{ $item->size }} @endif
                                        @if($item->color) | Color: {{ $item->color }} @endif
                                    </small>
                                </div>
                                <div class="col-2 text-center">
                                    <span class="badge rounded-pill bg-secondary">{{ $item->quantity }}</span>
                                </div>
                                <div class="col-2 text-end">
                                    <strong>${{ number_format($item->price * $item->quantity, 2) }}</strong>
                                    <br>
                                    <small class="text-muted">${{ number_format($item->price, 2) }} each</small>
                                </div>
                            </div>
                        @endforeach

                        <!-- Order Total -->
                        <div class="row">
                            <div class="col-12">
                                <div class="text-end">
                                    @php
                                        $subtotal = $order->items->sum(function($item) {
                                            return $item->price * $item->quantity;
                                        });
                                        $shipping = $order->total_amount - $subtotal;
                                    @endphp
                                    <div class="totals-line">
                                        <div class="label">Subtotal:</div>
                                        <div>${{ number_format($subtotal, 2) }}</div>
                                    </div>
                                    @if($shipping > 0)
                                        <div class="totals-line">
                                            <div class="label">Shipping:</div>
                                            <div>${{ number_format($shipping, 2) }}</div>
                                        </div>
                                    @endif
                                    <hr>
                                    <div class="totals-line">
                                        <div class="label"><strong>Total:</strong></div>
                                        <div><strong class="text-primary">${{ number_format($order->total_amount, 2) }}</strong></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fa fa-box fa-3x text-muted mb-3"></i>
                            <h5>No items found</h5>
                            <p class="text-muted">This order doesn't have any items.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            @if($order->status === 'pending')
                <div class="text-center mt-4">
                    <form method="POST" action="{{ route('shop.orders.cancel', $order) }}" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger"
                                onclick="return confirm('Are you sure you want to cancel this order?')">
                            <i class="fa fa-times"></i> Cancel Order
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.add('orders-page');
        });
    </script>
@endsection
