@extends('layouts.admin')

@section('content')

<div class="container">

    <h2>{{ $club->name }}</h2>

    <div class="card mt-3">
        <div class="card-body">

            <p>
                <strong>Description:</strong>
                {{ $club->description }}
            </p>

            <p>
                <strong>Founded:</strong>
                {{ $club->founded_date }}
            </p>

            <p>
                <strong>Admin:</strong>
                {{ $club->admin_name ?? 'Not Assigned' }}
            </p>

        </div>
    </div>

    <a
        href="{{ route('admin.clubs.index') }}"
        class="btn btn-secondary mt-3"
    >
        Back
    </a>

</div>

@endsection