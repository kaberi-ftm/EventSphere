CREATE OR REPLACE PROCEDURE PR_ASSIGN_EVENT_SPONSOR (
    P_EVENT_ID          NUMBER,
    P_SPONSOR_ID        NUMBER,
    P_AMOUNT            NUMBER,
    P_CONTRIBUTION_TYPE VARCHAR2,
    P_STATUS            VARCHAR2,
    P_NOTES             CLOB
)
IS
    V_EVENT_COUNT NUMBER;
    V_SPONSOR_COUNT NUMBER;
    V_DUPLICATE_COUNT NUMBER;
BEGIN
    SELECT COUNT(*)
    INTO V_EVENT_COUNT
    FROM EVENTS
    WHERE ID = P_EVENT_ID;

    IF V_EVENT_COUNT = 0 THEN
        RAISE_APPLICATION_ERROR(
            -20001,
            'Event does not exist.'
        );
    END IF;

    SELECT COUNT(*)
    INTO V_SPONSOR_COUNT
    FROM SPONSORS
    WHERE ID = P_SPONSOR_ID
      AND STATUS = 'active';

    IF V_SPONSOR_COUNT = 0 THEN
        RAISE_APPLICATION_ERROR(
            -20002,
            'Sponsor does not exist or is inactive.'
        );
    END IF;

    IF P_AMOUNT < 0 THEN
        RAISE_APPLICATION_ERROR(
            -20003,
            'Amount cannot be negative.'
        );
    END IF;

    SELECT COUNT(*)
    INTO V_DUPLICATE_COUNT
    FROM EVENT_SPONSORS
    WHERE EVENT_ID = P_EVENT_ID
      AND SPONSOR_ID = P_SPONSOR_ID;

    IF V_DUPLICATE_COUNT > 0 THEN
        RAISE_APPLICATION_ERROR(
            -20004,
            'Sponsor already assigned to event.'
        );
    END IF;

    INSERT INTO EVENT_SPONSORS
    (
        EVENT_ID,
        SPONSOR_ID,
        AMOUNT,
        CONTRIBUTION_TYPE,
        AGREEMENT_DATE,
        STATUS,
        NOTES,
        CREATED_AT,
        UPDATED_AT
    )
    VALUES
    (
        P_EVENT_ID,
        P_SPONSOR_ID,
        P_AMOUNT,
        LOWER(P_CONTRIBUTION_TYPE),
        SYSTIMESTAMP,
        LOWER(P_STATUS),
        P_NOTES,
        SYSTIMESTAMP,
        SYSTIMESTAMP
    );

EXCEPTION
    WHEN DUP_VAL_ON_INDEX THEN
        RAISE_APPLICATION_ERROR(
            -20005,
            'Duplicate sponsorship record.'
        );
END;
/