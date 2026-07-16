@extends('layouts.admin')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
<div class="container-fluid px-0">

    <div class="row justify-content-center">
        <div class="col-xl-9 col-lg-10">

            <div class="card shadow-sm">

                <div class="card-header">
                    <h4 class="mb-1">
                        <i class="bi bi-pencil-square me-2"></i>
                        Edit {{ $user->name }}
                    </h4>

                    <p class="text-muted mb-0">
                        Update account, role and engagement information.
                    </p>
                </div>

                <div class="card-body">
                    @include('admin.users.form', [
                        'action' => route(
                            'admin.users.update',
                            $user->id
                        ),
                        'method' => 'PUT',
                        'user' => $user
                    ])
                </div>

            </div>
        </div>
    </div>

</div>
@endsection