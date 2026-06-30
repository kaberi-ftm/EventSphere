@extends('layouts.admin')

@section('content')

<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Club Management</h2>

        <a href="{{ route('admin.clubs.create') }}"
           class="btn btn-primary">
            + Add Club
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">

            <table class="table table-bordered table-hover">

                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Club Name</th>
                        <th>Description</th>
                        <th>Founded Date</th>
                        <th>Admin</th>
                        <th width="220">Actions</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($clubs as $club)

                        <tr>

                            <td>{{ $club->id }}</td>

                            <td>{{ $club->name }}</td>

                            <td>
                                {{ $club->description ?? 'N/A' }}
                            </td>

                            <td>
                                {{ $club->founded_date ?? 'N/A' }}
                            </td>

                            <td>
                                {{ $club->admin_name ?? 'Not Assigned' }}
                            </td>

                            <td>

                                <a href="{{ route('admin.clubs.show', $club->id) }}"
                                   class="btn btn-info btn-sm">
                                    View
                                </a>

                                <a href="{{ route('admin.clubs.edit', $club->id) }}"
                                   class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <form action="{{ route('admin.clubs.destroy', $club->id) }}"
                                      method="POST"
                                      style="display:inline;">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Delete this club?')">
                                        Delete
                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="6" class="text-center">
                                No clubs found.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>
    </div>

</div>

@endsection