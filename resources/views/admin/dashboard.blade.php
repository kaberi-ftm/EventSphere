@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    .dashboard-header {
        margin-bottom: 24px;
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
    }

    .dashboard-header h2 {
        margin: 0;
        color: #172033;
        font-size: 26px;
        font-weight: 800;
        letter-spacing: -0.025em;
    }

    .dashboard-header p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 14px;
    }

    .dashboard-date {
        color: #64748b;
        font-size: 13px;
        font-weight: 600;
    }

    .dashboard-stat {
        height: 100%;
        padding: 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        border: 1px solid #e2e8f0;
        border-radius: 15px;
        background: #ffffff;
        box-shadow:
            0 5px 18px rgba(15,23,42,0.04);
    }

    .dashboard-stat-label {
        color: #64748b;
        font-size: 13px;
        font-weight: 600;
    }

    .dashboard-stat-value {
        margin-top: 4px;
        color: #172033;
        font-size: 29px;
        font-weight: 800;
        line-height: 1.1;
    }

    .dashboard-stat-icon {
        width: 49px;
        height: 49px;
        flex: 0 0 49px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        font-size: 21px;
    }

    .icon-blue {
        color: #2563eb;
        background: #eff6ff;
    }

    .icon-purple {
        color: #7c3aed;
        background: #f5f3ff;
    }

    .icon-green {
        color: #16a34a;
        background: #f0fdf4;
    }

    .icon-orange {
        color: #ea580c;
        background: #fff7ed;
    }

    .welcome-panel {
        position: relative;
        overflow: hidden;
        height: 100%;
        padding: 29px;
        border-radius: 17px;
        color: #ffffff;
        background:
            linear-gradient(
                135deg,
                #1d4ed8,
                #2563eb
            );
        box-shadow:
            0 14px 30px rgba(37,99,235,0.18);
    }

    .welcome-panel::after {
        position: absolute;
        right: -55px;
        bottom: -80px;
        width: 210px;
        height: 210px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 50%;
        content: "";
    }

    .welcome-panel h3 {
        position: relative;
        z-index: 2;
        margin: 0;
        font-size: 23px;
        font-weight: 800;
    }

    .welcome-panel p {
        position: relative;
        z-index: 2;
        max-width: 520px;
        margin: 10px 0 0;
        color: rgba(255,255,255,0.78);
        font-size: 14px;
    }

    .welcome-badge {
        position: relative;
        z-index: 2;
        margin-top: 24px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 11px;
        border: 1px solid rgba(255,255,255,0.16);
        border-radius: 999px;
        color: #ffffff;
        background: rgba(255,255,255,0.1);
        font-size: 12px;
        font-weight: 700;
    }

    .welcome-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #86efac;
    }

    .quick-card {
        height: 100%;
        padding: 23px;
        border: 1px solid #e2e8f0;
        border-radius: 17px;
        background: #ffffff;
        box-shadow:
            0 5px 18px rgba(15,23,42,0.04);
    }

    .quick-card h4 {
        margin: 0 0 17px;
        color: #172033;
        font-size: 17px;
        font-weight: 800;
    }

    .quick-actions {
        display: grid;
        gap: 10px;
    }

    .quick-action {
        min-height: 48px;
        padding: 11px 13px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #344054;
        border: 1px solid #e2e8f0;
        border-radius: 11px;
        background: #fbfcfe;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: 180ms ease;
    }

    .quick-action:hover {
        color: #2563eb;
        background: #eff6ff;
        border-color: #bfdbfe;
        transform: translateX(2px);
    }

    .quick-action-icon {
        width: 31px;
        height: 31px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        color: #2563eb;
        background: #eaf2ff;
        font-size: 15px;
    }

    .system-card {
        margin-top: 24px;
        padding: 21px 23px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        border: 1px solid #e2e8f0;
        border-radius: 15px;
        background: #ffffff;
    }

    .system-card h5 {
        margin: 0;
        color: #172033;
        font-size: 15px;
        font-weight: 800;
    }

    .system-card p {
        margin: 5px 0 0;
        color: #64748b;
        font-size: 13px;
    }

    .system-status {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 11px;
        color: #166534;
        background: #dcfce7;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }

    .system-status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #16a34a;
    }

    @media (max-width: 767px) {
        .dashboard-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .system-card {
            align-items: flex-start;
            flex-direction: column;
        }
    }
</style>
@endpush

@section('content')

@php
    $clubsTotal =
        $totalClubs
        ?? $clubCount
        ?? $clubsCount
        ?? 0;

    $eventsTotal =
        $totalEvents
        ?? $eventCount
        ?? $eventsCount
        ?? 0;

    $usersTotal =
        $totalUsers
        ?? $userCount
        ?? $usersCount
        ?? 0;

    $volunteersTotal =
        $totalVolunteers
        ?? $volunteerCount
        ?? $volunteersCount
        ?? 0;
