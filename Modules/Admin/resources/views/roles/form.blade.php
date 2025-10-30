@extends('admin::layouts.app')
@section('title', isset($role) ? 'Edit Role' : 'Create Role')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.roles.index') }}">
            Roles
        </a>
    </li>
@endsection
@section('content')
    <!-- form start -->
    <form method="POST"
        action="{{ isset($role) ? route('admin.roles.update', base64_encode($role->id)) : route('admin.roles.store') }}">
        @csrf
        @isset($role)
            @method('PUT')
        @endisset
        <div class="row">
            <x-admin::action-buttons :cancel-url="route('admin.roles.index')" save-label="{{ isset($role) ? 'Update' : 'Create' }}" />
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Main Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Role name*</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ $role->name ?? '' }}" data-rule-minlength="3" data-rule-maxlength="255"
                                        required>
                                    <span class="error-block"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="form-label mb-3">Role Permissions</label>
                            <div class="col-12">
                                <label class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" id="select_all"
                                        @checked(isset($role) && $permissions->every(fn($permission) => $role->hasPermissionTo($permission->name)))>
                                    <span class="form-check-label" for="select_all">Select All</span>
                                </label>
                            </div>
                            <div class="row g-3">
                                @foreach ($permissions as $permission)
                                    <div class="col-md-3 col-sm-4 col-6">
                                        <div class="card p-2">
                                            <label class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input permission-checkbox" type="checkbox"
                                                    name="permissions[]" value="{{ $permission->name }}"
                                                    @checked(isset($role) && $role->hasPermissionTo($permission->name))>
                                                <span class="ms-2">
                                                    {{ Str::title(str_replace(['-', '_'], ' ', $permission->name)) }}
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
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
    <script>
        $(document).ready(function() {
            const selectAllCheckbox = document.getElementById('select_all');
            const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');

            selectAllCheckbox.addEventListener('change', () => {
                permissionCheckboxes.forEach(checkbox => checkbox.checked = selectAllCheckbox.checked);
            });

            permissionCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                    selectAllCheckbox.checked = [...permissionCheckboxes].every(checkbox => checkbox
                        .checked);
                });
            });

            $('form').customValidate({
                successRoute: "{{ route('admin.roles.index') }}"
            });
        });
    </script>
@endpush
