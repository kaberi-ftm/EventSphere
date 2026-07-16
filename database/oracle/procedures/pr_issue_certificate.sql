SET SQLBLANKLINES ON
SET DEFINE OFF

CREATE OR REPLACE PROCEDURE PR_ISSUE_CERTIFICATE
(
    P_USER_ID           NUMBER,
    P_EVENT_ID          NUMBER,
    P_CERTIFICATE_TYPE  VARCHAR2,
    P_TITLE             VARCHAR2,
    P_DESCRIPTION       VARCHAR2,
    P_ISSUED_BY         NUMBER
)
IS
    V_USER_COUNT       NUMBER;
    V_EVENT_COUNT      NUMBER;
    V_ISSUER_COUNT     NUMBER;
    V_DUPLICATE_COUNT  NUMBER;
    V_TYPE             VARCHAR2(30);
BEGIN
    V_TYPE :=
        LOWER(TRIM(P_CERTIFICATE_TYPE));

    SELECT COUNT(*)
    INTO V_USER_COUNT
    FROM USERS
    WHERE ID = P_USER_ID;

    IF V_USER_COUNT = 0 THEN
        RAISE_APPLICATION_ERROR(
            -20401,
            'Certificate recipient does not exist.'
        );
    END IF;

    SELECT COUNT(*)
    INTO V_EVENT_COUNT
    FROM EVENTS
    WHERE ID = P_EVENT_ID;

    IF V_EVENT_COUNT = 0 THEN
        RAISE_APPLICATION_ERROR(
            -20402,
            'Selected event does not exist.'
        );
    END IF;

    IF P_ISSUED_BY IS NOT NULL THEN
        SELECT COUNT(*)
        INTO V_ISSUER_COUNT
        FROM USERS
        WHERE ID = P_ISSUED_BY;

        IF V_ISSUER_COUNT = 0 THEN
            RAISE_APPLICATION_ERROR(
                -20403,
                'Certificate issuer does not exist.'
            );
        END IF;
    END IF;

    IF V_TYPE NOT IN (
        'participation',
        'volunteer',
        'achievement',
        'organizer',
        'winner'
    ) THEN
        RAISE_APPLICATION_ERROR(
            -20404,
            'Invalid certificate type.'
        );
    END IF;

    SELECT COUNT(*)
    INTO V_DUPLICATE_COUNT
    FROM CERTIFICATES
    WHERE USER_ID = P_USER_ID
      AND EVENT_ID = P_EVENT_ID
      AND CERTIFICATE_TYPE = V_TYPE;

    IF V_DUPLICATE_COUNT > 0 THEN
        RAISE_APPLICATION_ERROR(
            -20405,
            'Certificate already exists for this user and event.'
        );
    END IF;

    INSERT INTO CERTIFICATES
    (
        USER_ID,
        EVENT_ID,
        ISSUED_BY,
        CERTIFICATE_NUMBER,
        VERIFICATION_CODE,
        CERTIFICATE_TYPE,
        TITLE,
        DESCRIPTION,
        ISSUED_AT,
        STATUS,
        CREATED_AT,
        UPDATED_AT
    )
    VALUES
    (
        P_USER_ID,
        P_EVENT_ID,
        P_ISSUED_BY,
        NULL,
        NULL,
        V_TYPE,
        TRIM(P_TITLE),
        P_DESCRIPTION,
        SYSTIMESTAMP,
        'issued',
        SYSTIMESTAMP,
        SYSTIMESTAMP
    );
END;
/

SHOW ERRORS PROCEDURE PR_ISSUE_CERTIFICATE