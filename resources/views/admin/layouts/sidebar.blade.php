<div class="sidebar p-3 bg-dark">
    <h4 class="text-white mb-4">Admin Panel</h4>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}"
               class="nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.users.index') }}"
               class="nav-link text-white {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people me-2"></i> Users
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.roles.index') }}"
               class="nav-link text-white {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                <i class="bi bi-shield-lock me-2"></i> Roles
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.settings.index') }}"
               class="nav-link text-white {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear me-2"></i> Settings
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.languages.index') }}"
               class="nav-link text-white {{ request()->routeIs('admin.languages.*') ? 'active' : '' }}">
                <i class="bi bi-gear me-2"></i> Languages
            </a>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link text-white dropdown-toggle {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}"
            href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle me-2"></i> Profile
            </a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('admin.profile') }}">My Profile</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.profile.edit') }}">Edit Profile</a></li>
            </ul>
        </li>
        <li class="nav-item mt-4">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger w-100">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </button>
            </form>
        </li>
    </ul>
</div>
