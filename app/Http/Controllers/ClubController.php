<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Club;
use Illuminate\Support\Facades\DB;

class ClubController extends Controller
{
    public function index()
    {
        // Oracle SQL example (DBMS proof)
        $clubs = DB::select("SELECT * FROM clubs");

        return view('clubs.index', compact('clubs'));
    }

    public function create()
    {
        return view('clubs.create');
    }

    public function store(Request $request)
    {
        DB::insert("
            INSERT INTO clubs (name, description, founded_date, admin_user_id)
            VALUES (?, ?, SYSDATE, ?)
        ", [
            $request->name,
            $request->description,
            $request->admin_user_id
        ]);

        return redirect()->route('clubs.index');
    }

    public function edit($id)
    {
        $club = DB::select("SELECT * FROM clubs WHERE club_id = ?", [$id]);

        return view('clubs.edit', compact('club'));
    }

    public function update(Request $request, $id)
    {
        DB::update("
            UPDATE clubs 
            SET name = ?, description = ?
            WHERE club_id = ?
        ", [
            $request->name,
            $request->description,
            $id
        ]);

        return redirect()->route('clubs.index');
    }

    public function destroy($id)
    {
        DB::delete("DELETE FROM clubs WHERE club_id = ?", [$id]);

        return back();
    }
}