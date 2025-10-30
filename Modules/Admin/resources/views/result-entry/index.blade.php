@extends('admin::layouts.app')
@section('title', 'List ' . ucfirst($baseRouteName ?? ''))
@section('buttons')
    <a href="{{ route($baseRouteName . '.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i> Create
    </a>
@endsection
<style>
    .badge-prize {
        font-size: 1rem!important;
        font-weight: bold;
        padding: 0.5rem 0.7rem;
        min-width: 55px;
        display: inline-block;
        text-align: center;
    }
</style>
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="dataTable" class="table table-bordered dt-responsive">
                        <thead>
                            <tr>
                                <th>SN</th>
                                <th>Game</th>
                                <th>Date And Time</th>
                                <th>Prize 1</th>
                                <th>Prize 2</th>
                                <th>Prize 3</th>
                                <th>Prize 4</th>
                                <th>Prize 5</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@include('admin::partials.data-tables-setup')
@include('admin::partials.sweet-alert-setup')
@push('js')
    <script>
        $(document).ready(function() {
            initializeDataTable({
                columns: [{
                        data: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },

                    {
                        data: 'game.time',
                        name: 'game.time',
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'prize_one',
                        name: 'prize_one',
                        render: function(data) {
                            return `<span class="badge bg-success text-white badge-prize">${data}</span>`;
                        }
                    },
                    {
                        data: 'prize_two',
                        name: 'prize_two',
                        render: function(data) {
                            return `<span class="badge bg-primary text-white badge-prize">${data}</span>`;
                        }
                    },
                    {
                        data: 'prize_three',
                        name: 'prize_three',
                        render: function(data) {
                            return `<span class="badge bg-warning text-dark badge-prize">${data}</span>`;
                        }
                    },
                    {
                        data: 'prize_four',
                        name: 'prize_four',
                        render: function(data) {
                            return `<span class="badge bg-danger text-white badge-prize">${data}</span>`;
                        }
                    },
                    {
                        data: 'prize_five',
                        name: 'prize_five',
                        render: function(data) {
                            return `<span class="badge bg-info text-white badge-prize">${data}</span>`;
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        searchable: false,
                        orderable: false
                    }
                ],
                ajaxOptions: {
                    url: "{{ $baseRouteName ? route($baseRouteName . '.index') : '#' }}",
                }
            });
        });
    </script>
@endpush
