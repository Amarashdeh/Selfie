@extends('admin.layouts.master')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">Welcome, {{ auth('admin')->user()->name }}</h1>
        <div class="row">
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Users</h5>
                        <p class="card-text display-6">{{ $totalUsers ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Admins</h5>
                        <p class="card-text display-6">{{ $totalAdmins ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
