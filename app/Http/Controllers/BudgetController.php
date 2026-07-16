<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BudgetController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = trim((string) $request->query('status', ''));
        $category = trim((string) $request->query('category', ''));

        $sortMap = [
            'event' => 'e.title',
            'category' => 'b.category',
            'allocated' => 'b.allocated_amount',
            'spent' => 'spent_amount',
            'remaining' => 'remaining_amount',
            'status' => 'b.status',
        ];

        $sort = (string) $request->query('sort', 'event');
        $sortColumn = $sortMap[$sort] ?? 'e.title';

        $direction = strtolower(
            (string) $request->query('direction', 'asc')
        ) === 'desc' ? 'DESC' : 'ASC';

        $conditions = ['1 = 1'];
        $bindings = [];

        if ($search !== '') {
            $keyword = '%' . strtolower($search) . '%';

            $conditions[] = "
                (
                    LOWER(e.title) LIKE ?
                    OR LOWER(b.category) LIKE ?
                    OR LOWER(NVL(b.description, '')) LIKE ?
                )
            ";

            array_push($bindings, $keyword, $keyword, $keyword);
        }

        if (in_array(
            $status,
            ['planned', 'approved', 'closed', 'cancelled'],
            true
        )) {
            $conditions[] = 'LOWER(b.status) = ?';
            $bindings[] = $status;
        }

        $validCategories = [
            'venue',
            'food',
            'marketing',
            'transport',
            'equipment',
            'decoration',
            'security',
            'miscellaneous',
        ];

        if (in_array($category, $validCategories, true)) {
            $conditions[] = 'LOWER(b.category) = ?';
            $bindings[] = $category;
        }

        $budgets = DB::select("
            SELECT
                b.*,
                e.title AS event_title,
                NVL(payment_summary.spent_amount, 0)
                    AS spent_amount,
                (
                    b.allocated_amount
                    - NVL(payment_summary.spent_amount, 0)
                ) AS remaining_amount,
                CASE
                    WHEN b.allocated_amount = 0 THEN 0
                    ELSE ROUND(
                        NVL(payment_summary.spent_amount, 0)
                        / b.allocated_amount * 100,
                        2
                    )
                END AS utilization_percentage
            FROM budgets b
            JOIN events e
                ON b.event_id = e.id
            LEFT JOIN
            (
                SELECT
                    budget_id,
                    SUM(
                        CASE
                            WHEN LOWER(payment_type) = 'expense'
                            AND LOWER(status) = 'paid'
                            THEN amount
                            ELSE 0
                        END
                    ) AS spent_amount
                FROM payments
                GROUP BY budget_id
            ) payment_summary
                ON payment_summary.budget_id = b.id
            WHERE " . implode(' AND ', $conditions) . "
            ORDER BY {$sortColumn} {$direction}, b.id DESC
        ", $bindings);

        return view('admin.budgets.index', compact(
            'budgets',
            'search',
            'status',
            'category',
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

        return view('admin.budgets.create', compact('events'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => ['required', 'integer'],
            'category' => [
                'required',
                'in:venue,food,marketing,transport,equipment,decoration,security,miscellaneous',
            ],
            'description' => ['nullable', 'string', 'max:500'],
            'allocated_amount' => ['required', 'numeric', 'min:0'],
            'status' => [
                'required',
                'in:planned,approved,closed,cancelled',
            ],
        ]);

        $event = DB::selectOne("
            SELECT id
            FROM events
            WHERE id = ?
        ", [$validated['event_id']]);

        if (!$event) {
            return back()
                ->withInput()
                ->with('error', 'Selected event does not exist.');
        }

        $duplicate = DB::selectOne("
            SELECT id
            FROM budgets
            WHERE event_id = ?
            AND LOWER(category) = ?
        ", [
            $validated['event_id'],
            $validated['category'],
        ]);

        if ($duplicate) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'This budget category already exists for the event.'
                );
        }

        try {
            DB::insert("
                INSERT INTO budgets
                (
                    event_id,
                    category,
                    description,
                    allocated_amount,
                    status,
                    created_at,
                    updated_at
                )
                VALUES
                (?, ?, ?, ?, ?, SYSTIMESTAMP, SYSTIMESTAMP)
            ", [
                $validated['event_id'],
                $validated['category'],
                $validated['description'] ?? null,
                $validated['allocated_amount'],
                $validated['status'],
            ]);
        } catch (QueryException $exception) {
            return back()
                ->withInput()
                ->with('error', 'Budget could not be created.');
        }

        return redirect()
            ->route('admin.budgets.index')
            ->with('success', 'Budget created successfully.');
    }

    public function show($id): View
    {
        $budget = DB::selectOne("
            SELECT
                b.*,
                e.title AS event_title,
                e.start_time,
                NVL(payment_summary.spent_amount, 0)
                    AS spent_amount,
                (
                    b.allocated_amount
                    - NVL(payment_summary.spent_amount, 0)
                ) AS remaining_amount,
                CASE
                    WHEN b.allocated_amount = 0 THEN 0
                    ELSE ROUND(
                        NVL(payment_summary.spent_amount, 0)
                        / b.allocated_amount * 100,
                        2
                    )
                END AS utilization_percentage
            FROM budgets b
            JOIN events e
                ON b.event_id = e.id
            LEFT JOIN
            (
                SELECT
                    budget_id,
                    SUM(
                        CASE
                            WHEN LOWER(payment_type) = 'expense'
                            AND LOWER(status) = 'paid'
                            THEN amount
                            ELSE 0
                        END
                    ) AS spent_amount
                FROM payments
                GROUP BY budget_id
            ) payment_summary
                ON payment_summary.budget_id = b.id
            WHERE b.id = ?
        ", [$id]);

        abort_if(!$budget, 404, 'Budget not found.');

        $payments = DB::select("
            SELECT *
            FROM payments
            WHERE budget_id = ?
            ORDER BY payment_date DESC, id DESC
        ", [$id]);

        return view('admin.budgets.show', compact(
            'budget',
            'payments'
        ));
    }

    public function edit($id): View
    {
        $budget = DB::selectOne("
            SELECT *
            FROM budgets
            WHERE id = ?
        ", [$id]);

        abort_if(!$budget, 404, 'Budget not found.');

        $events = DB::select("
            SELECT id, title, start_time
            FROM events
            ORDER BY start_time DESC
        ");

        return view('admin.budgets.edit', compact(
            'budget',
            'events'
        ));
    }

    public function update(
        Request $request,
        $id
    ): RedirectResponse {
        $budget = DB::selectOne("
            SELECT id
            FROM budgets
            WHERE id = ?
        ", [$id]);

        abort_if(!$budget, 404, 'Budget not found.');

        $validated = $request->validate([
            'event_id' => ['required', 'integer'],
            'category' => [
                'required',
                'in:venue,food,marketing,transport,equipment,decoration,security,miscellaneous',
            ],
            'description' => ['nullable', 'string', 'max:500'],
            'allocated_amount' => ['required', 'numeric', 'min:0'],
            'status' => [
                'required',
                'in:planned,approved,closed,cancelled',
            ],
        ]);

        $duplicate = DB::selectOne("
            SELECT id
            FROM budgets
            WHERE event_id = ?
            AND LOWER(category) = ?
            AND id <> ?
        ", [
            $validated['event_id'],
            $validated['category'],
            $id,
        ]);

        if ($duplicate) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'This category already exists for the event.'
                );
        }

        $spent = DB::selectOne("
            SELECT NVL(SUM(amount), 0) AS total
            FROM payments
            WHERE budget_id = ?
            AND LOWER(payment_type) = 'expense'
            AND LOWER(status) = 'paid'
        ", [$id])->total;

        if ($validated['allocated_amount'] < $spent) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Allocated amount cannot be less than the paid expense.'
                );
        }

        DB::update("
            UPDATE budgets
            SET
                event_id = ?,
                category = ?,
                description = ?,
                allocated_amount = ?,
                status = ?,
                updated_at = SYSTIMESTAMP
            WHERE id = ?
        ", [
            $validated['event_id'],
            $validated['category'],
            $validated['description'] ?? null,
            $validated['allocated_amount'],
            $validated['status'],
            $id,
        ]);

        return redirect()
            ->route('admin.budgets.index')
            ->with('success', 'Budget updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        try {
            DB::delete("
                DELETE FROM budgets
                WHERE id = ?
            ", [$id]);
        } catch (QueryException $exception) {
            return back()->with(
                'error',
                'Budget cannot be deleted because payment records exist.'
            );
        }

        return redirect()
            ->route('admin.budgets.index')
            ->with('success', 'Budget deleted successfully.');
    }
}