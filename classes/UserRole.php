<?php

class UserRole
{
    const SUPERUSER = 'SU';

    const WAREHOUSE_MANAGER = 'LM';
    const WAREHOUSE_KABAG = 'LK';
    const WAREHOUSE_ADMIN = 'LA';
    const WAREHOUSE_COORDINATOR = 'LC';
    const WAREHOUSE_SUPERVISOR = 'KS';
    const WAREHOUSE_CHECKER = 'CK';
    const WAREHOUSE_STOCKIST = 'SK';

    // TODO check
    const WAREHOUSE_HANDOVER = 'LH';

    const MARKETING_MANAGER = 'MM';
    const MARKETING_STAFF = 'MS';

    const ACCOUNTING_MANAGER = 'AM';
    const ACCOUNTING_STAFF = 'AS';

    const AUDIT_MANAGER = 'CM';
    const AUDIT_STAFF = 'CS';

    const QA_STAFF = 'QO';
    const QA_SUPEVISOR = 'QS';
    const QA_KABAG = 'QK';
    const QA_MANAGER = 'QM';

    const WMM_KABAG = 'WK';
    const PRODUCTION_KABAG = 'PK';

    const PLANT_MANAGER = 'PM';

    private static $AUTHORIZED_TO_EDIT_MASTER_DATA = array(
        self::SUPERUSER,
        self::PLANT_MANAGER,
        self::WAREHOUSE_MANAGER, self::WAREHOUSE_KABAG, self::WAREHOUSE_SUPERVISOR,
        self::QA_MANAGER
    );
    public static function isAuthorizedForEditMasterData(array $roles)
    {
        $isAuthorized = false;
        foreach (self::$AUTHORIZED_TO_EDIT_MASTER_DATA as $authorizedRole) {
            $isAuthorized = in_array($authorizedRole, $roles);
            if ($isAuthorized) {
                break;
            }
        }
        return $isAuthorized;
    }

    /**
     * Checks if the current authenticated user is a member of any of the roles.
     *
     * @param array $roles roles to check against
     * @return bool
     */
    public static function hasAnyRole(array $roles) {
        $user = SessionUtils::getUser();
        if ($user === null) {
            throw new RuntimeException('session not started or you are not authenticated!');
        }

        $hasRole = false;
        foreach ($roles as $role) {
            $hasRole = in_array($role, $user->roles);
            if ($hasRole) break;
        }
        return $hasRole;
    }

    public static function checkAuth(array $roles,$idMenu){
        $db = PostgresqlDatabase::getInstance();
        $user = SessionUtils::getUser();
        foreach ($roles as $a ) {
            $roles_new=$a;
        }
        // $response=array();
        // $isAuthorized=array();
        $sql="SELECT * FROM tbl_jabatan_auth where kode_jabatan=$1 and menu=$2 ";
        $findResult = $db->parameterizedQuery($sql, array($roles_new,$idMenu));
        $result = pg_fetch_assoc($findResult);
        $db->close();
        $response=array(
            "created" => $result['created']=$result['created']==1?true:false,
            "updated" => $result['updated']=$result['updated']==1?true:false,
            "deleted" => $result['deleted']=$result['deleted']==1?true:false
        );
        // if($result>0){
        //     return true;
        // }else{
        //     return false;
        // }
        $isAuthorized=array("is_authorized"=>$response);
        return $response;
    }


}
