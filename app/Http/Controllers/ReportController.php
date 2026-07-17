<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $year = trim(
            (string) $request->query('year', '')
        );

        $status = strtolower(trim(
            (string) $request->query('status', '')
        ));

        $health = strtoupper(trim(
            (string) $request->query('health', '')
        ));

        $sortMap = [
            'event' => 'event_title',
            'allocated' => 'total_allocated',
            'expense' => 'net_expense',
            'income' => 'total_income',
            'remaining' => 'remaining_budget',
            'utilization' => 'utilization_percentage',
            'rank' => 'expense_rank',
        ];

        $sort = (string) $request->query(
            'sort',
            'rank'
        );

        $sortColumn = $sortMap[$sort]
            ?? 'expense_rank';

        $direction = strtolower(
            (string) $request->query(
                'direction',
                'asc'
            )
        ) === 'desc'
            ? 'DESC'
            : 'ASC';

        $conditions = ['1 = 1'];
        $bindings = [];

        if ($year !== '') {
            $conditions[] = "
                EXTRACT(YEAR FROM start_time) = ?
            ";

            $bindings[] = (int) $year;
        }

        if ($status !== '') {
            $conditions[] = "
                LOWER(event_status) = ?
            ";

            $bindings[] = $status;
        }

        $allowedHealth = [
            'HEALTHY',
            'WARNING',
            'CRITICAL',
            'OVER_BUDGET',
            'NO_BUDGET',
        ];

        if (in_array(
            $health,
            $allowedHealth,
            true
        )) {
            $conditions[] = "
                financial_health = ?
            ";

            $bindings[] = $health;
        }

        $whereSql = implode(
            ' AND ',
            $conditions
        );

        $eventReports = DB::select("
            SELECT
                event_id,
                event_title,
                start_time,
                event_status,
                total_allocated,
                paid_expense,
                pending_expense,
                paid_refund,
                net_expense,
                sponsor_income,
                other_income,
                total_income,
                remaining_budget,
                utilization_percentage,
                expense_rank,
                financial_health
            FROM vw_report_event_finance
            WHERE {$whereSql}
            ORDER BY
                {$sortColumn} {$direction},
                event_title
        ", $bindings);

        $summary = DB::selectOne("
            SELECT
                COUNT(*) AS total_events,

                NVL(
                    SUM(total_allocated),
                    0
                ) AS total_allocated,

                NVL(
                    SUM(net_expense),
                    0
                ) AS total_expense,

                NVL(
                    SUM(total_income),
                    0
                ) AS total_income,

                NVL(
                    SUM(remaining_budget),
                    0
                ) AS total_remaining,

                NVL(
                    AVG(utilization_percentage),
                    0
                ) AS average_utilization,

                SUM(
                    CASE
                        WHEN financial_health =
                            'OVER_BUDGET'
                        THEN 1
                        ELSE 0
                    END
                ) AS over_budget_events,

                SUM(
                    CASE
                        WHEN financial_health =
                            'CRITICAL'
                        THEN 1
                        ELSE 0
                    END
                ) AS critical_events

            FROM vw_report_event_finance
            WHERE {$whereSql}
        ", $bindings);

        $sponsorRanking = DB::select("
            SELECT *
            FROM
            (
                SELECT
                    sponsor_id,
                    sponsor_name,
                    sponsor_type,
                    sponsor_status,
                    event_count,
                    total_pledged_amount,
                    confirmed_amount,
                    pending_amount,
                    sponsor_rank
                FROM vw_report_sponsor_ranking
                ORDER BY
                    sponsor_rank,
                    sponsor_name
            )
            WHERE ROWNUM <= 10
        ");

        $userRanking = DB::select("
            SELECT *
            FROM
            (
                SELECT
                    user_id,
                    user_name,
                    email,
                    role_name,
                    engagement_points,
                    club_count,
                    certificate_count,
                    unread_notification_count,
                    engagement_rank,
                    role_position
                FROM vw_report_user_activity
                ORDER BY
                    engagement_rank,
                    user_name
            )
            WHERE ROWNUM <= 10
        ");

        $monthlyConditions = ['1 = 1'];
        $monthlyBindings = [];

        if ($year !== '') {
            $monthlyConditions[] = "
                EXTRACT(
                    YEAR FROM payment_month
                ) = ?
            ";

            $monthlyBindings[] = (int) $year;
        }

        $monthlyCashflow = DB::select("
            SELECT
                payment_month,
                total_income,
                total_expense,
                total_refund,
                net_cashflow,
                previous_month_cashflow,
                cashflow_change,
                running_cashflow
            FROM vw_report_monthly_cashflow
            WHERE " . implode(
                ' AND ',
                $monthlyConditions
            ) . "
            ORDER BY payment_month
        ", $monthlyBindings);

        $certificateSummary = DB::selectOne("
            SELECT
                COUNT(*) AS total_certificates,

                SUM(
                    CASE
                        WHEN LOWER(status) = 'issued'
                        THEN 1
                        ELSE 0
                    END
                ) AS issued_certificates,

                SUM(
                    CASE
                        WHEN LOWER(status) = 'revoked'
                        THEN 1
                        ELSE 0
                    END
                ) AS revoked_certificates

            FROM certificates
        ");

        $notificationSummary = DB::selectOne("
            SELECT
                COUNT(*) AS total_notifications,

                SUM(
                    CASE
                        WHEN read_at IS NULL
                        THEN 1
                        ELSE 0
                    END
                ) AS unread_notifications,

                SUM(
                    CASE
                        WHEN read_at IS NOT NULL
                        THEN 1
                        ELSE 0
                    END
                ) AS read_notifications

            FROM notifications
        ");

        $years = DB::select("
            SELECT DISTINCT
                EXTRACT(
                    YEAR FROM start_time
                ) AS year_value
            FROM events
            WHERE start_time IS NOT NULL
            ORDER BY year_value DESC
        ");

        $monthlyLabels = [];
        $monthlyIncome = [];
        $monthlyExpense = [];
        $monthlyCashflowValues = [];

        foreach ($monthlyCashflow as $row) {
            $monthlyLabels[] = date(
                'M Y',
                strtotime($row->payment_month)
            );

            $monthlyIncome[] =
                (float) $row->total_income;

            $monthlyExpense[] =
                (float) $row->total_expense;

            $monthlyCashflowValues[] =
                (float) $row->net_cashflow;
        }

        $healthCounts = [
            'HEALTHY' => 0,
            'WARNING' => 0,
            'CRITICAL' => 0,
            'OVER_BUDGET' => 0,
            'NO_BUDGET' => 0,
        ];

        foreach ($eventReports as $report) {
            $reportHealth = strtoupper(
                (string) $report->financial_health
            );

            if (array_key_exists(
                $reportHealth,
                $healthCounts
            )) {
                $healthCounts[$reportHealth]++;
            }
        }

        return view('admin.reports.index', compact(
            'eventReports',
            'summary',
            'sponsorRanking',
            'userRanking',
            'monthlyCashflow',
            'certificateSummary',
            'notificationSummary',
            'years',
            'year',
            'status',
            'health',
            'sort',
            'direction',
            'monthlyLabels',
            'monthlyIncome',
            'monthlyExpense',
            'monthlyCashflowValues',
            'healthCounts'
        ));
    }

    public function exportFinance(
        Request $request
    ): StreamedResponse {
        $year = trim(
            (string) $request->query('year', '')
        );

        $status = strtolower(trim(
            (string) $request->query('status', '')
        ));

        $health = strtoupper(trim(
            (string) $request->query('health', '')
        ));

        $conditions = ['1 = 1'];
        $bindings = [];

        if ($year !== '') {
            $conditions[] = "
                EXTRACT(YEAR FROM start_time) = ?
            ";

            $bindings[] = (int) $year;
        }

        if ($status !== '') {
            $conditions[] = "
                LOWER(event_status) = ?
            ";

            $bindings[] = $status;
        }

        $allowedHealth = [
            'HEALTHY',
            'WARNING',
            'CRITICAL',
            'OVER_BUDGET',
            'NO_BUDGET',
        ];

        if (in_array(
            $health,
            $allowedHealth,
            true
        )) {
            $conditions[] = "
                financial_health = ?
            ";

            $bindings[] = $health;
        }

        $rows = DB::select("
            SELECT
                event_title,
                start_time,
                event_status,
                total_allocated,
                net_expense,
                total_income,
                remaining_budget,
                utilization_percentage,
                financial_health,
                expense_rank
            FROM vw_report_event_finance
            WHERE " . implode(
                ' AND ',
                $conditions
            ) . "
            ORDER BY expense_rank, event_title
        ", $bindings);

        $filename = 'event-financial-report-'
            . now()->format('Y-m-d-His')
            . '.csv';

        return response()->streamDownload(
            function () use ($rows) {
                $handle = fopen(
                    'php://output',
                    'w'
                );

                fwrite(
                    $handle,
                    "\xEF\xBB\xBF"
                );

                fputcsv($handle, [
                    'Event',
                    'Start Time',
                    'Status',
                    'Allocated',
                    'Net Expense',
                    'Total Income',
                    'Remaining',
                    'Utilization Percentage',
                    'Financial Health',
                    'Expense Rank',
                ]);

                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row->event_title,
                        $row->start_time,
                        $row->event_status,
                        $row->total_allocated,
                        $row->net_expense,
                        $row->total_income,
                        $row->remaining_budget,
                        $row->utilization_percentage,
                        $row->financial_health,
                        $row->expense_rank,
                    ]);
                }

                fclose($handle);
            },
            $filename,
            [
                'Content-Type' =>
                    'text/csv; charset=UTF-8',
            ]
        );
    }
}