@extends('admin::layouts.app')
@section('title', 'Prize Entry')
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
        action="{{ route($baseRouteName . '.update', base64_encode($price->id)) }}"
        enctype="multipart/form-data">

        @method('PUT')
        @csrf

        <div class="row">
            <x-admin::action-buttons :cancel-url="route($baseRouteName . '.index')" save-label="Update" />

            <div class="col-md-12">

                {{-- LSK Super Commission --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title">LSK Super Commission</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ([
                                'lsk_super_first_price'   => '1st Prize',
                                'lsk_super_second_price'  => '2nd Prize',
                                'lsk_super_third_price'   => '3rd Prize',
                                'lsk_super_fourth_price'  => '4th Prize',
                                'lsk_super_fifth_price'   => '5th Prize',
                                // 'lsk_super_sixth_price'   => '6th Prize',
                                // 'lsk_super_seventh_price' => '7th Prize',
                            ] as $field => $label)
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ $label }}</label>
                                        <input type="number" min="0" class="form-control"
                                            name="{{ $field }}"
                                            value="{{ $price->$field }}" required>
                                        <span class="error-block"></span>
                                    </div>
                                </div>
                            @endforeach

                            {{-- <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label class="form-label">30% Enabled</label>
                                    <select class="form-select" name="lsk_super_30_enabled">
                                        <option value="1" {{ $price->lsk_super_30_enabled ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ !$price->lsk_super_30_enabled ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label class="form-label">50% Enabled</label>
                                    <select class="form-select" name="lsk_super_50_enabled">
                                        <option value="1" {{ $price->lsk_super_50_enabled ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ !$price->lsk_super_50_enabled ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>

                {{-- Box: Three Different Numbers --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title">Box: Three Different Numbers</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ([
                                'box_three_diff_first_price'  => '1st Prize',
                                'box_three_diff_second_price' => '2nd Prize',
                            ] as $field => $label)
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ $label }}</label>
                                        <input type="number" min="0" class="form-control"
                                            name="{{ $field }}"
                                            value="{{ $price->$field }}" required>
                                        <span class="error-block"></span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Box: Two Same Numbers --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title">Box: Two Same Numbers</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ([
                                'box_two_same_first_price'  => '1st Prize',
                                'box_two_same_second_price' => '2nd Prize',
                            ] as $field => $label)
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ $label }}</label>
                                        <input type="number" min="0" class="form-control"
                                            name="{{ $field }}"
                                            value="{{ $price->$field }}" required>
                                        <span class="error-block"></span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Box: Three Same Numbers --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title">Box: Three Same Numbers</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label class="form-label">1st Prize</label>
                                    <input type="number" min="0" class="form-control"
                                        name="box_three_same_first_price"
                                        value="{{ $price->box_three_same_first_price }}" required>
                                    <span class="error-block"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ABC Commission --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title">ABC Commission</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ([
                                'abc_first_price'  => '1st Prize',
                                'abc_second_price' => '2nd Prize',
                            ] as $field => $label)
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ $label }}</label>
                                        <input type="number" min="0" class="form-control"
                                            name="{{ $field }}"
                                            value="{{ $price->$field }}" required>
                                        <span class="error-block"></span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- AB/AC/BC Commission --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title">AB / AC / BC Commission</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ([
                                'ab_ac_bc_first_price'  => '1st Prize',
                                'ab_ac_bc_second_price' => '2nd Prize',
                            ] as $field => $label)
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{ $label }}</label>
                                        <input type="number" min="0" class="form-control"
                                            name="{{ $field }}"
                                            value="{{ $price->$field }}" required>
                                        <span class="error-block"></span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div> <!-- col-md-12 -->
        </div> <!-- row -->
    </form>

    @include('admin::partials.crop-modal')
@endsection
@include('admin::partials.jquery-validate-setup')

@push('js')
    <script>
        $(document).ready(function() {
            $('form').customValidate({
                successRoute: "{{ route($baseRouteName . '.index') }}"
            });
        });
    </script>
@endpush
