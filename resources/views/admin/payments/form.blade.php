<form method="POST" action="{{ $action }}">
    @csrf

    @if($method !== 'POST')
        @method($method)
    @endif

    @php
        $selectedEvent = old(
            'event_id',
            $payment->event_id ?? request('event_id', '')
        );

        $selectedBudget = old(
            'budget_id',
            $payment->budget_id ?? request('budget_id', '')
        );

        $selectedType = old(
            'payment_type',
            $payment->payment_type ?? 'expense'
        );
    @endphp

    <div class="row g-3">

        <div class="col-md-6">
            <label class="form-label">
                Event <span class="text-danger">*</span>
            </label>

            <select name="event_id"
                    id="event_id"
                    class="form-select"
                    required>

                <option value="">Select Event</option>

                @foreach($events as $event)
                    <option value="{{ $event->id }}"
                        @selected(
                            (string) $selectedEvent ===
                            (string) $event->id
                        )>
                        {{ $event->title }} — {{ $event->start_time }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">
                Payment Type <span class="text-danger">*</span>
            </label>

            <select name="payment_type"
                    id="payment_type"
                    class="form-select"
                    required>

                @foreach(['expense', 'income', 'refund'] as $value)
                    <option value="{{ $value }}"
                        @selected($selectedType === $value)>
                        {{ ucfirst($value) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">
                Budget
                <span id="budgetRequiredText"
                      class="text-danger">
                    *
                </span>
            </label>

            <select name="budget_id"
                    id="budget_id"
                    class="form-select">

                <option value="">Select Budget</option>

                @foreach($budgets as $budget)
                    <option value="{{ $budget->id }}"
                            data-event-id="{{ $budget->event_id }}"
                        @selected(
                            (string) $selectedBudget ===
                            (string) $budget->id
                        )>

                        {{ $budget->event_title }}
                        — {{ ucfirst($budget->category) }}
                        — ৳{{ number_format(
                            (float) $budget->allocated_amount,
                            2
                        ) }}
                    </option>
                @endforeach
            </select>

            <small class="text-muted">
                Budget is mandatory for expense payments.
            </small>
        </div>

        <div class="col-md-6">
            <label class="form-label">
                Payee / Payer Name
                <span class="text-danger">*</span>
            </label>

            <input type="text"
                   name="payee_name"
                   class="form-control"
                   maxlength="150"
                   value="{{ old(
                       'payee_name',
                       $payment->payee_name ?? ''
                   ) }}"
                   required>
        </div>

        <div class="col-md-4">
            <label class="form-label">
                Amount <span class="text-danger">*</span>
            </label>

            <div class="input-group">
                <span class="input-group-text">৳</span>

                <input type="number"
                       name="amount"
                       step="0.01"
                       min="0.01"
                       class="form-control"
                       value="{{ old(
                           'amount',
                           $payment->amount ?? ''
                       ) }}"
                       required>
            </div>
        </div>

        <div class="col-md-4">
            <label class="form-label">
                Payment Method <span class="text-danger">*</span>
            </label>

            <select name="payment_method"
                    class="form-select"
                    required>

                @foreach([
                    'cash',
                    'bank',
                    'card',
                    'mobile_banking',
                    'cheque'
                ] as $value)

                    <option value="{{ $value }}"
                        @selected(
                            old(
                                'payment_method',
                                $payment->payment_method ?? 'cash'
                            ) === $value
                        )>

                        {{ ucwords(str_replace('_', ' ', $value)) }}
                    </option>

                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">
                Status <span class="text-danger">*</span>
            </label>

            <select name="status"
                    class="form-select"
                    required>

                @foreach([
                    'pending',
                    'approved',
                    'paid',
                    'cancelled'
                ] as $value)

                    <option value="{{ $value }}"
                        @selected(
                            old(
                                'status',
                                $payment->status ?? 'pending'
                            ) === $value
                        )>
                        {{ ucfirst($value) }}
                    </option>

                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">
                Payment Date <span class="text-danger">*</span>
            </label>

            <input type="date"
                   name="payment_date"
                   class="form-control"
                   value="{{ old(
                       'payment_date',
                       isset($payment) && $payment?->payment_date
                           ? \Carbon\Carbon::parse(
                               $payment->payment_date
                           )->format('Y-m-d')
                           : now()->format('Y-m-d')
                   ) }}"
                   required>
        </div>

        <div class="col-md-6">
            <label class="form-label">
                Reference Number
            </label>

            <input type="text"
                   name="reference_number"
                   class="form-control"
                   maxlength="100"
                   value="{{ old(
                       'reference_number',
                       $payment->reference_number ?? ''
                   ) }}"
                   placeholder="Bank, cheque or transaction reference">
        </div>

        <div class="col-12">
            <label class="form-label">Notes</label>

            <textarea name="notes"
                      rows="4"
                      class="form-control"
                      placeholder="Additional payment details">{{ old(
                          'notes',
                          $payment->notes ?? ''
                      ) }}</textarea>
        </div>

        <div class="col-12">
            <hr>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.payments.index') }}"
                   class="btn btn-outline-secondary">
                    Cancel
                </a>

                <button type="submit"
                        class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>
                    Save Payment
                </button>
            </div>
        </div>

    </div>
</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const eventSelect = document.getElementById('event_id');
        const typeSelect = document.getElementById('payment_type');
        const budgetSelect = document.getElementById('budget_id');
        const requiredText = document.getElementById(
            'budgetRequiredText'
        );

        function filterBudgets() {
            const selectedEvent = eventSelect.value;

            Array.from(budgetSelect.options).forEach(function (option) {
                if (!option.value) {
                    option.hidden = false;
                    return;
                }

                option.hidden =
                    option.dataset.eventId !== selectedEvent;
            });

            const selectedOption =
                budgetSelect.options[budgetSelect.selectedIndex];

            if (
                selectedOption &&
                selectedOption.value &&
                selectedOption.dataset.eventId !== selectedEvent
            ) {
                budgetSelect.value = '';
            }
        }

        function updateBudgetRequirement() {
            const isExpense = typeSelect.value === 'expense';

            budgetSelect.required = isExpense;
            requiredText.style.display = isExpense
                ? 'inline'
                : 'none';
        }

        eventSelect.addEventListener('change', filterBudgets);
        typeSelect.addEventListener(
            'change',
            updateBudgetRequirement
        );

        filterBudgets();
        updateBudgetRequirement();
    });
</script>
@endpush