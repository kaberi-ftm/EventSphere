SET SQLBLANKLINES ON
SET DEFINE OFF

CREATE OR REPLACE TRIGGER TRG_PAYMENT_BUDGET_LIMIT
FOR INSERT OR UPDATE OF
    BUDGET_ID,
    AMOUNT,
    PAYMENT_TYPE,
    STATUS
ON PAYMENTS
COMPOUND TRIGGER

    TYPE T_BUDGET_SET IS TABLE OF BOOLEAN
        INDEX BY VARCHAR2(100);

    G_BUDGET_IDS T_BUDGET_SET;

    AFTER EACH ROW
    IS
    BEGIN
        IF :NEW.BUDGET_ID IS NOT NULL THEN
            G_BUDGET_IDS(
                TO_CHAR(:NEW.BUDGET_ID)
            ) := TRUE;
        END IF;

        IF UPDATING
           AND :OLD.BUDGET_ID IS NOT NULL THEN
            G_BUDGET_IDS(
                TO_CHAR(:OLD.BUDGET_ID)
            ) := TRUE;
        END IF;
    END AFTER EACH ROW;

    AFTER STATEMENT
    IS
        V_KEY              VARCHAR2(100);
        V_BUDGET_ID        NUMBER;
        V_ALLOCATED_AMOUNT NUMBER;
        V_NET_EXPENSE      NUMBER;
    BEGIN
        V_KEY := G_BUDGET_IDS.FIRST;

        WHILE V_KEY IS NOT NULL LOOP
            V_BUDGET_ID := TO_NUMBER(V_KEY);

            SELECT ALLOCATED_AMOUNT
            INTO V_ALLOCATED_AMOUNT
            FROM BUDGETS
            WHERE ID = V_BUDGET_ID;

            SELECT NVL(
                SUM(
                    CASE
                        WHEN LOWER(PAYMENT_TYPE) = 'expense'
                         AND LOWER(STATUS) = 'paid'
                        THEN AMOUNT
                        WHEN LOWER(PAYMENT_TYPE) = 'refund'
                         AND LOWER(STATUS) = 'paid'
                        THEN -AMOUNT
                        ELSE 0
                    END
                ),
                0
            )
            INTO V_NET_EXPENSE
            FROM PAYMENTS
            WHERE BUDGET_ID = V_BUDGET_ID;

            IF V_NET_EXPENSE > V_ALLOCATED_AMOUNT THEN
                RAISE_APPLICATION_ERROR(
                    -20220,
                    'Paid expenses exceed allocated budget ID '
                    || V_BUDGET_ID || '.'
                );
            END IF;

            V_KEY := G_BUDGET_IDS.NEXT(V_KEY);
        END LOOP;
    END AFTER STATEMENT;

END TRG_PAYMENT_BUDGET_LIMIT;
/

SHOW ERRORS TRIGGER TRG_PAYMENT_BUDGET_LIMIT
