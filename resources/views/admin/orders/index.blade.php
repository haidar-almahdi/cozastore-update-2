@extends('layouts.admin')

@section('title', 'Orders Management')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="fa fa-shopping-cart text-primary"></i> Orders Management
                </h1>
                <p class="page-subtitle">View and manage all customer orders</p>
            </div>
            <div>
                <a href="{{ route('admin.orders.create') }}" class="btn btn-primary mr-2">
                    <i class="fa fa-plus"></i> New Order
                </a>
                <a href="{{ route('admin.orders.index', array_merge(request()->except('page'), ['status' => 'completed'])) }}" class="btn btn-outline-success mr-2">
                    <i class="fa fa-check-circle"></i> Delivered Orders
                </a>
                <a href="{{ route('admin.orders.export', request()->query()) }}" class="btn btn-success">
                    <i class="fa fa-download"></i> Export
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="stat-card blue">
                <i class="fa fa-shopping-cart stat-icon"></i>
                <div class="stat-number">{{ $stats['total_orders'] }}</div>
                <div class="stat-label">Total Orders</div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="stat-card green">
                <i class="fa fa-dollar stat-icon"></i>
                <div class="stat-number">${{ number_format($stats['total_revenue'], 0) }}</div>
                <div class="stat-label">Total Revenue</div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="stat-card orange">
                <i class="fa fa-clock-o stat-icon"></i>
                <div class="stat-number">{{ $stats['pending_orders'] }}</div>
                <div class="stat-label">Pending Orders</div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="stat-card purple">
                <i class="fa fa-calendar stat-icon"></i>
                <div class="stat-number">{{ $stats['today_orders'] }}</div>
                <div class="stat-label">Today's Orders</div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="stat-card red">
                <i class="fa fa-line-chart stat-icon"></i>
                <div class="stat-number">${{ number_format($stats['avg_order_value'], 0) }}</div>
                <div class="stat-label">Avg Order Value</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-section">
        <h5 class="mb-3"><i class="fa fa-filter text-primary"></i> Filter Orders</h5>
        <form method="GET" action="{{ route('admin.orders.index') }}">
            <div class="row">
                <div class="col-md-3">
                    <label class="small font-weight-bold text-muted">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Order number, customer name..."
                           class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="small font-weight-bold text-muted">Customer</label>
                    <select name="customer_id" class="form-control">
                        <option value="">All Customers</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small font-weight-bold text-muted">Status</label>
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small font-weight-bold text-muted">Min Amount</label>
                    <input type="number" name="min_amount" value="{{ request('min_amount') }}"
                           step="0.01" placeholder="0.00"
                           class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="small font-weight-bold text-muted">From Date</label>
                    <input type="date" name="from_date" value="{{ request('from_date') }}"
                           class="form-control">
                </div>
                <div class="col-md-1">
                    <label class="small font-weight-bold text-muted">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-search"></i>
                        </button>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary ml-1">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fa fa-list"></i> Orders List</h5>
        </div>
        <div class="card-body p-0">
            @if($orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width: 120px;">Order Number</th>
                                <th style="width: 200px;">Customer</th>
                                <th style="width: 120px;">Date</th>
                                <th style="width: 100px;">Status</th>
                                <th style="width: 80px;">Items</th>
                                <th style="width: 100px;">Amount</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-primary font-weight-bold">
                                        {{ $order->order_number }}
                                    </a>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $order->user->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $order->user->email }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $order->created_at->format('Y-m-d') }}</div>
                                    <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                </td>
                                <td>
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
                                    <span class="badge badge-{{ $statusColors[$order->status] ?? 'secondary' }}">
                                        {{ $statusLabels[$order->status] ?? $order->status }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light">{{ $order->items->sum('quantity') }}</span>
                                </td>
                                <td>
                                    <span class="font-weight-bold text-success">${{ number_format($order->total_amount, 2) }}</span>
                                </td>
                                <td>
                                    @if($order->status === 'completed')
                                        <span class="badge badge-success">
                                            <i class="fa fa-check"></i> Delivered
                                        </span>
                                    @elseif($order->status === 'cancelled')
                                        <span class="badge badge-danger">
                                            <i class="fa fa-times"></i> Cancelled
                                        </span>
                                    @else
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.orders.show', $order) }}"
                                               class="btn btn-info btn-sm" title="View Details">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <form method="POST"
                                                  action="{{ route('admin.orders.update', $order) }}"
                                                  class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit"
                                                        class="btn btn-success btn-sm"
                                                        title="Mark as Delivered">
                                                    <i class="fa fa-check"></i>
                                                </button>
                                            </form>
                                            <a href="{{ route('admin.orders.edit', $order) }}"
                                               class="btn btn-warning btn-sm" title="Edit Order">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                        title="Delete Order"
                                                        onclick="return confirmDelete('Are you sure you want to delete this order?')">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-3 border-top">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fa fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Orders Found</h5>
                    <p class="text-muted">No orders match your current filters.</p>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-primary">
                        <i class="fa fa-refresh"></i> Clear Filters
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Status update with AJAX
    function updateOrderStatus(orderId, status) {
        $.ajax({
            url: `/admin/orders/${orderId}/status`,
            method: 'PATCH',
            data: {
                status: status,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                location.reload();
            },
            error: function() {
                alert('Error updating order status');
            }
        });
    }
</script>
@endsection
