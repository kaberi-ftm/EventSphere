@extends('layouts.admin')

@section('title', 'Reports & Analytics')
@section('page-title', 'Reports & Analytics')

@push('styles')
<style>
    .report-heading {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }

    .report-heading h2 {
        margin: 0;
        color: #172033;
        font-size: 26px;
        font-weight: 800;
    }

    .report-heading p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 14px;
    }

    .report-stat {
        height: 100%;
        padding: 21px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #ffffff;
        box-shadow: 0 5px 18px rgba(15, 23, 42, 0.04);
    }

    .report-stat-label {
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
    }

    .report-stat-value {
        margin-top: 5px;
        color: #172033;
        font-size: 23px;
        font-weight: 800;
    }

    .report-stat-icon {
        width: 46px;
        height: 46px;
        flex: 0 0 46px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 13px;
        font-size: 20px;
    }

    .report-icon-blue {
        color: #2563eb;
        background: #eff6ff;
    }

    .report-icon-red {
        color: #dc2626;
        background: #fef2f2;
    }

    .report-icon-green {
        color: #16a34a;
        background: #f0fdf4;
    }

    .report-icon-purple {
        color: #7c3aed;
        background: #f5f3ff;
    }

    .chart-box {
        position: relative;
        min-height: 310px;
    }

    .report-health {
        min-width: 98px;
        text-align: center;
    }

    @media (max-width: 767px) {
        .report-heading {
            align-items: flex-start;
            flex-direction: column;
        }

        .report-heading .btn {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')

@php
    $eventReports = $eventReports ?? [];
    $sponsorRanking = $sponsorRanking ?? [];
    $userRanking = $userRanking ?? [];
    $monthlyCashflow = $monthlyCashflow ?? [];
    $years = $years ?? [];

    $year = $year ?? '';
    $status = $status ?? '';
    $health = $health ?? '';
    $sort = $sort ?? 'rank';
    $direction = strtoupper($direction ?? 'ASC');

    $summary = $summary ?? (object) [
        'total_events' => 0,
        'total_allocated' => 0,
        'total_expense' => 0,
        'total_income' => 0,
        'total_remaining' => 0,
        'average_utilization' => 0,
        'over_budget_events' => 0,
        'critical_events' => 0,
    ];

    $certificateSummary = $certificateSummary ?? (object) [
        'total_certificates' => 0,
        'issued_certificates' => 0,
        'revoked_certificates' => 0,
    ];

    $notificationSummary = $notificationSummary ?? (object) [
        'total_notifications' => 0,
        'unread_notifications' => 0,
        'read_notifications' => 0,
    ];

    $monthlyLabels = $monthlyLabels ?? [];
    $monthlyIncome = $monthlyIncome ?? [];
    $monthlyExpense = $monthlyExpense ?? [];
    $monthlyCashflowValues = $monthlyCashflowValues ?? [];

    $healthCounts = $healthCounts ?? [
        'HEALTHY' => 0,
        'WARNING' => 0,
        'CRITICAL' => 0,
        'OVER_BUDGET' => 0,
        'NO_BUDGET' => 0,
    ];
@endphp

<div class="container-fluid px-0">

    <div class="report-heading">
        <div>
            <h2>Financial and Operational Reports</h2>

            <p>
                Review event finance, sponsorship and user engagement data.
            </p>
        </div>

        @if(\Illuminate\Support\Facades\Route::has(
            'admin.reports.export.finance'
        ))
            <a href="{{ route(
                'admin.reports.export.finance',
                request()->query()
            ) }}"
               class="btn btn-success">

                <i class="bi bi-file-earmark-spreadsheet me-1"></i>
                Export Finance CSV
            </a>
        @endif
    </div>

    {{-- Filters --}}
    <form method="GET"
          action="{{ route('admin.reports.index') }}"
          class="card mb-4">

        <div class="card-body">
            <div class="row g-3">

                <div class="col-xl-2 col-md-4">
                    <label class="form-label">
                        Year
                    </label>

                    <select name="year"
                            class="form-select">

                        <option value="">
                            All Years
                        </option>

                        @foreach($years as $yearOption)
                            <option
                                value="{{ $yearOption->year_value }}"
                                @selected(
                                    (string) $year ===
                                    (string) $yearOption->year_value
                                )>

                                {{ $yearOption->year_value }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xl-2 col-md-4">
                    <label class="form-label">
                        Event Status
                    </label>

                    <select name="status"
                            class="form-select">

                        <option value="">
                            All Statuses
                        </option>

                        @foreach([
                            'draft',
                            'published',
                            'upcoming',
                            'ongoing',
                            'completed',
                            'cancelled'
                        ] as $value)

                            <option value="{{ $value }}"
                                @selected($status === $value)>

                                {{ ucfirst($value) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xl-2 col-md-4">
                    <label class="form-label">
                        Financial Health
                    </label>

                    <select name="health"
                            class="form-select">

                        <option value="">
                            All Levels
                        </option>

                        @foreach([
                            'HEALTHY',
                            'WARNING',
                            'CRITICAL',
                            'OVER_BUDGET',
                            'NO_BUDGET'
                        ] as $value)

                            <option value="{{ $value }}"
                                @selected($health === $value)>

                                {{ ucwords(
                                    strtolower(
                                        str_replace('_', ' ', $value)
                                    )
                                ) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xl-2 col-md-4">
                    <label class="form-label">
                        Sort By
                    </label>

                    <select name="sort"
                            class="form-select">

                        <option value="rank"
                            @selected($sort === 'rank')>
                            Expense Rank
                        </option>

                        <option value="event"
                            @selected($sort === 'event')>
                            Event Name
                        </option>

                        <option value="allocated"
                            @selected($sort === 'allocated')>
                            Allocated
                        </option>

                        <option value="expense"
                            @selected($sort === 'expense')>
                            Expense
                        </option>

                        <option value="income"
                            @selected($sort === 'income')>
                            Income
                        </option>

                        <option value="remaining"
                            @selected($sort === 'remaining')>
                            Remaining
                        </option>

                        <option value="utilization"
                            @selected($sort === 'utilization')>
                            Utilization
                        </option>
                    </select>
                </div>

                <div class="col-xl-2 col-md-4">
                    <label class="form-label">
                        Direction
                    </label>

                    <select name="direction"
                            class="form-select">

                        <option value="asc"
                            @selected($direction === 'ASC')>
                            Ascending
                        </option>

                        <option value="desc"
                            @selected($direction === 'DESC')>
                            Descending
                        </option>
                    </select>
                </div>

                <div class="col-xl-2 col-md-4 d-flex align-items-end gap-2">

                    <button type="submit"
                            class="btn btn-primary flex-grow-1">

                        <i class="bi bi-funnel me-1"></i>
                        Apply
                    </button>

                    <a href="{{ route('admin.reports.index') }}"
                       class="btn btn-outline-secondary"
                       title="Reset filters">

                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>

            </div>
        </div>
    </form>

    {{-- Summary cards --}}
    <div class="row g-4 mb-4">

        <div class="col-xl-3 col-md-6">
            <div class="report-stat">
                <div>
                    <div class="report-stat-label">
                        Total Allocated
                    </div>

                    <div class="report-stat-value">
                        ৳{{ number_format(
                            (float) ($summary->total_allocated ?? 0),
                            2
                        ) }}
                    </div>
                </div>

                <div class="report-stat-icon report-icon-blue">
                    <i class="bi bi-wallet2"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="report-stat">
                <div>
                    <div class="report-stat-label">
                        Net Expense
                    </div>

                    <div class="report-stat-value text-danger">
                        ৳{{ number_format(
                            (float) ($summary->total_expense ?? 0),
                            2
                        ) }}
                    </div>
                </div>

                <div class="report-stat-icon report-icon-red">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="report-stat">
                <div>
                    <div class="report-stat-label">
                        Total Income
                    </div>

                    <div class="report-stat-value text-success">
                        ৳{{ number_format(
                            (float) ($summary->total_income ?? 0),
                            2
                        ) }}
                    </div>
                </div>

                <div class="report-stat-icon report-icon-green">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="report-stat">
                <div>
                    <div class="report-stat-label">
                        Remaining Budget
                    </div>

                    <div class="report-stat-value">
                        ৳{{ number_format(
                            (float) ($summary->total_remaining ?? 0),
                            2
                        ) }}
                    </div>
                </div>

                <div class="report-stat-icon report-icon-purple">
                    <i class="bi bi-piggy-bank"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- Smaller metrics --}}
    <div class="row g-4 mb-4">

        <div class="col-lg-3 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small">
                        Total Events
                    </div>

                    <h3 class="fw-bold mt-2 mb-0">
                        {{ $summary->total_events ?? 0 }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small">
                        Average Utilization
                    </div>

                    <h3 class="fw-bold mt-2 mb-0">
                        {{ number_format(
                            (float) (
                                $summary->average_utilization ?? 0
                            ),
                            2
                        ) }}%
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small">
                        Critical Events
                    </div>

                    <h3 class="fw-bold text-warning mt-2 mb-0">
                        {{ $summary->critical_events ?? 0 }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small">
                        Over-Budget Events
                    </div>

                    <h3 class="fw-bold text-danger mt-2 mb-0">
                        {{ $summary->over_budget_events ?? 0 }}
                    </h3>
                </div>
            </div>
        </div>

    </div>

    {{-- Charts --}}
    <div class="row g-4 mb-4">

        <div class="col-xl-8">
            <div class="card h-100">

                <div class="card-header">
                    <h5 class="mb-0">
                        Monthly Cash Flow
                    </h5>
                </div>

                <div class="card-body">
                    <div class="chart-box">
                        <canvas id="cashflowChart"></canvas>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-xl-4">
            <div class="card h-100">

                <div class="card-header">
                    <h5 class="mb-0">
                        Financial Health
                    </h5>
                </div>

                <div class="card-body">
                    <div class="chart-box">
                        <canvas id="healthChart"></canvas>
                    </div>
                </div>

            </div>
        </div>

    </div>

    {{-- Event finance table --}}
    <div class="card mb-4">

        <div class="card-header justify-content-between">
            <h5 class="mb-0">
                Event Financial Report
            </h5>

            <span class="badge bg-primary">
                {{ count($eventReports) }} events
            </span>
        </div>

        <div class="table-responsive">

            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Event</th>
                        <th>Allocated</th>
                        <th>Expense</th>
                        <th>Income</th>
                        <th>Remaining</th>
                        <th>Utilization</th>
                        <th>Health</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($eventReports as $report)

                        @php
                            $financialHealth = strtoupper(
                                (string) (
                                    $report->financial_health
                                    ?? 'NO_BUDGET'
                                )
                            );

                            $healthClass = match($financialHealth) {
                                'HEALTHY' => 'bg-success',
                                'WARNING' => 'bg-warning text-dark',
                                'CRITICAL' => 'bg-danger',
                                'OVER_BUDGET' => 'bg-dark',
                                default => 'bg-secondary',
                            };
                        @endphp

                        <tr>
                            <td class="fw-bold">
                                #{{ $report->expense_rank ?? 0 }}
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $report->event_title
                                        ?? 'Unknown Event' }}
                                </div>

                                <small class="text-muted">
                                    {{ $report->start_time ?? 'N/A' }}
                                </small>
                            </td>

                            <td>
                                ৳{{ number_format(
                                    (float) (
                                        $report->total_allocated ?? 0
                                    ),
                                    2
                                ) }}
                            </td>

                            <td class="text-danger fw-semibold">
                                ৳{{ number_format(
                                    (float) (
                                        $report->net_expense ?? 0
                                    ),
                                    2
                                ) }}
                            </td>

                            <td class="text-success fw-semibold">
                                ৳{{ number_format(
                                    (float) (
                                        $report->total_income ?? 0
                                    ),
                                    2
                                ) }}
                            </td>

                            <td class="{{
                                (float) (
                                    $report->remaining_budget ?? 0
                                ) < 0
                                    ? 'text-danger fw-semibold'
                                    : 'text-primary fw-semibold'
                            }}">
                                ৳{{ number_format(
                                    (float) (
                                        $report->remaining_budget ?? 0
                                    ),
                                    2
                                ) }}
                            </td>

                            <td>
                                {{ number_format(
                                    (float) (
                                        $report
                                            ->utilization_percentage
                                        ?? 0
                                    ),
                                    2
                                ) }}%
                            </td>

                            <td>
                                <span class="badge report-health {{
                                    $healthClass
                                }}">
                                    {{ ucwords(
                                        strtolower(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $financialHealth
                                            )
                                        )
                                    ) }}
                                </span>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="8"
                                class="text-center py-5">

                                <i class="bi bi-bar-chart fs-1 text-muted"></i>

                                <h5 class="mt-3">
                                    No financial report data
                                </h5>

                                <p class="text-muted mb-0">
                                    Add budgets and payments to generate reports.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>

    {{-- Rankings --}}
    <div class="row g-4 mb-4">

        <div class="col-xl-6">
            <div class="card h-100">

                <div class="card-header">
                    <h5 class="mb-0">
                        Sponsor Ranking
                    </h5>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">

                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Sponsor</th>
                                <th>Events</th>
                                <th>Confirmed</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($sponsorRanking as $sponsor)
                                <tr>
                                    <td class="fw-bold">
                                        #{{ $sponsor->sponsor_rank ?? 0 }}
                                    </td>

                                    <td>
                                        <div class="fw-semibold">
                                            {{ $sponsor->sponsor_name
                                                ?? 'Unknown Sponsor' }}
                                        </div>

                                        <small class="text-muted">
                                            {{ ucfirst(
                                                $sponsor->sponsor_type
                                                ?? 'N/A'
                                            ) }}
                                        </small>
                                    </td>

                                    <td>
                                        {{ $sponsor->event_count ?? 0 }}
                                    </td>

                                    <td class="text-success fw-semibold">
                                        ৳{{ number_format(
                                            (float) (
                                                $sponsor
                                                    ->confirmed_amount
                                                ?? 0
                                            ),
                                            2
                                        ) }}
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="4"
                                        class="text-center py-4 text-muted">
                                        No sponsor data available.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card h-100">

                <div class="card-header">
                    <h5 class="mb-0">
                        User Engagement Ranking
                    </h5>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">

                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>User</th>
                                <th>Points</th>
                                <th>Clubs</th>
                                <th>Certificates</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($userRanking as $user)
                                <tr>
                                    <td class="fw-bold">
                                        #{{ $user->engagement_rank ?? 0 }}
                                    </td>

                                    <td>
                                        <div class="fw-semibold">
                                            {{ $user->user_name
                                                ?? 'Unknown User' }}
                                        </div>

                                        <small class="text-muted">
                                            {{ $user->role_name
                                                ?? 'No Role' }}
                                        </small>
                                    </td>

                                    <td class="text-primary fw-semibold">
                                        {{ number_format(
                                            $user->engagement_points ?? 0
                                        ) }}
                                    </td>

                                    <td>
                                        {{ $user->club_count ?? 0 }}
                                    </td>

                                    <td>
                                        {{ $user->certificate_count ?? 0 }}
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="5"
                                        class="text-center py-4 text-muted">
                                        No user ranking data available.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- System summaries --}}
    <div class="row g-4">

        <div class="col-md-6">
            <div class="card h-100">

                <div class="card-header">
                    <h5 class="mb-0">
                        Certificate Summary
                    </h5>
                </div>

                <div class="card-body">
                    <div class="row text-center">

                        <div class="col-4">
                            <h3 class="fw-bold">
                                {{ $certificateSummary
                                    ->total_certificates ?? 0 }}
                            </h3>

                            <small class="text-muted">
                                Total
                            </small>
                        </div>

                        <div class="col-4">
                            <h3 class="fw-bold text-success">
                                {{ $certificateSummary
                                    ->issued_certificates ?? 0 }}
                            </h3>

                            <small class="text-muted">
                                Issued
                            </small>
                        </div>

                        <div class="col-4">
                            <h3 class="fw-bold text-danger">
                                {{ $certificateSummary
                                    ->revoked_certificates ?? 0 }}
                            </h3>

                            <small class="text-muted">
                                Revoked
                            </small>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">

                <div class="card-header">
                    <h5 class="mb-0">
                        Notification Summary
                    </h5>
                </div>

                <div class="card-body">
                    <div class="row text-center">

                        <div class="col-4">
                            <h3 class="fw-bold">
                                {{ $notificationSummary
                                    ->total_notifications ?? 0 }}
                            </h3>

                            <small class="text-muted">
                                Total
                            </small>
                        </div>

                        <div class="col-4">
                            <h3 class="fw-bold text-warning">
                                {{ $notificationSummary
                                    ->unread_notifications ?? 0 }}
                            </h3>

                            <small class="text-muted">
                                Unread
                            </small>
                        </div>

                        <div class="col-4">
                            <h3 class="fw-bold text-success">
                                {{ $notificationSummary
                                    ->read_notifications ?? 0 }}
                            </h3>

                            <small class="text-muted">
                                Read
                            </small>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js/dist/chart.umd.min.js"></script>

