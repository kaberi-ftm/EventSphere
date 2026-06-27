<?php
namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Event;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'clubs' => Club::latest()->take(5)->get(),
            'events' => Event::latest()->take(5)->get(),
        ]);
    }
}