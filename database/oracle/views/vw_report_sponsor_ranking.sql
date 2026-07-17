SET SQLBLANKLINES ON
SET DEFINE OFF

CREATE OR REPLACE VIEW VW_REPORT_SPONSOR_RANKING AS
SELECT
    S.ID AS SPONSOR_ID,
    S.NAME AS SPONSOR_NAME,
    S.SPONSOR_TYPE,
    S.STATUS AS SPONSOR_STATUS,

    COUNT(
        DISTINCT ES.EVENT_ID
    ) AS EVENT_COUNT,

    NVL(
        SUM(ES.AMOUNT),
        0
    ) AS TOTAL_PLEDGED_AMOUNT,

    NVL(
        SUM(
            CASE
                WHEN LOWER(ES.STATUS) IN (
                    'confirmed',
                    'paid'
                )
                THEN ES.AMOUNT
                ELSE 0
            END
        ),
        0
    ) AS CONFIRMED_AMOUNT,

    NVL(
        SUM(
            CASE
                WHEN LOWER(ES.STATUS) NOT IN (
                    'confirmed',
                    'paid',
                    'cancelled'
                )
                THEN ES.AMOUNT
                ELSE 0
            END
        ),
        0
    ) AS PENDING_AMOUNT,

    DENSE_RANK() OVER
    (
        ORDER BY
            NVL(
                SUM(
                    CASE
                        WHEN LOWER(ES.STATUS) IN (
                            'confirmed',
                            'paid'
                        )
                        THEN ES.AMOUNT
                        ELSE 0
                    END
                ),
                0
            ) DESC
    ) AS SPONSOR_RANK

FROM SPONSORS S

LEFT JOIN EVENT_SPONSORS ES
    ON ES.SPONSOR_ID = S.ID

GROUP BY
    S.ID,
    S.NAME,
    S.SPONSOR_TYPE,
    S.STATUS;

SHOW ERRORS VIEW VW_REPORT_SPONSOR_RANKING