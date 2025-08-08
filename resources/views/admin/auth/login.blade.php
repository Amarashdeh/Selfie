@extends('admin.layouts.app')

@section('content')
    <form method="POST" action="{{ route('admin.login.submit') }}">
        @csrf

        <label>Email:</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus>
        @error('email')
            <div>{{ $message }}</div>
        @enderror

        <label>Password:</label>
        <input type="password" name="password" required>
        @error('password')
            <div>{{ $message }}</div>
        @enderror

        <label>
            <input type="checkbox" name="remember"> Remember Me
        </label>

        <button type="submit">Login</button>
    </form>
@endsection
