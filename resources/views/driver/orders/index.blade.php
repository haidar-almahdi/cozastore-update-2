@extends('layouts.driver')

@section('title', 'Delivery Orders')

@section('styles')
<style>
    .driver-title {
        background: linear-gradient(135deg, #4f6fc6 0%, #7a58c1 100%);
        color: #fff;
        border-radius: 14px;
        padding: 14px 18px;
        box-shadow: 0 10px 24px rgba(76, 109, 193, .26);
    }
    .section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
    }
    .order-item {
        border: 1px solid #e8edf6;
        border-radius: 12px;
        padding: 12px;
        margin-bottom: 10px;
        background: #fff;
        transition: all .2s ease;
    }
    .order-item:hover {
        border-color: #cfdaf0;
        box-shadow: 0 8px 18px rgba(62, 85, 125, .08);
        transform: translateY(-1px);
    }
    .order-no { font-weight: 700; color: #2f3f57; }
    .dim { font-size: .85rem; color: #758399; }
    .btn-pill { border-radius: 999px; }
</style>
@endsection

@section('content')
<div class="driver-title mb-4">
    <h3 class="mb-1"><i class="fa fa-truck"></i> Delivery Orders</h3>
    <div class="small">Accept available orders, deliver active ones, and track your completed deliveries.</div>
</div>

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="section-head">
                    <h5 class="mb-0">Available Orders</h5>
                    <span class="soft-badge">{{ $availableOrders->total() }}</span>
                </div>
                @forelse($availableOrders as $order)
                    <div class="order-item">
                        <div class="order-no">{{ $order->order_number }}</div>
                        <div class="dim"><i class="fa fa-user"></i> {{ $order->user->name }}</div>
                        <form method="POST" action="{{ route('driver.orders.accept', $order) }}" class="mt-2">
                            @csrf
                            <button class="btn btn-sm btn-primary btn-pill"><i class="fa fa-handshake-o"></i> Accept</button>
                        </form>
                    </div>
                @empty
                    <p class="text-muted">No available orders.</p>
                @endforelse
                {{ $availableOrders->links() }}
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="section-head">
                    <h5 class="mb-0">My Active Orders</h5>
                    <span class="soft-badge">{{ $myActiveOrders->total() }}</span>
                </div>
                @forelse($myActiveOrders as $order)
                    <div class="order-item">
                        <div class="order-no">{{ $order->order_number }}</div>
                        <div class="dim"><i class="fa fa-map-marker"></i> {{ $order->shipping_address }}</div>
                        <a class="btn btn-sm btn-outline-info btn-pill mt-2" href="{{ route('driver.orders.show', $order) }}">
                            <i class="fa fa-eye"></i> Details
                        </a>
                        <form method="POST" action="{{ route('driver.orders.delivered', $order) }}" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-success btn-pill mt-2"><i class="fa fa-check"></i> Mark Delivered</button>
                        </form>
                    </div>
                @empty
                    <p class="text-muted">No active orders.</p>
                @endforelse
                {{ $myActiveOrders->links() }}
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="section-head">
                    <h5 class="mb-0">Delivered by Me</h5>
                    <span class="soft-badge">{{ $myDeliveredOrders->total() }}</span>
                </div>
                @forelse($myDeliveredOrders as $order)
                    <div class="order-item">
                        <div class="order-no">{{ $order->order_number }}</div>
                        <div class="dim"><i class="fa fa-user"></i> {{ $order->user->name }}</div>
                        <span class="badge badge-success mt-2"><i class="fa fa-check"></i> Delivered</span>
                    </div>
                @empty
                    <p class="text-muted">No delivered orders yet.</p>
                @endforelse
                {{ $myDeliveredOrders->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
