@extends('layouts.admin')

@section('title', 'Loyalty - ' . $user->name)

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title"><i class="fa fa-user"></i> {{ $user->name }}</h1>
        <p class="page-subtitle">Email: {{ $user->email }}</p>
    </div>
    <div>
        <a href="{{ route('admin.loyalty.index') }}" class="btn btn-outline-secondary"><i class="fa fa-arrow-left"></i> Back</a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="stat-card purple">
            <div class="stat-number">{{ number_format($user->loyalty_points_balance) }}</div>
            <div class="stat-label">Current Balance</div>
            <i class="fa fa-star stat-icon"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card teal">
            <div class="stat-number">{{ number_format($transactions->total()) }}</div>
            <div class="stat-label">Total Transactions</div>
            <i class="fa fa-exchange stat-icon"></i>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fa fa-list"></i> Transactions</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th class="text-right">Points</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                        <tr>
                            <td>{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                            <td><span class="badge badge-{{ $tx->points >= 0 ? 'success' : 'danger' }}">{{ $tx->type }}</span></td>
                            <td>{{ $tx->description }}</td>
                            <td class="text-right">
                                <strong class="{{ $tx->points >= 0 ? 'text-success' : 'text-danger' }}">{{ $tx->points }}</strong>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No transactions.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection
