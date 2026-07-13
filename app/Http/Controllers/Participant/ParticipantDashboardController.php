<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
class ParticipantDashboardController extends Controller
{
    public function index(): View
    {
        $userId = Auth::id();

        $upcomingEvents = DB::select("
            SELECT
                e.*,
                c.name AS club_name,
                v.name AS venue_name
            FROM events e
            JOIN clubs c
                ON e.club_id = c.id
            LEFT JOIN venues v
                ON e.venue_id = v.id
            WHERE e.start_time > SYSDATE
            AND LOWER(e.status) <> 'cancelled'
            ORDER BY e.start_time
            FETCH FIRST 5 ROWS ONLY
        ");

        $registrations = DB::select("
            SELECT
                r.*,
                e.title AS event_title,
                e.start_time
            FROM registrations r
            JOIN events e
                ON r.event_id = e.id
            WHERE r.user_id = ?
            ORDER BY r.registered_at DESC
        ", [$userId]);

        $volunteerApplications = DB::select("
            SELECT
                v.*,
                e.title AS event_title
            FROM volunteers v
            JOIN events e
                ON v.event_id = e.id
            WHERE v.user_id = ?
            ORDER BY v.applied_at DESC
        ", [$userId]);

        return view(
            'participant.dashboard',
            compact(
                'upcomingEvents',
                'registrations',
                'volunteerApplications'
            )
        );
    }
}