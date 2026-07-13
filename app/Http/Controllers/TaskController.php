<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TaskController extends Controller
{
    /**
     * Display all tasks for admin.
     */
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

    /**
     * Show task create form.
     */
    public function create(): View
    {
        $volunteers = $this->approvedVolunteers();

        return view(
            'admin.tasks.create',
            compact('volunteers')
        );
    }

    /**
     * Store a new task.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'volunteer_id' => [
                'required',
                'exists:volunteers,id',
            ],
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'status' => [
                'nullable',
                'in:pending,in_progress,completed',
            ],
            'deadline' => [
                'nullable',
                'date',
            ],
        ]);

        $volunteer = DB::selectOne("
            SELECT id
            FROM volunteers
            WHERE id = ?
            AND LOWER(status) = 'approved'
        ", [
            $validated['volunteer_id'],
        ]);

        if (!$volunteer) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Tasks can only be assigned to approved volunteers.'
                );
        }

        $status = $validated['status'] ?? 'pending';

        try {
            DB::insert("
                INSERT INTO tasks
                (
                    volunteer_id,
                    title,
                    description,
                    status,
                    deadline,
                    completed_at,
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
                    CASE
                        WHEN ? = 'completed'
                        THEN SYSTIMESTAMP
                        ELSE NULL
                    END,
                    SYSTIMESTAMP,
                    SYSTIMESTAMP
                )
            ", [
                $validated['volunteer_id'],
                $validated['title'],
                $validated['description'] ?? null,
                $status,
                $validated['deadline'] ?? null,
                $status,
            ]);
        } catch (QueryException $exception) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Task could not be created. Please check the submitted information.'
                );
        }

        return redirect()
            ->route('admin.tasks.index')
            ->with(
                'success',
                'Task assigned successfully.'
            );
    }

    /**
     * Display one task.
     */
    public function show($id): View
    {
        $task = DB::selectOne("
            SELECT
                t.*,
                u.name AS volunteer_name,
                u.email AS volunteer_email,
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
        ", [
            $id,
        ]);

        abort_if(
            !$task,
            404,
            'Task not found.'
        );

        return view(
            'admin.tasks.show',
            compact('task')
        );
    }

    /**
     * Show task edit form.
     */
    public function edit($id): View
    {
        $task = DB::selectOne("
            SELECT *
            FROM tasks
            WHERE id = ?
        ", [
            $id,
        ]);

        abort_if(
            !$task,
            404,
            'Task not found.'
        );

        $volunteers = $this->approvedVolunteers();

        return view(
            'admin.tasks.edit',
            compact('task', 'volunteers')
        );
    }

    /**
     * Update a task.
     */
    public function update(
        Request $request,
        $id
    ): RedirectResponse {
        $task = DB::selectOne("
            SELECT
                id,
                status,
                completed_at
            FROM tasks
            WHERE id = ?
        ", [
            $id,
        ]);

        abort_if(
            !$task,
            404,
            'Task not found.'
        );

        $validated = $request->validate([
            'volunteer_id' => [
                'required',
                'exists:volunteers,id',
            ],
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'status' => [
                'required',
                'in:pending,in_progress,completed',
            ],
            'deadline' => [
                'nullable',
                'date',
            ],
        ]);

        $volunteer = DB::selectOne("
            SELECT id
            FROM volunteers
            WHERE id = ?
            AND LOWER(status) = 'approved'
        ", [
            $validated['volunteer_id'],
        ]);

        if (!$volunteer) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'The selected volunteer is not approved.'
                );
        }

        try {
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
                    completed_at =
                        CASE
                            WHEN ? = 'completed'
                            THEN NVL(
                                completed_at,
                                SYSTIMESTAMP
                            )
                            ELSE NULL
                        END,
                    updated_at = SYSTIMESTAMP
                WHERE id = ?
            ", [
                $validated['volunteer_id'],
                $validated['title'],
                $validated['description'] ?? null,
                $validated['status'],
                $validated['deadline'] ?? null,
                $validated['status'],
                $id,
            ]);
        } catch (QueryException $exception) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Task could not be updated.'
                );
        }

        return redirect()
            ->route('admin.tasks.index')
            ->with(
                'success',
                'Task updated successfully.'
            );
    }

    /**
     * Delete a task.
     */
    public function destroy($id): RedirectResponse
    {
        $task = DB::selectOne("
            SELECT id
            FROM tasks
            WHERE id = ?
        ", [
            $id,
        ]);

        abort_if(
            !$task,
            404,
            'Task not found.'
        );

        try {
            DB::delete("
                DELETE FROM tasks
                WHERE id = ?
            ", [
                $id,
            ]);
        } catch (QueryException $exception) {
            return back()->with(
                'error',
                'Task could not be deleted.'
            );
        }

        return redirect()
            ->route('admin.tasks.index')
            ->with(
                'success',
                'Task deleted successfully.'
            );
    }

    /**
     * Volunteer starts an assigned task.
     */
    public function start($id): RedirectResponse
    {
        $task = $this->findOwnedTask($id);

        if (strtolower($task->status) === 'completed') {
            return back()->with(
                'error',
                'A completed task cannot be started again.'
            );
        }

        if (strtolower($task->status) === 'in_progress') {
            return back()->with(
                'error',
                'This task is already in progress.'
            );
        }

        $updated = DB::update("
            UPDATE tasks
            SET
                status = 'in_progress',
                completed_at = NULL,
                updated_at = SYSTIMESTAMP
            WHERE id = ?
            AND LOWER(status) = 'pending'
        ", [
            $id,
        ]);

        if ($updated === 0) {
            return back()->with(
                'error',
                'Task status could not be changed.'
            );
        }

        return back()->with(
            'success',
            'Task marked as in progress.'
        );
    }

    /**
     * Volunteer completes an assigned task.
     */
    public function complete($id): RedirectResponse
    {
        $task = $this->findOwnedTask($id);

        if (strtolower($task->status) === 'completed') {
            return back()->with(
                'error',
                'This task is already completed.'
            );
        }

        $updated = DB::update("
            UPDATE tasks
            SET
                status = 'completed',
                completed_at = SYSTIMESTAMP,
                updated_at = SYSTIMESTAMP
            WHERE id = ?
            AND LOWER(status) IN (
                'pending',
                'in_progress'
            )
        ", [
            $id,
        ]);

        if ($updated === 0) {
            return back()->with(
                'error',
                'Task could not be completed.'
            );
        }

        return back()->with(
            'success',
            'Task completed successfully.'
        );
    }

    /**
     * Redirect volunteer to dashboard task list.
     */
    public function myTasks(): RedirectResponse
    {
        return redirect()
            ->route('volunteer.dashboard');
    }

    /**
     * Find a task owned by the authenticated approved volunteer.
     */
    private function findOwnedTask($taskId): object
    {
        $userId = Auth::id();

        abort_if(
            $userId === null,
            401,
            'Unauthenticated.'
        );

        $task = DB::selectOne("
            SELECT
                t.id,
                t.status,
                t.completed_at
            FROM tasks t
            JOIN volunteers v
                ON t.volunteer_id = v.id
            WHERE t.id = ?
            AND v.user_id = ?
            AND LOWER(v.status) = 'approved'
        ", [
            $taskId,
            $userId,
        ]);

        abort_if(
            !$task,
            403,
            'You are not authorized to update this task.'
        );

        return $task;
    }

    /**
     * Return all approved volunteers for admin task forms.
     */
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