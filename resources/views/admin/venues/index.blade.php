@extends('layouts.admin')

@section('title', 'Venues')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>Venue Management</h2>

    <a href="{{ route('admin.venues.create') }}"
       class="btn btn-primary">

        + Add Venue

    </a>

</div>

@if(session('success'))

    <div class="alert alert-success">
        {{ session('success') }}
    </div>

@endif

<div class="card">

    <div class="card-body">

        <table class="table table-hover table-bordered align-middle">

            <thead class="table-dark">

                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Capacity</th>
                    <th width="250">Actions</th>
                </tr>

            </thead>

            <tbody>

            @forelse($venues as $venue)

                <tr>

                    <td>{{ $venue->id }}</td>

                    <td>{{ $venue->name }}</td>

                    <td>{{ $venue->location }}</td>

                    <td>{{ $venue->capacity }}</td>

                    <td>

                        <a
                            href="{{ route('admin.venues.show',$venue->id) }}"
                            class="btn btn-info btn-sm">

                            View

                        </a>

                        <a
                            href="{{ route('admin.venues.edit',$venue->id) }}"
                            class="btn btn-warning btn-sm">

                            Edit

                        </a>

                        <form
                            action="{{ route('admin.venues.destroy',$venue->id) }}"
                            method="POST"
                            style="display:inline">

                            @csrf
                            @method('DELETE')

                            <button
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Delete this venue?')">

                                Delete

                            </button>

                        </form>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="5" class="text-center">

                        No venues found.

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection