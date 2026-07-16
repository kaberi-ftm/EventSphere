SET SQLBLANKLINES ON
SET DEFINE OFF

CREATE OR REPLACE PROCEDURE PR_RECORD_EVENT_PAYMENT
(
    P_EVENT_ID          NUMBER,
    P_BUDGET_ID         NUMBER,
    P_PAYEE_NAME        VARCHAR2,
    P_PAYMENT_TYPE      VARCHAR2,
    P_AMOUNT            NUMBER,
    P_PAYMENT_METHOD    VARCHAR2,
    P_REFERENCE_NUMBER  VARCHAR2,
    P_PAYMENT_DATE      TIMESTAMP,
    P_STATUS            VARCHAR2,
    P_NOTES             CLOB
)
IS
    V_EVENT_COUNT       NUMBER;
    V_BUDGET_EVENT_ID   NUMBER;
    V_ALLOCATED_AMOUNT  NUMBER;
    V_BUDGET_STATUS     VARCHAR2(20);
    V_CURRENT_EXPENSE   NUMBER := 0;
    V_CURRENT_REFUND    NUMBER := 0;
    V_DUPLICATE_COUNT   NUMBER := 0;
    V_PAYMENT_TYPE      VARCHAR2(20);
    V_PAYMENT_METHOD    VARCHAR2(30);
    V_STATUS            VARCHAR2(20);
BEGIN
    V_PAYMENT_TYPE :=
        LOWER(TRIM(P_PAYMENT_TYPE));

    V_PAYMENT_METHOD :=
        LOWER(TRIM(P_PAYMENT_METHOD));

    V_STATUS :=
        LOWER(TRIM(P_STATUS));

    SELECT COUNT(*)
    INTO V_EVENT_COUNT
    FROM EVENTS
    WHERE ID = P_EVENT_ID;

    IF V_EVENT_COUNT = 0 THEN
        RAISE_APPLICATION_ERROR(
            -20201,
            'Selected event does not exist.'
        );
    END IF;

    IF P_AMOUNT <= 0 THEN
        RAISE_APPLICATION_ERROR(
            -20202,
            'Payment amount must be greater than zero.'
        );
    END IF;

    IF V_PAYMENT_TYPE NOT IN (
        'expense',
        'income',
        'refund'
    ) THEN
        RAISE_APPLICATION_ERROR(
            -20203,
            'Invalid payment type.'
        );
    END IF;

    IF V_PAYMENT_METHOD NOT IN (
        'cash',
        'bank',
        'card',
        'mobile_banking',
        'cheque'
    ) THEN
        RAISE_APPLICATION_ERROR(
            -20204,
            'Invalid payment method.'
        );
    END IF;

    IF V_STATUS NOT IN (
        'pending',
        'approved',
        'paid',
        'cancelled'
    ) THEN
        RAISE_APPLICATION_ERROR(
            -20205,
            'Invalid payment status.'
        );
    END IF;

    IF V_PAYMENT_TYPE = 'expense'
       AND P_BUDGET_ID IS NULL THEN
        RAISE_APPLICATION_ERROR(
            -20206,
            'Expense payment requires a budget.'
        );
    END IF;

    IF P_REFERENCE_NUMBER IS NOT NULL THEN
        SELECT COUNT(*)
        INTO V_DUPLICATE_COUNT
        FROM PAYMENTS
        WHERE LOWER(REFERENCE_NUMBER) =
              LOWER(TRIM(P_REFERENCE_NUMBER));

        IF V_DUPLICATE_COUNT > 0 THEN
            RAISE_APPLICATION_ERROR(
                -20207,
                'Payment reference number already exists.'
            );
        END IF;
    END IF;

    IF P_BUDGET_ID IS NOT NULL THEN
        BEGIN
            SELECT
                EVENT_ID,
                ALLOCATED_AMOUNT,
                STATUS
            INTO
                V_BUDGET_EVENT_ID,
                V_ALLOCATED_AMOUNT,
                V_BUDGET_STATUS
            FROM BUDGETS
            WHERE ID = P_BUDGET_ID;
        EXCEPTION
            WHEN NO_DATA_FOUND THEN
                RAISE_APPLICATION_ERROR(
                    -20208,
                    'Selected budget does not exist.'
                );
        END;

        IF V_BUDGET_EVENT_ID <> P_EVENT_ID THEN
            RAISE_APPLICATION_ERROR(
                -20209,
                'Budget does not belong to the selected event.'
            );
        END IF;

        IF LOWER(V_BUDGET_STATUS) IN (
            'closed',
            'cancelled'
        ) THEN
            RAISE_APPLICATION_ERROR(
                -20210,
                'Closed or cancelled budget cannot receive payments.'
            );
        END IF;
    END IF;

    IF V_PAYMENT_TYPE = 'expense'
       AND V_STATUS = 'paid' THEN
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
            V_CURRENT_EXPENSE,
            V_CURRENT_REFUND
        FROM PAYMENTS
        WHERE BUDGET_ID = P_BUDGET_ID;

        IF (
            V_CURRENT_EXPENSE
            - V_CURRENT_REFUND
            + P_AMOUNT
        ) > V_ALLOCATED_AMOUNT THEN
            RAISE_APPLICATION_ERROR(
                -20211,
                'Payment exceeds the remaining budget.'
            );
        END IF;
    END IF;

    INSERT INTO PAYMENTS
    (
        EVENT_ID,
        BUDGET_ID,
        PAYEE_NAME,
        PAYMENT_TYPE,
        AMOUNT,
        PAYMENT_METHOD,
        REFERENCE_NUMBER,
        PAYMENT_DATE,
        STATUS,
        NOTES,
        CREATED_AT,
        UPDATED_AT
    )
    VALUES
    (
        P_EVENT_ID,
        P_BUDGET_ID,
        TRIM(P_PAYEE_NAME),
        V_PAYMENT_TYPE,
        P_AMOUNT,
        V_PAYMENT_METHOD,
        TRIM(P_REFERENCE_NUMBER),
        NVL(P_PAYMENT_DATE, SYSTIMESTAMP),
        V_STATUS,
        P_NOTES,
        SYSTIMESTAMP,
        SYSTIMESTAMP
    );
END;
/

SHOW ERRORS PROCEDURE PR_RECORD_EVENT_PAYMENT