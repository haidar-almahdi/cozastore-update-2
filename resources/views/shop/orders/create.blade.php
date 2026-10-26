@extends('layouts.app')

@section('content')
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" type="image/png" href="{{ asset('images/icons/favicon.png') }}"/>
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('fonts/font-awesome-4.7.0/css/font-awesome.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('fonts/iconic/css/material-design-iconic-font.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('fonts/linearicons-v1.0.0/icon-font.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/animate/animate.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/css-hamburgers/hamburgers.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/animsition/css/animsition.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/util.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/main.css') }}">
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Poppins", sans-serif;
        text-decoration: none;
        list-style: none;
    }

    body {
        background: linear-gradient(90deg, #e2e2e2, #c9d6ff);
        height: 1200px;
    }

    .checkout-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        padding: 40px 0;
    }

    .card {
        position: relative;
        width: 100%;
        background: #fff;
        border-radius: 30px;
        box-shadow: 0 0 30px rgba(0, 0, 0, .2);
        overflow: hidden;
        border: none;
    }

    .card-header {
        background: #fff;
        padding: 30px;
        border-bottom: 1px solid #eee;
    }

    .card-header h4 {
        font-size: 28px;
        color: #333;
        font-weight: 600;
        margin: 0;
    }

    .card-body {
        padding: 30px;
    }

    .form-label {
        color: #333;
        font-weight: 500;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .form-control {
        width: 100%;
        padding: 13px 20px;
        background: #eee;
        border-radius: 8px;
        border: none;
        outline: none;
        font-size: 16px;
        color: #333;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        background: #fff;
        box-shadow: 0 0 10px rgba(116, 148, 236, 0.2);
    }

    .form-control.is-invalid {
        border: 1px solid #dc3545;
    }

    .invalid-feedback {
        font-size: 12px;
        color: #dc3545;
        margin-top: 5px;
    }

    .btn-primary {
        width: 100%;
        height: 48px;
        background: #7494ec;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, .1);
        border: none;
        cursor: pointer;
        font-size: 16px;
        color: #fff;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: #5a7de8;
        transform: translateY(-2px);
    }

    .order-summary {
        background: #fff;
        border-radius: 30px;
        box-shadow: 0 0 30px rgba(0, 0, 0, .2);
        padding: 30px;
        height: 100%;
    }

    .order-summary h4 {
        font-size: 24px;
        color: #333;
        font-weight: 600;
        margin-bottom: 25px;
    }

    .order-item {
        padding: 15px 0;
        border-bottom: 1px solid #eee;
    }

    .order-item:last-child {
        border-bottom: none;
    }

    .order-item h6 {
        color: #333;
        font-weight: 600;
        margin-bottom: 5px;
        font-size: 16px;
    }

    .order-item small {
        color: #666;
        font-size: 14px;
    }

    .order-total {
        margin-top: 25px;
        padding-top: 20px;
        border-top: 2px solid #eee;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        color: #555;
        font-size: 15px;
    }

    .final-total {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin-top: 15px;
    }

    textarea.form-control {
        resize: none;
        min-height: 100px;
    }

    @media screen and (max-width: 768px) {
        .checkout-container {
            padding: 20px;
        }

        .card {
            margin-bottom: 20px;
        }

        .card-header {
            padding: 20px;
        }

        .card-body {
            padding: 20px;
        }

        .order-summary {
            margin-top: 20px;
        }
    }
</style>
<div class="checkout-container">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Shipping Information</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('shop.orders.store') }}" method="POST" id="orderForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="shipping_name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control @error('shipping_name') is-invalid @enderror"
                                        id="shipping_name" name="shipping_name" value="{{ old('shipping_name', auth()->user()->name) }}" required>
                                    @error('shipping_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="shipping_phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control @error('shipping_phone') is-invalid @enderror"
                                        id="shipping_phone" name="shipping_phone" value="{{ old('shipping_phone', auth()->user()->phone) }}" required>
                                    @error('shipping_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="shipping_address" class="form-label">Shipping Address</label>
                                <textarea class="form-control @error('shipping_address') is-invalid @enderror"
                                    id="shipping_address" name="shipping_address" rows="3" required>{{ old('shipping_address', auth()->user()->address) }}</textarea>
                                @error('shipping_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">Order Notes (Optional)</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror"
                                    id="notes" name="notes" rows="2" placeholder="Add any special instructions or notes for your order...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary" id="placeOrderBtn">Place Order</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="order-summary">
                    <h4>Order Summary</h4>
                    @php
                        $finalTotal = $cart->items->sum(function($item) { return $item->price * $item->quantity; });
                        $shipping = $finalTotal * 0.02;
                        $totalWithShipping = $finalTotal + $shipping;
                    @endphp
                    @foreach($cart->items as $item)
                    <div class="order-item">
                        <h6>{{ $item->product->name }}</h6>
                        <small>
                            Quantity: {{ $item->quantity }}
                            @if($item->size)
                                | Size: {{ $item->size }}
                            @endif
                            @if($item->color)
                                | Color: {{ $item->color }}
                            @endif
                        </small>
                        <div class="text-end">
                            <span>${{ number_format($item->price * $item->quantity, 2) }}</span>
                        </div>
                    </div>
                    @endforeach
                    <div class="order-total">
                        <div class="total-row">
                            <span>Subtotal:</span>
                            <span>${{ number_format($finalTotal, 2) }}</span>
                        </div>
                        <div class="total-row">
                            <span>Shipping (2%):</span>
                            <span>${{ number_format($shipping, 2) }}</span>
                        </div>
                        <hr>
                        <div class="total-row final-total">
                            <span>Total:</span>
                            <span>${{ number_format($totalWithShipping, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
