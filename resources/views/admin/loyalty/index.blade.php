@extends('layouts.admin')

@section('title', 'Loyalty Points')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title"><i class="fa fa-star"></i> Loyalty Points</h1>
        <p class="page-subtitle">Overview of user balances and transactions</p>
    </div>
    <form method="GET" class="form-inline">
        <div class="input-group">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search users...">
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </form>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="stat-card purple">
            <div class="stat-number">{{ number_format($stats['total_users']) }}</div>
            <div class="stat-label">Users</div>
            <i class="fa fa-users stat-icon"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card yellow">
            <div class="stat-number">{{ number_format($stats['total_points']) }}</div>
            <div class="stat-label">Total Points</div>
            <i class="fa fa-star stat-icon"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card teal">
            <div class="stat-number">{{ number_format($stats['total_transactions']) }}</div>
            <div class="stat-label">Transactions</div>
            <i class="fa fa-exchange stat-icon"></i>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fa fa-list"></i> Users by Points</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th class="text-right">Balance</th>
                        <th class="text-right">Transactions</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td class="text-right"><span class="badge badge-primary">{{ $user->loyalty_points_balance }} pts</span></td>
                            <td class="text-right">{{ $user->total_transactions }}</td>
                            <td class="text-right">
                                <a href="{{ route('admin.loyalty.show', $user) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fa fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
