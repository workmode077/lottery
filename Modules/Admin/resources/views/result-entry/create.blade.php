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
            @if (!isset($data) || $data->status != 'published')
                <x-admin::action-buttons :cancel-url="route($baseRouteName . '.index')" save-label="{{ isset($data) ? 'Update' : 'Create' }}" />
            @endif

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Main Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Game Selection -->
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="game_id" class="form-label">Game*</label>
                                    <select class="form-select" name="game_id" id="game_id" required>
                                        <option value="">Select Game</option>
                                        @foreach ($games as $game)
                                            <option value="{{ $game->id }}"
                                                {{ isset($data) && $data->game_id == $game->id ? 'selected' : '' }}>
                                                {{ $game->time }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="error-block"></span>
                                </div>
                            </div>

                            <!-- Date and Time -->
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="date" class="form-label">Date*</label>
                                    <input type="date" class="form-control" id="date" name="date"
                                        @if (isset($data)) value="{{ date('Y-m-d', strtotime($data->date)) }}"
                                        @else
                                            value="{{ date('Y-m-d') }}" @endif
                                        min="{{ date('Y-m-d') }}" required>
                                    <span class="error-block"></span>
                                </div>
                            </div>



                            <!-- Status -->
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" name="status">
                                        <option value="published"
                                            {{ isset($data) && $data->status == 'published' ? 'selected' : '' }}>
                                            Published
                                        </option>
                                        <option value="unpublished"
                                            {{ isset($data) && $data->status == 'unpublished' ? 'selected' : '' }}>
                                            Unpublished
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div> <!-- row -->

                        @php
                            $prizes = ['one', 'two', 'three', 'four', 'five'];
                        @endphp

                        <div class="row mt-5">
                            @foreach ($prizes as $index => $word)
                                <div class="col-md-2">
                                    <div class="form-group mb-3">
                                        <label for="prize_{{ $word }}" class="form-label">Prize
                                            {{ ucfirst($word) }}</label>
                                        <input type="number" min="100" max="999" class="form-control"
                                            name="prize_{{ $word }}"
                                            value="{{ isset($data) ? $data->{'prize_' . $word} : '' }}" required>
                                        <span class="error-block"></span>
                                    </div>
                                </div>
                            @endforeach
                        </div>


                        <div class="row mt-5">
                            <div class="col-12">
                                <h4 class="mb-3">Compliments</h4>
                            </div>
                        </div>
                        <!-- Number Columns 1-30 -->
                        <div class="row">
                            @php
                                $numbers = [
                                    'one',
                                    'two',
                                    'three',
                                    'four',
                                    'five',
                                    'six',
                                    'seven',
                                    'eight',
                                    'nine',
                                    'ten',
                                    'eleven',
                                    'twelve',
                                    'thirteen',
                                    'fourteen',
                                    'fifteen',
                                    'sixteen',
                                    'seventeen',
                                    'eighteen',
                                    'nineteen',
                                    'twenty',
                                    'twenty_one',
                                    'twenty_two',
                                    'twenty_three',
                                    'twenty_four',
                                    'twenty_five',
                                    'twenty_six',
                                    'twenty_seven',
                                    'twenty_eight',
                                    'twenty_nine',
                                    'thirty',
                                ];
                            @endphp

                            @foreach ($numbers as $col)
                                <div class="col-md-2">
                                    <div class="form-group mb-3">
                                        <label for="{{ $col }}"
                                            class="form-label">{{ ucfirst(str_replace('_', ' ', $col)) }}</label>
                                        <input type="number" min="100" max="999" class="form-control"
                                            name="{{ $col }}" value="{{ isset($data) ? $data->$col : '' }}"
                                            required>
                                        <span class="error-block"></span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
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
