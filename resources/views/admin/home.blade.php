@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="fa fa-tachometer text-primary"></i> Dashboard Overview
                </h1>
                <p class="page-subtitle">Welcome back, {{ Auth::user()->name }}! Here's what's happening with your store.</p>
            </div>
            <div>
                <a href="{{ route('shop.home') }}" class="btn btn-outline-primary mr-2">
                    <i class="fa fa-globe"></i> View Website
                </a>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-primary">
                    <i class="fa fa-shopping-cart"></i> View Orders
                </a>
            </div>
        </div>
    </div>

    @if(isset($loyaltyNotifications) && $loyaltyNotifications->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fa fa-bell"></i> Loyalty Conversion Alerts</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @foreach($loyaltyNotifications as $notification)
                        <div class="list-group-item">
                            <strong>{{ $notification->data['customer_name'] ?? 'Customer' }}</strong>
                            reached conversion threshold and received
                            <strong>${{ number_format((float)($notification->data['amount'] ?? 0), 2) }}</strong>
                            in wallet credit.
                            <div class="text-muted small">{{ $notification->created_at->diffForHumans() }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card blue">
                <i class="fa fa-users stat-icon"></i>
                <div class="stat-number">{{ number_format($userCount) }}</div>
                <div class="stat-label">Registered Users</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card green">
                <i class="fa fa-dollar stat-icon"></i>
                <div class="stat-number">${{ number_format($totalProfits, 2) }}</div>
                <div class="stat-label">Total Profits</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card orange">
                <i class="fa fa-male stat-icon"></i>
                <div class="stat-number">{{ $malePercent }}%</div>
                <div class="stat-label">Male Users</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card purple">
                <i class="fa fa-female stat-icon"></i>
                <div class="stat-number">{{ $femalePercent }}%</div>
                <div class="stat-label">Female Users</div>
            </div>
        </div>
    </div>

    <!-- Additional Statistics Row -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card red">
                <i class="fa fa-cube stat-icon"></i>
                <div class="stat-number">{{ number_format($productCount) }}</div>
                <div class="stat-label">Total Products</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card teal">
                <i class="fa fa-tags stat-icon"></i>
                <div class="stat-number">{{ number_format($categoryCount) }}</div>
                <div class="stat-label">Categories</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card yellow">
                <i class="fa fa-shopping-cart stat-icon"></i>
                <div class="stat-number">{{ number_format($orderCount) }}</div>
                <div class="stat-label">Total Orders</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card pink">
                <i class="fa fa-envelope stat-icon"></i>
                <div class="stat-number">{{ number_format($messageCount) }}</div>
                <div class="stat-label">Messages</div>
                @if($unreadMessageCount > 0)
                    <small class="text-white">({{ $unreadMessageCount }} unread)</small>
                @endif
            </div>
        </div>
    </div>

    <!-- Additional Statistics -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa fa-star"></i> Most Requested Products</h5>
                </div>
                <div class="card-body">
                    @if($topProducts->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($topProducts as $product)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $product->name }}</strong>
                                    <br><small class="text-muted">${{ number_format($product->price, 2) }}</small>
                                </div>
                                <span class="badge badge-primary badge-pill">{{ $product->order_items_count }} orders</span>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fa fa-cube fa-2x text-muted mb-2"></i>
                            <p class="text-muted">No product orders yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa fa-trophy"></i> Top Users by Orders</h5>
                </div>
                <div class="card-body">
                    @if($topUsers->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($topUsers as $user)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $user->name }}</strong>
                                    <br><small class="text-muted">{{ $user->email }}</small>
                                </div>
                                <span class="badge badge-success badge-pill">{{ $user->orders_count }} orders</span>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fa fa-users fa-2x text-muted mb-2"></i>
                            <p class="text-muted">No user orders yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
