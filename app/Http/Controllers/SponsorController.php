<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SponsorController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', '');

        $sortMap = [
            'name' => 's.name',
            'type' => 's.sponsor_type',
            'status' => 's.status',
            'contribution' => 'total_contribution',
            'events' => 'total_events',
        ];

        $sort = $request->query('sort', 'name');
        $sortColumn = $sortMap[$sort] ?? $sortMap['name'];

        $direction = strtolower(
            (string) $request->query('direction', 'asc')
        ) === 'desc' ? 'DESC' : 'ASC';

        $conditions = ['1 = 1'];
        $bindings = [];

        if ($search !== '') {
            $keyword = '%' . strtolower($search) . '%';

            $conditions[] = "
                (
                    LOWER(s.name) LIKE ?
                    OR LOWER(NVL(s.email, '')) LIKE ?
                    OR LOWER(NVL(s.contact_person, '')) LIKE ?
                )
            ";

            array_push($bindings, $keyword, $keyword, $keyword);
        }

        if (in_array($status, ['active', 'inactive'], true)) {
            $conditions[] = 'LOWER(s.status) = ?';
            $bindings[] = $status;
        }

        $sponsors = DB::select("
            SELECT
                s.*,
                NVL(summary_data.total_events, 0) AS total_events,
                NVL(summary_data.total_contribution, 0)
                    AS total_contribution
            FROM sponsors s
            LEFT JOIN
            (
                SELECT
                    sponsor_id,
                    COUNT(id) AS total_events,
                    SUM(
                        CASE
                            WHEN LOWER(status) <> 'cancelled'
                            THEN amount
                            ELSE 0
                        END
                    ) AS total_contribution
                FROM event_sponsors
                GROUP BY sponsor_id
            ) summary_data
                ON summary_data.sponsor_id = s.id
            WHERE " . implode(' AND ', $conditions) . "
            ORDER BY {$sortColumn} {$direction}
        ", $bindings);

        return view('admin.sponsors.index', compact(
            'sponsors',
            'search',
            'status',
            'sort',
            'direction'
        ));
    }

    public function create(): View
    {
        return view('admin.sponsors.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'contact_person' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:255', 'unique:sponsors,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'website' => ['nullable', 'url', 'max:255'],
            'description' => ['nullable', 'string'],
            'sponsor_type' => [
                'required',
                'in:corporate,individual,ngo,media,government,other'
            ],
            'status' => ['required', 'in:active,inactive'],
        ]);

        try {
            DB::insert("
                INSERT INTO sponsors
                (
                    name,
                    contact_person,
                    email,
                    phone,
                    address,
                    website,
                    description,
                    sponsor_type,
                    status,
                    created_at,
                    updated_at
                )
                VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?, SYSTIMESTAMP, SYSTIMESTAMP)
            ", [
                $validated['name'],
                $validated['contact_person'] ?? null,
                $validated['email'] ?? null,
                $validated['phone'] ?? null,
                $validated['address'] ?? null,
                $validated['website'] ?? null,
                $validated['description'] ?? null,
                $validated['sponsor_type'],
                $validated['status'],
            ]);
        } catch (QueryException $exception) {
            return back()
                ->withInput()
                ->with('error', 'Sponsor could not be created.');
        }

        return redirect()
            ->route('admin.sponsors.index')
            ->with('success', 'Sponsor created successfully.');
    }

    public function show($id): View
    {
        $sponsor = DB::selectOne("
            SELECT
                s.*,
                NVL(summary_data.total_events, 0) AS total_events,
                NVL(summary_data.total_contribution, 0)
                    AS total_contribution
            FROM sponsors s
            LEFT JOIN
            (
                SELECT
                    sponsor_id,
                    COUNT(id) AS total_events,
                    SUM(
                        CASE
                            WHEN LOWER(status) <> 'cancelled'
                            THEN amount
                            ELSE 0
                        END
                    ) AS total_contribution
                FROM event_sponsors
                GROUP BY sponsor_id
            ) summary_data
                ON summary_data.sponsor_id = s.id
            WHERE s.id = ?
        ", [$id]);

        abort_if(!$sponsor, 404, 'Sponsor not found.');

        $eventSponsorships = DB::select("
            SELECT
                es.*,
                e.title AS event_title,
                e.start_time
            FROM event_sponsors es
            JOIN events e
                ON es.event_id = e.id
            WHERE es.sponsor_id = ?
            ORDER BY es.agreement_date DESC
        ", [$id]);

        return view('admin.sponsors.show', compact(
            'sponsor',
            'eventSponsorships'
        ));
    }

    public function edit($id): View
    {
        $sponsor = DB::selectOne("
            SELECT *
            FROM sponsors
            WHERE id = ?
        ", [$id]);

        abort_if(!$sponsor, 404, 'Sponsor not found.');

        return view('admin.sponsors.edit', compact('sponsor'));
    }

    public function update(
        Request $request,
        $id
    ): RedirectResponse {
        $sponsor = DB::selectOne("
            SELECT id
            FROM sponsors
            WHERE id = ?
        ", [$id]);

        abort_if(!$sponsor, 404, 'Sponsor not found.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'contact_person' => ['nullable', 'string', 'max:150'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('sponsors', 'email')->ignore($id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'website' => ['nullable', 'url', 'max:255'],
            'description' => ['nullable', 'string'],
            'sponsor_type' => [
                'required',
                'in:corporate,individual,ngo,media,government,other'
            ],
            'status' => ['required', 'in:active,inactive'],
        ]);

        DB::update("
            UPDATE sponsors
            SET
                name = ?,
                contact_person = ?,
                email = ?,
                phone = ?,
                address = ?,
                website = ?,
                description = ?,
                sponsor_type = ?,
                status = ?,
                updated_at = SYSTIMESTAMP
            WHERE id = ?
        ", [
            $validated['name'],
            $validated['contact_person'] ?? null,
            $validated['email'] ?? null,
            $validated['phone'] ?? null,
            $validated['address'] ?? null,
            $validated['website'] ?? null,
            $validated['description'] ?? null,
            $validated['sponsor_type'],
            $validated['status'],
            $id,
        ]);

        return redirect()
            ->route('admin.sponsors.index')
            ->with('success', 'Sponsor updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        try {
            DB::delete("
                DELETE FROM sponsors
                WHERE id = ?
            ", [$id]);
        } catch (QueryException $exception) {
            return back()->with(
                'error',
                'Sponsor cannot be deleted because sponsorship records exist.'
            );
        }

        return redirect()
            ->route('admin.sponsors.index')
            ->with('success', 'Sponsor deleted successfully.');
    }
}