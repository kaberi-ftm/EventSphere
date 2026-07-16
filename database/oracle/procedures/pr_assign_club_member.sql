SET SQLBLANKLINES ON
SET DEFINE OFF

CREATE OR REPLACE PROCEDURE PR_ASSIGN_CLUB_MEMBER
(
    P_USER_ID      NUMBER,
    P_CLUB_ID      NUMBER,
    P_MEMBER_ROLE  VARCHAR2
)
IS
    V_USER_COUNT       NUMBER;
    V_CLUB_COUNT       NUMBER;
    V_DUPLICATE_COUNT  NUMBER;
    V_MEMBER_ROLE      VARCHAR2(30);
BEGIN
    V_MEMBER_ROLE :=
        LOWER(TRIM(P_MEMBER_ROLE));

    SELECT COUNT(*)
    INTO V_USER_COUNT
    FROM USERS
    WHERE ID = P_USER_ID;

    IF V_USER_COUNT = 0 THEN
        RAISE_APPLICATION_ERROR(
            -20301,
            'Selected user does not exist.'
        );
    END IF;

    SELECT COUNT(*)
    INTO V_CLUB_COUNT
    FROM CLUBS
    WHERE ID = P_CLUB_ID;

    IF V_CLUB_COUNT = 0 THEN
        RAISE_APPLICATION_ERROR(
            -20302,
            'Selected club does not exist.'
        );
    END IF;

    IF V_MEMBER_ROLE NOT IN (
        'member',
        'executive',
        'president',
        'secretary',
        'treasurer',
        'coordinator'
    ) THEN
        RAISE_APPLICATION_ERROR(
            -20303,
            'Invalid club member role.'
        );
    END IF;

    SELECT COUNT(*)
    INTO V_DUPLICATE_COUNT
    FROM CLUB_MEMBERSHIPS
    WHERE USER_ID = P_USER_ID
      AND CLUB_ID = P_CLUB_ID;

    IF V_DUPLICATE_COUNT > 0 THEN
        RAISE_APPLICATION_ERROR(
            -20304,
            'User is already a member of this club.'
        );
    END IF;

    INSERT INTO CLUB_MEMBERSHIPS
    (
        USER_ID,
        CLUB_ID,
        MEMBER_ROLE,
        JOINED_AT,
        CREATED_AT,
        UPDATED_AT
    )
    VALUES
    (
        P_USER_ID,
        P_CLUB_ID,
        V_MEMBER_ROLE,
        SYSDATE,
        SYSTIMESTAMP,
        SYSTIMESTAMP
    );
END;
/

SHOW ERRORS PROCEDURE PR_ASSIGN_CLUB_MEMBER