<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EventSponsorController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', '');
        $eventId = $request->query('event_id');

        $sortMap = [
            'amount' => 'es.amount',
            'date' => 'es.agreement_date',
            'sponsor' => 's.name',
            'event' => 'e.title',
            'status' => 'es.status',
        ];

        $sort = $request->query('sort', 'date');
        $sortColumn = $sortMap[$sort] ?? $sortMap['date'];

        $direction = strtolower(
            (string) $request->query('direction', 'desc')
        ) === 'asc' ? 'ASC' : 'DESC';

        $conditions = ['1 = 1'];
        $bindings = [];

        if ($search !== '') {
            $keyword = '%' . strtolower($search) . '%';

            $conditions[] = "
                (
                    LOWER(s.name) LIKE ?
                    OR LOWER(e.title) LIKE ?
                )
            ";

            array_push($bindings, $keyword, $keyword);
        }

        if (in_array(
            $status,
            ['pledged', 'confirmed', 'paid', 'cancelled'],
            true
        )) {
            $conditions[] = 'LOWER(es.status) = ?';
            $bindings[] = $status;
        }

        if ($eventId) {
            $conditions[] = 'es.event_id = ?';
            $bindings[] = $eventId;
        }

        $eventSponsors = DB::select("
            SELECT
                es.*,
                s.name AS sponsor_name,
                s.sponsor_type,
                e.title AS event_title,
                e.start_time,
                DENSE_RANK() OVER
                (
                    PARTITION BY es.event_id
                    ORDER BY es.amount DESC
                ) AS contribution_rank
            FROM event_sponsors es
            JOIN sponsors s
                ON es.sponsor_id = s.id
            JOIN events e
                ON es.event_id = e.id
            WHERE " . implode(' AND ', $conditions) . "
            ORDER BY {$sortColumn} {$direction}
        ", $bindings);

        $events = DB::select("
            SELECT id, title
            FROM events
            ORDER BY start_time DESC
        ");

        return view('admin.event-sponsors.index', compact(
            'eventSponsors',
            'events',
            'search',
            'status',
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

        $sponsors = DB::select("
            SELECT id, name, sponsor_type
            FROM sponsors
            WHERE LOWER(status) = 'active'
            ORDER BY name
        ");

        return view('admin.event-sponsors.create', compact(
            'events',
            'sponsors'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'sponsor_id' => ['required', 'exists:sponsors,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'contribution_type' => [
                'required',
                'in:cash,in_kind,media,venue,service'
            ],
            'agreement_date' => ['required', 'date'],
            'status' => [
                'required',
                'in:pledged,confirmed,paid,cancelled'
            ],
            'notes' => ['nullable', 'string'],
        ]);

        $duplicate = DB::selectOne("
            SELECT id
            FROM event_sponsors
            WHERE event_id = ?
            AND sponsor_id = ?
        ", [
            $validated['event_id'],
            $validated['sponsor_id'],
        ]);

        if ($duplicate) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'This sponsor is already assigned to this event.'
                );
        }

        try {
            DB::transaction(function () use ($validated) {
                DB::insert("
                    INSERT INTO event_sponsors
                    (
                        event_id,
                        sponsor_id,
                        amount,
                        contribution_type,
                        agreement_date,
                        status,
                        notes,
                        created_at,
                        updated_at
                    )
                    VALUES
                    (
                        ?,
                        ?,
                        ?,
                        ?,
                        TO_TIMESTAMP(?, 'YYYY-MM-DD'),
                        ?,
                        ?,
                        SYSTIMESTAMP,
                        SYSTIMESTAMP
                    )
                ", [
                    $validated['event_id'],
                    $validated['sponsor_id'],
                    $validated['amount'],
                    $validated['contribution_type'],
                    $validated['agreement_date'],
                    $validated['status'],
                    $validated['notes'] ?? null,
                ]);
            });
        } catch (QueryException $exception) {
            return back()
                ->withInput()
                ->with('error', 'Sponsorship could not be created.');
        }

        return redirect()
            ->route('admin.event-sponsors.index')
            ->with('success', 'Event sponsorship created successfully.');
    }

    public function show($id): View
    {
        $eventSponsor = DB::selectOne("
            SELECT
                es.*,
                s.name AS sponsor_name,
                s.email AS sponsor_email,
                s.phone AS sponsor_phone,
                e.title AS event_title,
                e.start_time
            FROM event_sponsors es
            JOIN sponsors s
                ON es.sponsor_id = s.id
            JOIN events e
                ON es.event_id = e.id
            WHERE es.id = ?
        ", [$id]);

        abort_if(
            !$eventSponsor,
            404,
            'Event sponsorship not found.'
        );

        return view(
            'admin.event-sponsors.show',
            compact('eventSponsor')
        );
    }

    public function edit($id): View
    {
        $eventSponsor = DB::selectOne("
            SELECT *
            FROM event_sponsors
            WHERE id = ?
        ", [$id]);

        abort_if(
            !$eventSponsor,
            404,
            'Event sponsorship not found.'
        );

        $events = DB::select("
            SELECT id, title, start_time
            FROM events
            ORDER BY start_time DESC
        ");

        $sponsors = DB::select("
            SELECT id, name, sponsor_type
            FROM sponsors
            ORDER BY name
        ");

        return view('admin.event-sponsors.edit', compact(
            'eventSponsor',
            'events',
            'sponsors'
        ));
    }

    public function update(
        Request $request,
        $id
    ): RedirectResponse {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'sponsor_id' => ['required', 'exists:sponsors,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'contribution_type' => [
                'required',
                'in:cash,in_kind,media,venue,service'
            ],
            'agreement_date' => ['required', 'date'],
            'status' => [
                'required',
                'in:pledged,confirmed,paid,cancelled'
            ],
            'notes' => ['nullable', 'string'],
        ]);

        $duplicate = DB::selectOne("
            SELECT id
            FROM event_sponsors
            WHERE event_id = ?
            AND sponsor_id = ?
            AND id <> ?
        ", [
            $validated['event_id'],
            $validated['sponsor_id'],
            $id,
        ]);

        if ($duplicate) {
            return back()
                ->withInput()
                ->with('error', 'Duplicate event sponsorship detected.');
        }

        DB::update("
            UPDATE event_sponsors
            SET
                event_id = ?,
                sponsor_id = ?,
                amount = ?,
                contribution_type = ?,
                agreement_date =
                    TO_TIMESTAMP(?, 'YYYY-MM-DD'),
                status = ?,
                notes = ?,
                updated_at = SYSTIMESTAMP
            WHERE id = ?
        ", [
            $validated['event_id'],
            $validated['sponsor_id'],
            $validated['amount'],
            $validated['contribution_type'],
            $validated['agreement_date'],
            $validated['status'],
            $validated['notes'] ?? null,
            $id,
        ]);

        return redirect()
            ->route('admin.event-sponsors.index')
            ->with('success', 'Event sponsorship updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        DB::delete("
            DELETE FROM event_sponsors
            WHERE id = ?
        ", [$id]);

        return redirect()
            ->route('admin.event-sponsors.index')
            ->with('success', 'Event sponsorship deleted successfully.');
    }
}