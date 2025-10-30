@extends('admin::layouts.app')
@section('title', 'Edit Admin Settings: ' . $adminSettings->key_value)
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin-settings.index') }}">
            Admin Settings
        </a>
    </li>
@endsection
@section('content')
    <!-- form start -->
    <form method="POST" action="{{ route('admin-settings.update', base64_encode($adminSettings->id)) }}"
        id="admin-settings-form" @if ($adminSettings->type == 2) enctype="multipart/form-data" @endif>
        @csrf
        @method('PUT')
        <input type="hidden" name="type" value="{{ $adminSettings->type }}">
        <input type="hidden" name="key" value="{{ $adminSettings->key }}">
        <div class="row">
            <x-admin::action-buttons :cancel-url="route('admin-settings.index')" save-label="Update" />
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="value" class="form-label">Value*</label>
                                    @if ($adminSettings->type == 1)
                                        <input type="text" class="form-control" name="value" id="value"
                                            value="{{ $adminSettings->value }}" required>
                                    @elseif ($adminSettings->type == 2)
                                        @if ($adminSettings->key == 'website-auth-background')
                                            <input type="file" class="form-control filepond-input" name="value"
                                                id="value" data-width="2400" data-height="1600"
                                                data-accept="image/jpeg, image/jpg, image/webp" required>
                                        @elseif ($adminSettings->key == 'website-logo')
                                            <input type="file" class="form-control filepond-input" name="value"
                                                id="value" data-accept="image/svg+xml" required>
                                        @elseif ($adminSettings->key == 'website-favicon')
                                            <input type="file" class="form-control filepond-input" name="value"
                                                id="value" data-accept="image/vnd.microsoft.icon, image/x-icon"
                                                required>
                                        @elseif ($adminSettings->key == 'website-dashboard-logo')
                                            <input type="file" class="form-control filepond-input" name="value"
                                                id="value" data-accept="image/svg+xml" required>
                                        @endif
                                    @endif
                                    @if ($adminSettings->key == 'website-auth-background')
                                        <div class="text-muted">
                                            Allowed formats: JPEG and WEBP.
                                        </div>
                                    @elseif ($adminSettings->key == 'website-logo')
                                        <div class="text-muted">
                                            Allowed format: SVG. Aspect ratio: 1:1.
                                        </div>
                                    @elseif ($adminSettings->key == 'website-favicon')
                                        <div class="text-muted">
                                            Allowed formats: ICO (preferred).
                                        </div>
                                    @elseif ($adminSettings->key == 'website-dashboard-logo')
                                        <div class="text-muted">
                                            Allowed format: SVG.
                                        </div>
                                    @endif
                                    <span class="error-block"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @if ($adminSettings->type == 2)
        @include('admin::partials.crop-modal')
    @endif
@endsection
@if ($adminSettings->type == 2)
    @include('admin::partials.crop-filepond-setup')
    @include('admin::partials.filepond-setup', ['mediaSource' => $adminSettings ?? null])
@endif
@include('admin::partials.jquery-validate-setup')
@push('js')
    <script>
        $(document).ready(function() {
            $("#admin-settings-form").validate({
                @if ($adminSettings->type == 1)
                    rules: {
                        value: {
                            @if ($adminSettings->key == 'backend-prefix')
                                maxlength: 255,
                                regex: /^[a-z0-9\-]+$/,
                            @else
                                maxlength: 15,
                            @endif
                        }
                    },
                @endif
                messages: {
                    value: {
                        required: "The value field is required.",
                        @if ($adminSettings->type == 1)
                            @if ($adminSettings->key == 'backend-prefix')
                                maxlength: "The value field must not be greater than 255 characters.",
                                regex: "The backend prefix may only contain lowercase letters, numbers, and dashes.",
                            @else
                                maxlength: "The value field must not be greater than 15 characters.",
                            @endif
                        @endif
                    }
                },
                errorPlacement: function(error, element) {
                    $(element).closest('.form-group')
                        .find('.error-block').text(error.text());
                },
                success: function(label, element) {
                    $(element).closest('.form-group')
                        .find('.error-block').text("");
                },
                onchange: function(element) {
                    $(element).valid();
                },
                onfocusout: function(element) {
                    if (
                        element.tagName === "TEXTAREA" ||
                        (element.tagName === "INPUT" &&
                            element.type !== "password")
                    ) {
                        element.value = $.trim(element.value);
                    }
                    if (
                        !this.checkable(element) &&
                        (element.name in this.submitted ||
                            !this.optional(element))
                    ) {
                        this.element(element);
                    }
                },
                submitHandler: function(form) {
                    $(form).find('.error-block').html('');

                    var $submitBtn = $(form).find(":submit:visible");
                    var originalText = $submitBtn.find('span').text();
                    $submitBtn
                        .prop("disabled", true)
                        .html(getLoadingButtonText(originalText.toLowerCase()));

                    // Delay form submission slightly to allow UI update
                    setTimeout(function() {
                            $.ajax({
                                type: "POST",
                                url: $(form).attr("action"),
                                data: new FormData(form),
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    if (response.success) {
                                        if (response.redirect) {
                                            showToast(
                                                response.message,
                                                "success"
                                            );
                                            setTimeout(
                                                () => (window.location.href =
                                                    response.redirect),
                                                1000
                                            );
                                        } else {
                                            showToast(
                                                response.message,
                                                "success"
                                            );
                                            var redirectUrl =
                                                "{{ route('admin-settings.index') }}" ||
                                                homeUrl;
                                            setTimeout(
                                                () => (window.location.href =
                                                    redirectUrl),
                                                1000
                                            );
                                        }
                                    } else {
                                        showToast(
                                            response.message,
                                            "error"
                                        );
                                        resetSubmitButton($submitBtn, originalText);
                                    }
                                },
                                error: function(xhr) {
                                    resetSubmitButton($submitBtn, originalText);
                                    if (xhr.status === 422)
                                        handleValidationErrors(xhr.responseJSON
                                            .errors);
                                    else {
                                        showToast(
                                            "Something went wrong",
                                            "error"
                                        );
                                        console.error(
                                            "Unexpected error:",
                                            xhr.responseText || xhr.statusText
                                        );
                                    }
                                },
                            });
                        },
                        100
                    ); // Small delay (100ms) before proceeding with the AJAX request
                },
            });

            function getLoadingButtonText(text) {
                const texts = {
                    update: "Updating...",
                    save: "Saving..."
                };
                return `<i class="bx bx-loader bx-spin"></i><span>${texts[text] || "Processing..."}</span>`;
            }

            function resetSubmitButton($button, originalText) {
                $button.prop("disabled", false).html(`<i class="bx bx-save"></i><span>${originalText}</span>`);
            }

            function handleValidationErrors(errors) {
                $.each(errors, function(field, messages) {
                    $('[name="' + field + '"]')
                        .closest(".form-group")
                        .find(".error-block")
                        .text(messages.join(", "));
                });
            }
        });
    </script>
@endpush
