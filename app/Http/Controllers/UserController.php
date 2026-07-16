<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $roleId = $request->query('role_id');

        $sortMap = [
            'name' => 'u.name',
            'email' => 'u.email',
            'role' => 'r.display_name',
            'points' => 'u.engagement_points',
            'clubs' => 'club_count',
            'created' => 'u.created_at',
        ];

        $sort = (string) $request->query('sort', 'name');
        $sortColumn = $sortMap[$sort] ?? 'u.name';

        $direction = strtolower(
            (string) $request->query('direction', 'asc')
        ) === 'desc' ? 'DESC' : 'ASC';

        $conditions = ['1 = 1'];
        $bindings = [];

        if ($search !== '') {
            $keyword = '%' . strtolower($search) . '%';

            $conditions[] = "
                (
                    LOWER(u.name) LIKE ?
                    OR LOWER(u.email) LIKE ?
                    OR LOWER(NVL(r.display_name, '')) LIKE ?
                )
            ";

            array_push($bindings, $keyword, $keyword, $keyword);
        }

        if ($roleId !== null && $roleId !== '') {
            $conditions[] = 'u.role_id = ?';
            $bindings[] = $roleId;
        }

        $users = DB::select("
            SELECT
                u.id,
                u.role_id,
                u.name,
                u.email,
                u.email_verified_at,
                u.engagement_points,
                u.created_at,
                u.updated_at,
                r.name AS role_name,
                r.display_name AS role_display_name,
                NVL(membership_summary.club_count, 0)
                    AS club_count,
                DENSE_RANK() OVER (
                    ORDER BY u.engagement_points DESC
                ) AS engagement_rank
            FROM users u
            LEFT JOIN roles r
                ON r.id = u.role_id
            LEFT JOIN
            (
                SELECT
                    user_id,
                    COUNT(*) AS club_count
                FROM club_memberships
                GROUP BY user_id
            ) membership_summary
                ON membership_summary.user_id = u.id
            WHERE " . implode(' AND ', $conditions) . "
            ORDER BY {$sortColumn} {$direction}, u.id DESC
        ", $bindings);

        $roles = DB::select("
            SELECT id, name, display_name
            FROM roles
            ORDER BY display_name
        ");

        return view('admin.users.index', compact(
            'users',
            'roles',
            'search',
            'roleId',
            'sort',
            'direction'
        ));
    }

    public function create(): View
    {
        $roles = DB::select("
            SELECT id, name, display_name
            FROM roles
            ORDER BY display_name
        ");

        $clubs = DB::select("
            SELECT id, name
            FROM clubs
            ORDER BY name
        ");

        return view('admin.users.create', compact(
            'roles',
            'clubs'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'role_id' => ['nullable', 'integer'],
            'engagement_points' => [
                'required',
                'integer',
                'min:0',
            ],
            'club_id' => ['nullable', 'integer'],
            'member_role' => [
                'nullable',
                'in:member,executive,president,secretary,treasurer,coordinator',
            ],
        ]);

        $email = strtolower(trim($validated['email']));

        $duplicate = DB::selectOne("
            SELECT id
            FROM users
            WHERE LOWER(email) = ?
        ", [$email]);

        if ($duplicate) {
            return back()
                ->withInput()
                ->with('error', 'This email address already exists.');
        }

        if (!empty($validated['role_id'])) {
            $role = DB::selectOne("
                SELECT id
                FROM roles
                WHERE id = ?
            ", [$validated['role_id']]);

            if (!$role) {
                return back()
                    ->withInput()
                    ->with('error', 'Selected role does not exist.');
            }
        }

        try {
            DB::transaction(function () use ($validated, $email) {
                DB::insert("
                    INSERT INTO users
                    (
                        role_id,
                        name,
                        email,
                        password,
                        engagement_points,
                        created_at,
                        updated_at
                    )
                    VALUES
                    (
                        ?, ?, ?, ?, ?,
                        SYSTIMESTAMP,
                        SYSTIMESTAMP
                    )
                ", [
                    $validated['role_id'] ?? null,
                    trim($validated['name']),
                    $email,
                    Hash::make($validated['password']),
                    $validated['engagement_points'],
                ]);

                if (!empty($validated['club_id'])) {
                    $user = DB::selectOne("
                        SELECT id
                        FROM users
                        WHERE LOWER(email) = ?
                    ", [$email]);

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
                        $user->id,
                        $validated['club_id'],
                        $validated['member_role'] ?? 'member',
                    ]);
                }
            });
        } catch (QueryException $exception) {
            return back()
                ->withInput()
                ->with('error', 'User could not be created.');
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function show($id): View
    {
        $user = DB::selectOne("
            SELECT
                u.id,
                u.role_id,
                u.name,
                u.email,
                u.email_verified_at,
                u.engagement_points,
                u.created_at,
                u.updated_at,
                r.name AS role_name,
                r.display_name AS role_display_name,
                DENSE_RANK() OVER (
                    ORDER BY u.engagement_points DESC
                ) AS engagement_rank
            FROM users u
            LEFT JOIN roles r
                ON r.id = u.role_id
            WHERE u.id = ?
        ", [$id]);

        abort_if(!$user, 404, 'User not found.');

        $memberships = DB::select("
            SELECT
                cm.id,
                cm.club_id,
                cm.member_role,
                cm.joined_at,
                cm.created_at,
                c.name AS club_name
            FROM club_memberships cm
            JOIN clubs c
                ON c.id = cm.club_id
            WHERE cm.user_id = ?
            ORDER BY c.name
        ", [$id]);

        $clubs = DB::select("
            SELECT id, name
            FROM clubs
            WHERE id NOT IN
            (
                SELECT club_id
                FROM club_memberships
                WHERE user_id = ?
            )
            ORDER BY name
        ", [$id]);

        return view('admin.users.show', compact(
            'user',
            'memberships',
            'clubs'
        ));
    }

    public function edit($id): View
    {
        $user = DB::selectOne("
            SELECT *
            FROM users
            WHERE id = ?
        ", [$id]);

        abort_if(!$user, 404, 'User not found.');

        $roles = DB::select("
            SELECT id, name, display_name
            FROM roles
            ORDER BY display_name
        ");

        return view('admin.users.edit', compact(
            'user',
            'roles'
        ));
    }

    public function update(
        Request $request,
        $id
    ): RedirectResponse {
        $user = DB::selectOne("
            SELECT id, email
            FROM users
            WHERE id = ?
        ", [$id]);

        abort_if(!$user, 404, 'User not found.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'min:8'],
            'role_id' => ['nullable', 'integer'],
            'engagement_points' => [
                'required',
                'integer',
                'min:0',
            ],
        ]);

        $email = strtolower(trim($validated['email']));

        $duplicate = DB::selectOne("
            SELECT id
            FROM users
            WHERE LOWER(email) = ?
            AND id <> ?
        ", [$email, $id]);

        if ($duplicate) {
            return back()
                ->withInput()
                ->with('error', 'This email is used by another user.');
        }

        if (!empty($validated['role_id'])) {
            $role = DB::selectOne("
                SELECT id
                FROM roles
                WHERE id = ?
            ", [$validated['role_id']]);

            if (!$role) {
                return back()
                    ->withInput()
                    ->with('error', 'Selected role does not exist.');
            }
        }

        if (!empty($validated['password'])) {
            DB::update("
                UPDATE users
                SET
                    role_id = ?,
                    name = ?,
                    email = ?,
                    password = ?,
                    engagement_points = ?,
                    updated_at = SYSTIMESTAMP
                WHERE id = ?
            ", [
                $validated['role_id'] ?? null,
                trim($validated['name']),
                $email,
                Hash::make($validated['password']),
                $validated['engagement_points'],
                $id,
            ]);
        } else {
            DB::update("
                UPDATE users
                SET
                    role_id = ?,
                    name = ?,
                    email = ?,
                    engagement_points = ?,
                    updated_at = SYSTIMESTAMP
                WHERE id = ?
            ", [
                $validated['role_id'] ?? null,
                trim($validated['name']),
                $email,
                $validated['engagement_points'],
                $id,
            ]);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        if ((string) Auth::id() === (string) $id) {
            return back()->with(
                'error',
                'You cannot delete your own account.'
            );
        }

        try {
            DB::transaction(function () use ($id) {
                DB::delete("
                    DELETE FROM club_memberships
                    WHERE user_id = ?
                ", [$id]);

                DB::delete("
                    DELETE FROM users
                    WHERE id = ?
                ", [$id]);
            });
        } catch (QueryException $exception) {
            return back()->with(
                'error',
                'User cannot be deleted because related records exist.'
            );
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}