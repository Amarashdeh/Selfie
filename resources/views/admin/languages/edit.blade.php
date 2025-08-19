<form id="editLanguageForm" action="{{ route('admin.languages.update', $language->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="modal-header">
        <h5 class="modal-title">Edit Language</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ $language->name }}" required>
        </div>
        <div class="mb-3">
            <label>Code</label>
            <input type="text" name="code" class="form-control" value="{{ $language->code }}" required>
        </div>
        <div class="mb-3">
            <label>Direction</label>
            <select name="rtl" class="form-select" required>
                <option value="0" {{ $language->rtl==0 ? 'selected' : '' }}>LTR</option>
                <option value="1" {{ $language->rtl==1 ? 'selected' : '' }}>RTL</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Flag Icon</label>
            <input type="file" name="icon" class="form-control">
            @if($language->icon)
                <img src="{{ asset('storage/'.$language->icon) }}" width="50" class="mt-2">
            @endif
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Save</button>
    </div>
</form>