@endphp

<div class="container-fluid px-0">

    <div class="dashboard-header">
        <div>
            <h2>Dashboard Overview</h2>

            <p>
                Welcome back,
                {{ auth()->user()?->name ?? 'Administrator' }}.
            </p>
        </div>

        <div class="dashboard-date">
            {{ now()->format('l, d F Y') }}
        </div>
    </div>

    <div class="row g-4 mb-4">

        <div class="col-xl-3 col-md-6">
            <div class="dashboard-stat">
                <div>
                    <div class="dashboard-stat-label">
                        Total Clubs
                    </div>

                    <div class="dashboard-stat-value">
                        {{ number_format($clubsTotal) }}
                    </div>
                </div>

                <div class="dashboard-stat-icon icon-blue">
                    <i class="bi bi-buildings"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="dashboard-stat">
                <div>
                    <div class="dashboard-stat-label">
                        Total Events
                    </div>

                    <div class="dashboard-stat-value">
                        {{ number_format($eventsTotal) }}
                    </div>
                </div>

                <div class="dashboard-stat-icon icon-purple">
                    <i class="bi bi-calendar-event"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="dashboard-stat">
                <div>
                    <div class="dashboard-stat-label">
                        Total Users
                    </div>

                    <div class="dashboard-stat-value">
                        {{ number_format($usersTotal) }}
                    </div>
                </div>

                <div class="dashboard-stat-icon icon-green">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="dashboard-stat">
                <div>
                    <div class="dashboard-stat-label">
                        Volunteers
                    </div>

                    <div class="dashboard-stat-value">
                        {{ number_format($volunteersTotal) }}
                    </div>
                </div>

                <div class="dashboard-stat-icon icon-orange">
                    <i class="bi bi-person-check"></i>
                </div>
            </div>
        </div>

    </div>

    <div class="row g-4">

        <div class="col-xl-8">

            <div class="welcome-panel">
                <h3>
                    Welcome to EventSphere
                </h3>

                <p>
                    Manage events, users, clubs, registrations,
                    volunteers, sponsors and financial activities
                    from your administration dashboard.
                </p>

                <div class="welcome-badge">
                    <span class="welcome-dot"></span>
                    System is operating normally
                </div>
            </div>

        </div>

        <div class="col-xl-4">

            <div class="quick-card">
                <h4>Quick Actions</h4>

                <div class="quick-actions">

                    @if(\Illuminate\Support\Facades\Route::has(
                        'admin.users.create'
                    ))
                        <a href="{{ route(
                            'admin.users.create'
                        ) }}"
                           class="quick-action">

                            <span class="quick-action-icon">
                                <i class="bi bi-person-plus"></i>
                            </span>

                            Add New User
                        </a>
                    @endif

                    @if(\Illuminate\Support\Facades\Route::has(
                        'events.create'
                    ))
                        <a href="{{ route(
                            'events.create'
                        ) }}"
                           class="quick-action">

                            <span class="quick-action-icon">
                                <i class="bi bi-calendar-plus"></i>
                            </span>

                            Create Event
                        </a>
                    @elseif(\Illuminate\Support\Facades\Route::has(
                        'admin.events.create'
                    ))
                        <a href="{{ route(
                            'admin.events.create'
                        ) }}"
                           class="quick-action">

                            <span class="quick-action-icon">
                                <i class="bi bi-calendar-plus"></i>
                            </span>

                            Create Event
                        </a>
                    @endif

                    @if(\Illuminate\Support\Facades\Route::has(
                        'admin.budgets.create'
                    ))
                        <a href="{{ route(
                            'admin.budgets.create'
                        ) }}"
                           class="quick-action">

                            <span class="quick-action-icon">
                                <i class="bi bi-wallet2"></i>
                            </span>

                            Create Budget
                        </a>
                    @endif

                    @if(\Illuminate\Support\Facades\Route::has(
                        'admin.reports.index'
                    ))
                        <a href="{{ route(
                            'admin.reports.index'
                        ) }}"
                           class="quick-action">

                            <span class="quick-action-icon">
                                <i class="bi bi-bar-chart"></i>
                            </span>

                            View Reports
                        </a>
                    @endif

                </div>
            </div>

        </div>

    </div>

    <div class="system-card">
        <div>
            <h5>EventSphere Administration</h5>

            <p>
                Laravel application connected with Oracle Database.
            </p>
        </div>

        <div class="system-status">
            <span class="system-status-dot"></span>
            Online
        </div>
    </div>

</div>
@endsection