SET SQLBLANKLINES ON
SET DEFINE OFF

CREATE OR REPLACE VIEW VW_REPORT_MONTHLY_CASHFLOW AS
WITH MONTHLY_DATA AS
(
    SELECT
        TRUNC(PAYMENT_DATE, 'MM') AS PAYMENT_MONTH,

        SUM(
            CASE
                WHEN LOWER(PAYMENT_TYPE) = 'income'
                 AND LOWER(STATUS) = 'paid'
                THEN AMOUNT
                ELSE 0
            END
        ) AS TOTAL_INCOME,

        SUM(
            CASE
                WHEN LOWER(PAYMENT_TYPE) = 'expense'
                 AND LOWER(STATUS) = 'paid'
                THEN AMOUNT
                ELSE 0
            END
        ) AS TOTAL_EXPENSE,

        SUM(
            CASE
                WHEN LOWER(PAYMENT_TYPE) = 'refund'
                 AND LOWER(STATUS) = 'paid'
                THEN AMOUNT
                ELSE 0
            END
        ) AS TOTAL_REFUND

    FROM PAYMENTS

    GROUP BY
        TRUNC(PAYMENT_DATE, 'MM')
),
CASHFLOW_DATA AS
(
    SELECT
        PAYMENT_MONTH,
        TOTAL_INCOME,
        TOTAL_EXPENSE,
        TOTAL_REFUND,

        TOTAL_INCOME
            + TOTAL_REFUND
            - TOTAL_EXPENSE
            AS NET_CASHFLOW

    FROM MONTHLY_DATA
)
SELECT
    PAYMENT_MONTH,
    TOTAL_INCOME,
    TOTAL_EXPENSE,
    TOTAL_REFUND,
    NET_CASHFLOW,

    LAG(NET_CASHFLOW) OVER
    (
        ORDER BY PAYMENT_MONTH
    ) AS PREVIOUS_MONTH_CASHFLOW,

    NET_CASHFLOW
        - LAG(NET_CASHFLOW) OVER
          (
              ORDER BY PAYMENT_MONTH
          ) AS CASHFLOW_CHANGE,

    SUM(NET_CASHFLOW) OVER
    (
        ORDER BY PAYMENT_MONTH
        ROWS BETWEEN UNBOUNDED PRECEDING
        AND CURRENT ROW
    ) AS RUNNING_CASHFLOW

FROM CASHFLOW_DATA;

SHOW ERRORS VIEW VW_REPORT_MONTHLY_CASHFLOW