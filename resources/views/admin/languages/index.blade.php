@extends('admin.layouts.master')

@section('content')
<div class="container mt-4">
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addLanguageModal">Add New Language</button>

    <table class="table table-bordered" id="languages-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Code</th>
                <th>RTL</th>
                <th>Icon</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Add Language Modal -->
<div class="modal fade" id="addLanguageModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="addLanguageForm" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Add New Language</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Code</label>
            <input type="text" name="code" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Direction</label>
            <select name="rtl" class="form-select" required>
                <option value="0">LTR</option>
                <option value="1">RTL</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Flag Icon</label>
            <input type="file" name="icon" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Language Modal (will be filled dynamically) -->
<div class="modal fade" id="editLanguageModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content" id="editLanguageContent">
      <!-- Filled by AJAX -->
    </div>
  </div>
</div>

@endsection


@push('styles')
<style>
.dropdown-toggle::after {
    display: none;
}
</style>
@endpush

@push('scripts')
<script>
$(function() {
    // Initialize DataTable
    var table = $('#languages-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{!! route('admin.languages.index') !!}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'code', name: 'code' },
            { data: 'rtl_text', name: 'rtl' },
            { data: 'icon_img', name: 'icon', orderable:false, searchable:false },
            { data: 'action', name: 'action', orderable:false, searchable:false }
        ]
    });

    // Add Language AJAX
    $('#addLanguageForm').submit(function(e){
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: "{{ route('admin.languages.store') }}",
            method: "POST",
            data: formData,
            processData:false,
            contentType:false,
            success:function(res){
                if(res.success){
                    $('#addLanguageModal').modal('hide'); // hide modal
                    $('#addLanguageForm')[0].reset();     // reset form
                    table.ajax.reload();                  // reload DataTable
                    alert(res.message);
                }
            },
            error:function(err){
                alert('Error! Check console for details.');
                console.log(err);
            }
        });
    });


    // Edit Language (open modal and fill form via AJAX)
    $(document).on('click','.btn-edit', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        $.get(url, function(data){
            $('#editLanguageContent').html(data);
            $('#editLanguageModal').modal('show');
        });
    });

    // Update Language via AJAX (form inside modal)
    $(document).on('submit','#editLanguageForm', function(e){
        e.preventDefault();
        var formData = new FormData(this);
        var url = $(this).attr('action');
        $.ajax({
            url: url,
            method: "POST",
            data: formData,
            processData:false,
            contentType:false,
            success:function(res){
                $('#editLanguageModal').modal('hide');
                table.ajax.reload();
                alert('Language updated successfully');
            },
            error:function(err){
                alert('Error! Check console for details.');
                console.log(err);
            }
        });
    });

    // Delete Language via AJAX
    $(document).on('click','.btn-delete', function(){
        var id = $(this).data('id');
        if(confirm('Are you sure you want to delete this language?')){
            $.ajax({
                url: '/admin/languages/' + id,
                method: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                },
                success: function(res){
                    if(res.success){
                        table.ajax.reload();
                        alert(res.message);
                    }
                },
                error: function(err){
                    alert('Error! Check console for details.');
                    console.log(err);
                }
            });
        }
    });

});
</script>
@endpush
