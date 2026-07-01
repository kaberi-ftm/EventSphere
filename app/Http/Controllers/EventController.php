<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class EventController extends Controller
{
    public function index()
    {
        $events = DB::select("
            SELECT
                e.*,
                c.name AS club_name,
                u.name AS creator_name
            FROM events e
            JOIN clubs c
                ON e.club_id = c.id
            JOIN users u
                ON e.created_by = u.id
            ORDER BY e.start_time DESC
        ");

        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        $clubs = DB::select("
            SELECT id, name
            FROM clubs
            ORDER BY name
        ");

        return view('admin.events.create', compact('clubs'));
    }

    public function store(Request $request)
{
    $request->validate([
        'club_id' => 'required',
        'title' => 'required|max:255',
        'description' => 'nullable',
        'start_time' => 'required',
        'max_participants' => 'required|integer',

        'poster' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $posterPath = null;

    if ($request->hasFile('poster')) {

        $file = $request->file('poster');

        $filename =
            time() . '_' .
            Str::slug($request->title) . '.' .
            $file->getClientOriginalExtension();

        $file->move(
            public_path('event-posters'),
            $filename
        );

        $posterPath = 'event-posters/' . $filename;
    }

    DB::insert("
        INSERT INTO events
        (
            club_id,
            created_by,
            title,
            description,
            start_time,
            status,
            max_participants,
            poster,
            created_at,
            updated_at
        )
        VALUES
        (?, ?, ?, ?, TO_TIMESTAMP(?, 'YYYY-MM-DD\"T\"HH24:MI'), ?, ?, ?, SYSDATE, SYSDATE)
    ", [

        $request->club_id,
        auth()->id(),

        $request->title,
        $request->description,

        $request->start_time,

        'upcoming',

        $request->max_participants,

        $posterPath

    ]);

    return redirect()
        ->route('admin.events.index')
        ->with('success', 'Event created successfully.');
}

    public function show($id)
    {
        $event = DB::selectOne("
            SELECT
                e.*,
                c.name AS club_name,
                u.name AS creator_name
            FROM events e
            JOIN clubs c
                ON e.club_id = c.id
            JOIN users u
                ON e.created_by = u.id
            WHERE e.id = ?
        ", [$id]);

        return view('admin.events.show', compact('event'));
    }

    public function edit($id)
    {
        $event = DB::selectOne(
            "SELECT * FROM events WHERE id = ?",
            [$id]
        );

        $clubs = DB::select(
            "SELECT id, name FROM clubs ORDER BY name"
        );

        return view('admin.events.edit', compact('event', 'clubs'));
    }

   public function update(Request $request, $id)
{
    $posterPath = DB::selectOne(
        "SELECT poster FROM events WHERE id = ?",
        [$id]
    )->poster;

    if ($request->hasFile('poster')) {

        $file = $request->file('poster');

        $filename =
            time() . '_' .
            $file->getClientOriginalName();

        $file->move(
            public_path('event-posters'),
            $filename
        );

        $posterPath = 'event-posters/' . $filename;
    }

    DB::update("
        UPDATE events
        SET
            club_id = ?,
            title = ?,
            description = ?,
            max_participants = ?,
            poster = ?,
            updated_at = SYSDATE
        WHERE id = ?
    ", [

        $request->club_id,
        $request->title,
        $request->description,
        $request->max_participants,
        $posterPath,
        $id

    ]);

    return redirect()
        ->route('admin.events.index')
        ->with('success', 'Event updated.');
}
    public function destroy($id)
    {
        DB::delete(
            "DELETE FROM events WHERE id = ?",
            [$id]
        );

        return back()->with('success', 'Event deleted.');
    }
}