@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ $action }}">
    @csrf

    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="mb-3">
        <label class="form-label">Event</label>

        <select name="event_id" class="form-select" required>
            <option value="">Select Event</option>

            @foreach($events as $event)
                <option value="{{ $event->id }}"
                    @selected(
                        old('event_id', $eventSponsor->event_id ?? '') == $event->id
                    )>
                    {{ $event->title }} — {{ $event->start_time }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Sponsor</label>

        <select name="sponsor_id" class="form-select" required>
            <option value="">Select Sponsor</option>

            @foreach($sponsors as $sponsor)
                <option value="{{ $sponsor->id }}"
                    @selected(
                        old('sponsor_id', $eventSponsor->sponsor_id ?? '') == $sponsor->id
                    )>
                    {{ $sponsor->name }} — {{ ucfirst($sponsor->sponsor_type) }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">Amount</label>

            <input type="number"
                   step="0.01"
                   min="0"
                   name="amount"
                   class="form-control"
                   value="{{ old('amount', $eventSponsor->amount ?? 0) }}"
                   required>
        </div>

        <div class="col-md-4 mb-3">
            <label class="form-label">Contribution Type</label>

            <select name="contribution_type"
                    class="form-select"
                    required>
                @foreach(['cash','in_kind','media','venue','service'] as $type)
                    <option value="{{ $type }}"
                        @selected(
                            old(
                                'contribution_type',
                                $eventSponsor->contribution_type ?? 'cash'
                            ) === $type
                        )>
                        {{ ucfirst(str_replace('_', ' ', $type)) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4 mb-3">
            <label class="form-label">Status</label>

            <select name="status" class="form-select" required>
                @foreach(['pledged','confirmed','paid','cancelled'] as $value)
                    <option value="{{ $value }}"
                        @selected(
                            old('status', $eventSponsor->status ?? 'pledged')
                                === $value
                        )>
                        {{ ucfirst($value) }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Agreement Date</label>

        <input type="date"
               name="agreement_date"
               class="form-control"
               value="{{ old(
                   'agreement_date',
                   isset($eventSponsor) && $eventSponsor->agreement_date
                       ? \Carbon\Carbon::parse($eventSponsor->agreement_date)
                           ->format('Y-m-d')
                       : now()->format('Y-m-d')
               ) }}"
               required>
    </div>

    <div class="mb-3">
        <label class="form-label">Notes</label>

        <textarea name="notes"
                  rows="4"
                  class="form-control">{{ old('notes', $eventSponsor->notes ?? '') }}</textarea>
    </div>

    <button class="btn btn-primary">Save Sponsorship</button>

    <a href="{{ route('admin.event-sponsors.index') }}"
       class="btn btn-secondary">
        Cancel
    </a>
</form>