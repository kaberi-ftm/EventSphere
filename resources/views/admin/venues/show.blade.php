@extends('layouts.admin')

@section('title', 'Venue Details')

@section('content')

<div class="container">

    <div class="d-flex justify-content-between mb-4">

        <h2>Venue Details</h2>

        <a href="{{ route('admin.venues.index') }}"
           class="btn btn-secondary">

            Back

        </a>

    </div>

    <div class="card">

        <div class="card-body">

            <table class="table table-bordered">

                <tr>
                    <th width="250">ID</th>
                    <td>{{ $venue->id }}</td>
                </tr>

                <tr>
                    <th>Name</th>
                    <td>{{ $venue->name }}</td>
                </tr>

                <tr>
                    <th>Location</th>
                    <td>{{ $venue->location }}</td>
                </tr>

                <tr>
                    <th>Capacity</th>
                    <td>{{ $venue->capacity }}</td>
                </tr>

                <tr>
                    <th>Description</th>
                    <td>{{ $venue->description }}</td>
                </tr>

                <tr>
                    <th>Created At</th>
                    <td>{{ $venue->created_at }}</td>
                </tr>

                <tr>
                    <th>Updated At</th>
                    <td>{{ $venue->updated_at }}</td>
                </tr>

            </table>

        </div>

    </div>

</div>

@endsection