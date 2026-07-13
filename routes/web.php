<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\VenueController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\VolunteerController;
use App\Http\Controllers\TaskController;

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Executive\ExecutiveDashboardController;
use App\Http\Controllers\Participant\ParticipantDashboardController;
use App\Http\Controllers\Volunteer\VolunteerDashboardController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Main Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Volunteer Application
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/volunteers/apply/{event}',
        [VolunteerController::class, 'applyForm']
    )->name('volunteers.apply');

    Route::post(
        '/volunteers/apply',
        [VolunteerController::class, 'store']
    )->name('volunteers.store');

    /*
    |--------------------------------------------------------------------------
    | Volunteer Dashboard
    |--------------------------------------------------------------------------
    |
    | This route is intentionally not protected by role:volunteer.
    | A participant can become an approved volunteer for an event.
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/volunteer/dashboard',
        [VolunteerDashboardController::class, 'index']
    )->name('volunteer.dashboard');

    /*
    |--------------------------------------------------------------------------
    | Volunteer Task Actions
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/volunteer/tasks/{id}/start',
        [TaskController::class, 'start']
    )->name('volunteer.tasks.start');

    Route::post(
        '/volunteer/tasks/{id}/complete',
        [TaskController::class, 'complete']
    )->name('volunteer.tasks.complete');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Admin Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Existing Admin Resources
        |--------------------------------------------------------------------------
        */

        Route::resource('clubs', ClubController::class);
        Route::resource('events', EventController::class);
        Route::resource('venues', VenueController::class);
        Route::resource('registrations', RegistrationController::class);
        Route::resource('attendances', AttendanceController::class);

        /*
        |--------------------------------------------------------------------------
        | Volunteer Administration
        |--------------------------------------------------------------------------
        */
Route::get(
    '/volunteers/create',
    [VolunteerController::class, 'create']
)->name('volunteers.create');

Route::post(
    '/volunteers',
    [VolunteerController::class, 'adminStore']
)->name('volunteers.store');

Route::get(
    '/volunteers/{id}/edit',
    [VolunteerController::class, 'edit']
)->name('volunteers.edit');

Route::put(
    '/volunteers/{id}',
    [VolunteerController::class, 'update']
)->name('volunteers.update');
        Route::post(
            '/volunteers/{id}/approve',
            [VolunteerController::class, 'approve']
        )->name('volunteers.approve');

        Route::post(
            '/volunteers/{id}/reject',
            [VolunteerController::class, 'reject']
        )->name('volunteers.reject');

        Route::resource('volunteers', VolunteerController::class)
            ->only([
                'index',
                'show',
                'destroy',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Task Administration
        |--------------------------------------------------------------------------
        */

        Route::resource('tasks', TaskController::class);
    });

/*
|--------------------------------------------------------------------------
| Executive Dashboard
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:executive'])->group(function () {
    Route::get(
        '/executive/dashboard',
        [ExecutiveDashboardController::class, 'index']
    )->name('executive.dashboard');
});

/*
|--------------------------------------------------------------------------
| Participant Dashboard
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:participant'])->group(function () {
    Route::get(
        '/participant/dashboard',
        [ParticipantDashboardController::class, 'index']
    )->name('participant.dashboard');
});

/*
|--------------------------------------------------------------------------
| Laravel Breeze Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';