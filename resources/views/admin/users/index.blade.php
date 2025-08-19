@extends('admin.layouts.master')

@section('title', 'Manage Users')

@section('content')
@php
    // Make sure you check the admin guard
    $isSuperAdmin = auth()->guard('admin')->check() && auth()->guard('admin')->user()->hasRole('SuperAdmin');
@endphp

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Manage Users</h1>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="bi bi-plus-circle"></i> Add User
        </button>
    </div>

    {{-- Tabs for Active / Deleted Users --}}
    <ul class="nav nav-tabs mb-3" id="userTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="active-users-tab" data-bs-toggle="tab" href="#active-users" role="tab">
                Active Users
            </a>
        </li>
        @if($isSuperAdmin)
        <li class="nav-item">
            <a class="nav-link" id="deleted-users-tab" data-bs-toggle="tab" href="#deleted-users" role="tab">
                Deleted Users
            </a>
        </li>
        @endif
    </ul>

    <div class="tab-content" id="userTabsContent">
        {{-- Active Users Table --}}
        <div class="tab-pane fade show active" id="active-users" role="tabpanel">
            <div class="card shadow-sm">
                <div class="card-body">
                    <table id="usersTable" class="table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Deleted Users Table --}}
        @if($isSuperAdmin)
        <div class="tab-pane fade" id="deleted-users" role="tabpanel">
            <div class="card shadow-sm">
                <div class="card-body">
                    <table id="deletedUsersTable" class="table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Include Modals --}}
@include('admin.users.partials.modal-create', ['roles' => $roles])
@include('admin.users.partials.modal-edit', ['roles' => $roles])
@endsection

@push('scripts')
<script>
$(function() {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    // Active Users DataTable
    const activeTable = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.users.data') }}?trashed=0",
        columns: [
            {data:'id'}, {data:'name'}, {data:'email'},
            {data:'role'}, {data:'type'},
            {data:'actions', orderable:false, searchable:false}
        ],
        order:[[0,'desc']]
    });

    // Deleted Users DataTable (SuperAdmin only)
    @if($isSuperAdmin)
    const deletedTable = $('#deletedUsersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.users.data') }}?trashed=1",
        columns: [
            {data:'id'}, {data:'name'}, {data:'email'},
            {data:'role'}, {data:'type'},
            {data:'actions', orderable:false, searchable:false}
        ],
        order:[[0,'desc']]
    });
    @endif

    // ---- Action Handlers ---- //

    // Edit User
    $(document).on('click', '.edit-user', function() {
        const id = $(this).data('id');
        const type = $(this).data('type');

        $.get(`/admin/users/${type}/${id}/edit`, function(res) {
            const user = res.user;
            const isAdminOnly = {!! auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('SuperAdmin') ? 'true' : 'false' !!};

            // Fill hidden fields
            $('#editUserForm input[name="id"]').val(user.id);
            $('#editUserForm input[name="type"]').val(type);

            // Fill the form fields
            $('#nameEdit').val(user.name);
            $('#emailEdit').val(user.email);
            $('#passwordEdit').val('');
            $('#passwordConfirmEdit').val('');

            // Fill roles dropdown
            let roleSelect = $('#roleEdit');
            roleSelect.empty(); // remove old options
            roleSelect.append('<option value="" selected disabled>Select Role</option>');

            res.allRoles.forEach(r => {
                // Admin-only cannot see Admin or SuperAdmin
                if(isAdminOnly && (r === 'Admin' || r === 'SuperAdmin')) return;

                const selected = r === res.role ? 'selected' : '';
                roleSelect.append(`<option value="${r}" ${selected}>${r}</option>`);
            });

            $('#editUserModal').modal('show');
        }).fail(() => showAlert('danger','Failed to load user data'));
    });


    // Handle Create User Form Submit
    $('#createUserForm').on('submit', function(e){
        e.preventDefault(); // prevent page reload

        const formData = $(this).serialize();

        $.ajax({
            url: "{{ route('admin.users.store') }}", // your store route
            type: 'POST',
            data: formData,
            success: res => {
                $('#createUserModal').modal('hide'); // hide modal
                $('#createUserForm')[0].reset(); // reset form
                $('#usersTable').DataTable().ajax.reload(); // reload table
                showAlert('success', res.message);
            },
            error: err => {
                if(err.status === 422){ // validation errors
                    let errors = err.responseJSON.errors;
                    let msg = Object.values(errors).flat().join('<br>');
                    showAlert('danger', msg);
                } else {
                    showAlert('danger','Create user failed');
                }
            }
        });
    });



    // Handle Edit User Form Submit
    $('#editUserForm').on('submit', function(e){
        e.preventDefault(); // prevent page reload

        const id = $(this).find('input[name="id"]').val();
        const type = $(this).find('input[name="type"]').val();
        const formData = $(this).serialize();

        $.ajax({
            url: `/admin/users/${type}/${id}`, // your update route
            type: 'PUT',
            data: formData,
            success: res => {
                $('#editUserModal').modal('hide');
                $('#usersTable').DataTable().ajax.reload(); // reload table
                showAlert('success', res.message);
            },
            error: err => {
                if(err.status === 422) { // validation errors
                    let errors = err.responseJSON.errors;
                    let msg = Object.values(errors).flat().join('<br>');
                    showAlert('danger', msg);
                } else {
                    showAlert('danger', 'Update failed');
                }
            }
        });
    });


    // Delete User
    $(document).on('click', '.delete-user', function() {
        const id = $(this).data('id');
        const type = $(this).data('type');

        // Use SweetAlert2 confirm dialog
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if(result.isConfirmed) {
                $.ajax({
                    url: `/admin/users/${type}/${id}`,
                    type: 'DELETE',
                    data: {_token: "{{ csrf_token() }}"},
                    success: res => {
                        activeTable.ajax.reload();
                        showAlert('success', res.message); // call your method
                    },
                    error: () => showAlert('danger', 'Delete failed') // call your method
                });
            }
        });
    });


    // Permanent Delete (SuperAdmin only)
    $(document).on('click', '.permanent-delete-user', function(){
        const id = $(this).data('id');
        const type = $(this).data('type');

        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently delete the user! This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete permanently!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if(result.isConfirmed){
                $.ajax({
                    url: `/admin/users/${type}/${id}/force-delete`,
                    type: 'DELETE',
                    data: {_token: "{{ csrf_token() }}"},
                    success: res => {
                        deletedTable.ajax.reload();
                        activeTable.ajax.reload();
                        showAlert('success', res.message);
                    },
                    error: () => showAlert('danger', 'Permanent delete failed')
                });
            }
        });
    });


    // Restore User (SuperAdmin only)
    @if($isSuperAdmin)
        $('#deletedUsersTable').on('click', '.restore-user', function(){
            const id = $(this).data('id');
            const type = $(this).data('type');

            Swal.fire({
                title: 'Are you sure?',
                text: "This will restore the user!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, restore!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if(result.isConfirmed){
                    $.post(`/admin/users/${type}/${id}/restore`, {_token:"{{ csrf_token() }}"}).done(res=>{
                        deletedTable.ajax.reload();
                        activeTable.ajax.reload();
                        showAlert('success', res.message); // call your method
                    }).fail(()=> showAlert('danger','Restore failed')); // call your method
                }
            });
        });
    @endif
});
</script>
@endpush
