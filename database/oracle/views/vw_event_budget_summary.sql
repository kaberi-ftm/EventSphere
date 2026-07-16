SET SQLBLANKLINES ON
SET DEFINE OFF

CREATE OR REPLACE VIEW VW_EVENT_BUDGET_SUMMARY AS
WITH BUDGET_DATA AS
(
    SELECT
        EVENT_ID,
        SUM(
            CASE
                WHEN LOWER(STATUS) <> 'cancelled'
                THEN ALLOCATED_AMOUNT
                ELSE 0
            END
        ) AS TOTAL_ALLOCATED
    FROM BUDGETS
    GROUP BY EVENT_ID
),
PAYMENT_DATA AS
(
    SELECT
        EVENT_ID,
        SUM(
            CASE
                WHEN LOWER(PAYMENT_TYPE) = 'expense'
                 AND LOWER(STATUS) = 'paid'
                THEN AMOUNT
                ELSE 0
            END
        ) AS PAID_EXPENSE,
        SUM(
            CASE
                WHEN LOWER(PAYMENT_TYPE) = 'expense'
                 AND LOWER(STATUS) IN ('pending', 'approved')
                THEN AMOUNT
                ELSE 0
            END
        ) AS PENDING_EXPENSE,
        SUM(
            CASE
                WHEN LOWER(PAYMENT_TYPE) = 'income'
                 AND LOWER(STATUS) = 'paid'
                THEN AMOUNT
                ELSE 0
            END
        ) AS OTHER_INCOME,
        SUM(
            CASE
                WHEN LOWER(PAYMENT_TYPE) = 'refund'
                 AND LOWER(STATUS) = 'paid'
                THEN AMOUNT
                ELSE 0
            END
        ) AS PAID_REFUND
    FROM PAYMENTS
    GROUP BY EVENT_ID
),
SPONSOR_DATA AS
(
    SELECT
        EVENT_ID,
        SUM(
            CASE
                WHEN LOWER(STATUS) IN ('confirmed', 'paid')
                THEN AMOUNT
                ELSE 0
            END
        ) AS SPONSOR_INCOME
    FROM EVENT_SPONSORS
    GROUP BY EVENT_ID
)
SELECT
    E.ID AS EVENT_ID,
    E.TITLE AS EVENT_TITLE,
    E.START_TIME,
    E.STATUS AS EVENT_STATUS,
    NVL(BD.TOTAL_ALLOCATED, 0) AS TOTAL_ALLOCATED,
    NVL(PD.PAID_EXPENSE, 0) AS PAID_EXPENSE,
    NVL(PD.PENDING_EXPENSE, 0) AS PENDING_EXPENSE,
    NVL(PD.PAID_REFUND, 0) AS PAID_REFUND,
    NVL(PD.PAID_EXPENSE, 0)
        - NVL(PD.PAID_REFUND, 0) AS NET_EXPENSE,
    NVL(PD.OTHER_INCOME, 0) AS OTHER_INCOME,
    NVL(SD.SPONSOR_INCOME, 0) AS SPONSOR_INCOME,
    NVL(PD.OTHER_INCOME, 0)
        + NVL(SD.SPONSOR_INCOME, 0) AS TOTAL_INCOME,
    NVL(BD.TOTAL_ALLOCATED, 0)
        - (
            NVL(PD.PAID_EXPENSE, 0)
            - NVL(PD.PAID_REFUND, 0)
        ) AS REMAINING_BUDGET,
    CASE
        WHEN NVL(BD.TOTAL_ALLOCATED, 0) = 0 THEN 0
        ELSE ROUND(
            (
                NVL(PD.PAID_EXPENSE, 0)
                - NVL(PD.PAID_REFUND, 0)
            )
            / BD.TOTAL_ALLOCATED * 100,
            2
        )
    END AS UTILIZATION_PERCENTAGE,
    DENSE_RANK() OVER
    (
        ORDER BY
            (
                NVL(PD.PAID_EXPENSE, 0)
                - NVL(PD.PAID_REFUND, 0)
            ) DESC
    ) AS EXPENSE_RANK
FROM EVENTS E
LEFT JOIN BUDGET_DATA BD
    ON BD.EVENT_ID = E.ID
LEFT JOIN PAYMENT_DATA PD
    ON PD.EVENT_ID = E.ID
LEFT JOIN SPONSOR_DATA SD
    ON SD.EVENT_ID = E.ID;

SHOW ERRORS VIEW VW_EVENT_BUDGET_SUMMARY