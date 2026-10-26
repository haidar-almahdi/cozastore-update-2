@extends('layouts.app')

@section('title', $product->name . ' - Size Guide')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ $product->image_url }}" class="rounded" alt="{{ $product->name }}" style="width:64px;height:64px;object-fit:contain;background:#fff;border:1px solid #eee;">
                <div>
                    <h2 class="mb-1">Size Guide · {{ $product->name }}</h2>
                    <div class="text-muted">Category: {{ $product->category->name ?? '—' }}</div>
                </div>
            </div>
            <a href="{{ route('shop.products.show', $product) }}" class="btn btn-outline-secondary">
                <i class="fa fa-arrow-left"></i> Back to product
            </a>
        </div>
    </div>

    @if(!$guide)
        <div class="alert alert-light">
            <i class="fa fa-info-circle"></i>
            No size guide has been provided for this product yet.
        </div>
    @else
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap" style="gap:12px;">
                <div>
                    <h4 class="mb-1">{{ $guide->name }}</h4>
                    <div class="text-muted small">Default unit: <span class="badge badge-secondary">{{ strtoupper($guide->unit) }}</span> @if($guide->gender) · {{ ucfirst($guide->gender) }} @endif</div>
                </div>
                <form method="GET" class="unit-toggle" style="margin:0;">
                    <input type="hidden" name="unit" value="{{ ($unit ?? $guide->unit) }}">
                    <div class="btn-group" role="group" aria-label="Unit toggle">
                        <button type="submit" name="unit" value="cm" class="btn btn-sm {{ ($unit ?? $guide->unit) === 'cm' ? 'btn-primary' : 'btn-outline-primary' }}">CM</button>
                        <button type="submit" name="unit" value="inch" class="btn btn-sm {{ ($unit ?? $guide->unit) === 'inch' ? 'btn-primary' : 'btn-outline-primary' }}">INCH</button>
                    </div>
                </form>
            </div>
            <div class="card-body p-0">
                @php
                    $schema = $guide->table_schema ?? [];
                    $rows = $guide->rows ?? [];
                    $useInch = ($unit ?? $guide->unit) === 'inch';
                    $isCmBase = ($guide->unit ?? 'cm') === 'cm';
                    $convert = function($value) use ($useInch, $isCmBase) {
                        if(!is_numeric($value)) return $value;
                        if($useInch && $isCmBase) return round($value / 2.54, 1);
                        if(!$useInch && !$isCmBase) return round($value * 2.54, 1);
                        return $value;
                    };
                @endphp

                @if(empty($schema) || empty($rows))
                    <div class="p-3 text-muted">No data available for this guide.</div>
                @else
                    <div class="sg-table-wrap">
                        <table class="table sg-table mb-0">
                            <thead>
                                <tr>
                                    @foreach($schema as $i => $col)
                                        <th class="{{ $i === 0 ? 'sg-col-first' : '' }}">{{ $col['label'] ?? $col['key'] }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rows as $r)
                                    <tr>
                                        @foreach($schema as $i => $col)
                                            @php
                                                $key = $col['key'];
                                                $val = $r[$key] ?? '';
                                                $show = $convert($val);
                                                $isNum = is_numeric($show);
                                            @endphp
                                            <td class="{{ $i === 0 ? 'sg-col-first' : '' }} {{ $isNum ? 'sg-num' : 'sg-text' }}">{{ $isNum ? number_format($show, is_float($show) ? 1 : 0) : $show }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                <div class="p-3 pt-2 small text-muted border-top">
                    Tips: Measure a similar item you own and compare. Inch values are rounded to 1 decimal.
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('styles')
<style>
  .sg-table-wrap{ width:100%; overflow-x:auto; }
  .sg-table{ width:100%; border-collapse:separate; border-spacing:0; }
  .sg-table thead th{ position:sticky; top:0; background:#0d1321; color:#fff; z-index:2; padding:.75rem .85rem; font-weight:600; white-space:nowrap; border-bottom:1px solid #0b101b; }
  .sg-table tbody td{ padding:.7rem .85rem; border-top:1px solid #eceff3; background:#fff; }
  .sg-table tbody tr:nth-child(odd) td{ background:#fafbfc; }
  .sg-table .sg-col-first{ font-weight:600; background:#f6f8fb; position:sticky; left:0; z-index:1; }
  .sg-table .sg-num{ text-align:center; font-variant-numeric: tabular-nums; }
  .sg-table .sg-text{ text-align:left; }
  .unit-toggle .btn{ min-width:64px; }
  @media (max-width:768px){ .sg-table thead th{ font-size:.9rem; } .sg-table tbody td{ font-size:.92rem; } }
</style>
@endsection
