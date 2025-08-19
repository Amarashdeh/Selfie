@extends('admin.layouts.master')

@section('content')
<div class="container mt-4">
    <h3>My Profile</h3>
    <table class="table table-bordered">
        <tr><th>Name</th><td>{{ $user->name }}</td></tr>
        <tr><th>Email</th><td>{{ $user->email }}</td></tr>
        <tr><th>Phone</th><td>{{ $user->phone }}</td></tr>
    </table>
    <a href="{{ route('admin.profile.edit') }}" class="btn btn-primary">Edit Profile</a>
</div>
@endsection
