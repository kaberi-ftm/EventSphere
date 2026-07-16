<form method="POST" action="{{ $action }}">
    @csrf

    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="row g-3">

        <div class="col-md-6">
            <label class="form-label">
                Event <span class="text-danger">*</span>
            </label>

            <select name="event_id"
                    class="form-select"
                    required>

                <option value="">Select Event</option>

                @foreach($events as $event)
                    <option value="{{ $event->id }}"
                        @selected(
                            old(
                                'event_id',
                                $budget->event_id ?? ''
                            ) == $event->id
                        )>
                        {{ $event->title }}
                        — {{ $event->start_time }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">
                Category <span class="text-danger">*</span>
            </label>

            <select name="category"
                    class="form-select"
                    required>

                @foreach([
                    'venue',
                    'food',
                    'marketing',
                    'transport',
                    'equipment',
                    'decoration',
                    'security',
                    'miscellaneous'
                ] as $value)

                    <option value="{{ $value }}"
                        @selected(
                            old(
                                'category',
                                $budget->category ?? 'miscellaneous'
                            ) === $value
                        )>
                        {{ ucfirst($value) }}
                    </option>

                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">
                Allocated Amount <span class="text-danger">*</span>
            </label>

            <div class="input-group">
                <span class="input-group-text">৳</span>

                <input type="number"
                       name="allocated_amount"
                       step="0.01"
                       min="0"
                       class="form-control"
                       value="{{ old(
                           'allocated_amount',
                           $budget->allocated_amount ?? ''
                       ) }}"
                       required>
            </div>
        </div>

        <div class="col-md-6">
            <label class="form-label">
                Status <span class="text-danger">*</span>
            </label>

            <select name="status"
                    class="form-select"
                    required>

                @foreach([
                    'planned',
                    'approved',
                    'closed',
                    'cancelled'
                ] as $value)

                    <option value="{{ $value }}"
                        @selected(
                            old(
                                'status',
                                $budget->status ?? 'planned'
                            ) === $value
                        )>
                        {{ ucfirst($value) }}
                    </option>

                @endforeach
            </select>
        </div>

        <div class="col-12">
            <label class="form-label">
                Description
            </label>

            <textarea name="description"
                      rows="4"
                      maxlength="500"
                      class="form-control"
                      placeholder="Enter budget purpose or details">{{ old(
                          'description',
                          $budget->description ?? ''
                      ) }}</textarea>
        </div>

        <div class="col-12">
            <hr>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.budgets.index') }}"
                   class="btn btn-outline-secondary">
                    Cancel
                </a>

                <button type="submit"
                        class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>
                    Save Budget
                </button>
            </div>
        </div>

    </div>
</form>