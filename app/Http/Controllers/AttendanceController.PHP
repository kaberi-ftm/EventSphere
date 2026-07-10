<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(): View
    {
        $attendances = DB::select("
            SELECT
                a.*,
                e.title AS event_title,
                u.name AS user_name
            FROM attendances a
            JOIN events e
                ON a.event_id = e.id
            JOIN users u
                ON a.user_id = u.id
            ORDER BY a.id DESC
        ");

        return view(
            'admin.attendances.index',
            compact('attendances')
        );
    }

    public function create(): View
    {
        $events = DB::select("
            SELECT id, title
            FROM events
            WHERE LOWER(status) <> 'cancelled'
            ORDER BY start_time DESC
        ");

        $users = DB::select("
            SELECT id, name
            FROM users
            ORDER BY name
        ");

        return view(
            'admin.attendances.create',
            compact('events', 'users')
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'user_id' => ['required', 'exists:users,id'],
            'is_present' => ['required', 'in:Y,N'],
        ]);

        $registration = DB::selectOne("
            SELECT id
            FROM registrations
            WHERE event_id = ?
            AND user_id = ?
            AND LOWER(status) IN
            (
                'registered',
                'pending',
                'attended'
            )
        ", [
            $validated['event_id'],
            $validated['user_id'],
        ]);

        if (!$registration) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'This user is not registered for the selected event.'
                );
        }

        $exists = DB::selectOne("
            SELECT id
            FROM attendances
            WHERE event_id = ?
            AND user_id = ?
        ", [
            $validated['event_id'],
            $validated['user_id'],
        ]);

        if ($exists) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Attendance has already been recorded.'
                );
        }

        DB::transaction(function () use ($validated) {
            DB::insert("
                INSERT INTO attendances
                (
                    event_id,
                    user_id,
                    is_present,
                    checked_in_at,
                    created_at,
                    updated_at
                )
                VALUES
                (
                    ?,
                    ?,
                    ?,
                    CASE
                        WHEN ? = 'Y'
                        THEN SYSTIMESTAMP
                        ELSE NULL
                    END,
                    SYSTIMESTAMP,
                    SYSTIMESTAMP
                )
            ", [
                $validated['event_id'],
                $validated['user_id'],
                $validated['is_present'],
                $validated['is_present'],
            ]);

            if ($validated['is_present'] === 'Y') {
                DB::update("
                    UPDATE registrations
                    SET
                        status = 'attended',
                        updated_at = SYSTIMESTAMP
                    WHERE event_id = ?
                    AND user_id = ?
                ", [
                    $validated['event_id'],
                    $validated['user_id'],
                ]);
            }
        });

        return redirect()
            ->route('admin.attendances.index')
            ->with('success', 'Attendance recorded successfully.');
    }

    public function show($id): View
    {
        $attendance = DB::selectOne("
            SELECT
                a.*,
                e.title AS event_title,
                u.name AS user_name
            FROM attendances a
            JOIN events e
                ON a.event_id = e.id
            JOIN users u
                ON a.user_id = u.id
            WHERE a.id = ?
        ", [$id]);

        abort_if(
            !$attendance,
            404,
            'Attendance record not found.'
        );

        return view(
            'admin.attendances.show',
            compact('attendance')
        );
    }

    public function edit($id): View
    {
        $attendance = DB::selectOne("
            SELECT *
            FROM attendances
            WHERE id = ?
        ", [$id]);

        abort_if(
            !$attendance,
            404,
            'Attendance record not found.'
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
            'admin.attendances.edit',
            compact('attendance', 'events', 'users')
        );
    }

    public function update(
        Request $request,
        $id
    ): RedirectResponse {
        $attendance = DB::selectOne("
            SELECT id
            FROM attendances
            WHERE id = ?
        ", [$id]);

        abort_if(
            !$attendance,
            404,
            'Attendance record not found.'
        );

        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'user_id' => ['required', 'exists:users,id'],
            'is_present' => ['required', 'in:Y,N'],
        ]);

        $duplicate = DB::selectOne("
            SELECT id
            FROM attendances
            WHERE event_id = ?
            AND user_id = ?
            AND id <> ?
        ", [
            $validated['event_id'],
            $validated['user_id'],
            $id,
        ]);

        if ($duplicate) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Another attendance record already exists.'
                );
        }

        DB::transaction(function () use ($validated, $id) {
            DB::update("
                UPDATE attendances
                SET
                    event_id = ?,
                    user_id = ?,
                    is_present = ?,
                    checked_in_at =
                        CASE
                            WHEN ? = 'Y'
                            THEN NVL(
                                checked_in_at,
                                SYSTIMESTAMP
                            )
                            ELSE NULL
                        END,
                    updated_at = SYSTIMESTAMP
                WHERE id = ?
            ", [
                $validated['event_id'],
                $validated['user_id'],
                $validated['is_present'],
                $validated['is_present'],
                $id,
            ]);

            $registrationStatus =
                $validated['is_present'] === 'Y'
                    ? 'attended'
                    : 'registered';

            DB::update("
                UPDATE registrations
                SET
                    status = ?,
                    updated_at = SYSTIMESTAMP
                WHERE event_id = ?
                AND user_id = ?
            ", [
                $registrationStatus,
                $validated['event_id'],
                $validated['user_id'],
            ]);
        });

        return redirect()
            ->route('admin.attendances.index')
            ->with('success', 'Attendance updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        $attendance = DB::selectOne("
            SELECT id
            FROM attendances
            WHERE id = ?
        ", [$id]);

        abort_if(
            !$attendance,
            404,
            'Attendance record not found.'
        );

        try {
            DB::delete("
                DELETE FROM attendances
                WHERE id = ?
            ", [$id]);

            return redirect()
                ->route('admin.attendances.index')
                ->with('success', 'Attendance deleted.');
        } catch (QueryException $exception) {
            return back()->with(
                'error',
                'Attendance record could not be deleted.'
            );
        }
    }
}