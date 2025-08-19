<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="createUserForm" novalidate>
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createUserLabel">Add User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          @csrf

          <div class="mb-3">
            <label for="nameCreate" class="form-label">Name</label>
            <input type="text" class="form-control" id="nameCreate" name="name" required data-parsley-nospecial>
          </div>

          <div class="mb-3">
            <label for="emailCreate" class="form-label">Email</label>
            <input type="email" class="form-control" id="emailCreate" name="email" required data-parsley-strictemail>
          </div>

          <div class="mb-3">
            <label for="passwordCreate" class="form-label">Password</label>
            <input type="password" class="form-control" id="passwordCreate" name="password" required minlength="8">
          </div>

          <div class="mb-3">
            <label for="passwordConfirmCreate" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="passwordConfirmCreate" name="password_confirmation" required data-parsley-equalto="#passwordCreate">
          </div>

          <div class="mb-3">
              <label for="roleCreate" class="form-label">Role</label>
              <select name="role" id="roleCreate" class="form-select" required>
                  <option value="" selected disabled>Select Role</option>
                  @php
                      $currentUser = auth()->guard('admin')->user();
                  @endphp
                  @foreach ($roles as $role)
                      @if($currentUser->hasRole('Admin') && $role === 'SuperAdmin')
                          @continue
                      @endif
                      <option value="{{ $role }}">{{ $role }}</option>
                  @endforeach
              </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Create</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>
