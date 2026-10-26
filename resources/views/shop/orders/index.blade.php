@extends('layouts.app')

@section('title', 'My Orders')

@section('styles')
    <style>
        .orders-hero-title {
            display: flex; align-items: center; gap: 10px; font-weight: 700;
        }
        .orders-hero-title i { color: #6c63ff; }

        .stat-card { border: 1px solid #eee; border-radius: 12px; box-shadow: 0 6px 18px rgba(0,0,0,0.04); }
        .stat-card .card-body { padding: 18px; }
        .stat-icon { width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; margin-bottom: 6px; }
        .stat-icon.primary { background: rgba(0,123,255,.1); color: #007bff; }
        .stat-icon.success { background: rgba(40,167,69,.12); color: #28a745; }
        .stat-icon.warning { background: rgba(255,193,7,.15); color: #ffc107; }
        .stat-icon.info { background: rgba(108,99,255,.12); color: #6c63ff; }

        .order-row { transition: box-shadow .2s ease, transform .08s ease; border-bottom: 1px solid #f1f1f1; }
        .order-row:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.06); transform: translateY(-1px); border-radius: 10px; }
        .order-thumb { width: 56px; height: 56px; object-fit: contain; background: #fff; border: 1px solid #eee; border-radius: 10px; }
        .order-meta small { color: #6c757d; }
        .badge.rounded-pill { padding: .5em .75em; font-weight: 600; }
        .btn-soft { border-width: 1px; }
        .btn-soft.btn-outline-primary { border-color: rgba(0,123,255,.35); }
        .btn-soft.btn-outline-danger { border-color: rgba(220,53,69,.35); }
        @media (max-width: 576px) {
            .order-actions { margin-top: 8px; }
        }
    </style>
@endsection

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h2 class="orders-hero-title mb-0">
                    <i class="fa fa-shopping-bag"></i>
                    <span>My Orders</span>
                </h2>
            </div>

            <!-- Statistics Cards -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card stat-card text-center h-100">
                        <div class="card-body">
                            <div class="stat-icon info"><i class="fa fa-list"></i></div>
                            <h5 class="card-title mb-1">{{ $stats['total_orders'] }}</h5>
                            <small class="text-muted">Total Orders</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card stat-card text-center h-100">
                        <div class="card-body">
                            <div class="stat-icon primary"><i class="fa fa-dollar"></i></div>
                            <h5 class="card-title mb-1">${{ number_format($stats['total_spent'], 2) }}</h5>
                            <small class="text-muted">Total Spent</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card stat-card text-center h-100">
                        <div class="card-body">
                            <div class="stat-icon warning"><i class="fa fa-clock-o"></i></div>
                            <h5 class="card-title mb-1">{{ $stats['pending_orders'] }}</h5>
                            <small class="text-muted">Pending Orders</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card stat-card text-center h-100">
                        <div class="card-body">
                            <div class="stat-icon success"><i class="fa fa-check"></i></div>
                            <h5 class="card-title mb-1">{{ $stats['completed_orders'] }}</h5>
                            <small class="text-muted">Completed Orders</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders List -->
            @if($orders->count() > 0)
                <div class="card">
                    <div class="card-body">
                        @foreach($orders as $order)
                            <div class="row align-items-center mb-3 pb-3 order-row">
                                <div class="col-6 col-md-3 order-meta">
                                    <strong>{{ $order->order_number }}</strong>
                                    <br>
                                    <small>{{ $order->created_at->format('M d, Y') }}</small>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="d-flex align-items-center gap-2">
                                        @if($order->items->first() && $order->items->first()->product)
                                            <img src="{{ $order->items->first()->product->image_url }}"
                                                 alt="Product" class="order-thumb me-2">
                                        @endif
                                        <div>
                                            <small class="text-muted">{{ $order->items->count() }} item(s)</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-2 mt-2 mt-md-0">
                                    <strong>${{ number_format($order->total_amount, 2) }}</strong>
                                </div>
                                <div class="col-6 col-md-2 mt-2 mt-md-0">
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
                                <div class="col-12 col-md-2 text-md-end order-actions mt-2 mt-md-0">
                                    <a href="{{ route('shop.orders.show', $order) }}" class="btn btn-sm btn-outline-primary btn-soft me-1">
                                        View Details
                                    </a>
                                    @if($order->status === 'pending')
                                        <form method="POST" action="{{ route('shop.orders.cancel', $order) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-danger btn-soft"
                                                    onclick="return confirm('Are you sure you want to cancel this order?')">
                                                Cancel
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pagination -->
                @if($orders->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $orders->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fa fa-shopping-bag fa-3x text-muted mb-3"></i>
                    <h4>No orders yet</h4>
                    <p class="text-muted">You haven't placed any orders yet.</p>
                    <a href="{{ route('shop.products.index') }}" class="btn btn-primary">Start Shopping</a>
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
