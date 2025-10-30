@extends('admin::layouts.app')
@section('title',  'List '.ucfirst($baseRouteName ?? ''))
@section('buttons')
    <a href="{{ route($baseRouteName . '.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i> Create
    </a>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="dataTable" class="table table-bordered dt-responsive">
                        <thead>
                            <tr>
                                <th>SN</th>
                                <th>Super Agent</th>
                                <th>User Name</th>
                                 <th>Password</th>
                                <th>Status</th>
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
                        data: 'super_agent.username',
                        name: 'super_agent.username',
                    },
                    {
                        data: 'username',
                        name: 'username',
                    },
                      {
                        data: 'plain_password',
                        name: 'plain_password',
                    },
                     {
                        data: 'status',
                        name: 'status'
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
