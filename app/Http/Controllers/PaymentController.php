<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = trim((string) $request->query('status', ''));
        $type = trim((string) $request->query('type', ''));
        $eventId = $request->query('event_id');

        $sortMap = [
            'date' => 'p.payment_date',
            'amount' => 'p.amount',
            'payee' => 'p.payee_name',
            'event' => 'e.title',
            'status' => 'p.status',
        ];

        $sort = (string) $request->query('sort', 'date');
        $sortColumn = $sortMap[$sort] ?? 'p.payment_date';

        $direction = strtolower(
            (string) $request->query('direction', 'desc')
        ) === 'asc' ? 'ASC' : 'DESC';

        $conditions = ['1 = 1'];
        $bindings = [];

        if ($search !== '') {
            $keyword = '%' . strtolower($search) . '%';

            $conditions[] = "
                (
                    LOWER(p.payee_name) LIKE ?
                    OR LOWER(e.title) LIKE ?
                    OR LOWER(NVL(p.reference_number, '')) LIKE ?
                )
            ";

            array_push($bindings, $keyword, $keyword, $keyword);
        }

        if (in_array(
            $status,
            ['pending', 'approved', 'paid', 'cancelled'],
            true
        )) {
            $conditions[] = 'LOWER(p.status) = ?';
            $bindings[] = $status;
        }

        if (in_array(
            $type,
            ['expense', 'income', 'refund'],
            true
        )) {
            $conditions[] = 'LOWER(p.payment_type) = ?';
            $bindings[] = $type;
        }

        if ($eventId) {
            $conditions[] = 'p.event_id = ?';
            $bindings[] = $eventId;
        }

        $payments = DB::select("
            SELECT
                p.*,
                e.title AS event_title,
                b.category AS budget_category,
                SUM(
                    CASE
                        WHEN LOWER(p.payment_type) = 'expense'
                        AND LOWER(p.status) = 'paid'
                        THEN p.amount
                        ELSE 0
                    END
                ) OVER (
                    PARTITION BY p.event_id
                    ORDER BY p.payment_date, p.id
                ) AS running_expense
            FROM payments p
            JOIN events e
                ON p.event_id = e.id
            LEFT JOIN budgets b
                ON p.budget_id = b.id
            WHERE " . implode(' AND ', $conditions) . "
            ORDER BY {$sortColumn} {$direction}, p.id DESC
        ", $bindings);

        $events = DB::select("
            SELECT id, title
            FROM events
            ORDER BY start_time DESC
        ");

        return view('admin.payments.index', compact(
            'payments',
            'events',
            'search',
            'status',
            'type',
            'eventId',
            'sort',
            'direction'
        ));
    }

    public function create(): View
    {
        $events = DB::select("
            SELECT id, title, start_time
            FROM events
            WHERE LOWER(status) <> 'cancelled'
            ORDER BY start_time DESC
        ");

        $budgets = DB::select("
            SELECT
                b.id,
                b.event_id,
                b.category,
                b.allocated_amount,
                e.title AS event_title
            FROM budgets b
            JOIN events e
                ON b.event_id = e.id
            WHERE LOWER(b.status) IN ('planned', 'approved')
            ORDER BY e.title, b.category
        ");

        return view('admin.payments.create', compact(
            'events',
            'budgets'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePayment($request);

        $error = $this->validateBudgetSelection(
            $validated['event_id'],
            $validated['budget_id'] ?? null,
            $validated['payment_type'],
            $validated['amount'],
            $validated['status']
        );

        if ($error) {
            return back()
                ->withInput()
                ->with('error', $error);
        }

        try {
            DB::transaction(function () use ($validated) {
                DB::insert("
                    INSERT INTO payments
                    (
                        event_id,
                        budget_id,
                        payee_name,
                        payment_type,
                        amount,
                        payment_method,
                        reference_number,
                        payment_date,
                        status,
                        notes,
                        created_at,
                        updated_at
                    )
                    VALUES
                    (
                        ?, ?, ?, ?, ?, ?, ?,
                        TO_TIMESTAMP(?, 'YYYY-MM-DD'),
                        ?, ?,
                        SYSTIMESTAMP,
                        SYSTIMESTAMP
                    )
                ", [
                    $validated['event_id'],
                    $validated['budget_id'] ?? null,
                    $validated['payee_name'],
                    $validated['payment_type'],
                    $validated['amount'],
                    $validated['payment_method'],
                    $validated['reference_number'] ?? null,
                    $validated['payment_date'],
                    $validated['status'],
                    $validated['notes'] ?? null,
                ]);
            });
        } catch (QueryException $exception) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Payment could not be created. Reference number may already exist.'
                );
        }

        return redirect()
            ->route('admin.payments.index')
            ->with('success', 'Payment created successfully.');
    }

    public function show($id): View
    {
        $payment = DB::selectOne("
            SELECT
                p.*,
                e.title AS event_title,
                b.category AS budget_category,
                b.allocated_amount
            FROM payments p
            JOIN events e
                ON p.event_id = e.id
            LEFT JOIN budgets b
                ON p.budget_id = b.id
            WHERE p.id = ?
        ", [$id]);

        abort_if(!$payment, 404, 'Payment not found.');

        return view('admin.payments.show', compact('payment'));
    }

    public function edit($id): View
    {
        $payment = DB::selectOne("
            SELECT *
            FROM payments
            WHERE id = ?
        ", [$id]);

        abort_if(!$payment, 404, 'Payment not found.');

        $events = DB::select("
            SELECT id, title, start_time
            FROM events
            ORDER BY start_time DESC
        ");

        $budgets = DB::select("
            SELECT
                b.id,
                b.event_id,
                b.category,
                b.allocated_amount,
                e.title AS event_title
            FROM budgets b
            JOIN events e
                ON b.event_id = e.id
            ORDER BY e.title, b.category
        ");

        return view('admin.payments.edit', compact(
            'payment',
            'events',
            'budgets'
        ));
    }

    public function update(
        Request $request,
        $id
    ): RedirectResponse {
        $payment = DB::selectOne("
            SELECT id
            FROM payments
            WHERE id = ?
        ", [$id]);

        abort_if(!$payment, 404, 'Payment not found.');

        $validated = $this->validatePayment($request);

        $error = $this->validateBudgetSelection(
            $validated['event_id'],
            $validated['budget_id'] ?? null,
            $validated['payment_type'],
            $validated['amount'],
            $validated['status'],
            $id
        );

        if ($error) {
            return back()
                ->withInput()
                ->with('error', $error);
        }

        try {
            DB::update("
                UPDATE payments
                SET
                    event_id = ?,
                    budget_id = ?,
                    payee_name = ?,
                    payment_type = ?,
                    amount = ?,
                    payment_method = ?,
                    reference_number = ?,
                    payment_date =
                        TO_TIMESTAMP(?, 'YYYY-MM-DD'),
                    status = ?,
                    notes = ?,
                    updated_at = SYSTIMESTAMP
                WHERE id = ?
            ", [
                $validated['event_id'],
                $validated['budget_id'] ?? null,
                $validated['payee_name'],
                $validated['payment_type'],
                $validated['amount'],
                $validated['payment_method'],
                $validated['reference_number'] ?? null,
                $validated['payment_date'],
                $validated['status'],
                $validated['notes'] ?? null,
                $id,
            ]);
        } catch (QueryException $exception) {
            return back()
                ->withInput()
                ->with('error', 'Payment could not be updated.');
        }

        return redirect()
            ->route('admin.payments.index')
            ->with('success', 'Payment updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        DB::delete("
            DELETE FROM payments
            WHERE id = ?
        ", [$id]);

        return redirect()
            ->route('admin.payments.index')
            ->with('success', 'Payment deleted successfully.');
    }

    private function validatePayment(Request $request): array
    {
        return $request->validate([
            'event_id' => ['required', 'integer'],
            'budget_id' => ['nullable', 'integer'],
            'payee_name' => ['required', 'string', 'max:150'],
            'payment_type' => [
                'required',
                'in:expense,income,refund',
            ],
            'amount' => ['required', 'numeric', 'gt:0'],
            'payment_method' => [
                'required',
                'in:cash,bank,card,mobile_banking,cheque',
            ],
            'reference_number' => [
                'nullable',
                'string',
                'max:100',
            ],
            'payment_date' => ['required', 'date'],
            'status' => [
                'required',
                'in:pending,approved,paid,cancelled',
            ],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function validateBudgetSelection(
        $eventId,
        $budgetId,
        string $paymentType,
        $amount,
        string $status,
        $excludePaymentId = null
    ): ?string {
        $event = DB::selectOne("
            SELECT id
            FROM events
            WHERE id = ?
        ", [$eventId]);

        if (!$event) {
            return 'Selected event does not exist.';
        }

        if ($paymentType === 'expense' && !$budgetId) {
            return 'Expense payment requires a budget.';
        }

        if (!$budgetId) {
            return null;
        }

        $budget = DB::selectOne("
            SELECT id, event_id, allocated_amount
            FROM budgets
            WHERE id = ?
        ", [$budgetId]);

        if (!$budget) {
            return 'Selected budget does not exist.';
        }

        if ((string) $budget->event_id !== (string) $eventId) {
            return 'Selected budget does not belong to this event.';
        }

        if (
            $paymentType === 'expense'
            && $status === 'paid'
        ) {
            $sql = "
                SELECT NVL(SUM(amount), 0) AS total
                FROM payments
                WHERE budget_id = ?
                AND LOWER(payment_type) = 'expense'
                AND LOWER(status) = 'paid'
            ";

            $bindings = [$budgetId];

            if ($excludePaymentId !== null) {
                $sql .= ' AND id <> ?';
                $bindings[] = $excludePaymentId;
            }

            $paidExpense = DB::selectOne(
                $sql,
                $bindings
            )->total;

            $remainingAmount =
                $budget->allocated_amount - $paidExpense;

            if ($amount > $remainingAmount) {
                return 'Payment exceeds the remaining budget amount.';
            }
        }

        return null;
    }
}