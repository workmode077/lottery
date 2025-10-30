@extends('admin::layouts.app')
@section('title', 'Change Admin Password')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.index') }}">
            Admin
        </a>
    </li>
@endsection
@section('content')
    <!-- form start -->
    <form method="POST" action="{{ route('admin.change-password.update', base64_encode($admin->id)) }}">
        @csrf
        @method('PUT')
        <div class="row">
            <x-admin::action-buttons :cancel-url="route('admin.index')" save-label="Update" />
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Main Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-grow-1">
                                            <label class="form-label">Current Password*</label>
                                        </div>
                                    </div>
                                    <div class="input-group auth-pass-inputgroup">
                                        <input type="password" name="current_password" class="form-control" required>
                                        <button class="btn btn-light shadow-none ms-0 password-toggle" type="button">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </button>
                                        <span class="error-block"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-grow-1">
                                            <label class="form-label">Password*</label>
                                        </div>
                                    </div>
                                    <div class="input-group auth-pass-inputgroup">
                                        <input type="password" name="password" class="form-control"
                                            data-rule-strongPassword="true" required>
                                        <button class="btn btn-light shadow-none ms-0 password-toggle" type="button">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </button>
                                        <span class="error-block"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-grow-1">
                                            <label class="form-label">Confirm Password*</label>
                                        </div>
                                    </div>
                                    <div class="input-group auth-pass-inputgroup">
                                        <input type="password" name="password_confirmation" class="form-control"
                                            data-rule-equalTo="[name='password']" required>
                                        <button class="btn btn-light shadow-none ms-0 password-toggle" type="button">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </button>
                                        <span class="error-block"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@include('admin::partials.jquery-validate-setup')
@push('js')
    <script src="{{ asset('backend/js/pages/pass-addon.init.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('form').customValidate({
                messages: {
                    password_confirmation: {
                        equalTo: "Passwords do not match."
                    },
                },
                successRoute: "{{ route('admin.index') }}"
            });
        });
    </script>
@endpush
