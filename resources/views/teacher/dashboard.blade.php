@extends('teacher.layouts.app')

@section('content')
<h1>Teacher Dashboard</h1>
<p>Welcome, {{ auth('web')->user()->name }}!</p>

<form method="POST" action="{{ route('user.logout') }}">
    @csrf
    <button type="submit">Logout</button>
</form>
@endsection
