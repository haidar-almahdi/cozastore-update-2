@extends('layouts.admin')

@section('title', 'Quick Offers')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa fa-bolt text-warning"></i> Quick Offers</h1>
    <p class="page-subtitle">Manage product offers separately from Black Friday</p>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Products Quick Offers</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Base Price</th>
                        <th>Quick Offer</th>
                        <th>Schedule</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            @php($formId = 'quick-offer-form-' . $product->id)
                            <td>
                                <strong>{{ $product->name }}</strong>
                                <br><small class="text-muted">ID: {{ $product->id }}</small>
                            </td>
                            <td>{{ $product->category->name ?? '-' }}</td>
                            <td>${{ number_format($product->price, 2) }}</td>
                            <td style="min-width: 220px;">
                                <form id="{{ $formId }}" method="POST" action="{{ route('admin.offers.quick.update', $product) }}" class="d-none">
                                    @csrf
                                    @method('PATCH')
                                </form>
                                <input type="hidden" form="{{ $formId }}" name="quick_offer_enabled" value="0">
                                <label class="form-check mb-2">
                                    <input form="{{ $formId }}" type="checkbox" class="form-check-input" name="quick_offer_enabled" value="1"
                                        {{ $product->quick_offer_enabled ? 'checked' : '' }}>
                                    <span class="form-check-label">Enabled</span>
                                </label>
                                <input form="{{ $formId }}" type="number" name="quick_offer_percentage" class="form-control form-control-sm"
                                       min="0" max="90" value="{{ (int) ($product->quick_offer_percentage ?? 0) }}" placeholder="Discount %">
                            </td>
                            <td style="min-width: 260px;">
                                <input form="{{ $formId }}" type="datetime-local" name="quick_offer_starts_at" class="form-control form-control-sm mb-1"
                                       value="{{ optional($product->quick_offer_starts_at)->format('Y-m-d\TH:i') }}">
                                <input form="{{ $formId }}" type="datetime-local" name="quick_offer_ends_at" class="form-control form-control-sm"
                                       value="{{ optional($product->quick_offer_ends_at)->format('Y-m-d\TH:i') }}">
                            </td>
                            <td>
                                <button form="{{ $formId }}" type="submit" class="btn btn-primary btn-sm">
                                    <i class="fa fa-save"></i> Save
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No products found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $products->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endsection
