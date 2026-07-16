@extends('layouts.admin')

@section('title', 'Create User')
@section('page-title', 'Create User')

@section('content')
<div class="container-fluid px-0">

    <div class="row justify-content-center">
        <div class="col-xl-9 col-lg-10">

            <div class="card shadow-sm">

                <div class="card-header">
                    <h4 class="mb-1">
                        <i class="bi bi-person-plus-fill me-2"></i>
                        New System User
                    </h4>

                    <p class="text-muted mb-0">
                        Create a user, assign a role and optionally add a club.
                    </p>
                </div>

                <div class="card-body">
                    @include('admin.users.form', [
                        'action' => route('admin.users.store'),
                        'method' => 'POST',
                        'user' => null
                    ])
                </div>

            </div>
        </div>
    </div>

</div>
@endsection