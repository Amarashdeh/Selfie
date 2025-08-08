@extends('moderator.layouts.app')

@section('content')
<h1>Moderator Dashboard</h1>
<p>Welcome, {{ auth()->user()->name }}!</p>

<form method="POST" action="{{ route('user.logout') }}">
    @csrf
    <button type="submit">Logout</button>
</form>
@endsection
