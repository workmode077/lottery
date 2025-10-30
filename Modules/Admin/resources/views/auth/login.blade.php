@extends('admin::auth.layouts.app')
@section('title', 'Login')
@section('content')
    <div class="text-center">
        <h5 class="mb-0">Welcome Back !</h5>
        <p class="text-muted mt-2">Sign in to continue to @settings('website-name').</p>
    </div>
    <form method="POST" action="{{ route('admin.login') }}" class="mt-4 pt-2">
        @csrf
        <div class="form-group mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" id="email" placeholder="Enter email"
                value="{{ $email }}" data-rule-emailOnly="true" required>
            <span class="error-block"></span>
        </div>
        <div class="form-group mb-3">
            <div class="d-flex align-items-start">
                <div class="flex-grow-1">
                    <label class="form-label">Password</label>
                </div>
            </div>
            <div class="input-group auth-pass-inputgroup">
                <input type="password" name="password" class="form-control" placeholder="Enter password"
                    value="{{ $password }}" required>
                <button class="btn btn-light shadow-none ms-0 password-toggle" type="button">
                    <i class="mdi mdi-eye-outline"></i>
                </button>
                <span class="error-block"></span>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember-check"
                        @checked($remember)>
                    <label class="form-check-label" for="remember-check">
                        Remember me
                    </label>
                </div>
            </div>
        </div>
        <div class="mb-3">
            <button type="submit" class="btn btn-primary w-100 waves-effect waves-light">
                <i class="bx bx-log-in"></i>
                <span>Login</span>
            </button>
        </div>
    </form>
@endsection
@include('admin::partials.jquery-validate-setup')
@push('js')
    <!-- Initialization script for password addon functionality -->
    <script src="{{ asset('backend/js/pages/pass-addon.init.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('form').customValidate({
                successRoute: "{{ url(app('backend.prefix')) }}"
            });
        });
    </script>
@endpush
