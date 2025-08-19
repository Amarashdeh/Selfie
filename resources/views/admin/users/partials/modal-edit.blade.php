<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editUserForm" novalidate>
      <input type="hidden" name="id" />
      <input type="hidden" name="type" />
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editUserLabel">Edit User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          @csrf
          @method('PUT')

          <div class="mb-3">
            <label for="nameEdit" class="form-label">Name</label>
            <input type="text" class="form-control" id="nameEdit" name="name" required data-parsley-nospecial>
          </div>

          <div class="mb-3">
            <label for="emailEdit" class="form-label">Email</label>
            <input type="email" class="form-control" id="emailEdit" name="email" required data-parsley-strictemail>
          </div>

          <div class="mb-3">
            <label for="passwordEdit" class="form-label">Password (leave empty to keep unchanged)</label>
            <input type="password" class="form-control" id="passwordEdit" name="password" minlength="8">
          </div>

          <div class="mb-3">
            <label for="passwordConfirmEdit" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="passwordConfirmEdit" name="password_confirmation" data-parsley-equalto="#passwordEdit">
          </div>

          <div class="mb-3">
              <label for="roleEdit" class="form-label">Role</label>
              <select name="role" id="roleEdit" class="form-select" required>
                  <option value="" selected disabled>Select Role</option>
                  @php
                      $currentUser = auth()->guard('admin')->user();
                  @endphp
                  @foreach ($roles as $role)
                      @if($currentUser->hasRole('Admin') && ($role === 'SuperAdmin'))
                          @continue
                      @endif
                      <option value="{{ $role }}">{{ $role }}</option>
                  @endforeach
              </select>
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Update</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>
