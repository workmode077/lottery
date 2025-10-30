@extends('admin::layouts.app')
@section('title', 'Admin Settings')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="dataTable" class="table table-bordered dt-responsive">
                        <thead>
                            <tr>
                                <th>SN</th>
                                <th>Key</th>
                                <th>Value</th>
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
                        data: 'key',
                        name: 'key'
                    },
                    {
                        data: 'value',
                        name: 'value',
                        render: (data, type, row) => {
                            if (row.type == 1) {
                                return formatData(data);
                            }
                            return data;
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
                    url: "{{ route('admin-settings.index') }}",
                }
            });
        });
    </script>
@endpush
