<?php

namespace App\Http\Controllers\Executive;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExecutiveDashboardController extends Controller
{
     public function index()
    {
        return view('executive.dashboard');
    }
}
