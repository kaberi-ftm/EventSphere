@extends('layouts.admin')

@section('title', 'Certificate Details')
@section('page-title', 'Certificate Details')

@section('content')
<div class="container-fluid px-0">

    <div class="card shadow-sm">
        <div class="card-body text-center py-5">

            <i class="bi bi-award-fill display-1 text-warning"></i>

            <h2 class="fw-bold mt-3">
                {{ $certificate->title }}
            </h2>

            <p class="text-muted">
                This certificate is presented to
            </p>

            <h3 class="text-primary">
                {{ $certificate->recipient_name }}
            </h3>

            <p class="fs-5">
                For
                <strong>{{ $certificate->event_title }}</strong>
            </p>

            <p>
                {{ $certificate->description }}
            </p>

            <hr>

            <p>
                <strong>Certificate Number:</strong>
                {{ $certificate->certificate_number }}
            </p>

            <p>
                <strong>Verification Code:</strong>
                {{ $certificate->verification_code }}
            </p>

            <p>
                <strong>Status:</strong>

                <span class="badge {{
                    $certificate->status === 'issued'
                        ? 'bg-success'
                        : 'bg-danger'
                }}">
                    {{ ucfirst($certificate->status) }}
                </span>
            </p>

            <p>
                <strong>Issued At:</strong>
                {{ $certificate->issued_at }}
            </p>

            <a href="{{ route(
                'admin.certificates.edit',
                $certificate->id
            ) }}"
               class="btn btn-warning">
                Edit
            </a>

            <a href="{{ route(
                'admin.certificates.index'
            ) }}"
               class="btn btn-outline-secondary">
                Back
            </a>

        </div>
    </div>

</div>
@endsection