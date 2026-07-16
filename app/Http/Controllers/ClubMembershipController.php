<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClubMembershipController extends Controller
{
    public function store(
        Request $request,
        $userId
    ): RedirectResponse {
        $validated = $request->validate([
            'club_id' => ['required', 'integer'],
            'member_role' => [
                'required',
                'in:member,executive,president,secretary,treasurer,coordinator',
            ],
        ]);

        $user = DB::selectOne("
            SELECT id
            FROM users
            WHERE id = ?
        ", [$userId]);

        abort_if(!$user, 404, 'User not found.');

        $club = DB::selectOne("
            SELECT id
            FROM clubs
            WHERE id = ?
        ", [$validated['club_id']]);

        if (!$club) {
            return back()->with(
                'error',
                'Selected club does not exist.'
            );
        }

        $duplicate = DB::selectOne("
            SELECT id
            FROM club_memberships
            WHERE user_id = ?
            AND club_id = ?
        ", [
            $userId,
            $validated['club_id'],
        ]);

        if ($duplicate) {
            return back()->with(
                'error',
                'User is already a member of this club.'
            );
        }

        try {
            DB::insert("
                INSERT INTO club_memberships
                (
                    user_id,
                    club_id,
                    member_role,
                    joined_at,
                    created_at,
                    updated_at
                )
                VALUES
                (
                    ?, ?, ?, SYSDATE,
                    SYSTIMESTAMP,
                    SYSTIMESTAMP
                )
            ", [
                $userId,
                $validated['club_id'],
                $validated['member_role'],
            ]);
        } catch (QueryException $exception) {
            return back()->with(
                'error',
                'Club membership could not be created.'
            );
        }

        return back()->with(
            'success',
            'Club membership added successfully.'
        );
    }

    public function update(
        Request $request,
        $id
    ): RedirectResponse {
        $validated = $request->validate([
            'member_role' => [
                'required',
                'in:member,executive,president,secretary,treasurer,coordinator',
            ],
        ]);

        $membership = DB::selectOne("
            SELECT id
            FROM club_memberships
            WHERE id = ?
        ", [$id]);

        abort_if(
            !$membership,
            404,
            'Club membership not found.'
        );

        DB::update("
            UPDATE club_memberships
            SET
                member_role = ?,
                updated_at = SYSTIMESTAMP
            WHERE id = ?
        ", [
            $validated['member_role'],
            $id,
        ]);

        return back()->with(
            'success',
            'Membership role updated successfully.'
        );
    }

    public function destroy($id): RedirectResponse
    {
        DB::delete("
            DELETE FROM club_memberships
            WHERE id = ?
        ", [$id]);

        return back()->with(
            'success',
            'Club membership removed successfully.'
        );
    }
}