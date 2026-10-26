@extends('layouts.admin')

@section('title', 'Discount Settings')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa fa-percent text-warning"></i> Black Friday Settings</h1>
    <p class="page-subtitle">Control percentage and campaign dates from dashboard</p>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Campaign Configuration</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.settings.discounts.update') }}">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label>Discount Percentage (%)</label>
                        <input type="number" name="discount_percentage" class="form-control @error('discount_percentage') is-invalid @enderror"
                               min="1" max="90" value="{{ old('discount_percentage', $settings['discount_percentage']) }}" required>
                        @error('discount_percentage')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label>Month (1-12)</label>
                        <input type="number" name="month" class="form-control @error('month') is-invalid @enderror"
                               min="1" max="12" value="{{ old('month', $settings['month']) }}" required>
                        @error('month')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label>Start Day</label>
                        <input type="number" name="start_day" class="form-control @error('start_day') is-invalid @enderror"
                               min="1" max="31" value="{{ old('start_day', $settings['start_day']) }}" required>
                        @error('start_day')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label>End Day</label>
                        <input type="number" name="end_day" class="form-control @error('end_day') is-invalid @enderror"
                               min="1" max="31" value="{{ old('end_day', $settings['end_day']) }}" required>
                        @error('end_day')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        Changes here are applied automatically only during configured Black Friday dates.
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i> Save Settings
            </button>
        </form>
    </div>
</div>
@endsection
