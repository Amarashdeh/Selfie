@extends('admin.layouts.app')

@section('content')
    <h1>Admin Dashboard</h1>
    <p>Welcome, {{ auth()->guard('admin')->user()->name }}!</p>

    <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
@endsection
