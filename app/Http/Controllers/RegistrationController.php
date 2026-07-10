<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function index(): View
    {
        $registrations = DB::select("
            SELECT
                r.id,
                r.event_id,
                r.user_id,
                e.title AS event_name,
                u.name AS user_name,
                r.status,
                r.registered_at
            FROM registrations r
            JOIN events e
                ON r.event_id = e.id
            JOIN users u
                ON r.user_id = u.id
            ORDER BY r.registered_at DESC
        ");

        return view(
            'admin.registrations.index',
            compact('registrations')
        );
    }

    public function create(): View
    {
        $events = DB::select("
            SELECT
                id,
                title,
                max_participants
            FROM events
            WHERE start_time > SYSDATE
            AND LOWER(status) <> 'cancelled'
            ORDER BY start_time
        ");

        $users = DB::select("
            SELECT id, name
            FROM users
            ORDER BY name
        ");

        return view(
            'admin.registrations.create',
            compact('events', 'users')
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $result = DB::transaction(function () use ($validated) {
            $event = DB::selectOne("
                SELECT
                    id,
                    max_participants
                FROM events
                WHERE id = ?
                AND start_time > SYSDATE
                AND LOWER(status) <> 'cancelled'
                FOR UPDATE
            ", [$validated['event_id']]);

            if (!$event) {
                return [
                    'success' => false,
                    'message' => 'The selected event is unavailable.',
                ];
            }

            $exists = DB::selectOne("
                SELECT id
                FROM registrations
                WHERE event_id = ?
                AND user_id = ?
            ", [
                $validated['event_id'],
                $validated['user_id'],
            ]);

            if ($exists) {
                return [
                    'success' => false,
                    'message' => 'User is already registered.',
                ];
            }

            $registrationCount = DB::selectOne("
                SELECT COUNT(*) AS total
                FROM registrations
                WHERE event_id = ?
                AND LOWER(status) <> 'cancelled'
            ", [$validated['event_id']])->total;

            if (
                $event->max_participants !== null
                && $registrationCount >= $event->max_participants
            ) {
                return [
                    'success' => false,
                    'message' => 'The event registration limit is full.',
                ];
            }

            DB::insert("
                INSERT INTO registrations
                (
                    event_id,
                    user_id,
                    status,
                    registered_at,
                    created_at,
                    updated_at
                )
                VALUES
                (
                    ?,
                    ?,
                    'registered',
                    SYSTIMESTAMP,
                    SYSTIMESTAMP,
                    SYSTIMESTAMP
                )
            ", [
                $validated['event_id'],
                $validated['user_id'],
            ]);

            return ['success' => true];
        });

        if (!$result['success']) {
            return back()
                ->withInput()
                ->with('error', $result['message']);
        }

        return redirect()
            ->route('admin.registrations.index')
            ->with('success', 'Registration created successfully.');
    }

    public function show($id): View
    {
        $registration = DB::selectOne("
            SELECT
                r.*,
                e.title AS event_name,
                u.name AS user_name
            FROM registrations r
            JOIN events e
                ON r.event_id = e.id
            JOIN users u
                ON r.user_id = u.id
            WHERE r.id = ?
        ", [$id]);

        abort_if(
            !$registration,
            404,
            'Registration not found.'
        );

        return view(
            'admin.registrations.show',
            compact('registration')
        );
    }

    public function edit($id): View
    {
        $registration = DB::selectOne("
            SELECT *
            FROM registrations
            WHERE id = ?
        ", [$id]);

        abort_if(
            !$registration,
            404,
            'Registration not found.'
        );

        $events = DB::select("
            SELECT id, title
            FROM events
            ORDER BY title
        ");

        $users = DB::select("
            SELECT id, name
            FROM users
            ORDER BY name
        ");

        return view(
            'admin.registrations.edit',
            compact('registration', 'events', 'users')
        );
    }

    public function update(
        Request $request,
        $id
    ): RedirectResponse {
        $registration = DB::selectOne("
            SELECT id
            FROM registrations
            WHERE id = ?
        ", [$id]);

        abort_if(
            !$registration,
            404,
            'Registration not found.'
        );

        $validated = $request->validate([
            'status' => [
                'required',
                'in:registered,cancelled,attended'
            ],
        ]);

        DB::update("
            UPDATE registrations
            SET
                status = ?,
                updated_at = SYSTIMESTAMP
            WHERE id = ?
        ", [
            $validated['status'],
            $id,
        ]);

        return redirect()
            ->route('admin.registrations.index')
            ->with('success', 'Registration updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        $registration = DB::selectOne("
            SELECT id
            FROM registrations
            WHERE id = ?
        ", [$id]);

        abort_if(
            !$registration,
            404,
            'Registration not found.'
        );

        try {
            DB::delete("
                DELETE FROM registrations
                WHERE id = ?
            ", [$id]);

            return redirect()
                ->route('admin.registrations.index')
                ->with('success', 'Registration deleted.');
        } catch (QueryException $exception) {
            return back()->with(
                'error',
                'Registration cannot be deleted because attendance or related records exist.'
            );
        }
    }
}