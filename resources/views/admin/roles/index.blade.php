@extends('admin.layouts.master')

@section('title', 'Manage Roles')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Manage Roles</h1>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createRoleModal">
            <i class="bi bi-plus-circle"></i> Add Role
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table id="rolesTable" class="table table-striped table-bordered w-100">
                <thead>
                    <tr>
                        <th style="width:60px">#</th>
                        <th>Name</th>
                        <th style="width:150px">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

@include('admin.roles.partials.modal-create')
@include('admin.roles.partials.modal-edit')
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css"/>
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet"/>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/parsleyjs@2.9.2/dist/parsley.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function() {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    const table = $('#rolesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.roles.data') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { 
                data: 'actions', 
                name: 'actions', 
                orderable: false, 
                searchable: false,
                render: function(data) { return data; }
            },
        ],
        order: [[0, 'desc']],
    });

    // Reset create modal on show
    $('#createRoleModal').on('show.bs.modal', function () {
        const form = $('#createRoleForm');
        form[0].reset();
        form.parsley().reset();
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();
    });

    // Reset edit modal on show
    $('#editRoleModal').on('show.bs.modal', function () {
        const form = $('#editRoleForm');
        form.parsley().reset();
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();
    });

    // Create role
    $('#createRoleForm').on('submit', function(e){
        e.preventDefault();
        const form = $(this);
        if (!form.parsley().validate()) return;

        $.post("{{ route('admin.roles.store') }}", form.serialize())
            .done(function(res){
                $('#createRoleModal').modal('hide');
                table.ajax.reload(null, false);
                showAlert('success', res.message);
            })
            .fail(function(xhr){
                if(xhr.status === 422){
                    handleErrors(xhr, '#createRoleForm');
                } else {
                    showAlert('danger', 'Server error');
                }
            });
    });

    // Edit button click
    $('#rolesTable').on('click', '.edit-role', function(){
        const id = $(this).data('id');
        $.get("{{ url('admin/roles') }}/" + id + "/edit")
            .done(function(res){
                $('#editRoleForm input[name=id]').val(res.role.id);
                $('#editRoleForm input[name=name]').val(res.role.name);
                $('#editRoleModal').modal('show');
            });
    });

    // Update role
    $('#editRoleForm').on('submit', function(e){
        e.preventDefault();
        const form = $(this);
        if (!form.parsley().validate()) return;

        const id = form.find('input[name=id]').val();
        $.ajax({
            url: "{{ url('admin/roles') }}/" + id,
            method: 'PUT',
            data: form.serialize()
        }).done(function(res){
            $('#editRoleModal').modal('hide');
            table.ajax.reload(null, false);
            showAlert('success', res.message);
        }).fail(function(xhr){
            if(xhr.status === 422){
                handleErrors(xhr, '#editRoleForm');
            } else {
                showAlert('danger', 'Server error');
            }
        });
    });

    // Delete role
    $('#rolesTable').on('click', '.delete-role', function(){
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "Role will be deleted permanently!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if(result.isConfirmed){
                $.ajax({
                    url: "{{ url('admin/roles') }}/" + id,
                    method: 'DELETE'
                }).done(function(res){
                    table.ajax.reload(null, false);
                    showAlert('success', res.message);
                }).fail(function(){
                    showAlert('danger', 'Delete failed');
                });
            }
        });
    });

    function handleErrors(xhr, formSelector) {
        if (xhr.status === 422) {
            const errors = xhr.responseJSON.errors;
            const form = $(formSelector);
            form.find('.is-invalid').removeClass('is-invalid');
            form.find('.invalid-feedback').remove();

            $.each(errors, function(name, messages){
                let field = form.find('[name="'+name+'"]');
                if(field.length){
                    field.addClass('is-invalid');
                    field.after('<div class="invalid-feedback d-block">'+messages[0]+'</div>');
                }
            });
        }
    }
});
</script>
@endpush
