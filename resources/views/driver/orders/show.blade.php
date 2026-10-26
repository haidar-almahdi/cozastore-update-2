@extends('layouts.driver')

@section('title', 'Order Details')

@section('styles')
<style>
    .head-card {
        background: linear-gradient(135deg, #4f6fc6 0%, #7a58c1 100%);
        color: #fff;
        border-radius: 14px;
        padding: 16px 18px;
        margin-bottom: 16px;
        box-shadow: 0 10px 24px rgba(76,109,193,.24);
    }
    .meta-card {
        border: 1px solid #e9edf7;
        border-radius: 12px;
        padding: 12px;
        background: #fff;
    }
    .table thead th { background: #f7f9fd; border-bottom: 1px solid #e3e9f4; }
    .btn-pill { border-radius: 999px; }
</style>
@endsection

@section('content')
<div class="head-card">
    <h4 class="mb-1"><i class="fa fa-file-text-o"></i> {{ $order->order_number }}</h4>
    <div class="small">Order details and delivery actions</div>
</div>

<div class="card">
    <div class="card-body">
        <div class="meta-card mb-3">
            <p class="mb-1"><strong>Customer:</strong> {{ $order->user->name }} ({{ $order->user->email }})</p>
            <p class="mb-1"><strong>Phone:</strong> {{ $order->shipping_phone }}</p>
            <p class="mb-0"><strong>Address:</strong> {{ $order->shipping_address }}</p>
        </div>

        <table class="table table-sm">
            <thead>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Size</th>
                <th>Color</th>
            </tr>
            </thead>
            <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product->name ?? 'Deleted product' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->size ?: '-' }}</td>
                    <td>{{ $item->color ?: '-' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <a href="{{ route('driver.orders.index') }}" class="btn btn-secondary btn-pill">Back</a>
        @if($order->driver_id === auth()->id() && $order->status !== 'completed')
            <form method="POST" action="{{ route('driver.orders.delivered', $order) }}" class="d-inline">
                @csrf
                <button class="btn btn-success btn-pill"><i class="fa fa-check"></i> Mark Delivered</button>
            </form>
        @endif
    </div>
</div>
@endsection
