<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
class VolunteerController extends Controller
{
    public function index(): View
    {
        $volunteers = DB::select("
            SELECT
                v.*,
                u.name AS user_name,
                u.email AS user_email,
                e.title AS event_title,
                e.start_time
            FROM volunteers v
            JOIN users u
                ON v.user_id = u.id
            JOIN events e
                ON v.event_id = e.id
            ORDER BY
                CASE LOWER(v.status)
                    WHEN 'pending' THEN 1
                    WHEN 'approved' THEN 2
                    ELSE 3
                END,
                v.applied_at DESC
        ");

        return view(
            'admin.volunteers.index',
            compact('volunteers')
        );
    }

    public function applyForm($eventId): View
    {
        $event = DB::selectOne("
            SELECT
                e.*,
                c.name AS club_name
            FROM events e
            JOIN clubs c
                ON e.club_id = c.id
            WHERE e.id = ?
            AND e.start_time > SYSDATE
            AND LOWER(e.status) <> 'cancelled'
        ", [$eventId]);

        abort_if(
            !$event,
            404,
            'The event is unavailable.'
        );

        $existingApplication = DB::selectOne("
            SELECT id, status
            FROM volunteers
            WHERE user_id = ?
            AND event_id = ?
        ", [
            Auth::id(),
            $eventId,
        ]);

        $availableRoles = [
            'General Volunteer',
            'Registration Desk',
            'Photography',
            'Media',
            'Stage Management',
            'Technical Support',
            'Decoration',
        ];

        return view(
            'volunteers.apply',
            compact(
                'event',
                'existingApplication',
                'availableRoles'
            )
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'role' => ['required', 'string', 'max:50'],
        ]);

        $event = DB::selectOne("
            SELECT id
            FROM events
            WHERE id = ?
            AND start_time > SYSDATE
            AND LOWER(status) <> 'cancelled'
        ", [$validated['event_id']]);

        if (!$event) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'The selected event is not available.'
                );
        }

