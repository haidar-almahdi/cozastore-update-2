@extends('layouts.admin')

@section('title', 'Order Details #' . $order->order_number)

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="fa fa-shopping-cart text-primary"></i> Order Details #{{ $order->order_number }}
                </h1>
                <p class="page-subtitle">View and manage order information</p>
            </div>
            <div>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary mr-2">
                    <i class="fa fa-arrow-left"></i> Back to List
                </a>
                <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-primary">
                    <i class="fa fa-edit"></i> Edit Order
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Order Information -->
        <div class="col-lg-8">
            <!-- Customer Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa fa-user"></i> Customer Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="small font-weight-bold text-muted">Name</label>
                                <p class="mb-0 font-weight-bold">{{ $order->user->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="small font-weight-bold text-muted">Email</label>
                                <p class="mb-0">{{ $order->user->email }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="small font-weight-bold text-muted">Phone</label>
                                <p class="mb-0">{{ $order->user->phone ?? 'Not provided' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="small font-weight-bold text-muted">Address</label>
                                <p class="mb-0">{{ $order->user->address ?? 'Not provided' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa fa-list"></i> Order Items</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->product->image)
                                                <img src="{{ asset('storage/' . $item->product->image) }}"
                                                     alt="{{ $item->product->name }}"
                                                     class="rounded mr-3"
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <strong>{{ $item->product->name }}</strong>
                                                <br><small class="text-muted">{{ $item->product->category->name ?? 'No Category' }}</small>
                                                @if($item->size || $item->color)
                                                    <br>
                                                    @if($item->size)
                                                        <span class="badge badge-info badge-sm">Size: {{ $item->size }}</span>
                                                    @endif
                                                    @if($item->color)
                                                        <span class="badge badge-secondary badge-sm ml-1">Color: {{ $item->color }}</span>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="font-weight-bold">${{ number_format($item->price, 2) }}</td>
                                    <td>
                                        <span class="badge badge-primary">{{ $item->quantity }}</span>
                                    </td>
                                    <td class="font-weight-bold text-success">${{ number_format($item->price * $item->quantity, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-active">
                                    <th colspan="3" class="text-right">Total Amount:</th>
                                    <th class="text-success">${{ number_format($order->total_amount, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4">
            <!-- Order Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa fa-info-circle"></i> Order Status</h5>
                </div>
                <div class="card-body">
                    @php
                        $statusColors = [
                            'pending' => 'warning',
                            'processing' => 'info',
                            'completed' => 'success',
                            'cancelled' => 'danger'
                        ];
                        $statusLabels = [
                            'pending' => 'Pending',
                            'processing' => 'Processing',
                            'completed' => 'Completed',
                            'cancelled' => 'Cancelled'
                        ];
                    @endphp
                    <div class="text-center mb-3">
                        <span class="badge badge-{{ $statusColors[$order->status] ?? 'secondary' }} p-3" style="font-size: 1rem;">
                            {{ $statusLabels[$order->status] ?? $order->status }}
                        </span>
                    </div>

                    <form method="POST" action="{{ route('admin.orders.update', $order) }}">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label class="small font-weight-bold text-muted">Update Status</label>
                            <select name="status" class="form-control">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fa fa-save"></i> Update Status
                        </button>
                    </form>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa fa-calculator"></i> Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Items Count:</span>
                        <strong>{{ $order->items->sum('quantity') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <strong>${{ number_format($order->total_amount, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping:</span>
                        <strong>Free</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Total:</strong>
                        <strong class="text-success">${{ number_format($order->total_amount, 2) }}</strong>
                    </div>
                </div>
            </div>

            <!-- Order Timeline -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa fa-clock-o"></i> Order Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Order Placed</h6>
                                <small class="text-muted">{{ $order->created_at->format('M d, Y H:i') }}</small>
                            </div>
                        </div>
                        @if($order->status != 'pending')
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Status Updated</h6>
                                <small class="text-muted">{{ $order->updated_at->format('M d, Y H:i') }}</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #dee2e6;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }

    .timeline-marker {
        position: absolute;
        left: -23px;
        top: 5px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 3px solid #fff;
        box-shadow: 0 0 0 2px #dee2e6;
    }

    .timeline-content h6 {
        margin-bottom: 5px;
        font-weight: 600;
    }
</style>
@endsection
