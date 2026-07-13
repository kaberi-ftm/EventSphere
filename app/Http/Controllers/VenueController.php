<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VenueController extends Controller
{
    public function index(): View
    {
        $venues = DB::select("
            SELECT *
            FROM venues
            ORDER BY id DESC
        ");

        return view('admin.venues.index', compact('venues'));
    }

    public function create(): View
    {
        return view('admin.venues.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'location' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
        ]);

        DB::insert("
            INSERT INTO venues
            (
                name,
                location,
                capacity,
                description,
                created_at,
                updated_at
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                SYSTIMESTAMP,
                SYSTIMESTAMP
            )
        ", [
            $validated['name'],
            $validated['location'],
            $validated['capacity'],
            $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('admin.venues.index')
            ->with('success', 'Venue created successfully.');
    }

    public function show($id): View
    {
        $venue = DB::selectOne("
            SELECT *
            FROM venues
            WHERE id = ?
        ", [$id]);

        abort_if(!$venue, 404, 'Venue not found.');

        return view('admin.venues.show', compact('venue'));
    }

    public function edit($id): View
    {
        $venue = DB::selectOne("
            SELECT *
            FROM venues
            WHERE id = ?
        ", [$id]);

        abort_if(!$venue, 404, 'Venue not found.');

        return view('admin.venues.edit', compact('venue'));
    }

    public function update(
        Request $request,
        $id
    ): RedirectResponse {
        $venue = DB::selectOne("
            SELECT id
            FROM venues
            WHERE id = ?
        ", [$id]);

        abort_if(!$venue, 404, 'Venue not found.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'location' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
        ]);

        DB::update("
            UPDATE venues
            SET
                name = ?,
                location = ?,
                capacity = ?,
                description = ?,
                updated_at = SYSTIMESTAMP
            WHERE id = ?
        ", [
            $validated['name'],
            $validated['location'],
            $validated['capacity'],
            $validated['description'] ?? null,
            $id,
        ]);

        return redirect()
            ->route('admin.venues.index')
            ->with('success', 'Venue updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        $venue = DB::selectOne("
            SELECT id
            FROM venues
            WHERE id = ?
        ", [$id]);

        abort_if(!$venue, 404, 'Venue not found.');

        try {
            DB::delete("
                DELETE FROM venues
                WHERE id = ?
            ", [$id]);

            return redirect()
                ->route('admin.venues.index')
                ->with('success', 'Venue deleted successfully.');
        } catch (QueryException $exception) {
            return back()->with(
                'error',
                'Venue cannot be deleted because one or more events are using it.'
            );
        }
    }
}