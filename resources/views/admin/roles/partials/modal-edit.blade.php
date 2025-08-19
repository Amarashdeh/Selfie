<div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editRoleForm" novalidate>
      <input type="hidden" name="id" />
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editRoleLabel">Edit Role</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          @csrf
          @method('PUT')
          <div class="mb-3">
            <label for="roleNameEdit" class="form-label">Role Name</label>
            <input type="text" class="form-control" id="roleNameEdit" name="name" required maxlength="50" data-parsley-pattern="^[A-Za-z0-9_\-\s]+$" data-parsley-pattern-message="Only letters, numbers, spaces, dash and underscore allowed.">
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
