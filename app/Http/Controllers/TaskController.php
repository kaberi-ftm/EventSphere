<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(): View
    {
        $tasks = DB::select("
            SELECT
                t.*,
                u.name AS volunteer_name,
                e.title AS event_title,
                v.role AS volunteer_role
            FROM tasks t
            JOIN volunteers v
                ON t.volunteer_id = v.id
            JOIN users u
                ON v.user_id = u.id
            JOIN events e
                ON v.event_id = e.id
            ORDER BY t.id DESC
        ");

        return view(
            'admin.tasks.index',
            compact('tasks')
        );
    }

    public function create(): View
    {
        $volunteers = $this->approvedVolunteers();

        return view(
            'admin.tasks.create',
            compact('volunteers')
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'volunteer_id' => [
                'required',
                'exists:volunteers,id'
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => [
                'nullable',
                'in:pending,in_progress,completed'
            ],
            'deadline' => ['nullable', 'date'],
        ]);

        $volunteer = DB::selectOne("
            SELECT id
            FROM volunteers
            WHERE id = ?
            AND LOWER(status) = 'approved'
        ", [$validated['volunteer_id']]);

        if (!$volunteer) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Tasks can only be assigned to approved volunteers.'
                );
        }

        DB::insert("
            INSERT INTO tasks
            (
                volunteer_id,
                title,
                description,
                status,
                deadline,
                created_at,
                updated_at
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                TO_TIMESTAMP(
                    ?,
                    'YYYY-MM-DD\"T\"HH24:MI'
                ),
                SYSTIMESTAMP,
                SYSTIMESTAMP
            )
        ", [
            $validated['volunteer_id'],
            $validated['title'],
            $validated['description'] ?? null,
            $validated['status'] ?? 'pending',
            $validated['deadline'] ?? null,
        ]);

        return redirect()
            ->route('admin.tasks.index')
            ->with('success', 'Task assigned successfully.');
    }

    public function show($id): View
    {
        $task = DB::selectOne("
            SELECT
                t.*,
                u.name AS volunteer_name,
                e.title AS event_title,
                v.role AS volunteer_role
            FROM tasks t
            JOIN volunteers v
                ON t.volunteer_id = v.id
            JOIN users u
                ON v.user_id = u.id
            JOIN events e
                ON v.event_id = e.id
            WHERE t.id = ?
        ", [$id]);

        abort_if(!$task, 404, 'Task not found.');

        return view('admin.tasks.show', compact('task'));
    }

    public function edit($id): View
    {
        $task = DB::selectOne("
            SELECT *
            FROM tasks
            WHERE id = ?
        ", [$id]);

        abort_if(!$task, 404, 'Task not found.');

        $volunteers = $this->approvedVolunteers();

        return view(
            'admin.tasks.edit',
            compact('task', 'volunteers')
        );
    }

    public function update(
        Request $request,
        $id
    ): RedirectResponse {
        $task = DB::selectOne("
            SELECT id
            FROM tasks
            WHERE id = ?
        ", [$id]);

        abort_if(!$task, 404, 'Task not found.');

        $validated = $request->validate([
            'volunteer_id' => [
                'required',
                'exists:volunteers,id'
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => [
                'required',
                'in:pending,in_progress,completed'
            ],
            'deadline' => ['nullable', 'date'],
        ]);

        $volunteer = DB::selectOne("
            SELECT id
            FROM volunteers
            WHERE id = ?
            AND LOWER(status) = 'approved'
        ", [$validated['volunteer_id']]);

        if (!$volunteer) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'The selected volunteer is not approved.'
                );
        }

        DB::update("
            UPDATE tasks
            SET
                volunteer_id = ?,
                title = ?,
                description = ?,
                status = ?,
                deadline = TO_TIMESTAMP(
                    ?,
                    'YYYY-MM-DD\"T\"HH24:MI'
                ),
                updated_at = SYSTIMESTAMP
            WHERE id = ?
        ", [
            $validated['volunteer_id'],
            $validated['title'],
            $validated['description'] ?? null,
            $validated['status'],
            $validated['deadline'] ?? null,
            $id,
        ]);

        return redirect()
            ->route('admin.tasks.index')
            ->with('success', 'Task updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        $task = DB::selectOne("
            SELECT id
            FROM tasks
            WHERE id = ?
        ", [$id]);

        abort_if(!$task, 404, 'Task not found.');

        try {
            DB::delete("
                DELETE FROM tasks
                WHERE id = ?
            ", [$id]);

            return redirect()
                ->route('admin.tasks.index')
                ->with('success', 'Task deleted successfully.');
        } catch (QueryException $exception) {
            return back()->with(
                'error',
                'Task could not be deleted.'
            );
        }
    }

    public function start($id): RedirectResponse
    {
        $this->findOwnedTask($id);

        DB::update("
            UPDATE tasks
            SET
                status = 'in_progress',
                updated_at = SYSTIMESTAMP
            WHERE id = ?
            AND LOWER(status) = 'pending'
        ", [$id]);

        return back()->with(
            'success',
            'Task marked as in progress.'
        );
    }

    public function complete($id): RedirectResponse
    {
        $this->findOwnedTask($id);

        DB::update("
            UPDATE tasks
            SET
                status = 'completed',
                updated_at = SYSTIMESTAMP
            WHERE id = ?
            AND LOWER(status) IN
            (
                'pending',
                'in_progress'
            )
        ", [$id]);

        return back()->with(
            'success',
            'Task completed successfully.'
        );
    }

    public function myTasks(): RedirectResponse
    {
        return redirect()->route('volunteer.dashboard');
    }

    private function findOwnedTask($taskId): object
    {
        $task = DB::selectOne("
            SELECT t.id, t.status
            FROM tasks t
            JOIN volunteers v
                ON t.volunteer_id = v.id
            WHERE t.id = ?
            AND v.user_id = ?
            AND LOWER(v.status) = 'approved'
        ", [
            $taskId,
            auth()->id(),
        ]);

        abort_if(
            !$task,
            403,
            'You are not authorized to update this task.'
        );

        return $task;
    }

    private function approvedVolunteers(): array
    {
        return DB::select("
            SELECT
                v.id,
                v.role,
                u.name AS user_name,
                e.title AS event_title
            FROM volunteers v
            JOIN users u
                ON v.user_id = u.id
            JOIN events e
                ON v.event_id = e.id
            WHERE LOWER(v.status) = 'approved'
            ORDER BY
                e.title,
                u.name
        ");
    }
}