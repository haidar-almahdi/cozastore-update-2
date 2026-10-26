@extends('layouts.admin')

@section('title', 'Users Management')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="fa fa-users text-primary"></i> Users Management
                </h1>
                <p class="page-subtitle">Manage customer accounts and administrators</p>
            </div>
            <div>
                <a href="{{ route('admin.register.form') }}" class="btn btn-primary mr-2">
                    <i class="fa fa-user-plus"></i> Add New Admin
                </a>
                <a href="{{ route('admin.drivers.create') }}" class="btn btn-info">
                    <i class="fa fa-motorcycle"></i> Add Delivery Driver
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="stat-card blue">
                <i class="fa fa-users stat-icon"></i>
                <div class="stat-number">{{ $users->total() }}</div>
                <div class="stat-label">Total Users</div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="stat-card green">
                <i class="fa fa-user stat-icon"></i>
                <div class="stat-number">{{ $users->where('role', 'customer')->count() }}</div>
                <div class="stat-label">Customers</div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="stat-card purple">
                <i class="fa fa-user-secret stat-icon"></i>
                <div class="stat-number">{{ $users->where('role', 'admin')->count() }}</div>
                <div class="stat-label">Administrators</div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="stat-card teal">
                <i class="fa fa-motorcycle stat-icon"></i>
                <div class="stat-number">{{ $users->where('role', 'driver')->count() }}</div>
                <div class="stat-label">Drivers</div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="stat-card orange">
                <i class="fa fa-calendar stat-icon"></i>
                <div class="stat-number">{{ $users->where('created_at', '>=', now()->startOfMonth())->count() }}</div>
                <div class="stat-label">New This Month</div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="stat-card red">
                <i class="fa fa-shopping-cart stat-icon"></i>
                <div class="stat-number">{{ $users->filter(function($user) { return $user->orders->count() > 0; })->count() }}</div>
                <div class="stat-label">Active Buyers</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-section">
        <h5 class="mb-3"><i class="fa fa-filter text-primary"></i> Filter Users</h5>
        <form method="GET" action="{{ route('admin.users.index') }}">
            <div class="row">
                <div class="col-md-3">
                    <label class="small font-weight-bold text-muted">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Name, email..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="small font-weight-bold text-muted">Role</label>
                    <select name="role" class="form-control">
                        <option value="">All Roles</option>
                        <option value="customer" {{ request('role') == 'customer' ? 'selected' : '' }}>Customer</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                        <option value="driver" {{ request('role') == 'driver' ? 'selected' : '' }}>Driver</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small font-weight-bold text-muted">Gender</label>
                    <select name="gender" class="form-control">
                        <option value="">All Genders</option>
                        <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small font-weight-bold text-muted">Registration Date</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="small font-weight-bold text-muted">To Date</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-1">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="mt-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary ml-1">
                    <i class="fa fa-refresh"></i> Clear Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fa fa-list"></i> Users List</h5>
        </div>
        <div class="card-body p-0">
            @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width: 80px;">ID</th>
                                <th style="width: 200px;">User</th>
                                <th style="width: 100px;">Role</th>
                                <th style="width: 80px;">Gender</th>
                                <th style="width: 100px;">Orders</th>
                                <th style="width: 120px;">Total Spent</th>
                                <th style="width: 120px;">Joined</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td class="font-weight-bold">{{ $user->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white mr-3"
                                                 style="width: 40px; height: 40px; font-weight: bold;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <strong>{{ $user->name }}</strong>
                                                <br><small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{
                                            $user->role == 'admin' ? 'danger' :
                                            ($user->role == 'driver' ? 'info' : 'primary')
                                        }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($user->gender)
                                            <span class="badge badge-{{ $user->gender == 'male' ? 'info' : 'pink' }}">
                                                <i class="fa fa-{{ $user->gender == 'male' ? 'male' : 'female' }}"></i>
                                                {{ ucfirst($user->gender) }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-light">{{ $user->orders->count() }}</span>
                                    </td>
                                    <td class="font-weight-bold text-success">
                                        ${{ number_format($user->orders->sum('total_amount'), 2) }}
                                    </td>
                                    <td>
                                        <div>{{ $user->created_at->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-info btn-sm" title="View Details" onclick="showUserDetails({{ $user->id }})">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            @if($user->role == 'customer')
                                                <a href="{{ route('admin.orders.index', ['customer_id' => $user->id]) }}" class="btn btn-success btn-sm" title="View Orders">
                                                    <i class="fa fa-shopping-cart"></i>
                                                </a>
                                            @endif
                                            @if($user->id != auth()->id())
                                                <button class="btn btn-danger btn-sm" title="Delete User" onclick="deleteUser({{ $user->id }})">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-3 border-top">
                    {{ $users->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fa fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Users Found</h5>
                    <p class="text-muted">No users match your current filters.</p>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-primary">
                        <i class="fa fa-refresh"></i> Clear Filters
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- User Details Modal -->
    <div class="modal fade" id="userDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa fa-user"></i> User Details</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="userDetailsContent">
                    <div class="text-center">
                        <i class="fa fa-spinner fa-spin fa-2x"></i>
                        <p>Loading...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
    .badge-pink {
        background-color: #e91e63;
        color: white;
    }
</style>
@endsection

@section('scripts')
<script>
    function showUserDetails(userId) {
        $('#userDetailsModal').modal('show');

        // Simulate loading user details (you can implement actual AJAX call)
        setTimeout(() => {
            const user = @json($users->keyBy('id'));
            const userData = user[userId];

            if (userData) {
                $('#userDetailsContent').html(`
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Personal Information</h6>
                            <table class="table table-sm">
                                <tr><td><strong>Name:</strong></td><td>${userData.name}</td></tr>
                                <tr><td><strong>Email:</strong></td><td>${userData.email}</td></tr>
                                <tr><td><strong>Role:</strong></td><td><span class="badge badge-${userData.role == 'admin' ? 'danger' : 'primary'}">${userData.role}</span></td></tr>
                                <tr><td><strong>Gender:</strong></td><td>${userData.gender || 'Not specified'}</td></tr>
                                <tr><td><strong>Phone:</strong></td><td>${userData.phone || 'Not provided'}</td></tr>
                                <tr><td><strong>Address:</strong></td><td>${userData.address || 'Not provided'}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Account Statistics</h6>
                            <table class="table table-sm">
                                <tr><td><strong>Joined:</strong></td><td>${new Date(userData.created_at).toLocaleDateString()}</td></tr>
                                <tr><td><strong>Last Updated:</strong></td><td>${new Date(userData.updated_at).toLocaleDateString()}</td></tr>
                                <tr><td><strong>Total Orders:</strong></td><td>${userData.orders ? userData.orders.length : 0}</td></tr>
                                <tr><td><strong>Total Spent:</strong></td><td>$${userData.orders ? userData.orders.reduce((sum, order) => sum + parseFloat(order.total_amount), 0).toFixed(2) : '0.00'}</td></tr>
                            </table>
                        </div>
                    </div>
                `);
            }
        }, 500);
    }

    function deleteUser(userId) {
        if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
            // Implement delete functionality
            alert('Delete functionality would be implemented here');
        }
    }
</script>
@endsection
