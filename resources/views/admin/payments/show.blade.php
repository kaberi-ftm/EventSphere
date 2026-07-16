@extends('layouts.admin')

@section('title', 'Payment Details')
@section('page-title', 'Payment Details')

@section('content')
<div class="container-fluid px-0">

    @php
        $paymentType = strtolower(
            (string) $payment->payment_type
        );

        $paymentStatus = strtolower(
            (string) $payment->status
        );

        $typeClass = match($paymentType) {
            'income' => 'bg-success',
            'expense' => 'bg-danger',
            'refund' => 'bg-warning text-dark',
            default => 'bg-secondary'
        };

        $statusClass = match($paymentStatus) {
            'paid' => 'bg-success',
            'approved' => 'bg-primary',
            'pending' => 'bg-warning text-dark',
            'cancelled' => 'bg-secondary',
            default => 'bg-dark'
        };
    @endphp

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                Payment #{{ $payment->id }}
            </h2>

            <p class="text-muted mb-0">
                {{ $payment->event_title }}
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route(
                'admin.payments.edit',
                $payment->id
            ) }}"
               class="btn btn-warning">
                <i class="bi bi-pencil-square me-1"></i>
                Edit
            </a>

            <a href="{{ route('admin.payments.index') }}"
               class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Back
            </a>
        </div>
    </div>

    <div class="row g-4">

        <div class="col-lg-4">

            <div class="card shadow-sm h-100">
                <div class="card-body text-center">

                    <div class="mb-3">
                        <i class="bi bi-cash-coin display-4 text-primary"></i>
                    </div>

                    <small class="text-muted">
                        Payment Amount
                    </small>

                    <h2 class="fw-bold my-2">
                        ৳{{ number_format(
                            (float) $payment->amount,
                            2
                        ) }}
                    </h2>

                    <span class="badge {{ $typeClass }}">
                        {{ ucfirst($paymentType) }}
                    </span>

                    <span class="badge {{ $statusClass }}">
                        {{ ucfirst($paymentStatus) }}
                    </span>

                </div>
            </div>

        </div>

        <div class="col-lg-8">

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">
                        Payment Information
                    </h5>
                </div>

                <div class="card-body">
                    <div class="row g-4">

                        <div class="col-md-6">
                            <strong>Event</strong>
                            <div class="text-muted">
                                {{ $payment->event_title }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <strong>Payee / Payer</strong>
                            <div class="text-muted">
                                {{ $payment->payee_name }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <strong>Budget Category</strong>
                            <div class="text-muted">
                                {{ $payment->budget_category
                                    ? ucfirst($payment->budget_category)
                                    : 'Not applicable' }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <strong>Allocated Budget</strong>
                            <div class="text-muted">
                                {{ $payment->allocated_amount !== null
                                    ? '৳' . number_format(
                                        (float) $payment->allocated_amount,
                                        2
                                    )
                                    : 'N/A' }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <strong>Payment Method</strong>
                            <div class="text-muted">
                                {{ ucwords(str_replace(
                                    '_',
                                    ' ',
                                    $payment->payment_method
                                )) }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <strong>Payment Date</strong>
                            <div class="text-muted">
                                {{ $payment->payment_date }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <strong>Reference Number</strong>
                            <div class="text-muted">
                                {{ $payment->reference_number ?? 'N/A' }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <strong>Last Updated</strong>
                            <div class="text-muted">
                                {{ $payment->updated_at ?? 'N/A' }}
                            </div>
                        </div>

                        <div class="col-12">
                            <strong>Notes</strong>
                            <div class="text-muted">
                                {{ $payment->notes ?? 'No notes provided.' }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection