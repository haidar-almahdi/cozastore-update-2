@extends('layouts.app')

@section('title', 'Edit Profile - CozaStore')
    <link rel="stylesheet" type="text/css" href="{{ asset('fonts/iconic/css/material-design-iconic-font.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('fonts/linearicons-v1.0.0/icon-font.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/animate/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/css-hamburgers/hamburgers.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/animsition/css/animsition.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/util.css') }}">
@section('content')


<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Poppins", sans-serif;
    }

    body {
        background: linear-gradient(90deg, #e2e2e2, #c9d6ff);
    }

    .container {
        position: relative;
        padding: 20px;
    }

    .card {
        border: none;
        box-shadow: none;
        border-radius: 30px !important;
    }

    .card-header {
        background: #7494ec !important;
        border-radius: 15px 15px 0 0 !important;
        padding: 20px;
    }

    .card-header h4 {
        font-size: 24px;
        font-weight: 600;
        margin: 0;
    }

    .card-body {
        padding: 30px;
        overflow: auto;
        height: 450px;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #333;
        font-weight: 500;
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
    }

    .form-control:focus {
        box-shadow: none;
        border-color: #7494ec;
        border-width: 3px;
        border-style: solid;
    }

    .form-control::placeholder {
        color: #888;
        font-weight: 400;
    }

    textarea.form-control {
        resize: none;
        height: 120px;
    }

    .btn {
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

    .btn:hover {
        background: #5a7de0;
        transform: translateY(-2px);
    }

    .btn-outline-secondary {
        background: transparent;
        border: 2px solid #7494ec;
        color: #7494ec;
        display: flex;
        align-content: center;
        justify-content: center;
        align-items: center;
    }

    .btn-outline-secondary:hover {
        background: #7494ec;
        color: #fff;
        display: flex;
        align-content: center;
        justify-content: center;
        align-items: center;
        border-color: #7494ec;
    }

    .alert {
        border-radius: 8px;
        padding: 15px 20px;
        margin-bottom: 25px;
    }

    .alert-success {
        background: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }

    .invalid-feedback {
        color: #dc3545;
        font-size: 14px;
        margin-top: 5px;
    }

    .form-control.is-invalid {
        border: 1px solid #dc3545;
    }

    .btn-container {
        padding-top: 10px;
        width: 100%;
        display: flex !important;
        justify-content: flex-start !important;
        gap: 15px;
    }

    hr {
        margin: 30px 0;
        border-color: #eee;
    }

    h5 {
        color: #333;
        font-weight: 600;
        margin-bottom: 20px;
    }

    h4 {
        color: #fff !important;
    }
</style>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Edit Profile</h4>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('shop.profile.update') }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                           id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                    @error('phone')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea class="form-control @error('address') is-invalid @enderror"
                              id="address" name="address" rows="3">{{ old('address', $user->address) }}</textarea>
                    @error('address')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <hr>

                <h5>Change Password</h5>

                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                           id="current_password" name="current_password">
                    @error('current_password')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                           id="new_password" name="new_password">
                    @error('new_password')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="new_password_confirmation">Confirm New Password</label>
                    <input type="password" class="form-control"
                           id="new_password_confirmation" name="new_password_confirmation">
                </div>

            </form>
        </div>
    </div>
    <div class="btn-container d-flex justify-content-between align-items-center">
        <button type="submit" class="btn">
            Update Profile
        </button>
        <a href="{{ route('shop.home') }}" class="btn btn-outline-secondary">
            Cancel
        </a>
    </div>
</div>
@endsection
