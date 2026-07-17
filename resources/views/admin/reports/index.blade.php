@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js/dist/chart.umd.min.js"></script>

<script type="application/json" id="report-chart-data">
{!! json_encode(
    [
        'monthlyLabels' => $monthlyLabels,
        'monthlyIncome' => $monthlyIncome,
        'monthlyExpense' => $monthlyExpense,
        'monthlyCashflow' => $monthlyCashflowValues,
        'healthCounts' => array_values($healthCounts),
    ],
    JSON_HEX_TAG
    | JSON_HEX_APOS
    | JSON_HEX_AMP
    | JSON_HEX_QUOT
) !!}
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dataElement = document.getElementById(
            'report-chart-data'
        );

        if (!dataElement) {
            return;
        }

        let reportData;

        try {
            reportData = JSON.parse(
                dataElement.textContent
            );
        } catch (error) {
            console.error(
                'Report chart data could not be parsed.',
                error
            );

            return;
        }

        const monthlyLabels =
            reportData.monthlyLabels || [];

        const monthlyIncome =
            reportData.monthlyIncome || [];

        const monthlyExpense =
            reportData.monthlyExpense || [];

        const monthlyCashflow =
            reportData.monthlyCashflow || [];

        const healthCounts =
            reportData.healthCounts || [];

        const cashflowCanvas =
            document.getElementById(
                'cashflowChart'
            );

        const healthCanvas =
            document.getElementById(
                'healthChart'
            );

        if (typeof Chart === 'undefined') {
            console.error(
                'Chart.js could not be loaded.'
            );

            return;
        }

        if (cashflowCanvas) {
            new Chart(cashflowCanvas, {
                type: 'line',

                data: {
                    labels: monthlyLabels,

                    datasets: [
                        {
                            label: 'Income',
                            data: monthlyIncome,
                            borderWidth: 2,
                            tension: 0.3
                        },
                        {
                            label: 'Expense',
                            data: monthlyExpense,
                            borderWidth: 2,
                            tension: 0.3
                        },
                        {
                            label: 'Net Cashflow',
                            data: monthlyCashflow,
                            borderWidth: 2,
                            tension: 0.3
                        }
                    ]
                },

                options: {
                    responsive: true,
                    maintainAspectRatio: false,

                    interaction: {
                        mode: 'index',
                        intersect: false
                    },

                    plugins: {
                        legend: {
                            position: 'bottom'
                        },

                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const value =
                                        Number(
                                            context.raw || 0
                                        );

                                    return context.dataset.label
                                        + ': ৳'
                                        + value.toLocaleString();
                                }
                            }
                        }
                    },

                    scales: {
                        y: {
                            beginAtZero: true,

                            ticks: {
                                callback: function (value) {
                                    return '৳'
                                        + Number(value)
                                            .toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }

        if (healthCanvas) {
            new Chart(healthCanvas, {
                type: 'doughnut',

                data: {
                    labels: [
                        'Healthy',
                        'Warning',
                        'Critical',
                        'Over Budget',
                        'No Budget'
                    ],

                    datasets: [
                        {
                            data: healthCounts
                        }
                    ]
                },

                options: {
                    responsive: true,
                    maintainAspectRatio: false,

                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    });
</script>

@endpush