@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

<h2 class="mb-4">Dashboard</h2>

<div class="row">

    <div class="col-md-3 mb-4">
        <div class="card">
            <div class="card-body text-center">
                <h6>Total Clubs</h6>
                <h2>{{ $totalClubs ?? 0 }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card">
            <div class="card-body text-center">
                <h6>Total Events</h6>
                <h2>{{ $totalEvents ?? 0 }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card">
            <div class="card-body text-center">
                <h6>Total Users</h6>
                <h2>{{ $totalUsers ?? 0 }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card">
            <div class="card-body text-center">
                <h6>Volunteers</h6>
                <h2>{{ $totalVolunteers ?? 0 }}</h2>
            </div>
        </div>
    </div>

</div>

<div class="card">
    <div class="card-body">

        <h4>Welcome, {{ auth()->user()->name }}</h4>

        <p class="mb-0">
            Welcome to EventSphere Admin Dashboard.
        </p>

    </div>
</div>

@endsection