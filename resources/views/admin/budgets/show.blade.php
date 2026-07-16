@extends('layouts.admin')

@section('title', 'Budget Details')
@section('page-title', 'Budget Details')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                {{ $budget->event_title }}
            </h2>

            <p class="text-muted mb-0">
                {{ ucfirst($budget->category) }} budget details
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route(
                'admin.payments.create',
                [
                    'event_id' => $budget->event_id,
                    'budget_id' => $budget->id
                ]
            ) }}"
               class="btn btn-success">

                <i class="bi bi-plus-circle me-1"></i>
                Add Payment
            </a>

            <a href="{{ route(
                'admin.budgets.edit',
                $budget->id
            ) }}"
               class="btn btn-warning">

                <i class="bi bi-pencil-square me-1"></i>
                Edit
            </a>

            <a href="{{ route('admin.budgets.index') }}"
               class="btn btn-outline-secondary">

                <i class="bi bi-arrow-left me-1"></i>
                Back
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">

        <div class="col-lg-3 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted">
                        Allocated Amount
                    </small>

                    <h3 class="fw-bold mt-2 mb-0 text-primary">
                        ৳{{ number_format(
                            (float) $budget->allocated_amount,
                            2
                        ) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted">
                        Paid Expense
                    </small>

                    <h3 class="fw-bold mt-2 mb-0 text-danger">
                        ৳{{ number_format(
                            (float) $budget->spent_amount,
                            2
                        ) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted">
                        Remaining Amount
                    </small>

                    <h3 class="fw-bold mt-2 mb-0 text-success">
                        ৳{{ number_format(
                            (float) $budget->remaining_amount,
                            2
                        ) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted">
                        Utilization
                    </small>

                    <h3 class="fw-bold mt-2 mb-0">
                        {{ number_format(
                            (float) $budget->utilization_percentage,
                            2
                        ) }}%
                    </h3>
                </div>
            </div>
        </div>

    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0">Budget Information</h5>
        </div>

        <div class="card-body">
            <div class="row g-3">

                <div class="col-md-6">
                    <strong>Event:</strong>
                    <div class="text-muted">
                        {{ $budget->event_title }}
                    </div>
                </div>

                <div class="col-md-6">
                    <strong>Event Start:</strong>
                    <div class="text-muted">
                        {{ $budget->start_time }}
                    </div>
                </div>

                <div class="col-md-6">
                    <strong>Category:</strong>
                    <div class="text-muted">
                        {{ ucfirst($budget->category) }}
                    </div>
                </div>

                <div class="col-md-6">
                    <strong>Status:</strong>
                    <div>
                        <span class="badge bg-primary">
                            {{ ucfirst($budget->status) }}
                        </span>
                    </div>
                </div>

                <div class="col-12">
                    <strong>Description:</strong>
                    <div class="text-muted">
                        {{ $budget->description ?? 'No description provided.' }}
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                Payment History
            </h5>

            <span class="badge bg-dark">
                {{ count($payments) }} payments
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Payee</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Reference</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payment->payee_name }}</td>

                            <td>
                                {{ ucfirst($payment->payment_type) }}
                            </td>

                            <td class="fw-semibold">
                                ৳{{ number_format(
                                    (float) $payment->amount,
                                    2
                                ) }}
                            </td>

                            <td>
                                {{ ucwords(str_replace(
                                    '_',
                                    ' ',
                                    $payment->payment_method
                                )) }}
                            </td>

                            <td>
                                <span class="badge bg-secondary">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>

                            <td>{{ $payment->payment_date }}</td>

                            <td>
                                {{ $payment->reference_number ?? 'N/A' }}
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="7"
                                class="text-center py-4 text-muted">
                                No payment records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection