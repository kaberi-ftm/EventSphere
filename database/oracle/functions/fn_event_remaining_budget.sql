SET SQLBLANKLINES ON
SET DEFINE OFF

CREATE OR REPLACE FUNCTION FN_EVENT_REMAINING_BUDGET
(
    P_EVENT_ID NUMBER
)
RETURN NUMBER
IS
    V_TOTAL_ALLOCATED NUMBER := 0;
    V_PAID_EXPENSE    NUMBER := 0;
    V_PAID_REFUND     NUMBER := 0;
BEGIN
    SELECT NVL(SUM(ALLOCATED_AMOUNT), 0)
    INTO V_TOTAL_ALLOCATED
    FROM BUDGETS
    WHERE EVENT_ID = P_EVENT_ID
      AND LOWER(STATUS) <> 'cancelled';

    SELECT
        NVL(
            SUM(
                CASE
                    WHEN LOWER(PAYMENT_TYPE) = 'expense'
                     AND LOWER(STATUS) = 'paid'
                    THEN AMOUNT
                    ELSE 0
                END
            ),
            0
        ),
        NVL(
            SUM(
                CASE
                    WHEN LOWER(PAYMENT_TYPE) = 'refund'
                     AND LOWER(STATUS) = 'paid'
                    THEN AMOUNT
                    ELSE 0
                END
            ),
            0
        )
    INTO
        V_PAID_EXPENSE,
        V_PAID_REFUND
    FROM PAYMENTS
    WHERE EVENT_ID = P_EVENT_ID;

    RETURN
        V_TOTAL_ALLOCATED
        - V_PAID_EXPENSE
        + V_PAID_REFUND;
END;
/

SHOW ERRORS FUNCTION FN_EVENT_REMAINING_BUDGET