<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClubController extends Controller
{
    /**
     * Display all clubs.
     */
    public function index()
    {
        $clubs = DB::select("
            SELECT
                c.*,
                u.name AS admin_name
            FROM clubs c
            LEFT JOIN users u
                ON c.admin_user_id = u.id
            ORDER BY c.id
        ");

        return view('admin.clubs.index', compact('clubs'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $admins = DB::select("
            SELECT id, name
            FROM users
            ORDER BY name
        ");

        return view('admin.clubs.create', compact('admins'));
    }

    /**
     * Store a new club.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'description' => 'nullable',
            'founded_date' => 'nullable|date',
            'admin_user_id' => 'nullable|exists:users,id',
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
            VALUES (?, ?, ?, ?, SYSDATE, SYSDATE)
        ", [
            $request->name,
            $request->description,
            $request->founded_date,
            $request->admin_user_id,
        ]);

        return redirect()
            ->route('admin.clubs.index')
            ->with('success', 'Club created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit($id)
    {
        $club = DB::selectOne("
            SELECT *
            FROM clubs
            WHERE id = ?
        ", [$id]);

        $admins = DB::select("
            SELECT id, name
            FROM users
            ORDER BY name
        ");

        return view('admin.clubs.edit', compact('club', 'admins'));
    }

    /**
     * Update club.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:100',
            'description' => 'nullable',
            'founded_date' => 'nullable|date',
            'admin_user_id' => 'nullable|exists:users,id',
        ]);

        DB::update("
            UPDATE clubs
            SET
                name = ?,
                description = ?,
                founded_date = ?,
                admin_user_id = ?,
                updated_at = SYSDATE
            WHERE id = ?
        ", [
            $request->name,
            $request->description,
            $request->founded_date,
            $request->admin_user_id,
            $id
        ]);

        return redirect()
            ->route('admin.clubs.index')
            ->with('success', 'Club updated successfully.');
    }

    /**
     * Delete club.
     */
    public function destroy($id)
    {
        DB::delete("
            DELETE FROM clubs
            WHERE id = ?
        ", [$id]);

        return redirect()
            ->route('admin.clubs.index')
            ->with('success', 'Club deleted successfully.');
    }
  public function show($id)
{
    $club = DB::selectOne("
        SELECT c.*, u.name AS admin_name
        FROM clubs c
        LEFT JOIN users u
        ON c.admin_user_id = u.id
        WHERE c.id = ?
    ", [$id]);

    return view('admin.clubs.show', compact('club'));
}
}