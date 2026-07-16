@extends('layouts.admin')

@section('title', 'Record Payment')
@section('page-title', 'Record Payment')

@section('content')
<div class="container-fluid px-0">

    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-11">

            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 class="mb-1">
                        <i class="bi bi-credit-card me-2"></i>
                        New Event Payment
                    </h4>

                    <p class="text-muted mb-0">
                        Record an expense, income or refund.
                    </p>
                </div>

                <div class="card-body">
                    @include('admin.payments.form', [
                        'action' => route('admin.payments.store'),
                        'method' => 'POST',
                        'payment' => null
                    ])
                </div>
            </div>

        </div>
    </div>

</div>
@endsection