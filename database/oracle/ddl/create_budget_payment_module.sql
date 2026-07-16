SET SQLBLANKLINES ON
SET DEFINE OFF
SET SERVEROUTPUT ON

/*
|--------------------------------------------------------------------------
| COMPLETE EXISTING BUDGETS TABLE
|--------------------------------------------------------------------------
*/

ALTER TABLE BUDGETS
ADD (
    EVENT_ID          NUMBER(19) NOT NULL,
    CATEGORY          VARCHAR2(30)
        DEFAULT 'miscellaneous' NOT NULL,
    DESCRIPTION       VARCHAR2(500),
    ALLOCATED_AMOUNT  NUMBER(12,2)
        DEFAULT 0 NOT NULL,
    STATUS            VARCHAR2(20)
        DEFAULT 'planned' NOT NULL
);

ALTER TABLE BUDGETS
ADD CONSTRAINT FK_BUDGET_EVENT
FOREIGN KEY (EVENT_ID)
REFERENCES EVENTS(ID);

ALTER TABLE BUDGETS
ADD CONSTRAINT UQ_BUDGET_EVENT_CAT
UNIQUE (EVENT_ID, CATEGORY);

ALTER TABLE BUDGETS
ADD CONSTRAINT CK_BUDGET_AMOUNT
CHECK (ALLOCATED_AMOUNT >= 0);

ALTER TABLE BUDGETS
ADD CONSTRAINT CK_BUDGET_CATEGORY
CHECK (
    CATEGORY IN (
        'venue',
        'food',
        'marketing',
        'transport',
        'equipment',
        'decoration',
        'security',
        'miscellaneous'
    )
);

ALTER TABLE BUDGETS
ADD CONSTRAINT CK_BUDGET_STATUS
CHECK (
    STATUS IN (
        'planned',
        'approved',
        'closed',
        'cancelled'
    )
);


/*
|--------------------------------------------------------------------------
| CREATE PAYMENTS TABLE
|--------------------------------------------------------------------------
*/

CREATE TABLE PAYMENTS (
    ID                NUMBER(19) NOT NULL,
    EVENT_ID          NUMBER(19) NOT NULL,
    BUDGET_ID         NUMBER(19),
    PAYEE_NAME        VARCHAR2(150) NOT NULL,
    PAYMENT_TYPE      VARCHAR2(20)
        DEFAULT 'expense' NOT NULL,
    AMOUNT            NUMBER(12,2) NOT NULL,
    PAYMENT_METHOD    VARCHAR2(30)
        DEFAULT 'cash' NOT NULL,
    REFERENCE_NUMBER  VARCHAR2(100),
    PAYMENT_DATE      TIMESTAMP(6)
        DEFAULT SYSTIMESTAMP NOT NULL,
    STATUS            VARCHAR2(20)
        DEFAULT 'pending' NOT NULL,
    NOTES             CLOB,
    CREATED_AT        TIMESTAMP(6),
    UPDATED_AT        TIMESTAMP(6),

    CONSTRAINT PAYMENTS_ID_PK
        PRIMARY KEY (ID),

    CONSTRAINT FK_PAYMENT_EVENT
        FOREIGN KEY (EVENT_ID)
        REFERENCES EVENTS(ID),

    CONSTRAINT FK_PAYMENT_BUDGET
        FOREIGN KEY (BUDGET_ID)
        REFERENCES BUDGETS(ID),

    CONSTRAINT UQ_PAYMENT_REFERENCE
        UNIQUE (REFERENCE_NUMBER),

    CONSTRAINT CK_PAYMENT_AMOUNT
        CHECK (AMOUNT > 0),

    CONSTRAINT CK_PAYMENT_TYPE
        CHECK (
            PAYMENT_TYPE IN (
                'expense',
                'income',
                'refund'
            )
        ),

    CONSTRAINT CK_PAYMENT_METHOD
        CHECK (
            PAYMENT_METHOD IN (
                'cash',
                'bank',
                'card',
                'mobile_banking',
                'cheque'
            )
        ),

    CONSTRAINT CK_PAYMENT_STATUS
        CHECK (
            STATUS IN (
                'pending',
                'approved',
                'paid',
                'cancelled'
            )
        )
);


