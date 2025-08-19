@extends('layouts.app')

@section('title', 'Confirm Password')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow rounded-3">
                <div class="card-header bg-warning text-dark text-center">
                    <h4>Confirm Your Password</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted text-center">This is a secure area. Please confirm your password before continuing.</p>
                    <form method="POST" action="{{ route('password.confirm') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input id="password" type="password"
                                class="form-control @error('password') is-invalid @enderror" name="password" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <button type="submit" class="btn btn-warning w-100">Confirm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
