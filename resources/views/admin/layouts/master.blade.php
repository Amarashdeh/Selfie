<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Admin Panel')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    @include('admin.layouts.styles')

    {{-- Custom styles --}}
    <style>
        body {
            min-height: 100vh;
            display: flex;
            margin: 0;
        }
        .sidebar {
            width: 250px;
            background-color: #343a40;
            flex-shrink: 0;
            height: 100vh;
            position: fixed;
            overflow-y: auto;
        }
        .sidebar .nav-link {
            color: #adb5bd;
            padding: 12px 20px;
            font-weight: 500;
        }
        .sidebar .nav-link:hover {
            color: #fff;
            background-color: #495057;
        }
        .sidebar .nav-link.active {
            background-color: #495057;
            color: #fff;
        }
        .content {
            margin-left: 250px;
            flex-grow: 1;
            background: #f8f9fa;
            padding: 20px;
            min-height: 100vh;
        }
        /* Responsive sidebar collapse (optional) */
        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }
            .content {
                margin-left: 0;
            }
        }

        .parsley-errors-list {
            color: #dc3545;
            list-style-type: none;
            padding-left: 0;
            margin-top: 0.25rem;
            font-size: 0.875em;
        }

    </style>

    @stack('styles')
</head>
<body>

    {{-- Sidebar --}}
    @include('admin.layouts.sidebar')

    {{-- Main Content --}}
    <div class="content">
        @yield('content')
    </div>


    @include('admin.layouts.scripts')
    @stack('scripts')
</body>
</html>
