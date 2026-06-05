<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        
        
        DB::unprepared("DROP FUNCTION IF EXISTS isUserAllowPolicyControl");

        DB::unprepared("
CREATE FUNCTION `isUserAllowPolicyControl`(_AppAccountID BIGINT, _BranchID BIGINT, _PolicyRouteName VARCHAR(100)) RETURNS tinyint(1)
BEGIN
    DECLARE _XRoleId INT;
    DECLARE _PolicyId TINYINT(1);
    DECLARE _PolicyIsActive TINYINT(1);
    DECLARE _PolicyIsVisible TINYINT(1);
    DECLARE _PolicyDefaultIsAllow TINYINT(1);
    
    
    IF(_BranchID IS NULL ) THEN
		SET _BranchID = 1;
    END IF;
    
    #CHECK IF POLICY CONTROL A ROUTE IS NOT RESTRICTED
    SELECT 
		id, is_active, is_visible, default_is_allow INTO
        _PolicyId, _PolicyIsActive, _PolicyIsVisible, _PolicyDefaultIsAllow
	FROM policy_controls WHERE `name`=_PolicyRouteName LIMIT 1;
    
    #IF ROUTE POLICY NOT EXISTS RETURN OK
    IF(_PolicyId IS NULL OR 0 = _PolicyId  OR 0 = _PolicyIsActive ) THEN
		RETURN 1;
	#NOT CONSTRAINT FOR VISIBLE MODIFICATION IF NOT VISIBILE AND JUST ALLOW THEN ITS OKAY
	ELSEIF( 0 = _PolicyIsVisible AND 1 = _PolicyDefaultIsAllow)THEN
		RETURN 1;
	END IF;
    
    #CHECK IF ROLE EXISTS IF RETURNS SOMETHING UNEXPECED VALUE
    SELECT xrole_id INTO _XRoleId FROM xuser_roles WHERE app_account_id = _AppAccountID AND branch_id = _BranchID LIMIT 1;
    #CHECK IF USER HAS THAT ROLE 
    IF(_XRoleId IS NULL OR _XRoleId <= 0 ) THEN
		RETURN -1;
    END IF;
    
    #ROLE CHECK IF ROLE HAS THAT ROUTE
    IF NOT EXISTS( SELECT * FROM policy_control_role_policy_routes WHERE xrole_id = _XRoleId AND route_name = _PolicyRouteName AND branch_id = _BranchID LIMIT 1)THEN
		RETURN 0;
    END IF;
    
    #DISABLE ROUTE TO USER
    IF EXISTS( SELECT * FROM policy_control_user_disable_policy_routes WHERE app_account_id = _AppAccountID AND route_name = _PolicyRouteName AND branch_id = _BranchID LIMIT 1)THEN
		RETURN 0;
    END IF;
    
	RETURN 1;
END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
