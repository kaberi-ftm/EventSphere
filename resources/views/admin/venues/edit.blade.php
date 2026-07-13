@extends('layouts.admin')

@section('title', 'Edit Venue')

@section('content')

<div class="container">

    <div class="d-flex justify-content-between mb-4">

        <h2>Edit Venue</h2>

        <a href="{{ route('admin.venues.index') }}"
           class="btn btn-secondary">

            Back

        </a>

    </div>

    <div class="card">

        <div class="card-body">

            <form action="{{ route('admin.venues.update',$venue->id) }}"
                  method="POST">

                @csrf
                @method('PUT')

                <div class="mb-3">

                    <label class="form-label">
                        Venue Name
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ $venue->name }}"
                        class="form-control"
                        required>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Location
                    </label>

                    <input
                        type="text"
                        name="location"
                        value="{{ $venue->location }}"
                        class="form-control"
                        required>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Capacity
                    </label>

                    <input
                        type="number"
                        name="capacity"
                        value="{{ $venue->capacity }}"
                        class="form-control"
                        required>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Description
                    </label>

                    <textarea
                        name="description"
                        rows="5"
                        class="form-control">{{ $venue->description }}</textarea>

                </div>

                <button class="btn btn-primary">

                    Update Venue

                </button>

            </form>

        </div>

    </div>

</div>

@endsection