<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $totalClubs = DB::table('clubs')->count();
        $totalEvents = DB::table('events')->count();
        $totalUsers = DB::table('users')->count();
        $totalVolunteers = DB::table('volunteers')->count();

        $pendingVolunteers = DB::table('volunteers')
            ->whereRaw('LOWER(status) = ?', ['pending'])
            ->count();

        $totalRegistrations = DB::table('registrations')->count();

        $totalAttendances = DB::table('attendances')
            ->where('is_present', 'Y')
            ->count();

        $pendingTasks = DB::table('tasks')
            ->whereRaw('LOWER(status) = ?', ['pending'])
            ->count();

        return view('admin.dashboard', compact(
            'totalClubs',
            'totalEvents',
            'totalUsers',
            'totalVolunteers',
            'pendingVolunteers',
            'totalRegistrations',
            'totalAttendances',
            'pendingTasks'
        ));
    }
}