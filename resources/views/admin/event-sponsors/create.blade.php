@extends('layouts.admin')

@section('content')
<div class="container-fluid py-3">
    <div class="card shadow-sm">
        <div class="card-header">
            <h4>Add Event Sponsorship</h4>
        </div>

        <div class="card-body">
            @include('admin.event-sponsors.form', [
                'action' => route('admin.event-sponsors.store'),
                'method' => 'POST',
                'eventSponsor' => null
            ])
        </div>
    </div>
</div>
@endsection