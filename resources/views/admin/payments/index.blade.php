@extends('layouts.admin')

@section('title', 'Payments')
@section('page-title', 'Payment Management')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Event Payments</h2>
            <p class="text-muted mb-0">
                Manage event income, expenses, refunds and running totals.
            </p>
        </div>

        <a href="{{ route('admin.payments.create') }}"
           class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>
            Record Payment
        </a>
    </div>

    <form method="GET"
          action="{{ route('admin.payments.index') }}"
          class="card shadow-sm mb-4">

        <div class="card-body">
            <div class="row g-3">

                <div class="col-lg-3 col-md-6">
                    <label class="form-label">Search</label>

                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           class="form-control"
                           placeholder="Payee, event or reference">
                </div>

                <div class="col-lg-2 col-md-6">
                    <label class="form-label">Event</label>

                    <select name="event_id" class="form-select">
                        <option value="">All Events</option>

                        @foreach($events as $event)
                            <option value="{{ $event->id }}"
                                @selected((string) $eventId === (string) $event->id)>
                                {{ $event->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label class="form-label">Type</label>

                    <select name="type" class="form-select">
                        <option value="">All Types</option>

                        @foreach(['expense', 'income', 'refund'] as $value)
                            <option value="{{ $value }}"
                                @selected($type === $value)>
                                {{ ucfirst($value) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label class="form-label">Status</label>

                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>

                        @foreach([
                            'pending',
                            'approved',
                            'paid',
                            'cancelled'
                        ] as $value)
                            <option value="{{ $value }}"
                                @selected($status === $value)>
                                {{ ucfirst($value) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label class="form-label">Sort By</label>

                    <select name="sort" class="form-select">
                        <option value="date" @selected($sort === 'date')}>
                            Payment Date
                        </option>

                        <option value="amount" @selected($sort === 'amount')}>
                            Amount
                        </option>

                        <option value="payee" @selected($sort === 'payee')}>
                            Payee
                        </option>

                        <option value="event" @selected($sort === 'event')}>
                            Event
                        </option>

                        <option value="status" @selected($sort === 'status')}>
                            Status
                        </option>
                    </select>
                </div>

                <div class="col-lg-1 col-md-6">
                    <label class="form-label">Order</label>

                    <select name="direction" class="form-select">
                        <option value="asc"
                            @selected(strtoupper($direction) === 'ASC')>
                            ASC
                        </option>

                        <option value="desc"
                            @selected(strtoupper($direction) === 'DESC')>
                            DESC
                        </option>
                    </select>
                </div>

                <div class="col-12 d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.payments.index') }}"
                       class="btn btn-outline-secondary">
                        Reset
                    </a>

                    <button type="submit" class="btn btn-dark">
                        <i class="bi bi-funnel me-1"></i>
                        Apply Filters
                    </button>
                </div>

            </div>
        </div>
    </form>

    <div class="card shadow-sm">

        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Payment Records</h5>

            <span class="badge bg-primary">
                {{ count($payments) }} records
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">

                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Payee</th>
                        <th>Type</th>
                        <th>Budget</th>
                        <th>Amount</th>
                        <th>Running Expense</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($payments as $payment)

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

                        <tr>
                            <td>
                                <div class="fw-semibold">
                                    {{ $payment->event_title }}
                                </div>

                                <small class="text-muted">
                                    Payment #{{ $payment->id }}
                                </small>
                            </td>

                            <td>
                                {{ $payment->payee_name }}

                                @if($payment->reference_number)
                                    <div>
                                        <small class="text-muted">
                                            Ref: {{ $payment->reference_number }}
                                        </small>
                                    </div>
                                @endif
                            </td>

                            <td>
                                <span class="badge {{ $typeClass }}">
                                    {{ ucfirst($paymentType) }}
                                </span>
                            </td>

                            <td>
                                {{ $payment->budget_category
                                    ? ucfirst($payment->budget_category)
                                    : 'N/A' }}
                            </td>

                            <td class="fw-bold">
                                ৳{{ number_format(
                                    (float) $payment->amount,
                                    2
                                ) }}
                            </td>

                            <td class="text-danger fw-semibold">
                                ৳{{ number_format(
                                    (float) ($payment->running_expense ?? 0),
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
                                <span class="badge {{ $statusClass }}">
                                    {{ ucfirst($paymentStatus) }}
                                </span>
                            </td>

                            <td class="text-nowrap">
                                {{ $payment->payment_date }}
                            </td>

                            <td class="text-end text-nowrap">
                                <a href="{{ route(
                                    'admin.payments.show',
                                    $payment->id
                                ) }}"
                                   class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <a href="{{ route(
                                    'admin.payments.edit',
                                    $payment->id
                                ) }}"
                                   class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <form method="POST"
                                      action="{{ route(
                                          'admin.payments.destroy',
                                          $payment->id
                                      ) }}"
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Delete this payment?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="10"
                                class="text-center py-5">

                                <i class="bi bi-credit-card fs-1 text-muted"></i>

                                <h5 class="mt-3">No payments found</h5>

                                <p class="text-muted">
                                    Record the first event payment.
                                </p>

                                <a href="{{ route('admin.payments.create') }}"
                                   class="btn btn-primary">
                                    Record Payment
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

</div>
@endsection