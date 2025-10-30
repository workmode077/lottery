@extends('admin::layouts.app')
@section('title', isset($admin) ? 'Edit Admin' : 'Create Admin')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.index') }}">
            Admin
        </a>
    </li>
@endsection
@section('content')
    <!-- form start -->
    <form method="POST"
        action="{{ isset($admin) ? route('admin.update', base64_encode($admin->id)) : route('admin.store') }}">
        @csrf
        @isset($admin)
            @method('PUT')
        @endisset
        <div class="row">
            <x-admin::action-buttons :cancel-url="route('admin.index')" save-label="{{ isset($admin) ? 'Update' : 'Create' }}" />
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Main Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Name*</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ $admin->name ?? '' }}" data-rule-minlength="3" data-rule-maxlength="25"
                                        required>
                                    <span class="error-block"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">Email*</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ $admin->email ?? '' }}" data-rule-emailOnly="true" required>
                                    <span class="error-block"></span>
                                </div>
                            </div>
                            @if (isset($admin))
                                @if ($admin->id !== $superAdminId)
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="role_id" class="form-label">Role*</label>
                                            <select id="role_id" name="role_id[]" class="form-control" multiple required>
                                                <option></option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}" @selected($admin->roles->pluck('id')->contains($role->id))>
                                                        {{ $role->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="error-block"></span>
                                        </div>
                                    </div>
                                    @if ($admin->id !== auth('admin')->id())
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="form-label">Status</label>
                                                <select class="form-select" name="status">
                                                    <option value="1" @selected($admin->status == 1)>Enabled</option>
                                                    <option value="0" @selected($admin->status == 0)>Disabled</option>
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            @else
                                <div class="col-md-6">
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
                                <div class="col-md-6">
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
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="role_id" class="form-label">Role*</label>
                                        <select id="role_id" name="role_id[]" class="form-control" multiple required>
                                            <option></option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}">
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="error-block"></span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@include('admin::partials.choices-setup')
@include('admin::partials.jquery-validate-setup')
@push('js')
    <script src="{{ asset('backend/js/pages/pass-addon.init.js') }}"></script>
    <script>
        $(document).ready(function() {
            initializeChoices("#role_id", {
                placeholderValue: 'Select Roles'
            });
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
