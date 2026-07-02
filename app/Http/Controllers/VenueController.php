<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VenueController extends Controller
{
    public function index()
    {
        $venues = DB::select("
            SELECT *
            FROM venues
            ORDER BY id
        ");

        return view('admin.venues.index', compact('venues'));
    }

    public function create()
    {
        return view('admin.venues.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'location' => 'required',
            'capacity' => 'required|integer|min:1'
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
            VALUES (?, ?, ?, ?, SYSTIMESTAMP, SYSTIMESTAMP)
        ", [
            $request->name,
            $request->location,
            $request->capacity,
            $request->description
        ]);

        return redirect()
            ->route('admin.venues.index')
            ->with('success', 'Venue created successfully.');
    }

    public function show($id)
    {
        $venue = DB::selectOne("
            SELECT *
            FROM venues
            WHERE id = ?
        ", [$id]);

        return view('admin.venues.show', compact('venue'));
    }

    public function edit($id)
    {
        $venue = DB::selectOne("
            SELECT *
            FROM venues
            WHERE id = ?
        ", [$id]);

        return view('admin.venues.edit', compact('venue'));
    }

    public function update(Request $request, $id)
    {
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
            $request->name,
            $request->location,
            $request->capacity,
            $request->description,
            $id
        ]);

        return redirect()
            ->route('admin.venues.index')
            ->with('success', 'Venue updated successfully.');
    }

    public function destroy($id)
    {
        DB::delete("
            DELETE FROM venues
            WHERE id = ?
        ", [$id]);

        return redirect()
            ->route('admin.venues.index')
            ->with('success', 'Venue deleted successfully.');
    }
}