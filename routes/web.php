<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Executive\ExecutiveDashboardController;
use App\Http\Controllers\Volunteer\VolunteerDashboardController;
use App\Http\Controllers\Participant\ParticipantDashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard');
});

Route::middleware(['auth', 'role:executive'])->group(function () {
    Route::get('/executive/dashboard', [ExecutiveDashboardController::class, 'index'])
        ->name('executive.dashboard');
});

Route::middleware(['auth', 'role:volunteer'])->group(function () {
    Route::get('/volunteer/dashboard', [VolunteerDashboardController::class, 'index'])
        ->name('volunteer.dashboard');
});

Route::middleware(['auth', 'role:participant'])->group(function () {
    Route::get('/participant/dashboard', [ParticipantDashboardController::class, 'index'])
        ->name('participant.dashboard');
});
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
 
});

require __DIR__.'/auth.php';
