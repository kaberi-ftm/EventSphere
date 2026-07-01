<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalClubs = DB::table('clubs')->count();
        $totalEvents = DB::table('events')->count();
        $totalUsers = DB::table('users')->count();
        $totalVolunteers = DB::table('volunteers')->count();

        return view('admin.dashboard', compact(
            'totalClubs',
            'totalEvents',
            'totalUsers',
            'totalVolunteers'
        ));
    }
}