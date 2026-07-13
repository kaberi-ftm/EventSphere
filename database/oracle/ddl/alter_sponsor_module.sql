/*
|--------------------------------------------------------------------------
| EventSphere Sponsor and Event Sponsorship Module
|--------------------------------------------------------------------------
| Oracle Database 21c XE
| Existing tables are altered without dropping them.
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| SPONSORS TABLE
|--------------------------------------------------------------------------
*/

ALTER TABLE SPONSORS
ADD (
    NAME             VARCHAR2(150) NOT NULL,
    CONTACT_PERSON   VARCHAR2(150),
    EMAIL            VARCHAR2(255),
    PHONE            VARCHAR2(30),
    ADDRESS          VARCHAR2(500),
    WEBSITE          VARCHAR2(255),
    DESCRIPTION      CLOB,
    SPONSOR_TYPE     VARCHAR2(30)
        DEFAULT 'corporate' NOT NULL,
    STATUS           VARCHAR2(20)
        DEFAULT 'active' NOT NULL,
    DESCRIPTION CLOB
);

ALTER TABLE SPONSORS
ADD CONSTRAINT UQ_SPONSOR_EMAIL
UNIQUE (EMAIL);

ALTER TABLE SPONSORS
ADD CONSTRAINT CK_SPONSOR_TYPE
CHECK (
    SPONSOR_TYPE IN (
        'corporate',
        'individual',
        'ngo',
        'media',
        'government',
        'other'
    )
);

ALTER TABLE SPONSORS
ADD CONSTRAINT CK_SPONSOR_STATUS
CHECK (
    STATUS IN (
        'active',
        'inactive'
    )
);


/*
|--------------------------------------------------------------------------
| EVENT_SPONSORS TABLE
|--------------------------------------------------------------------------
*/

ALTER TABLE EVENT_SPONSORS
ADD (
    EVENT_ID           NUMBER(19) NOT NULL,
    SPONSOR_ID         NUMBER(19) NOT NULL,
    AMOUNT             NUMBER(12,2)
        DEFAULT 0 NOT NULL,
    CONTRIBUTION_TYPE  VARCHAR2(30)
        DEFAULT 'cash' NOT NULL,
    AGREEMENT_DATE     TIMESTAMP(6)
        DEFAULT SYSTIMESTAMP NOT NULL,
    STATUS             VARCHAR2(20)
        DEFAULT 'pledged' NOT NULL,
    NOTES              CLOB
);

ALTER TABLE EVENT_SPONSORS
ADD CONSTRAINT FK_EVSP_EVENT
FOREIGN KEY (EVENT_ID)
REFERENCES EVENTS(ID);

ALTER TABLE EVENT_SPONSORS
ADD CONSTRAINT FK_EVSP_SPONSOR
FOREIGN KEY (SPONSOR_ID)
REFERENCES SPONSORS(ID);

ALTER TABLE EVENT_SPONSORS
ADD CONSTRAINT UQ_EVSP_EVENT_SPONSOR
UNIQUE (EVENT_ID, SPONSOR_ID);

ALTER TABLE EVENT_SPONSORS
ADD CONSTRAINT CK_EVSP_AMOUNT
CHECK (AMOUNT >= 0);

ALTER TABLE EVENT_SPONSORS
ADD CONSTRAINT CK_EVSP_CONTRIB
CHECK (
    CONTRIBUTION_TYPE IN (
        'cash',
        'in_kind',
        'media',
        'venue',
        'service'
    )
);

ALTER TABLE EVENT_SPONSORS
ADD CONSTRAINT CK_EVSP_STATUS
CHECK (
    STATUS IN (
        'pledged',
        'confirmed',
        'paid',
        'cancelled'
    )
);


/*
|--------------------------------------------------------------------------
| PERFORMANCE INDEXES
|--------------------------------------------------------------------------
*/

CREATE INDEX IDX_SPONSOR_NAME
ON SPONSORS(NAME);

CREATE INDEX IDX_SPONSOR_STATUS
ON SPONSORS(STATUS);

CREATE INDEX IDX_EVSP_EVENT
ON EVENT_SPONSORS(EVENT_ID);

CREATE INDEX IDX_EVSP_SPONSOR
ON EVENT_SPONSORS(SPONSOR_ID);

CREATE INDEX IDX_EVSP_STATUS
ON EVENT_SPONSORS(STATUS);

CREATE INDEX IDX_EVSP_AMOUNT
ON EVENT_SPONSORS(AMOUNT);


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
    'SPONSORS',
    'EVENT_SPONSORS'
)
ORDER BY TABLE_NAME, COLUMN_ID;

SELECT
    CONSTRAINT_NAME,
    TABLE_NAME,
    CONSTRAINT_TYPE,
    STATUS
FROM USER_CONSTRAINTS
WHERE TABLE_NAME IN (
    'SPONSORS',
    'EVENT_SPONSORS'
)
ORDER BY TABLE_NAME, CONSTRAINT_TYPE, CONSTRAINT_NAME;

SELECT
    INDEX_NAME,
    TABLE_NAME,
    UNIQUENESS,
    STATUS
FROM USER_INDEXES
WHERE TABLE_NAME IN (
    'SPONSORS',
    'EVENT_SPONSORS'
)
ORDER BY TABLE_NAME, INDEX_NAME;