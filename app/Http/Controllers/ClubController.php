<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ClubController extends Controller
{
    public function index(): View
    {
        $clubs = DB::select("
            SELECT
                c.*,
                u.name AS admin_name
            FROM clubs c
            LEFT JOIN users u
                ON c.admin_user_id = u.id
            ORDER BY c.id DESC
        ");

        return view('admin.clubs.index', compact('clubs'));
    }

    public function create(): View
    {
        $admins = DB::select("
            SELECT id, name
            FROM users
            ORDER BY name
        ");

        return view('admin.clubs.create', compact('admins'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'founded_date' => ['nullable', 'date'],
            'admin_user_id' => ['nullable', 'exists:users,id'],
        ]);

        DB::insert("
            INSERT INTO clubs
            (
                name,
                description,
                founded_date,
                admin_user_id,
                created_at,
                updated_at
            )
            VALUES
            (
                ?,
                ?,
                TO_DATE(?, 'YYYY-MM-DD'),
                ?,
                SYSTIMESTAMP,
                SYSTIMESTAMP
            )
        ", [
            $validated['name'],
            $validated['description'] ?? null,
            $validated['founded_date'] ?? null,
            $validated['admin_user_id'] ?? null,
        ]);

        return redirect()
            ->route('admin.clubs.index')
            ->with('success', 'Club created successfully.');
    }

    public function show($id): View
    {
        $club = DB::selectOne("
            SELECT
                c.*,
                u.name AS admin_name
            FROM clubs c
            LEFT JOIN users u
                ON c.admin_user_id = u.id
            WHERE c.id = ?
        ", [$id]);

        abort_if(!$club, 404, 'Club not found.');

        return view('admin.clubs.show', compact('club'));
    }

    public function edit($id): View
    {
        $club = DB::selectOne("
            SELECT *
            FROM clubs
            WHERE id = ?
        ", [$id]);

        abort_if(!$club, 404, 'Club not found.');

        $admins = DB::select("
            SELECT id, name
            FROM users
            ORDER BY name
        ");

        return view('admin.clubs.edit', compact('club', 'admins'));
    }

    public function update(
        Request $request,
        $id
    ): RedirectResponse {
        $club = DB::selectOne("
            SELECT id
            FROM clubs
            WHERE id = ?
        ", [$id]);

        abort_if(!$club, 404, 'Club not found.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'founded_date' => ['nullable', 'date'],
            'admin_user_id' => ['nullable', 'exists:users,id'],
        ]);

        DB::update("
            UPDATE clubs
            SET
                name = ?,
                description = ?,
                founded_date = TO_DATE(?, 'YYYY-MM-DD'),
                admin_user_id = ?,
                updated_at = SYSTIMESTAMP
            WHERE id = ?
        ", [
            $validated['name'],
            $validated['description'] ?? null,
            $validated['founded_date'] ?? null,
            $validated['admin_user_id'] ?? null,
            $id,
        ]);

        return redirect()
            ->route('admin.clubs.index')
            ->with('success', 'Club updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        $club = DB::selectOne("
            SELECT id
            FROM clubs
            WHERE id = ?
        ", [$id]);

        abort_if(!$club, 404, 'Club not found.');

        try {
            DB::delete("
                DELETE FROM clubs
                WHERE id = ?
            ", [$id]);

            return redirect()
                ->route('admin.clubs.index')
                ->with('success', 'Club deleted successfully.');
        } catch (QueryException $exception) {
            return back()->with(
                'error',
                'Club cannot be deleted because related events or memberships exist.'
            );
        }
    }
}