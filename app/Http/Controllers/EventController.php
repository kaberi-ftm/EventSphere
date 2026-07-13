<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
class EventController extends Controller
{
    public function index(): View
    {
        $events = DB::select("
            SELECT
                e.*,
                c.name AS club_name,
                u.name AS creator_name,
                v.name AS venue_name
            FROM events e
            JOIN clubs c
                ON e.club_id = c.id
            JOIN users u
                ON e.created_by = u.id
            LEFT JOIN venues v
                ON e.venue_id = v.id
            ORDER BY e.start_time DESC
        ");

        return view('admin.events.index', compact('events'));
    }

    public function create(): View
    {
        $clubs = DB::select("
            SELECT id, name
            FROM clubs
            ORDER BY name
        ");

        $venues = DB::select("
            SELECT id, name, capacity
            FROM venues
            ORDER BY name
        ");

        return view(
            'admin.events.create',
            compact('clubs', 'venues')
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'club_id' => ['required', 'exists:clubs,id'],
            'venue_id' => ['nullable', 'exists:venues,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_time' => ['required', 'date'],
            'end_time' => ['nullable', 'date', 'after:start_time'],
            'status' => [
                'nullable',
                'in:upcoming,ongoing,completed,cancelled'
            ],
            'max_participants' => ['required', 'integer', 'min:1'],
            'poster' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048'
            ],
        ]);

        $posterPath = $this->storePoster(
            $request,
            $validated['title']
        );

        try {
            DB::insert("
                INSERT INTO events
                (
                    club_id,
                    venue_id,
                    created_by,
                    title,
                    description,
                    start_time,
                    end_time,
                    status,
                    max_participants,
                    poster,
                    created_at,
                    updated_at
                )
                VALUES
                (
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    TO_TIMESTAMP(
                        ?,
                        'YYYY-MM-DD\"T\"HH24:MI'
                    ),
                    TO_TIMESTAMP(
                        ?,
                        'YYYY-MM-DD\"T\"HH24:MI'
                    ),
                    ?,
                    ?,
                    ?,
                    SYSTIMESTAMP,
                    SYSTIMESTAMP
                )
            ", [
                $validated['club_id'],
                $validated['venue_id'] ?? null,
                Auth::id(),
                $validated['title'],
                $validated['description'] ?? null,
                $validated['start_time'],
                $validated['end_time'] ?? null,
                $validated['status'] ?? 'upcoming',
                $validated['max_participants'],
                $posterPath,
            ]);
        } catch (\Throwable $exception) {
            $this->deletePoster($posterPath);

            throw $exception;
        }

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event created successfully.');
    }

    public function show($id): View
    {
        $event = DB::selectOne("
            SELECT
                e.*,
                c.name AS club_name,
                u.name AS creator_name,
                v.name AS venue_name
            FROM events e
            JOIN clubs c
                ON e.club_id = c.id
            JOIN users u
                ON e.created_by = u.id
            LEFT JOIN venues v
                ON e.venue_id = v.id
            WHERE e.id = ?
        ", [$id]);

        abort_if(!$event, 404, 'Event not found.');

        return view('admin.events.show', compact('event'));
    }

    public function edit($id): View
    {
        $event = DB::selectOne("
            SELECT *
            FROM events
            WHERE id = ?
        ", [$id]);

        abort_if(!$event, 404, 'Event not found.');

        $clubs = DB::select("
            SELECT id, name
            FROM clubs
            ORDER BY name
        ");

        $venues = DB::select("
            SELECT id, name, capacity
            FROM venues
            ORDER BY name
        ");

        return view(
            'admin.events.edit',
            compact('event', 'clubs', 'venues')
        );
    }

    public function update(
        Request $request,
        $id
    ): RedirectResponse {
        $event = DB::selectOne("
            SELECT *
            FROM events
            WHERE id = ?
        ", [$id]);

        abort_if(!$event, 404, 'Event not found.');

        $validated = $request->validate([
            'club_id' => ['required', 'exists:clubs,id'],
            'venue_id' => ['nullable', 'exists:venues,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_time' => ['nullable', 'date'],
            'end_time' => ['nullable', 'date'],
            'status' => [
                'nullable',
                'in:upcoming,ongoing,completed,cancelled'
            ],
            'max_participants' => ['required', 'integer', 'min:1'],
            'poster' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048'
            ],
        ]);

        $oldPoster = $event->poster;
        $posterPath = $oldPoster;

        if ($request->hasFile('poster')) {
            $posterPath = $this->storePoster(
                $request,
                $validated['title']
            );
        }

        try {
            DB::update("
                UPDATE events
                SET
                    club_id = ?,
                    venue_id = ?,
                    title = ?,
                    description = ?,
                    start_time = COALESCE(
                        TO_TIMESTAMP(
                            ?,
                            'YYYY-MM-DD\"T\"HH24:MI'
                        ),
                        start_time
                    ),
                    end_time = COALESCE(
                        TO_TIMESTAMP(
                            ?,
                            'YYYY-MM-DD\"T\"HH24:MI'
                        ),
                        end_time
                    ),
                    status = ?,
                    max_participants = ?,
                    poster = ?,
                    updated_at = SYSTIMESTAMP
                WHERE id = ?
            ", [
                $validated['club_id'],
                $validated['venue_id'] ?? null,
                $validated['title'],
                $validated['description'] ?? null,
                $validated['start_time'] ?? null,
                $validated['end_time'] ?? null,
                $validated['status'] ?? $event->status,
                $validated['max_participants'],
                $posterPath,
                $id,
            ]);
        } catch (\Throwable $exception) {
            if ($posterPath !== $oldPoster) {
                $this->deletePoster($posterPath);
            }

            throw $exception;
        }

        if ($posterPath !== $oldPoster) {
            $this->deletePoster($oldPoster);
        }

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        $event = DB::selectOne("
            SELECT id, poster
            FROM events
            WHERE id = ?
        ", [$id]);

        abort_if(!$event, 404, 'Event not found.');

        try {
            DB::delete("
                DELETE FROM events
                WHERE id = ?
            ", [$id]);

            $this->deletePoster($event->poster);

            return redirect()
                ->route('admin.events.index')
                ->with('success', 'Event deleted successfully.');
        } catch (QueryException $exception) {
            return back()->with(
                'error',
                'Event cannot be deleted because registrations or related records exist.'
            );
        }
    }

    private function storePoster(
        Request $request,
        string $title
    ): ?string {
        if (!$request->hasFile('poster')) {
            return null;
        }

        File::ensureDirectoryExists(
            public_path('event-posters')
        );

        $file = $request->file('poster');

        $filename = time()
            . '_'
            . Str::slug($title)
            . '.'
            . $file->getClientOriginalExtension();

        $file->move(
            public_path('event-posters'),
            $filename
        );

        return 'event-posters/' . $filename;
    }

    private function deletePoster(?string $posterPath): void
    {
        if (!$posterPath) {
            return;
        }

        $fullPath = public_path($posterPath);

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }
}