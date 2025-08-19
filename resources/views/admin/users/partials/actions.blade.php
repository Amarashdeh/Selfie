@php
    $isSuperAdmin = auth()->user()->hasRole('SuperAdmin');
@endphp

<div class="btn-group" role="group" aria-label="Actions">

    <button type="button" class="btn btn-sm btn-primary edit-user"
        data-id="{{ $user['id'] }}"
        data-type="{{ $user['type'] }}"
        @if($user['trashed']) disabled @endif
        title="Edit User"
    >
        <i class="bi bi-pencil"></i>
    </button>

    @if(!$user['trashed'])
        <button type="button" class="btn btn-sm btn-danger delete-user"
            data-id="{{ $user['id'] }}"
            data-type="{{ $user['type'] }}"
            title="Delete User"
        >
            <i class="bi bi-trash"></i>
        </button>
    @endif

    @if($user['trashed'] && auth()->user()->hasRole('SuperAdmin'))
        <button type="button" class="btn btn-sm btn-success restore-user"
            data-id="{{ $user['id'] }}"
            data-type="{{ $user['type'] }}">
            <i class="bi bi-arrow-counterclockwise"></i>
        </button>
        <button class="btn btn-sm btn-danger permanent-delete-user" data-id="{{ $user['id'] }}" data-type="{{ $user['type'] }}">
            <i class="bi bi-x-circle"></i>
        </button>
    @endif
</div>
