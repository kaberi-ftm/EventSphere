SET SQLBLANKLINES ON
SET DEFINE OFF

CREATE OR REPLACE PROCEDURE PR_SEND_USER_NOTIFICATION
(
    P_USER_ID     NUMBER,
    P_TITLE       VARCHAR2,
    P_MESSAGE     VARCHAR2,
    P_LEVEL       VARCHAR2,
    P_ACTION_URL  VARCHAR2
)
IS
    V_USER_COUNT NUMBER;
    V_RAW_ID     VARCHAR2(32);
    V_UUID       VARCHAR2(36);
    V_DATA       CLOB;
BEGIN
    SELECT COUNT(*)
    INTO V_USER_COUNT
    FROM USERS
    WHERE ID = P_USER_ID;

    IF V_USER_COUNT = 0 THEN
        RAISE_APPLICATION_ERROR(
            -20501,
            'Notification recipient does not exist.'
        );
    END IF;

    IF LOWER(P_LEVEL) NOT IN (
        'info',
        'success',
        'warning',
        'danger'
    ) THEN
        RAISE_APPLICATION_ERROR(
            -20502,
            'Invalid notification level.'
        );
    END IF;

    V_RAW_ID := LOWER(
        RAWTOHEX(SYS_GUID())
    );

    V_UUID :=
        SUBSTR(V_RAW_ID, 1, 8)
        || '-'
        || SUBSTR(V_RAW_ID, 9, 4)
        || '-'
        || SUBSTR(V_RAW_ID, 13, 4)
        || '-'
        || SUBSTR(V_RAW_ID, 17, 4)
        || '-'
        || SUBSTR(V_RAW_ID, 21, 12);

    V_DATA :=
        '{"title":"'
        || REPLACE(P_TITLE, '"', '\"')
        || '","message":"'
        || REPLACE(P_MESSAGE, '"', '\"')
        || '","level":"'
        || LOWER(P_LEVEL)
        || '","action_url":"'
        || REPLACE(NVL(P_ACTION_URL, ''), '"', '\"')
        || '"}';

    INSERT INTO NOTIFICATIONS
    (
        ID,
        TYPE,
        NOTIFIABLE_TYPE,
        NOTIFIABLE_ID,
        DATA,
        READ_AT,
        CREATED_AT,
        UPDATED_AT
    )
    VALUES
    (
        V_UUID,
        'App\Notifications\SystemNotification',
        'App\Models\User',
        P_USER_ID,
        V_DATA,
        NULL,
        SYSTIMESTAMP,
        SYSTIMESTAMP
    );
END;
/

SHOW ERRORS PROCEDURE PR_SEND_USER_NOTIFICATION