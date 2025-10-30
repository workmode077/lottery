@extends('admin::layouts.app')
@section('title', 'Roles')
@section('buttons')
    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm">
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
                                <th>Name</th>
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
                        data: 'name',
                        name: 'name',
                        render: data => formatData(data)
                    },
                    {
                        data: 'action',
                        name: 'action',
                        searchable: false,
                        orderable: false
                    }
                ],
                ajaxOptions: {
                    url: "{{ route('admin.roles.index') }}",
                }
            });

            $("body").on("click", ".role-delete-btn", function(event) {
                event.preventDefault();

                const $button = $(this);
                const tableId = $button.closest("table").attr("id");

                Swal.fire({
                    title: "Are you sure?",
                    text: "This will delete the role!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#2ab57d",
                    cancelButtonColor: "#fd625e",
                    confirmButtonText: "Yes, delete it!",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: $button.closest("form").attr("action"),
                            type: "POST",
                            data: $button.closest("form").serialize(),
                            success: function(response) {
                                if (response.success === false) {
                                    Swal.fire({
                                        title: "Admins Exist!",
                                        text: response.message,
                                        icon: "warning",
                                        showCancelButton: true,
                                        confirmButtonColor: "#2ab57d",
                                        cancelButtonColor: "#fd625e",
                                        confirmButtonText: "Yes, delete all!",
                                    }).then((confirmDelete) => {
                                        if (confirmDelete.isConfirmed) {
                                            $.ajax({
                                                url: $button
                                                    .closest("form")
                                                    .attr("action"),
                                                type: "POST",
                                                data: {
                                                    _method: "DELETE",
                                                    cascade_delete: true,
                                                },
                                                success: function(
                                                    finalResponse) {
                                                    showToast(
                                                        finalResponse
                                                        .message,
                                                        finalResponse
                                                        .success ?
                                                        "success" :
                                                        "error"
                                                    );
                                                    if (finalResponse
                                                        .success)
                                                        $("#" +
                                                            tableId)
                                                        .DataTable()
                                                        .ajax
                                                        .reload(
                                                            null,
                                                            false);
                                                },
                                                error: function(xhr) {
                                                    showToast(
                                                        "An error occurred. Please try again.",
                                                        "error"
                                                    );
                                                    console.error(
                                                        "Unexpected error:",
                                                        xhr
                                                        .responseText ||
                                                        xhr
                                                        .statusText
                                                    );
                                                },
                                            });
                                        }
                                    });
                                } else {
                                    showToast(
                                        response.message,
                                        response.success ? "success" : "error"
                                    );
                                    if (response.success)
                                        $("#" + tableId)
                                        .DataTable()
                                        .ajax.reload(null, false);
                                }
                            },
                            error: function(xhr) {
                                showToast(
                                    "An error occurred. Please try again.",
                                    "error"
                                );
                                console.error(
                                    "Unexpected error:",
                                    xhr.responseText || xhr.statusText
                                );
                            },
                        });
                    }
                });
            });
        });
    </script>
@endpush
