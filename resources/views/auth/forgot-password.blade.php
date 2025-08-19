@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow rounded-3">
                <div class="card-header bg-info text-white text-center">
                    <h4>Forgot Your Password?</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted">Enter your email and we'll send you a password reset link.</p>
                    @if (session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif
                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input id="email" type="email"
                                class="form-control @error('email') is-invalid @enderror" name="email"
                                value="{{ old('email') }}" required autofocus>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <button type="submit" class="btn btn-info w-100 text-white">Send Reset Link</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
