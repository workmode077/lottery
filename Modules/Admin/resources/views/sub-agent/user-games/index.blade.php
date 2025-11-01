@extends('admin::layouts.app')
@section('title', isset($data) ? 'Edit ' : 'Create Interior')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('model-years.interior.index', ['model_year' => base64_encode($modelyearid)]) }}">
            Interior
        </a>
    </li>

@endsection
@section('content')
    <!-- form start -->
    <form method="POST"
        action="{{ isset($data)
            ? route($baseRouteName . '.update', [
                'model_year' => base64_encode($modelyearid),
                'interior' => base64_encode($data->id),
            ])
            : route($baseRouteName . '.store', base64_encode($modelyearid)) }}"
        enctype="multipart/form-data">
        @csrf

        @if (isset($data))
            @method('PUT')
        @endif

        <div class="row">
            <x-admin::action-buttons :cancel-url="route('model-years.interior.index', ['model_year' => base64_encode($modelyearid)])" save-label="{{ isset($data) ? 'Update' : 'Create' }}" />
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Main Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="color_name" class="form-label">Color Name*</label>
                                    <input type="text" class="form-control" id="color_name" name="color_name"
                                        value="{{ isset($data) ? $data->color_name : '' }}" required>
                                    <span class="error-block"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="color_name_ar" class="form-label">Color Name[ar]*</label>
                                    <input type="text" class="form-control" id="color_name_ar" name="color_name_ar"
                                        value="{{ isset($data) ? $data->color_name_ar : '' }}" required>
                                    <span class="error-block"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="sort_order" class="form-label">Sort Order</label>
                                    <input type="number" class="form-control numeric-input" id="sort_order"
                                        name="sort_order" value="{{ isset($data) ? $data->sort_order : $sort_order }}" required>
                                    <span class="error-block"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" name="status" required>
                                        <option value="1" @selected(isset($data) && $data->status == 1)>Enabled</option>
                                        <option value="0" @selected(isset($data) && $data->status == 0)>Disabled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Media Upload</h3>
                    </div>
                    <div class="card-body">
                         <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label for="int_ex_hglt_banner_web" class="form-label">Color Thumbnail*</label>
                                        <input type="file" class="form-control filepond-input"
                                            name="color_thumb" id="color_thumb"
                                            data-accept="image/jpeg, image/png, image/jpg, image/webp" data-size="600KB"
                                            required>
                                        <div class="text-muted">Dimensions:49px * 49px </div>
                                        <span class="error-block"></span>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="color_thumb_alt" class="form-label">Color Thumbnail Alt*</label>
                                                <input type="text" class="form-control" id="color_thumb_alt"
                                                    name="color_thumb_alt"
                                                    value="{{ $data->color_thumb_alt ?? '' }}" required>
                                                <span class="error-block"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="color_thumb_alt_ar" class="form-label">Color Thumbnail Alt[ar]*</label>
                                                <input type="text" class="form-control"
                                                    id="color_thumb_alt_ar" name="color_thumb_alt_ar"
                                                    value="{{ $data->color_thumb_alt_ar ?? '' }}" required>
                                                <span class="error-block"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label for="images" class="form-label">Images*</label>
                                        <input type="file" class="form-control filepond-input"
                                            name="images[]" id="images[]" multiple
                                            data-accept="image/jpeg, image/png, image/jpg, image/webp" data-size="600KB"
                                            required>
                                        <div class="text-muted">Dimensions: 1920px * 957px </div>
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
    @include('admin::partials.crop-modal')
@endsection
@include('admin::partials.jquery-validate-setup')
@include('admin::partials.crop-filepond-setup')
@include('admin::partials.filepond-setup', ['mediaSource' => $data ?? null])
@push('js')
 <script>
        $(document).ready(function() {
            $('form').customValidate({
                rules: {
                    color_name: {
                        maxlength: 255
                    },
                    color_name_ar: {
                        maxlength: 255
                    },
                },
                successRoute: "{{ route('model-years.interior.index', ['model_year' => base64_encode($modelyearid)]) }}"
            });
        });
    </script>
@endpush