@extends('layouts.admin')

@section('title', 'Messages Management')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="fa fa-envelope text-primary"></i> Messages Management
                </h1>
                <p class="page-subtitle">View and respond to customer messages</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="stat-card blue">
                <i class="fa fa-envelope stat-icon"></i>
                <div class="stat-number">{{ $messages->total() }}</div>
                <div class="stat-label">Total Messages</div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="stat-card orange">
                <i class="fa fa-envelope-open stat-icon"></i>
                <div class="stat-number">{{ $messages->where('is_read', false)->count() }}</div>
                <div class="stat-label">Unread Messages</div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="stat-card green">
                <i class="fa fa-check-circle stat-icon"></i>
                <div class="stat-number">{{ $messages->where('is_read', true)->count() }}</div>
                <div class="stat-label">Read Messages</div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="stat-card purple">
                <i class="fa fa-calendar stat-icon"></i>
                <div class="stat-number">{{ $messages->where('created_at', '>=', now()->startOfDay())->count() }}</div>
                <div class="stat-label">Today's Messages</div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="stat-card red">
                <i class="fa fa-reply stat-icon"></i>
                <div class="stat-number">{{ $messages->whereNotNull('reply')->count() }}</div>
                <div class="stat-label">Replied Messages</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-section">
        <h5 class="mb-3"><i class="fa fa-filter text-primary"></i> Filter Messages</h5>
        <form method="GET" action="{{ route('admin.messages.index') }}">
            <div class="row">
                <div class="col-md-3">
                    <label class="small font-weight-bold text-muted">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Name, email, subject..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="small font-weight-bold text-muted">Status</label>
                    <select name="status" class="form-control">
                        <option value="">All Messages</option>
                        <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Unread</option>
                        <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Read</option>
                        <option value="replied" {{ request('status') == 'replied' ? 'selected' : '' }}>Replied</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small font-weight-bold text-muted">From Date</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="small font-weight-bold text-muted">To Date</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <label class="small font-weight-bold text-muted">Sort By</label>
                    <select name="sort" class="form-control">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        <option value="unread" {{ request('sort') == 'unread' ? 'selected' : '' }}>Unread First</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="mt-2">
                <a href="{{ route('admin.messages.index') }}" class="btn btn-secondary ml-1">
                    <i class="fa fa-refresh"></i> Clear Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Messages List -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fa fa-list"></i> Messages List</h5>
        </div>
        <div class="card-body p-0">
            @if($messages->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($messages as $message)
                        <div class="list-group-item {{ !$message->is_read ? 'bg-light' : '' }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="mr-3">
                                            @if(!$message->is_read)
                                                <span class="badge badge-warning">New</span>
                                            @endif
                                            @if($message->reply)
                                                <span class="badge badge-success">Replied</span>
                                            @endif
                                        </div>
                                        <h6 class="mb-0 font-weight-bold">{{ $message->subject }}</h6>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <i class="fa fa-user"></i> {{ $message->name }}
                                                <span class="mx-2">|</span>
                                                <i class="fa fa-envelope"></i> {{ $message->email }}
                                            </small>
                                        </div>
                                        <div class="col-md-6 text-md-right">
                                            <small class="text-muted">
                                                <i class="fa fa-calendar"></i> {{ $message->created_at->format('M d, Y H:i') }}
                                                <span class="mx-2">|</span>
                                                {{ $message->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>

                                    <p class="mb-2 mt-2">{{ Str::limit($message->message, 150) }}</p>
                                </div>

                                <div class="ml-3">
                                    <div class="btn-group-vertical" role="group">
                                        <a href="{{ route('admin.messages.show', $message) }}" class="btn btn-info btn-sm mb-1">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                        @if(!$message->is_read)
                                            <form method="POST" action="{{ route('admin.messages.read', $message) }}" class="mb-1">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-success btn-sm w-100">
                                                    <i class="fa fa-check"></i> Mark Read
                                                </button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('admin.messages.destroy', $message) }}"
                                              onclick="return confirmDelete('Are you sure you want to delete this message?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm w-100">
                                                <i class="fa fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="p-3 border-top">
                    {{ $messages->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fa fa-envelope fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Messages Found</h5>
                    <p class="text-muted">No messages match your current filters.</p>
                    <a href="{{ route('admin.messages.index') }}" class="btn btn-primary">
                        <i class="fa fa-refresh"></i> Clear Filters
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Confirm delete actions
    function confirmDelete(message) {
        return confirm(message || 'Are you sure you want to delete this item?');
    }
</script>
@endsection
