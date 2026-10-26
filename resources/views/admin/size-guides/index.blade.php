@extends('layouts.admin')

@section('title', 'Size Guides')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
  <div>
    <h1 class="page-title"><i class="fa fa-ruler"></i> Size Guides</h1>
    <p class="page-subtitle">Manage size references per category or product</p>
  </div>
  <div class="d-flex align-items-center" style="gap:10px;">
    <form method="POST" action="{{ route('admin.size-guides.generate-defaults') }}" onsubmit="return confirm('Generate default English size guides (Clothing, Shoes, Accessories) for all categories? Existing ones will be skipped.');">
      @csrf
      <button type="submit" class="btn btn-warning"><i class="fa fa-magic"></i> Generate Defaults</button>
    </form>
    <a href="{{ route('admin.size-guides.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> New Guide</a>
  </div>
</div>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0"><i class="fa fa-list"></i> All Guides</h5>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Name</th>
            <th>Scope</th>
            <th>Unit</th>
            <th>Status</th>
            <th>Updated</th>
            <th class="text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($guides as $g)
            <tr>
              <td>{{ $g->name }}</td>
              <td><span class="badge badge-info">{{ ucfirst($g->scope_type) }}</span> #{{ $g->scope_id }}</td>
              <td><span class="badge badge-secondary">{{ strtoupper($g->unit) }}</span></td>
              <td>
                <span class="badge badge-{{ $g->is_active ? 'success' : 'secondary' }}">{{ $g->is_active ? 'Active' : 'Inactive' }}</span>
              </td>
              <td>{{ $g->updated_at->diffForHumans() }}</td>
              <td class="text-right">
                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.size-guides.edit', $g) }}"><i class="fa fa-edit"></i></a>
                <form action="{{ route('admin.size-guides.destroy', $g) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this guide?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted">No size guides yet.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="d-flex justify-content-end">{{ $guides->links() }}</div>
  </div>
</div>
@endsection