        $existingApplication = DB::selectOne("
            SELECT id, status
            FROM volunteers
            WHERE user_id = ?
            AND event_id = ?
        ", [
            Auth::id(),
            $validated['event_id'],
        ]);

        if ($existingApplication) {
            return back()->with(
                'error',
                'You have already applied for this event.'
            );
        }

        try {
            DB::insert("
                INSERT INTO volunteers
                (
                    user_id,
                    event_id,
                    status,
                    role,
                    applied_at,
                    created_at,
                    updated_at
                )
                VALUES
                (
                    ?,
                    ?,
                    'pending',
                    ?,
                    SYSTIMESTAMP,
                    SYSTIMESTAMP,
                    SYSTIMESTAMP
                )
            ", [
                Auth::id(),
                $validated['event_id'],
                $validated['role'],
            ]);
        } catch (QueryException $exception) {
            return back()->with(
                'error',
                'Application could not be submitted or already exists.'
            );
        }

        return redirect()
            ->route('volunteer.dashboard')
            ->with(
                'success',
                'Volunteer application submitted successfully.'
            );
    }

    public function apply(Request $request): RedirectResponse
    {
        return $this->store($request);
    }

    public function show($id): View
    {
        $volunteer = DB::selectOne("
            SELECT
                v.*,
                u.name AS user_name,
                u.email AS user_email,
                e.title AS event_title,
                e.start_time,
                e.end_time
            FROM volunteers v
            JOIN users u
                ON v.user_id = u.id
            JOIN events e
                ON v.event_id = e.id
            WHERE v.id = ?
        ", [$id]);

        abort_if(
            !$volunteer,
            404,
            'Volunteer application not found.'
        );

        return view(
            'admin.volunteers.show',
            compact('volunteer')
        );
    }

   public function approve($id): RedirectResponse
{
    $volunteer = DB::selectOne("
        SELECT id, status
        FROM volunteers
        WHERE id = ?
    ", [$id]);

    abort_if(
        !$volunteer,
        404,
        'Volunteer application not found.'
    );

    if ($volunteer->status === 'approved') {
        return back()->with(
            'error',
            'This volunteer application is already approved.'
        );
    }

    DB::update("
        UPDATE volunteers
        SET
            status = 'approved',
            approved_at = SYSTIMESTAMP,
            updated_at = SYSTIMESTAMP
        WHERE id = ?
    ", [$id]);

    return back()->with(
        'success',
        'Volunteer application approved successfully.'
    );
}
public function create(): View
{
    $users = DB::select("
        SELECT id, name, email
        FROM users
        ORDER BY name
    ");

   $events = DB::select("
    SELECT id, title, start_time
    FROM events
    WHERE start_time > SYSDATE
    AND LOWER(status) <> 'cancelled'
    ORDER BY start_time
");

    $availableRoles = [
        'General Volunteer',
        'Registration Desk',
        'Photography',
        'Media',
        'Stage Management',
        'Technical Support',
        'Decoration',
    ];

    return view(
        'admin.volunteers.create',
        compact('users', 'events', 'availableRoles')
    );
}

public function adminStore(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'user_id' => ['required', 'exists:users,id'],
        'event_id' => ['required', 'exists:events,id'],
        'role' => ['required', 'string', 'max:50'],
        'status' => ['required', 'in:pending,approved,rejected'],
    ]);

    $exists = DB::selectOne("
        SELECT id
        FROM volunteers
        WHERE user_id = ?
        AND event_id = ?
    ", [
        $validated['user_id'],
        $validated['event_id'],
    ]);

    if ($exists) {
        return back()
            ->withInput()
            ->with(
                'error',
                'This user already has a volunteer application for this event.'
            );
    }

    DB::insert("
        INSERT INTO volunteers
        (
            user_id,
            event_id,
            status,
            role,
            applied_at,
            approved_at,
            created_at,
            updated_at
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            SYSTIMESTAMP,
            CASE
                WHEN ? = 'approved'
                THEN SYSTIMESTAMP
                ELSE NULL
            END,
            SYSTIMESTAMP,
            SYSTIMESTAMP
        )
    ", [
        $validated['user_id'],
        $validated['event_id'],
        $validated['status'],
        $validated['role'],
        $validated['status'],
    ]);

    return redirect()
        ->route('admin.volunteers.index')
        ->with('success', 'Volunteer application created successfully.');
}

public function edit($id): View
{
    $volunteer = DB::selectOne("
        SELECT *
        FROM volunteers
        WHERE id = ?
    ", [$id]);

    abort_if(
        !$volunteer,
        404,
        'Volunteer application not found.'
    );

    $users = DB::select("
        SELECT id, name, email
        FROM users
        ORDER BY name
    ");

    $events = DB::select("
        SELECT id, title, start_time
        FROM events
        ORDER BY start_time DESC
    ");

    $availableRoles = [
        'General Volunteer',
        'Registration Desk',
        'Photography',
        'Media',
        'Stage Management',
        'Technical Support',
        'Decoration',
    ];

    return view(
        'admin.volunteers.edit',
        compact(
            'volunteer',
            'users',
            'events',
            'availableRoles'
        )
    );
}

public function update(
    Request $request,
    $id
): RedirectResponse {
    $volunteer = DB::selectOne("
        SELECT id
        FROM volunteers
        WHERE id = ?
    ", [$id]);

    abort_if(
        !$volunteer,
        404,
        'Volunteer application not found.'
    );

    $validated = $request->validate([
        'user_id' => ['required', 'exists:users,id'],
        'event_id' => ['required', 'exists:events,id'],
        'role' => ['required', 'string', 'max:50'],
        'status' => ['required', 'in:pending,approved,rejected'],
    ]);

    $duplicate = DB::selectOne("
        SELECT id
        FROM volunteers
        WHERE user_id = ?
        AND event_id = ?
        AND id <> ?
    ", [
        $validated['user_id'],
        $validated['event_id'],
        $id,
    ]);

    if ($duplicate) {
        return back()
            ->withInput()
            ->with(
                'error',
                'Another application already exists for this user and event.'
            );
    }

    DB::update("
        UPDATE volunteers
        SET
            user_id = ?,
            event_id = ?,
            role = ?,
            status = ?,
            approved_at =
                CASE
                    WHEN ? = 'approved'
                    THEN NVL(approved_at, SYSTIMESTAMP)
                    ELSE NULL
                END,
            updated_at = SYSTIMESTAMP
        WHERE id = ?
    ", [
        $validated['user_id'],
        $validated['event_id'],
        $validated['role'],
        $validated['status'],
        $validated['status'],
        $id,
    ]);

    return redirect()
        ->route('admin.volunteers.index')
        ->with('success', 'Volunteer application updated successfully.');
}
   public function reject($id): RedirectResponse
{
    $volunteer = DB::selectOne("
        SELECT id, status
        FROM volunteers
        WHERE id = ?
    ", [$id]);

    abort_if(
        !$volunteer,
        404,
        'Volunteer application not found.'
    );

    if ($volunteer->status === 'rejected') {
        return back()->with(
            'error',
            'This volunteer application is already rejected.'
        );
    }

    DB::update("
        UPDATE volunteers
        SET
            status = 'rejected',
            approved_at = NULL,
            updated_at = SYSTIMESTAMP
        WHERE id = ?
    ", [$id]);

    return back()->with(
        'success',
        'Volunteer application rejected.'
    );
}

    public function destroy($id): RedirectResponse
    {
        $volunteer = DB::selectOne("
            SELECT id
            FROM volunteers
            WHERE id = ?
        ", [$id]);

        abort_if(
            !$volunteer,
            404,
            'Volunteer application not found.'
        );

        try {
            DB::delete("
                DELETE FROM volunteers
                WHERE id = ?
            ", [$id]);

            return redirect()
                ->route('admin.volunteers.index')
                ->with(
                    'success',
                    'Volunteer application deleted.'
                );
        } catch (QueryException $exception) {
            return back()->with(
                'error',
                'Volunteer cannot be deleted because assigned tasks exist.'
            );
        }
    }
}