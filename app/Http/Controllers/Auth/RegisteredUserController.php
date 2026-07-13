<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:users,email'
            ],
            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults()
            ],
        ]);

        $participantRoleId = DB::table('roles')
            ->whereRaw('LOWER(name) = ?', ['participant'])
            ->value('id');

        if (!$participantRoleId) {
            throw ValidationException::withMessages([
                'email' => 'Participant role was not found in the roles table.',
            ]);
        }

        $user = DB::transaction(function () use (
            $validated,
            $participantRoleId
        ) {
            return User::create([
                'name' => $validated['name'],
                'email' => strtolower($validated['email']),
                'password' => Hash::make($validated['password']),
                'role_id' => $participantRoleId,
            ]);
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('participant.dashboard');
    }
}