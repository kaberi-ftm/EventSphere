<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VolunteerDashboardController extends Controller
{
    public function index(): View
    {
        $userId = Auth::id();

        $applications = DB::select("
            SELECT
                v.*,
                e.title AS event_title,
                e.start_time,
                e.end_time
            FROM volunteers v
            JOIN events e
                ON v.event_id = e.id
            WHERE v.user_id = ?
            ORDER BY v.applied_at DESC
        ", [$userId]);

        $tasks = DB::select("
            SELECT
                t.*,
                e.title AS event_title,
                v.role AS volunteer_role
            FROM tasks t
            JOIN volunteers v
                ON t.volunteer_id = v.id
            JOIN events e
                ON v.event_id = e.id
            WHERE v.user_id = ?
            AND LOWER(v.status) = 'approved'
            ORDER BY
                CASE
                    WHEN t.deadline IS NULL THEN 1
                    ELSE 0
                END,
                t.deadline,
                t.id DESC
        ", [$userId]);

        $pendingTasks = DB::selectOne("
            SELECT COUNT(*) AS total
            FROM tasks t
            JOIN volunteers v
                ON t.volunteer_id = v.id
            WHERE v.user_id = ?
            AND LOWER(v.status) = 'approved'
            AND LOWER(t.status) = 'pending'
        ", [$userId])->total;

        $inProgressTasks = DB::selectOne("
            SELECT COUNT(*) AS total
            FROM tasks t
            JOIN volunteers v
                ON t.volunteer_id = v.id
            WHERE v.user_id = ?
            AND LOWER(v.status) = 'approved'
            AND LOWER(t.status) = 'in_progress'
        ", [$userId])->total;

        $completedTasks = DB::selectOne("
            SELECT COUNT(*) AS total
            FROM tasks t
            JOIN volunteers v
                ON t.volunteer_id = v.id
            WHERE v.user_id = ?
            AND LOWER(v.status) = 'approved'
            AND LOWER(t.status) = 'completed'
        ", [$userId])->total;

        return view('volunteer.dashboard', compact(
            'applications',
            'tasks',
            'pendingTasks',
            'inProgressTasks',
            'completedTasks'
        ));
    }
}