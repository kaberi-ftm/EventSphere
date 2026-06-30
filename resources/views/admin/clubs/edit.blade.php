@extends('layouts.admin')

@section('content')

<div class="container">

    <div class="card">

        <div class="card-header">
            <h3>Edit Club</h3>
        </div>

        <div class="card-body">

            <form
                action="{{ route('admin.clubs.update', $club->id) }}"
                method="POST"
            >

                @csrf
                @method('PUT')

                <div class="mb-3">

                    <label class="form-label">
                        Club Name
                    </label>

                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="{{ $club->name }}"
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
                    >{{ $club->description }}</textarea>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Founded Date
                    </label>

                    <input
                        type="date"
                        name="founded_date"
                        class="form-control"
                        value="{{ $club->founded_date }}"
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

                            <option
                                value="{{ $admin->id }}"
                                {{ $club->admin_user_id == $admin->id ? 'selected' : '' }}
                            >
                                {{ $admin->name }}
                            </option>

                        @endforeach

                    </select>

                </div>

                <button class="btn btn-primary">
                    Update Club
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