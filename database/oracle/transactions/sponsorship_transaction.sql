SET SERVEROUTPUT ON;

BEGIN
    PR_ASSIGN_EVENT_SPONSOR(
        P_EVENT_ID          => 1,
        P_SPONSOR_ID        => 1,
        P_AMOUNT            => 50000,
        P_CONTRIBUTION_TYPE => 'cash',
        P_STATUS            => 'confirmed',
        P_NOTES             => 'Main event sponsor'
    );

    COMMIT;

    DBMS_OUTPUT.PUT_LINE(
        'Sponsorship transaction completed.'
    );

EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;

        DBMS_OUTPUT.PUT_LINE(
            'Transaction rolled back: ' || SQLERRM
        );
END;
/