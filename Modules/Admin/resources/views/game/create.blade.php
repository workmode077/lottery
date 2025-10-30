@extends('admin::layouts.app')
@section('title', isset($data) ? 'Edit ' . ucfirst($baseRouteName ?? '') : 'Create ' . ucfirst($baseRouteName ?? ''))
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route($baseRouteName . '.index') }}">
            {{ str_replace('-', ' ', ucfirst($baseRouteName ?? '')) }}
        </a>
    </li>
@endsection
@section('content')
    <!-- form start -->
    <form method="POST"
        action="{{ isset($data) ? route($baseRouteName . '.update', base64_encode($data->id)) : route($baseRouteName . '.store') }}"
        enctype="multipart/form-data">

        @isset($data)
            @method('PUT')
        @endisset
        @csrf

        <div class="row">
            <x-admin::action-buttons :cancel-url="route($baseRouteName . '.index')" save-label="{{ isset($data) ? 'Update' : 'Create' }}" />

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Main Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Time Field -->
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="time" class="form-label">Time*</label>
                                    <input type="time" class="form-control" id="time" name="time"
                                        value="{{ isset($data) ? \Carbon\Carbon::parse($data->time)->format('H:i') : '' }}"
                                        required>
                                    <span class="error-block"></span>
                                </div>
                            </div>

                            <!-- Status Field -->
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" name="status">
                                        <option value="1"
                                            {{ isset($data) ? ($data->status == 1 ? 'selected' : '') : '' }}>
                                            Enabled
                                        </option>
                                        <option value="0"
                                            {{ isset($data) ? ($data->status == 0 ? 'selected' : '') : '' }}>
                                            Disabled
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div> <!-- row -->
                    </div> <!-- card-body -->
                </div> <!-- card -->
            </div> <!-- col-md-8 -->
        </div> <!-- row -->
    </form>

    @include('admin::partials.crop-modal')
@endsection
@include('admin::partials.slug-setup')
@include('admin::partials.tinymce-setup')
@include('admin::partials.datepicker-setup')
@include('admin::partials.choices-setup')
@include('admin::partials.crop-filepond-setup')
@include('admin::partials.jquery-validate-setup')

@push('js')
    <script>
        $(document).ready(function() {
            $('form').customValidate({
                successRoute: "{{ isset($baseRouteName) ? route($baseRouteName . '.index') : 'admin.dashboard' }}"
            });
        });
    </script>
@endpush
