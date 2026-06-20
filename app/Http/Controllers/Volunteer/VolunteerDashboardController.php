<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VolunteerDashboardController extends Controller
{
     public function index()
    {
        return view('volunteer.dashboard');
    }
}
