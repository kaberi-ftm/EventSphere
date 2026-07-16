<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = trim((string) $request->query('status', ''));
        $type = trim((string) $request->query('type', ''));

        $sortMap = [
            'number' => 'c.certificate_number',
            'recipient' => 'u.name',
            'event' => 'e.title',
            'issued' => 'c.issued_at',
            'status' => 'c.status',
        ];

        $sort = (string) $request->query('sort', 'issued');
        $sortColumn = $sortMap[$sort] ?? 'c.issued_at';

        $direction = strtolower(
            (string) $request->query('direction', 'desc')
        ) === 'asc' ? 'ASC' : 'DESC';

        $conditions = ['1 = 1'];
        $bindings = [];

        if ($search !== '') {
            $keyword = '%' . strtolower($search) . '%';

            $conditions[] = "
                (
                    LOWER(c.certificate_number) LIKE ?
                    OR LOWER(c.verification_code) LIKE ?
                    OR LOWER(u.name) LIKE ?
                    OR LOWER(u.email) LIKE ?
                    OR LOWER(e.title) LIKE ?
                )
            ";

            array_push(
                $bindings,
                $keyword,
                $keyword,
                $keyword,
                $keyword,
                $keyword
            );
        }

        if (in_array($status, ['issued', 'revoked'], true)) {
            $conditions[] = 'LOWER(c.status) = ?';
            $bindings[] = $status;
        }

        $types = [
            'participation',
            'volunteer',
            'achievement',
            'organizer',
            'winner',
        ];

        if (in_array($type, $types, true)) {
            $conditions[] = 'LOWER(c.certificate_type) = ?';
            $bindings[] = $type;
        }

        $certificates = DB::select("
            SELECT
                c.*,
                u.name AS recipient_name,
                u.email AS recipient_email,
                e.title AS event_title,
                issuer.name AS issued_by_name,
                ROW_NUMBER() OVER (
                    PARTITION BY c.event_id
                    ORDER BY c.issued_at, c.id
                ) AS issue_order
            FROM certificates c
            JOIN users u
                ON u.id = c.user_id
            JOIN events e
                ON e.id = c.event_id
            LEFT JOIN users issuer
                ON issuer.id = c.issued_by
            WHERE " . implode(' AND ', $conditions) . "
            ORDER BY {$sortColumn} {$direction}, c.id DESC
        ", $bindings);

        return view('admin.certificates.index', compact(
            'certificates',
            'search',
            'status',
            'type',
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

        $events = DB::select("
            SELECT id, title, start_time
            FROM events
            ORDER BY start_time DESC
        ");

        return view('admin.certificates.create', compact(
            'users',
            'events'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer'],
            'event_id' => ['required', 'integer'],
            'certificate_type' => [
                'required',
                'in:participation,volunteer,achievement,organizer,winner',
            ],
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            DB::statement("
                BEGIN
                    PR_ISSUE_CERTIFICATE(
                        ?, ?, ?, ?, ?, ?
                    );
                END;
            ", [
                $validated['user_id'],
                $validated['event_id'],
                $validated['certificate_type'],
                $validated['title'],
                $validated['description'] ?? null,
                Auth::id(),
            ]);
        } catch (QueryException $exception) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Certificate could not be issued. It may already exist.'
                );
        }

        return redirect()
            ->route('admin.certificates.index')
            ->with('success', 'Certificate issued successfully.');
    }

    public function show($id): View
    {
        $certificate = $this->findCertificate($id);

        abort_if(!$certificate, 404, 'Certificate not found.');

        return view(
            'admin.certificates.show',
            compact('certificate')
        );
    }

    public function edit($id): View
    {
        $certificate = $this->findCertificate($id);

        abort_if(!$certificate, 404, 'Certificate not found.');

        return view(
            'admin.certificates.edit',
            compact('certificate')
        );
    }

    public function update(
        Request $request,
        $id
    ): RedirectResponse {
        $certificate = DB::selectOne("
            SELECT id
            FROM certificates
            WHERE id = ?
        ", [$id]);

        abort_if(!$certificate, 404, 'Certificate not found.');

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:issued,revoked'],
        ]);

        DB::update("
            UPDATE certificates
            SET
                title = ?,
                description = ?,
                status = ?,
                updated_at = SYSTIMESTAMP
            WHERE id = ?
        ", [
            $validated['title'],
            $validated['description'] ?? null,
            $validated['status'],
            $id,
        ]);

        return redirect()
            ->route('admin.certificates.show', $id)
            ->with('success', 'Certificate updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        DB::delete("
            DELETE FROM certificates
            WHERE id = ?
        ", [$id]);

        return redirect()
            ->route('admin.certificates.index')
            ->with('success', 'Certificate deleted successfully.');
    }

    public function verifyForm(): View
    {
        return view('certificates.verify', [
            'certificate' => null,
            'verificationStatus' => null,
            'searched' => false,
        ]);
    }

    public function verify(Request $request): View
    {
        $validated = $request->validate([
            'verification_code' => [
                'required',
                'string',
                'max:64',
            ],
        ]);

        $code = trim($validated['verification_code']);

        $result = DB::selectOne("
            SELECT
                FN_VERIFY_CERTIFICATE(?) AS verification_status
            FROM dual
        ", [$code]);

        $certificate = null;

        if ($result->verification_status !== 'NOT_FOUND') {
            $certificate = DB::selectOne("
                SELECT *
                FROM vw_certificate_details
                WHERE LOWER(verification_code) = LOWER(?)
            ", [$code]);
        }

        return view('certificates.verify', [
            'certificate' => $certificate,
            'verificationStatus' =>
                $result->verification_status,
            'searched' => true,
        ]);
    }

    private function findCertificate($id): ?object
    {
        return DB::selectOne("
            SELECT
                c.*,
                u.name AS recipient_name,
                u.email AS recipient_email,
                e.title AS event_title,
                e.start_time,
                issuer.name AS issued_by_name
            FROM certificates c
            JOIN users u
                ON u.id = c.user_id
            JOIN events e
                ON e.id = c.event_id
            LEFT JOIN users issuer
                ON issuer.id = c.issued_by
            WHERE c.id = ?
        ", [$id]);
    }
}