<script type="application/json"
        id="reportChartData">
{!! json_encode(
    [
        'monthlyLabels' =>
            $monthlyLabels,

        'monthlyIncome' =>
            $monthlyIncome,

        'monthlyExpense' =>
            $monthlyExpense,

        'monthlyCashflow' =>
            $monthlyCashflowValues,

        'healthCounts' =>
            array_values($healthCounts),
    ],
    JSON_HEX_TAG
    | JSON_HEX_APOS
    | JSON_HEX_AMP
    | JSON_HEX_QUOT
) !!}
</script>

<script>
    document.addEventListener(
        'DOMContentLoaded',
        function () {
            const dataElement =
                document.getElementById(
                    'reportChartData'
                );

            if (
                !dataElement ||
                typeof Chart === 'undefined'
            ) {
                return;
            }

            let reportData;

            try {
                reportData = JSON.parse(
                    dataElement.textContent
                );
            } catch (error) {
                console.error(
                    'Unable to parse report chart data.',
                    error
                );

                return;
            }

            const cashflowCanvas =
                document.getElementById(
                    'cashflowChart'
                );

            const healthCanvas =
                document.getElementById(
                    'healthChart'
                );

            if (cashflowCanvas) {
                new Chart(cashflowCanvas, {
                    type: 'line',

                    data: {
                        labels:
                            reportData.monthlyLabels || [],

                        datasets: [
                            {
                                label: 'Income',
                                data:
                                    reportData.monthlyIncome
                                    || [],
                                borderColor: '#16a34a',
                                backgroundColor:
                                    'rgba(22, 163, 74, 0.08)',
                                borderWidth: 2,
                                tension: 0.3
                            },
                            {
                                label: 'Expense',
                                data:
                                    reportData.monthlyExpense
                                    || [],
                                borderColor: '#dc2626',
                                backgroundColor:
                                    'rgba(220, 38, 38, 0.08)',
                                borderWidth: 2,
                                tension: 0.3
                            },
                            {
                                label: 'Net Cashflow',
                                data:
                                    reportData.monthlyCashflow
                                    || [],
                                borderColor: '#2563eb',
                                backgroundColor:
                                    'rgba(37, 99, 235, 0.08)',
                                borderWidth: 2,
                                tension: 0.3
                            }
                        ]
                    },

                    options: {
                        responsive: true,
                        maintainAspectRatio: false,

                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        },

                        scales: {
                            y: {
                                beginAtZero: true,

                                ticks: {
                                    callback: function (value) {
                                        return '৳'
                                            + Number(value)
                                                .toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            }

            if (healthCanvas) {
                new Chart(healthCanvas, {
                    type: 'doughnut',

                    data: {
                        labels: [
                            'Healthy',
                            'Warning',
                            'Critical',
                            'Over Budget',
                            'No Budget'
                        ],

                        datasets: [
                            {
                                data:
                                    reportData.healthCounts
                                    || [],

                                backgroundColor: [
                                    '#16a34a',
                                    '#f59e0b',
                                    '#dc2626',
                                    '#111827',
                                    '#94a3b8'
                                ],

                                borderWidth: 0
                            }
                        ]
                    },

                    options: {
                        responsive: true,
                        maintainAspectRatio: false,

                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        }
    );
</script>

@endpush