/*
|--------------------------------------------------------------------------
| PERFORMANCE INDEXES
|--------------------------------------------------------------------------
*/

CREATE INDEX IDX_BUDGET_EVENT
ON BUDGETS(EVENT_ID);

CREATE INDEX IDX_BUDGET_STATUS
ON BUDGETS(STATUS);

CREATE INDEX IDX_PAYMENT_EVENT
ON PAYMENTS(EVENT_ID);

CREATE INDEX IDX_PAYMENT_BUDGET
ON PAYMENTS(BUDGET_ID);

CREATE INDEX IDX_PAYMENT_STATUS
ON PAYMENTS(STATUS);

CREATE INDEX IDX_PAYMENT_DATE
ON PAYMENTS(PAYMENT_DATE);

CREATE INDEX IDX_PAYMENT_AMOUNT
ON PAYMENTS(AMOUNT);


/*
|--------------------------------------------------------------------------
| BUDGET ID SEQUENCE
|--------------------------------------------------------------------------
*/

DECLARE
    V_COUNT NUMBER;
    V_START NUMBER;
BEGIN
    SELECT COUNT(*)
    INTO V_COUNT
    FROM USER_SEQUENCES
    WHERE SEQUENCE_NAME = 'BUDGETS_ID_SEQ';

    IF V_COUNT = 0 THEN
        SELECT NVL(MAX(ID), 0) + 1
        INTO V_START
        FROM BUDGETS;

        EXECUTE IMMEDIATE
            'CREATE SEQUENCE BUDGETS_ID_SEQ ' ||
            'START WITH ' || V_START || ' ' ||
            'INCREMENT BY 1 NOCACHE NOCYCLE';

        DBMS_OUTPUT.PUT_LINE('BUDGETS_ID_SEQ created.');
    ELSE
        DBMS_OUTPUT.PUT_LINE('BUDGETS_ID_SEQ already exists.');
    END IF;
END;
/


/*
|--------------------------------------------------------------------------
| PAYMENT ID SEQUENCE
|--------------------------------------------------------------------------
*/

DECLARE
    V_COUNT NUMBER;
    V_START NUMBER;
BEGIN
    SELECT COUNT(*)
    INTO V_COUNT
    FROM USER_SEQUENCES
    WHERE SEQUENCE_NAME = 'PAYMENTS_ID_SEQ';

    IF V_COUNT = 0 THEN
        SELECT NVL(MAX(ID), 0) + 1
        INTO V_START
        FROM PAYMENTS;

        EXECUTE IMMEDIATE
            'CREATE SEQUENCE PAYMENTS_ID_SEQ ' ||
            'START WITH ' || V_START || ' ' ||
            'INCREMENT BY 1 NOCACHE NOCYCLE';

        DBMS_OUTPUT.PUT_LINE('PAYMENTS_ID_SEQ created.');
    ELSE
        DBMS_OUTPUT.PUT_LINE('PAYMENTS_ID_SEQ already exists.');
    END IF;
END;
/


/*
|--------------------------------------------------------------------------
| BUDGET ID AND VALIDATION TRIGGER
|--------------------------------------------------------------------------
*/

CREATE OR REPLACE TRIGGER TRG_BUDGET_VALIDATE
BEFORE INSERT OR UPDATE
ON BUDGETS
FOR EACH ROW
BEGIN
    IF INSERTING AND :NEW.ID IS NULL THEN
        :NEW.ID := BUDGETS_ID_SEQ.NEXTVAL;
    END IF;

    :NEW.CATEGORY := LOWER(TRIM(:NEW.CATEGORY));
    :NEW.STATUS := LOWER(TRIM(:NEW.STATUS));

    IF :NEW.ALLOCATED_AMOUNT < 0 THEN
        RAISE_APPLICATION_ERROR(
            -20101,
            'Allocated budget cannot be negative.'
        );
    END IF;

    IF INSERTING AND :NEW.CREATED_AT IS NULL THEN
        :NEW.CREATED_AT := SYSTIMESTAMP;
    END IF;

    :NEW.UPDATED_AT := SYSTIMESTAMP;
