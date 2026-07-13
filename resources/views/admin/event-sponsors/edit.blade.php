@extends('layouts.admin')

@section('content')
<div class="container-fluid py-3">
    <div class="card shadow-sm">
        <div class="card-header">
            <h4>Edit Event Sponsorship</h4>
        </div>

        <div class="card-body">
            @include('admin.event-sponsors.form', [
                'action' => route(
                    'admin.event-sponsors.update',
                    $eventSponsor->id
                ),
                'method' => 'PUT',
                'eventSponsor' => $eventSponsor
            ])
        </div>
    </div>
</div>
@endsection