<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = trim((string) $request->query('status', ''));
        $userId = $request->query('user_id');

        $sortMap = [
            'created' => 'n.created_at',
            'recipient' => 'u.name',
            'status' => 'n.read_at',
        ];

        $sort = (string) $request->query('sort', 'created');
        $sortColumn = $sortMap[$sort] ?? 'n.created_at';

        $direction = strtolower(
            (string) $request->query('direction', 'desc')
        ) === 'asc' ? 'ASC' : 'DESC';

        $conditions = ['1 = 1'];
        $bindings = [];

        if ($search !== '') {
            $keyword = '%' . strtolower($search) . '%';

            $conditions[] = "
                (
                    LOWER(u.name) LIKE ?
                    OR LOWER(u.email) LIKE ?
                    OR LOWER(
                        DBMS_LOB.SUBSTR(n.data, 4000, 1)
                    ) LIKE ?
                )
            ";

            array_push(
                $bindings,
                $keyword,
                $keyword,
                $keyword
            );
        }

        if ($status === 'read') {
            $conditions[] = 'n.read_at IS NOT NULL';
        }

        if ($status === 'unread') {
            $conditions[] = 'n.read_at IS NULL';
        }

        if ($userId !== null && $userId !== '') {
            $conditions[] = 'n.notifiable_id = ?';
            $bindings[] = $userId;
        }

        $notifications = DB::select("
            SELECT
                n.id,
                n.type,
                n.notifiable_type,
                n.notifiable_id,
                DBMS_LOB.SUBSTR(
                    n.data,
                    4000,
                    1
                ) AS data_text,
                n.read_at,
                n.created_at,
                n.updated_at,
                u.name AS recipient_name,
                u.email AS recipient_email,
                CASE
                    WHEN n.read_at IS NULL
                    THEN 'unread'
                    ELSE 'read'
                END AS read_status
            FROM notifications n
            LEFT JOIN users u
                ON u.id = n.notifiable_id
            WHERE " . implode(' AND ', $conditions) . "
            ORDER BY {$sortColumn} {$direction}, n.id
        ", $bindings);

        foreach ($notifications as $notification) {
            $data = json_decode(
                (string) $notification->data_text,
                true
            );

            $notification->title =
                $data['title'] ?? 'Notification';

            $notification->message =
                $data['message'] ?? '';

            $notification->level =
                $data['level'] ?? 'info';

            $notification->action_url =
                $data['action_url'] ?? null;
        }

        $users = DB::select("
            SELECT id, name, email
            FROM users
            ORDER BY name
        ");

        $summary = DB::selectOne("
            SELECT
                COUNT(*) AS total_notifications,
                SUM(
                    CASE
                        WHEN read_at IS NULL THEN 1
                        ELSE 0
                    END
                ) AS unread_notifications,
                SUM(
                    CASE
                        WHEN read_at IS NOT NULL THEN 1
                        ELSE 0
                    END
                ) AS read_notifications
            FROM notifications
        ");

        return view('admin.notifications.index', compact(
            'notifications',
            'users',
            'summary',
            'search',
            'status',
            'userId',
            'sort',
            'direction'
        ));
    }

    public function create(): View
    {
        $users = DB::select("
            SELECT id, name, email
            FROM users
            ORDER BY name
        ");

        return view(
            'admin.notifications.create',
            compact('users')
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'recipient_mode' => [
                'required',
                'in:single,all',
            ],
            'user_id' => [
                'nullable',
                'integer',
            ],
            'title' => [
                'required',
                'string',
                'max:200',
            ],
            'message' => [
                'required',
                'string',
                'max:1000',
            ],
            'level' => [
                'required',
                'in:info,success,warning,danger',
            ],
            'action_url' => [
                'nullable',
                'string',
                'max:500',
            ],
        ]);

        if (
            $validated['recipient_mode'] === 'single'
            && empty($validated['user_id'])
        ) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Please select a recipient.'
                );
        }

        if ($validated['recipient_mode'] === 'all') {
            $recipients = DB::select("
                SELECT id
                FROM users
                ORDER BY id
            ");
        } else {
            $recipient = DB::selectOne("
                SELECT id
                FROM users
                WHERE id = ?
            ", [$validated['user_id']]);

            if (!$recipient) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Selected user does not exist.'
                    );
            }

            $recipients = [$recipient];
        }

        $data = json_encode([
            'title' => trim($validated['title']),
            'message' => trim($validated['message']),
            'level' => $validated['level'],
            'action_url' =>
                $validated['action_url'] ?? null,
        ], JSON_UNESCAPED_UNICODE);

        DB::transaction(function () use (
            $recipients,
            $data
        ) {
            foreach ($recipients as $recipient) {
                DB::insert("
                    INSERT INTO notifications
                    (
                        id,
                        type,
                        notifiable_type,
                        notifiable_id,
                        data,
                        read_at,
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
                        NULL,
                        SYSTIMESTAMP,
                        SYSTIMESTAMP
                    )
                ", [
                    (string) Str::uuid(),
                    'App\\Notifications\\SystemNotification',
                    'App\\Models\\User',
                    $recipient->id,
                    $data,
                ]);
            }
        });

        return redirect()
            ->route('admin.notifications.index')
            ->with(
                'success',
                count($recipients)
                . ' notification(s) sent successfully.'
            );
    }

    public function show(string $id): View
    {
        $notification = DB::selectOne("
            SELECT
                n.id,
                n.type,
                n.notifiable_type,
                n.notifiable_id,
                DBMS_LOB.SUBSTR(
                    n.data,
                    4000,
                    1
                ) AS data_text,
                n.read_at,
                n.created_at,
                n.updated_at,
                u.name AS recipient_name,
                u.email AS recipient_email
            FROM notifications n
            LEFT JOIN users u
                ON u.id = n.notifiable_id
            WHERE n.id = ?
        ", [$id]);

        abort_if(
            !$notification,
            404,
            'Notification not found.'
        );

        $data = json_decode(
            (string) $notification->data_text,
            true
        );

        $notification->title =
            $data['title'] ?? 'Notification';

        $notification->message =
            $data['message'] ?? '';

        $notification->level =
            $data['level'] ?? 'info';

        $notification->action_url =
            $data['action_url'] ?? null;

        return view(
            'admin.notifications.show',
            compact('notification')
        );
    }

    public function markAsRead(
        string $id
    ): RedirectResponse {
        DB::update("
            UPDATE notifications
            SET
                read_at = NVL(
                    read_at,
                    SYSTIMESTAMP
                ),
                updated_at = SYSTIMESTAMP
            WHERE id = ?
        ", [$id]);

        return back()->with(
            'success',
            'Notification marked as read.'
        );
    }

    public function markAllAsRead(): RedirectResponse
    {
        DB::update("
            UPDATE notifications
            SET
                read_at = NVL(
                    read_at,
                    SYSTIMESTAMP
                ),
                updated_at = SYSTIMESTAMP
            WHERE read_at IS NULL
        ");

        return back()->with(
            'success',
            'All notifications marked as read.'
        );
    }

    public function destroy(
        string $id
    ): RedirectResponse {
        DB::delete("
            DELETE FROM notifications
            WHERE id = ?
        ", [$id]);

        return redirect()
            ->route('admin.notifications.index')
            ->with(
                'success',
                'Notification deleted successfully.'
            );
    }
}