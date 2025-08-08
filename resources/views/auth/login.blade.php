<!DOCTYPE html>
<html>
<head>
    <title>User Login</title>
</head>
<body>
    <h1>User Login</h1>
    <form method="POST" action="{{ route('user.login.submit') }}">
        @csrf
        <label>Email:</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus />
        @error('email') <div>{{ $message }}</div> @enderror

        <label>Password:</label>
        <input type="password" name="password" required />
        @error('password') <div>{{ $message }}</div> @enderror

        <label><input type="checkbox" name="remember" /> Remember Me</label>

        <button type="submit">Login</button>
    </form>
</body>
</html>
