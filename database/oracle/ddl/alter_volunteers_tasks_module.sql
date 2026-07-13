/*
|--------------------------------------------------------------------------
| EventSphere
| Volunteer and Task Module Schema Update
|--------------------------------------------------------------------------
|
| Tables:
|   VOLUNTEERS
|   TASKS
|
| Purpose:
|   1. Add approval timestamp
|   2. Add task completion timestamp
|   3. Prevent duplicate volunteer applications
|   4. Add valid status constraints
|
| Oracle Database 21c XE
|--------------------------------------------------------------------------
*/

SET SERVEROUTPUT ON;


/*
|--------------------------------------------------------------------------
| 1. Add VOLUNTEERS.APPROVED_AT
|--------------------------------------------------------------------------
*/

DECLARE
    v_count NUMBER;
BEGIN
    SELECT COUNT(*)
    INTO v_count
    FROM USER_TAB_COLUMNS
    WHERE TABLE_NAME = 'VOLUNTEERS'
      AND COLUMN_NAME = 'APPROVED_AT';

    IF v_count = 0 THEN
        EXECUTE IMMEDIATE '
            ALTER TABLE VOLUNTEERS
            ADD APPROVED_AT TIMESTAMP(6)
        ';

        DBMS_OUTPUT.PUT_LINE(
            'APPROVED_AT column added to VOLUNTEERS.'
        );
    ELSE
        DBMS_OUTPUT.PUT_LINE(
            'VOLUNTEERS.APPROVED_AT already exists.'
        );
    END IF;
END;
/


/*
|--------------------------------------------------------------------------
| 2. Add TASKS.COMPLETED_AT
|--------------------------------------------------------------------------
*/

DECLARE
    v_count NUMBER;
BEGIN
    SELECT COUNT(*)
    INTO v_count
    FROM USER_TAB_COLUMNS
    WHERE TABLE_NAME = 'TASKS'
      AND COLUMN_NAME = 'COMPLETED_AT';

    IF v_count = 0 THEN
        EXECUTE IMMEDIATE '
            ALTER TABLE TASKS
            ADD COMPLETED_AT TIMESTAMP(6)
        ';

        DBMS_OUTPUT.PUT_LINE(
            'COMPLETED_AT column added to TASKS.'
        );
    ELSE
        DBMS_OUTPUT.PUT_LINE(
            'TASKS.COMPLETED_AT already exists.'
        );
    END IF;
END;
/


/*
|--------------------------------------------------------------------------
| 3. Prevent duplicate event volunteer applications
|--------------------------------------------------------------------------
*/

DECLARE
    v_count NUMBER;
BEGIN
    SELECT COUNT(*)
    INTO v_count
    FROM USER_CONSTRAINTS
    WHERE TABLE_NAME = 'VOLUNTEERS'
      AND CONSTRAINT_NAME = 'UQ_VOL_USER_EVENT';

    IF v_count = 0 THEN
        EXECUTE IMMEDIATE '
            ALTER TABLE VOLUNTEERS
            ADD CONSTRAINT UQ_VOL_USER_EVENT
            UNIQUE (USER_ID, EVENT_ID)
        ';

        DBMS_OUTPUT.PUT_LINE(
            'UQ_VOL_USER_EVENT constraint created.'
        );
    ELSE
        DBMS_OUTPUT.PUT_LINE(
            'UQ_VOL_USER_EVENT already exists.'
        );
    END IF;
END;
/


/*
|--------------------------------------------------------------------------
| 4. Set VOLUNTEERS status default
|--------------------------------------------------------------------------
*/

ALTER TABLE VOLUNTEERS
MODIFY (
    STATUS DEFAULT 'pending' NOT NULL
);


/*
|--------------------------------------------------------------------------
| 5. Add volunteer status validation
|--------------------------------------------------------------------------
*/

DECLARE
    v_count NUMBER;
BEGIN
    SELECT COUNT(*)
    INTO v_count
    FROM USER_CONSTRAINTS
    WHERE TABLE_NAME = 'VOLUNTEERS'
      AND CONSTRAINT_NAME = 'CK_VOL_STATUS';

    IF v_count = 0 THEN
        EXECUTE IMMEDIATE '
            ALTER TABLE VOLUNTEERS
            ADD CONSTRAINT CK_VOL_STATUS
            CHECK (
                STATUS IN (
                    ''pending'',
                    ''approved'',
                    ''rejected''
                )
            )
        ';

        DBMS_OUTPUT.PUT_LINE(
            'CK_VOL_STATUS constraint created.'
        );
    ELSE
        DBMS_OUTPUT.PUT_LINE(
            'CK_VOL_STATUS already exists.'
        );
    END IF;
END;
/


/*
|--------------------------------------------------------------------------
| 6. Set VOLUNTEERS role default
|--------------------------------------------------------------------------
*/

ALTER TABLE VOLUNTEERS
MODIFY (
    ROLE DEFAULT 'general' NOT NULL
);


/*
|--------------------------------------------------------------------------
| 7. Set application timestamp default
|--------------------------------------------------------------------------
*/

ALTER TABLE VOLUNTEERS
MODIFY (
    APPLIED_AT DEFAULT SYSTIMESTAMP NOT NULL
);


/*
|--------------------------------------------------------------------------
| 8. Set TASKS status default
|--------------------------------------------------------------------------
*/

ALTER TABLE TASKS
MODIFY (
    STATUS DEFAULT 'pending' NOT NULL
);


/*
|--------------------------------------------------------------------------
| 9. Add task status validation
|--------------------------------------------------------------------------
*/

DECLARE
    v_count NUMBER;
BEGIN
    SELECT COUNT(*)
    INTO v_count
    FROM USER_CONSTRAINTS
    WHERE TABLE_NAME = 'TASKS'
      AND CONSTRAINT_NAME = 'CK_TASK_STATUS';

    IF v_count = 0 THEN
        EXECUTE IMMEDIATE '
            ALTER TABLE TASKS
            ADD CONSTRAINT CK_TASK_STATUS
            CHECK (
                STATUS IN (
                    ''pending'',
                    ''in_progress'',
                    ''completed''
                )
            )
        ';

        DBMS_OUTPUT.PUT_LINE(
            'CK_TASK_STATUS constraint created.'
        );
    ELSE
        DBMS_OUTPUT.PUT_LINE(
            'CK_TASK_STATUS already exists.'
        );
    END IF;
END;
/


/*
|--------------------------------------------------------------------------
| Verification
|--------------------------------------------------------------------------
*/

SELECT
    TABLE_NAME,
    COLUMN_NAME,
    DATA_TYPE,
    NULLABLE,
    DATA_DEFAULT
FROM USER_TAB_COLUMNS
WHERE TABLE_NAME IN ('VOLUNTEERS', 'TASKS')
ORDER BY TABLE_NAME, COLUMN_ID;


SELECT
    CONSTRAINT_NAME,
    TABLE_NAME,
    CONSTRAINT_TYPE,
    STATUS
FROM USER_CONSTRAINTS
WHERE TABLE_NAME IN ('VOLUNTEERS', 'TASKS')
ORDER BY TABLE_NAME, CONSTRAINT_TYPE, CONSTRAINT_NAME;