@extends('user.layouts.app')

@section('content')
<h1>Parent Dashboard</h1>
<p>Welcome, {{ auth()->user()->name }}!</p>

<form method="POST" action="{{ route('user.logout') }}">
    @csrf
    <button type="submit">Logout</button>
</form>
@endsection
