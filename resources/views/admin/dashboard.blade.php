@extends('layouts.admin')

@section('title','Dashboard')

@section('page-title','Dashboard')

@section('content')

<div class="row">

    <div class="col-md-3 mb-4">

        <div class="card">

            <div class="card-body">

                <h6>Total Events</h6>

                <h2>25</h2>

            </div>

        </div>

    </div>

    <div class="col-md-3 mb-4">

        <div class="card">

            <div class="card-body">

                <h6>Total Clubs</h6>

                <h2>8</h2>

            </div>

        </div>

    </div>

    <div class="col-md-3 mb-4">

        <div class="card">

            <div class="card-body">

                <h6>Participants</h6>

                <h2>450</h2>

            </div>

        </div>

    </div>

    <div class="col-md-3 mb-4">

        <div class="card">

            <div class="card-body">

                <h6>Volunteers</h6>

                <h2>62</h2>

            </div>

        </div>

    </div>

</div>

<div class="card">

    <div class="card-body">

        <h4>

            Welcome, {{ auth()->user()->name }}

        </h4>

        <p>

            Welcome to the EventSphere Admin Dashboard.

        </p>

    </div>

</div>

@endsection