END;
/

SHOW ERRORS TRIGGER TRG_BUDGET_VALIDATE


/*
|--------------------------------------------------------------------------
| PAYMENT ID AND VALIDATION TRIGGER
|--------------------------------------------------------------------------
*/

CREATE OR REPLACE TRIGGER TRG_PAYMENT_VALIDATE
BEFORE INSERT OR UPDATE
ON PAYMENTS
FOR EACH ROW
DECLARE
    V_BUDGET_EVENT_ID BUDGETS.EVENT_ID%TYPE;
BEGIN
    IF INSERTING AND :NEW.ID IS NULL THEN
        :NEW.ID := PAYMENTS_ID_SEQ.NEXTVAL;
    END IF;

    :NEW.PAYMENT_TYPE :=
        LOWER(TRIM(:NEW.PAYMENT_TYPE));

    :NEW.PAYMENT_METHOD :=
        LOWER(TRIM(:NEW.PAYMENT_METHOD));

    :NEW.STATUS :=
        LOWER(TRIM(:NEW.STATUS));

    IF :NEW.AMOUNT <= 0 THEN
        RAISE_APPLICATION_ERROR(
            -20110,
            'Payment amount must be greater than zero.'
        );
    END IF;

    IF :NEW.PAYMENT_TYPE = 'expense'
       AND :NEW.BUDGET_ID IS NULL THEN
        RAISE_APPLICATION_ERROR(
            -20111,
            'Expense payment requires a budget.'
        );
    END IF;

    IF :NEW.BUDGET_ID IS NOT NULL THEN
        SELECT EVENT_ID
        INTO V_BUDGET_EVENT_ID
        FROM BUDGETS
        WHERE ID = :NEW.BUDGET_ID;

        IF V_BUDGET_EVENT_ID <> :NEW.EVENT_ID THEN
            RAISE_APPLICATION_ERROR(
                -20112,
                'Budget does not belong to the selected event.'
            );
        END IF;
    END IF;

    IF INSERTING AND :NEW.PAYMENT_DATE IS NULL THEN
        :NEW.PAYMENT_DATE := SYSTIMESTAMP;
    END IF;

    IF INSERTING AND :NEW.CREATED_AT IS NULL THEN
        :NEW.CREATED_AT := SYSTIMESTAMP;
    END IF;

    :NEW.UPDATED_AT := SYSTIMESTAMP;

EXCEPTION
    WHEN NO_DATA_FOUND THEN
        RAISE_APPLICATION_ERROR(
            -20113,
            'Selected budget does not exist.'
        );
END;
/

SHOW ERRORS TRIGGER TRG_PAYMENT_VALIDATE


/*
|--------------------------------------------------------------------------
| VERIFICATION
|--------------------------------------------------------------------------
*/

SELECT
    TABLE_NAME,
    COLUMN_NAME,
    DATA_TYPE,
    NULLABLE,
    DATA_DEFAULT
FROM USER_TAB_COLUMNS
WHERE TABLE_NAME IN (
    'BUDGETS',
    'PAYMENTS'
)
ORDER BY TABLE_NAME, COLUMN_ID;

SELECT
    CONSTRAINT_NAME,
    TABLE_NAME,
    CONSTRAINT_TYPE,
    STATUS
FROM USER_CONSTRAINTS
WHERE TABLE_NAME IN (
    'BUDGETS',
    'PAYMENTS'
)
ORDER BY TABLE_NAME, CONSTRAINT_TYPE, CONSTRAINT_NAME;

SELECT
    SEQUENCE_NAME,
    LAST_NUMBER
FROM USER_SEQUENCES
WHERE SEQUENCE_NAME IN (
    'BUDGETS_ID_SEQ',
    'PAYMENTS_ID_SEQ'
)
ORDER BY SEQUENCE_NAME;

SELECT
    TRIGGER_NAME,
    TABLE_NAME,
    STATUS
FROM USER_TRIGGERS
WHERE TRIGGER_NAME IN (
    'TRG_BUDGET_VALIDATE',
    'TRG_PAYMENT_VALIDATE'
)
ORDER BY TABLE_NAME;