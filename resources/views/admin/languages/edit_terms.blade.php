@extends('admin.layouts.master')

@section('content')
<div class="container mt-4">
    <h3>Edit Terms for {{ $language->name }}</h3>

    <form method="GET">
        <select name="module" onchange="this.form.submit()" class="form-select mb-3">
            <option value="">Select Module</option>
            <option value="Settings" {{ request('module')=='Settings'?'selected':'' }}>Settings</option>
            <option value="Dashboard" {{ request('module')=='Dashboard'?'selected':'' }}>Dashboard</option>
            <!-- Add more modules here -->
        </select>
    </form>

    @if(request('module'))
    <table class="table table-bordered" id="translations-table">
        <thead>
            <tr>
                <th>Key (English / Arabic)</th>
                <th>Translation ({{ $language->name }})</th>
                <th>Action</th>
            </tr>
            <!-- Add new row -->
            <tr>
                <td><input type="text" id="new-key" class="form-control"></td>
                <td><input type="text" id="new-value" class="form-control"></td>
                <td>
                    <button class="btn btn-success btn-sm" onclick="saveNew(event)">Save</button>
                </td>
            </tr>
        </thead>
    </table>
    @endif
</div>
@endsection

@push('scripts')
<script>
let table;

$(function() {
    let module = "{{ request('module') }}";
    if(module){
        table = $('#translations-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.languages.editTerms',$language->id) }}?module="+module,
            columns: [
                { data: 'key', name: 'key', render: function(data,type,row){
                    return `<span id="key-${row.id}">${row.key}</span>`;
                }},
                { data: 'value', name: 'value', render: function(data,type,row){
                    return `<span id="text-${row.id}">${row.value}</span>`;
                }},
                { data: 'action', name: 'action', orderable:false, searchable:false }
            ]
        });
    }
});

function saveNew(e){
    e.preventDefault();
    $.post("{{ route('admin.languages.storeTerm',$language->id) }}",{
        _token: "{{ csrf_token() }}",
        module: "{{ request('module') }}",
        key: $('#new-key').val(),
        value: $('#new-value').val()
    },function(res){
        if(res.success){
            Swal.fire('Saved!','','success');
            $('#new-key').val('');
            $('#new-value').val('');
            table.ajax.reload();
        }
    }).fail(function(xhr){
        Swal.fire('Error',xhr.responseJSON.message,'error');
    });
}

function editRow(id){
    let keyText = $('#key-'+id).text().trim();
    let valueText = $('#text-'+id).text().trim();

    // Replace key & value with input fields
    $('#key-'+id).html(`<input type="text" id="input-key-${id}" class="form-control" value="${keyText}">`);
    $('#text-'+id).html(`<input type="text" id="input-value-${id}" class="form-control" value="${valueText}">`);

    // Replace action with Save/Cancel buttons
    $('td:has(#key-'+id+')').siblings().last().html(`
        <button class="btn btn-sm btn-primary" onclick="saveRow(${id})">Save</button>
        <button class="btn btn-sm btn-secondary" onclick="table.ajax.reload(null,false)">Cancel</button>
    `);
}

function saveRow(id){
    $.post("{{ url('admin/languages/'.$language->id.'/terms/update') }}/"+id,{
        _token: "{{ csrf_token() }}",
        key: $('#input-key-'+id).val(),
        value: $('#input-value-'+id).val()
    },function(res){
        if(res.success){
            Swal.fire('Updated!','','success');
            table.ajax.reload(null,false);
        }
    }).fail(function(xhr){
        let msg = xhr.responseJSON.message || 'Validation error';
        Swal.fire('Error', msg, 'error');
    });
}

function deleteRow(id){
    Swal.fire({
        title: 'Delete?',
        text: "Are you sure you want to delete this term?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete'
    }).then((result)=>{
        if(result.isConfirmed){
            $.ajax({
                url: "{{ url('admin/languages/'.$language->id.'/terms/delete') }}/"+id,
                type: 'DELETE',
                data:{_token:"{{ csrf_token() }}"},
                success:function(res){
                    Swal.fire('Deleted!','','success');
                    table.ajax.reload(null,false);
                }
            });
        }
    });
}
</script>
@endpush
