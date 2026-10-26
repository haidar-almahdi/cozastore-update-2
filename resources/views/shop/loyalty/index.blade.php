@extends('layouts.app')

@section('title', 'My Points')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h2 class="mb-0"><i class="fa fa-star text-warning"></i> My Points</h2>
            <a href="{{ route('shop.orders.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa fa-list"></i> My Orders
            </a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted">Current Balance</div>
                            <div class="h3 mb-0">{{ $balance }} pts</div>
                        </div>
                        <div class="rounded-circle bg-warning d-inline-flex align-items-center justify-content-center" style="width:56px;height:56px;color:#fff;">
                            <i class="fa fa-star"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted">Wallet Balance</div>
                    <div class="h3 mb-1">${{ number_format($walletBalance, 2) }}</div>
                    <small class="text-muted">Auto-added from loyalty conversions</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted">Redeemable Blocks</div>
                    <div class="h3 mb-1">{{ $blocks }}</div>
                    <small class="text-muted">Every 100 pts = $10 discount</small>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted">Next $10 unlocks in</div>
                    <div class="h3 mb-1">{{ max(0, 100 - ($balance % 100)) }} pts</div>
                    <small class="text-muted">Points are converted automatically in blocks of 100.</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fa fa-info-circle text-primary"></i> Loyalty Rules</h5>
        </div>
        <div class="card-body">
            <ul class="mb-0">
                <li>You earn <strong>3 points</strong> only when your order status becomes <strong>Delivered</strong>.</li>
                <li>You earn <strong>1 point</strong> for commenting on a product <strong>only if you purchased it before</strong>.</li>
                <li>Comment reward is granted <strong>once per product</strong> only.</li>
                <li>Every <strong>100 points</strong> are automatically converted into <strong>$10</strong> in your wallet.</li>
            </ul>
        </div>
    </div>

    @if(isset($notifications) && $notifications->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fa fa-bell text-success"></i> Recent Wallet Notifications</h5>
            </div>
            <div class="card-body">
                @foreach($notifications as $notification)
                    <div class="alert alert-success mb-2">
                        {{ $notification->data['message'] ?? 'Wallet updated from loyalty points.' }}
                        <div class="small text-muted mt-1">{{ $notification->created_at->diffForHumans() }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="card mt-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0"><i class="fa fa-exchange"></i> Transactions</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th class="text-end">Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $tx)
                            <tr>
                                <td>{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                                <td><span class="badge {{ $tx->points >= 0 ? 'bg-success' : 'bg-danger' }}">{{ $tx->type }}</span></td>
                                <td>{{ $tx->description }}</td>
                                <td class="text-end">
                                    <strong class="{{ $tx->points >= 0 ? 'text-success' : 'text-danger' }}">{{ $tx->points }}</strong>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No transactions yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-3">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
