@extends('layouts.admin')

@section('content')

<div class="container">

    <div class="card">
        <div class="card-header">
            <h3>Create New Club</h3>
        </div>

        <div class="card-body">

            <form action="{{ route('admin.clubs.store') }}" method="POST">

                @csrf

                <div class="mb-3">
                    <label class="form-label">
                        Club Name
                    </label>

                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Description
                    </label>

                    <textarea
                        name="description"
                        class="form-control"
                        rows="4"
                    ></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Founded Date
                    </label>

                    <input
                        type="date"
                        name="founded_date"
                        class="form-control"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Club Admin
                    </label>

                    <select
                        name="admin_user_id"
                        class="form-control"
                    >

                        <option value="">
                            Select Admin
                        </option>

                        @foreach($admins as $admin)

                            <option value="{{ $admin->id }}">
                                {{ $admin->name }}
                            </option>

                        @endforeach

                    </select>

                </div>

                <button class="btn btn-success">
                    Create Club
                </button>

                <a
                    href="{{ route('admin.clubs.index') }}"
                    class="btn btn-secondary"
                >
                    Back
                </a>

            </form>

        </div>
    </div>

</div>

@endsection