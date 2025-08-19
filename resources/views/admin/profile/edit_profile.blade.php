@extends('admin.layouts.master')

@section('content')
<div class="container mt-4">

    <h3>Edit Profile</h3>
    <form id="updateProfileForm">
        @csrf
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ $user->name }}">
        </div>
        <button type="submit" class="btn btn-success">Save</button>
    </form>

    <hr>

    {{-- Phone change step 1 --}}
    <h4>Change Phone Number</h4>
    <form id="requestPhoneChangeForm">
        @csrf
        <div class="mb-3">
            <label>New Phone Number</label>
            <input type="text" name="phone" class="form-control" placeholder="+962790000000" required>
        </div>
        <button type="submit" class="btn btn-primary">Send Verification Code</button>
    </form>

    {{-- Placeholder for dynamic phone verification form --}}
    <div id="verifyPhoneSection"></div>

    <hr>

    <h4>Change Password</h4>
    <form id="updatePasswordForm">
        @csrf
        <div class="mb-3">
            <label>Current Password</label>
            <input type="password" name="current_password" class="form-control">
        </div>
        <div class="mb-3">
            <label>New Password</label>
            <input type="password" name="new_password" class="form-control">
        </div>
        <div class="mb-3">
            <label>Confirm New Password</label>
            <input type="password" name="new_password_confirmation" class="form-control">
        </div>
        <button type="submit" class="btn btn-warning">Update Password</button>
    </form>

    <hr>

    <h4>Change Email</h4>
    <form id="changeEmailForm">
        @csrf
        <div class="mb-3">
            <label>New Email</label>
            <input type="email" name="new_email" class="form-control">
        </div>
        <button type="submit" class="btn btn-info">Change Email</button>
    </form>
</div>
@endsection

@push('scripts')
<script>
function showAlert(type, message) {
    let icon = 'info';
    if(type==='success') icon='success';
    else if(type==='danger') icon='error';
    else if(type==='warning') icon='warning';

    Swal.fire({
        icon: icon,
        title: message,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
}

// --- Profile Update ---
$('#updateProfileForm').submit(function(e){
    e.preventDefault();
    $.post('{{ route("admin.profile.update") }}', $(this).serialize(), function(data){
        showAlert('success', data.message);
        location.reload();
    }).fail(function(xhr){
        showAlert('danger', xhr.responseJSON?.message || 'Something went wrong');
    });
});

// --- Request Phone Change ---
$('#requestPhoneChangeForm').submit(function(e){
    e.preventDefault();
    $.post('{{ route("admin.profile.requestPhoneChange") }}', $(this).serialize(), function(data){
        showAlert('success', data.message);

        // Dynamically inject verification form
        let phone = $('input[name="phone"]').val();
        $('#verifyPhoneSection').html(`
            <div class="mt-3 border p-3">
                <h5>Verify Phone Number</h5>
                <p class="text-muted">A code was sent to: ${phone}. Expires in 3 minutes.</p>
                <form id="verifyPhoneChangeForm">
                    @csrf
                    <div class="mb-3">
                        <label>Verification Code</label>
                        <input type="text" name="verification_code" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success">Verify & Update Phone</button>
                </form>
            </div>
        `);

        // Attach submit handler for verification
        $('#verifyPhoneChangeForm').submit(function(e){
            e.preventDefault();
            $.post('{{ route("admin.profile.verifyPhoneChange") }}', $(this).serialize(), function(data){
                showAlert('success', data.message);
                $('#verifyPhoneSection').html('');
                $('input[name="phone"]').val('');
            }).fail(function(xhr){
                showAlert('danger', xhr.responseJSON?.message || 'Something went wrong');
            });
        });

    }).fail(function(xhr){
        showAlert('danger', xhr.responseJSON?.message || 'Something went wrong');
    });
});

// --- Update Password ---
$('#updatePasswordForm').submit(function(e){
    e.preventDefault();
    $.post('{{ route("admin.profile.updatePassword") }}', $(this).serialize(), function(data){
        showAlert('success', data.message);
        $('#updatePasswordForm')[0].reset();
    }).fail(function(xhr){
        showAlert('danger', xhr.responseJSON?.message || 'Something went wrong');
    });
});

// --- Change Email ---
$('#changeEmailForm').submit(function(e){
    e.preventDefault();
    $.post('{{ route("admin.profile.changeEmail") }}', $(this).serialize(), function(data){
        showAlert('success', data.message);
    }).fail(function(xhr){
        showAlert('danger', xhr.responseJSON?.message || 'Something went wrong');
    });
});
</script>
@endpush
