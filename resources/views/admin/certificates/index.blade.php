@extends('layouts.admin')

@section('title', 'Certificates')
@section('page-title', 'Certificate Management')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Certificates</h2>
            <p class="text-muted mb-0">
                Issue, revoke and verify event certificates.
            </p>
        </div>

        <a href="{{ route('admin.certificates.create') }}"
           class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>
            Issue Certificate
        </a>
    </div>

    <form method="GET"
          class="card shadow-sm mb-4">

        <div class="card-body">
            <div class="row g-3">

                <div class="col-lg-4">
                    <input name="search"
                           value="{{ $search }}"
                           class="form-control"
                           placeholder="Number, recipient, event or code">
                </div>

                <div class="col-lg-2">
                    <select name="type" class="form-select">
                        <option value="">All Types</option>

                        @foreach([
                            'participation',
                            'volunteer',
                            'achievement',
                            'organizer',
                            'winner'
                        ] as $value)
                            <option value="{{ $value }}"
                                @selected($type === $value)>
                                {{ ucfirst($value) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>

                        <option value="issued"
                            @selected($status === 'issued')>
                            Issued
                        </option>

                        <option value="revoked"
                            @selected($status === 'revoked')>
                            Revoked
                        </option>
                    </select>
                </div>

                <div class="col-lg-2">
                    <select name="sort" class="form-select">
                        <option value="issued"
                            @selected($sort === 'issued')>
                            Issue Date
                        </option>

                        <option value="recipient"
                            @selected($sort === 'recipient')>
                            Recipient
                        </option>

                        <option value="event"
                            @selected($sort === 'event')>
                            Event
                        </option>

                        <option value="number"
                            @selected($sort === 'number')>
                            Number
                        </option>
                    </select>
                </div>

                <div class="col-lg-2 d-flex gap-2">
                    <button class="btn btn-dark flex-grow-1">
                        Apply
                    </button>

                    <a href="{{ route('admin.certificates.index') }}"
                       class="btn btn-outline-secondary">
                        Reset
                    </a>
                </div>

            </div>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">

            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Certificate</th>
                        <th>Recipient</th>
                        <th>Event</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Issued</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($certificates as $certificate)
                        <tr>
                            <td>
                                <div class="fw-semibold">
                                    {{ $certificate->certificate_number }}
                                </div>

                                <small class="text-muted">
                                    {{ $certificate->verification_code }}
                                </small>
                            </td>

                            <td>
                                {{ $certificate->recipient_name }}
                                <br>
                                <small class="text-muted">
                                    {{ $certificate->recipient_email }}
                                </small>
                            </td>

                            <td>{{ $certificate->event_title }}</td>

                            <td>
                                {{ ucfirst(
                                    $certificate->certificate_type
                                ) }}
                            </td>

                            <td>
                                <span class="badge {{
                                    $certificate->status === 'issued'
                                        ? 'bg-success'
                                        : 'bg-danger'
                                }}">
                                    {{ ucfirst($certificate->status) }}
                                </span>
                            </td>

                            <td>{{ $certificate->issued_at }}</td>

                            <td class="text-end text-nowrap">
                                <a href="{{ route(
                                    'admin.certificates.show',
                                    $certificate->id
                                ) }}"
                                   class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <a href="{{ route(
                                    'admin.certificates.edit',
                                    $certificate->id
                                ) }}"
                                   class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <form method="POST"
                                      action="{{ route(
                                          'admin.certificates.destroy',
                                          $certificate->id
                                      ) }}"
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-sm btn-danger"
                                            onclick="return confirm(
                                                'Delete this certificate?'
                                            )">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="7"
                                class="text-center py-5">
                                No certificates found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>

</div>
@endsection