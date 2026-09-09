@extends('layout.layout')

@php
    $title = 'Change Password';
    $subTitle = 'Account Security';
@endphp

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-6 col-lg-8">
            <div class="card border shadow-none">
                <div class="card-body p-24">
                    <h5 class="mb-8">Change Password</h5>
                    <p class="text-secondary-light mb-24">Update the password for your account.</p>

                    @if (session('status'))
                        <div class="alert alert-success mb-20">{{ session('status') }}</div>
                    @endif

                    <form action="{{ route('users.change-password.update') }}" method="POST">
                        @csrf
                        <div class="mb-20">
                            <label class="form-label">Current password</label>
                            <input type="password" name="current_password" class="form-control" required>
                            @error('current_password')
                                <div class="text-danger-main text-sm mt-8">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-20">
                            <label class="form-label">New password</label>
                            <input type="password" name="password" class="form-control" required minlength="8">
                            @error('password')
                                <div class="text-danger-main text-sm mt-8">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-24">
                            <label class="form-label">Confirm new password</label>
                            <input type="password" name="password_confirmation" class="form-control" required
                                minlength="8">
                        </div>
                        <button type="submit" class="btn btn-primary">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
