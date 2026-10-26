@extends('layouts.app')

@section('title', 'Black Friday')

@section('content')
<div class="container py-5">
    <div class="bf-hero mb-4">
        <div>
            <h2 class="mb-1"><i class="fa fa-bolt text-warning"></i> Black Friday</h2>
            <p class="mb-0 text-light">
                @if($isBlackFridayActive)
                    الحملة شغالة حالياً - التقط أفضل الخصومات.
                @else
                    الحملة قادمة - جهز مشترياتك من الآن.
                @endif
            </p>
        </div>
        <div class="bf-countdown-wrap">
            <small class="d-block text-uppercase mb-1">{{ $isBlackFridayActive ? 'Ends in' : 'Starts in' }}</small>
            <div id="bf-countdown" class="bf-countdown" data-target="{{ ($isBlackFridayActive ? $endAt : $startAt)->toIso8601String() }}">
                <span>--d</span> : <span>--h</span> : <span>--m</span> : <span>--s</span>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Eligible Products</h5>
        <span class="badge badge-dark px-3 py-2">{{ $products->total() }} products</span>
    </div>

    <div class="row">
        @forelse($products as $product)
            <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                <div class="card h-100 bf-card">
                    <img src="{{ $product->image_url }}" class="card-img-top" alt="{{ $product->name }}" style="height: 220px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title">{{ $product->name }}</h6>
                        <p class="text-muted small mb-2">{{ $product->category->name ?? 'Uncategorized' }}</p>

                        <div class="mt-auto">
                            @if($isBlackFridayActive)
                                <div class="mb-1">
                                    <strong class="text-danger">${{ number_format($product->current_price, 2) }}</strong>
                                    <small class="text-muted ml-1" style="text-decoration: line-through;">${{ number_format($product->price, 2) }}</small>
                                </div>
                                <span class="badge badge-danger">-{{ $product->effective_discount_percentage }}%</span>
                            @else
                                @php
                                    $bfPercent = (int) ($bfSettings['discount_percentage'] ?? 30);
                                    $bfPreviewPrice = round(((float) $product->price) * (1 - ($bfPercent / 100)), 2);
                                @endphp
                                <div class="mb-1">
                                    <strong class="text-primary">${{ number_format($bfPreviewPrice, 2) }}</strong>
                                    <small class="text-muted ml-1" style="text-decoration: line-through;">${{ number_format($product->price, 2) }}</small>
                                </div>
                                <span class="badge badge-warning text-dark">Expected -{{ $bfPercent }}%</span>
                            @endif
                            <a href="{{ route('shop.products.show', $product) }}" class="btn btn-outline-dark btn-sm btn-block mt-2">View Product</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light border">No products are configured for Black Friday yet.</div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center">
        {{ $products->links() }}
    </div>
</div>
@endsection

@section('styles')
<style>
.bf-hero {
    background: linear-gradient(135deg, #131313 0%, #202020 60%, #5a0000 100%);
    border-radius: 16px;
    color: #fff;
    padding: 22px;
    display: flex;
    justify-content: space-between;
    gap: 18px;
    align-items: center;
    box-shadow: 0 14px 32px rgba(0,0,0,0.25);
}
.bf-countdown {
    font-size: 1.2rem;
    font-weight: 700;
    letter-spacing: 1px;
}
.bf-card {
    transition: transform .2s ease, box-shadow .2s ease;
}
.bf-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 14px 26px rgba(0,0,0,.15);
}
</style>
@endsection

@section('scripts')
<script>
(function() {
    const el = document.getElementById('bf-countdown');
    if (!el) return;
    const target = new Date(el.getAttribute('data-target')).getTime();

    function tick() {
        const now = Date.now();
        let diff = Math.max(0, target - now);
        const d = Math.floor(diff / (1000 * 60 * 60 * 24));
        diff -= d * 1000 * 60 * 60 * 24;
        const h = Math.floor(diff / (1000 * 60 * 60));
        diff -= h * 1000 * 60 * 60;
        const m = Math.floor(diff / (1000 * 60));
        diff -= m * 1000 * 60;
        const s = Math.floor(diff / 1000);
        el.innerHTML = `<span>${d}d</span> : <span>${h}h</span> : <span>${m}m</span> : <span>${s}s</span>`;
    }

    tick();
    setInterval(tick, 1000);
})();
</script>
@endsection
