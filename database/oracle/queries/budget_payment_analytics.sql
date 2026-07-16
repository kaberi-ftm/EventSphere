SET SQLBLANKLINES ON
SET LINESIZE 220
SET PAGESIZE 100

-- Event financial ranking
SELECT
    EVENT_ID,
    EVENT_TITLE,
    TOTAL_ALLOCATED,
    NET_EXPENSE,
    REMAINING_BUDGET,
    UTILIZATION_PERCENTAGE,
    EXPENSE_RANK
FROM VW_EVENT_BUDGET_SUMMARY
ORDER BY EXPENSE_RANK, EVENT_TITLE;

-- Category utilization ranking inside each event
SELECT
    EVENT_TITLE,
    CATEGORY,
    ALLOCATED_AMOUNT,
    PAID_EXPENSE,
    REMAINING_AMOUNT,
    UTILIZATION_PERCENTAGE,
    DENSE_RANK() OVER
    (
        PARTITION BY EVENT_ID
        ORDER BY UTILIZATION_PERCENTAGE DESC
    ) AS CATEGORY_UTILIZATION_RANK
FROM
(
    SELECT
        B.EVENT_ID,
        E.TITLE AS EVENT_TITLE,
        B.CATEGORY,
        B.ALLOCATED_AMOUNT,
        NVL(
            SUM(
                CASE
                    WHEN LOWER(P.PAYMENT_TYPE) = 'expense'
                     AND LOWER(P.STATUS) = 'paid'
                    THEN P.AMOUNT
                    WHEN LOWER(P.PAYMENT_TYPE) = 'refund'
                     AND LOWER(P.STATUS) = 'paid'
                    THEN -P.AMOUNT
                    ELSE 0
                END
            ),
            0
        ) AS PAID_EXPENSE,
        B.ALLOCATED_AMOUNT
            - NVL(
                SUM(
                    CASE
                        WHEN LOWER(P.PAYMENT_TYPE) = 'expense'
                         AND LOWER(P.STATUS) = 'paid'
                        THEN P.AMOUNT
                        WHEN LOWER(P.PAYMENT_TYPE) = 'refund'
                         AND LOWER(P.STATUS) = 'paid'
                        THEN -P.AMOUNT
                        ELSE 0
                    END
                ),
                0
            ) AS REMAINING_AMOUNT,
        CASE
            WHEN B.ALLOCATED_AMOUNT = 0 THEN 0
            ELSE ROUND(
                NVL(
                    SUM(
                        CASE
                            WHEN LOWER(P.PAYMENT_TYPE) = 'expense'
                             AND LOWER(P.STATUS) = 'paid'
                            THEN P.AMOUNT
                            WHEN LOWER(P.PAYMENT_TYPE) = 'refund'
                             AND LOWER(P.STATUS) = 'paid'
                            THEN -P.AMOUNT
                            ELSE 0
                        END
                    ),
                    0
                ) / B.ALLOCATED_AMOUNT * 100,
                2
            )
        END AS UTILIZATION_PERCENTAGE
    FROM BUDGETS B
    JOIN EVENTS E
        ON E.ID = B.EVENT_ID
    LEFT JOIN PAYMENTS P
        ON P.BUDGET_ID = B.ID
    GROUP BY
        B.EVENT_ID,
        E.TITLE,
        B.CATEGORY,
        B.ALLOCATED_AMOUNT
)
ORDER BY EVENT_TITLE, CATEGORY_UTILIZATION_RANK;

-- Running paid expense
SELECT
    E.TITLE AS EVENT_TITLE,
    P.ID AS PAYMENT_ID,
    P.PAYEE_NAME,
    P.PAYMENT_DATE,
    P.AMOUNT,
    SUM(
        CASE
            WHEN LOWER(P.PAYMENT_TYPE) = 'expense'
             AND LOWER(P.STATUS) = 'paid'
            THEN P.AMOUNT
            WHEN LOWER(P.PAYMENT_TYPE) = 'refund'
             AND LOWER(P.STATUS) = 'paid'
            THEN -P.AMOUNT
            ELSE 0
        END
    ) OVER
    (
        PARTITION BY P.EVENT_ID
        ORDER BY P.PAYMENT_DATE, P.ID
    ) AS RUNNING_NET_EXPENSE
FROM PAYMENTS P
JOIN EVENTS E
    ON E.ID = P.EVENT_ID
ORDER BY E.TITLE, P.PAYMENT_DATE, P.ID;

-- Top three payments in each event
SELECT
    EVENT_TITLE,
    PAYEE_NAME,
    AMOUNT,
    PAYMENT_RANK
FROM
(
    SELECT
        E.TITLE AS EVENT_TITLE,
        P.PAYEE_NAME,
        P.AMOUNT,
        DENSE_RANK() OVER
        (
            PARTITION BY P.EVENT_ID
            ORDER BY P.AMOUNT DESC
        ) AS PAYMENT_RANK
    FROM PAYMENTS P
    JOIN EVENTS E
        ON E.ID = P.EVENT_ID
    WHERE LOWER(P.STATUS) <> 'cancelled'
)
WHERE PAYMENT_RANK <= 3
ORDER BY EVENT_TITLE, PAYMENT_RANK;

-- Monthly expense trend with previous-month comparison
SELECT
    PAYMENT_MONTH,
    MONTHLY_EXPENSE,
    LAG(MONTHLY_EXPENSE) OVER
    (
        ORDER BY PAYMENT_MONTH
    ) AS PREVIOUS_MONTH_EXPENSE,
    MONTHLY_EXPENSE
        - LAG(MONTHLY_EXPENSE) OVER
          (
              ORDER BY PAYMENT_MONTH
          ) AS EXPENSE_CHANGE
FROM
(
    SELECT
        TRUNC(PAYMENT_DATE, 'MM') AS PAYMENT_MONTH,
        SUM(AMOUNT) AS MONTHLY_EXPENSE
    FROM PAYMENTS
    WHERE LOWER(PAYMENT_TYPE) = 'expense'
      AND LOWER(STATUS) = 'paid'
    GROUP BY TRUNC(PAYMENT_DATE, 'MM')
)
ORDER BY PAYMENT_MONTH;