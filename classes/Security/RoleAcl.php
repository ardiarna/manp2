<?php

namespace Security;

use UserRole;

/**
 * Class RoleAcl
 *
 * Defines related {@see UserRole} that can access a particular feature (service/screen)
 * @package Security
 */
class RoleAcl
{

    /**
     * @return array
     */
    public static function mutationReport()
    {
        return array(
            UserRole::SUPERUSER,
            UserRole::ACCOUNTING_MANAGER, UserRole::ACCOUNTING_STAFF,
            UserRole::AUDIT_MANAGER, UserRole::AUDIT_STAFF,
            UserRole::PLANT_MANAGER,
            UserRole::QA_MANAGER, UserRole::QA_KABAG,
            UserRole::WAREHOUSE_ADMIN, UserRole::WAREHOUSE_STOCKIST, UserRole::WAREHOUSE_SUPERVISOR,
            UserRole::WAREHOUSE_KABAG, UserRole::WAREHOUSE_MANAGER
        );
    }

    /**
     * @return array
     */
    public static function palletsWithoutLocation()
    {
        return array(
            UserRole::SUPERUSER,
            UserRole::ACCOUNTING_MANAGER, UserRole::ACCOUNTING_STAFF,
            UserRole::AUDIT_MANAGER, UserRole::AUDIT_STAFF,
            UserRole::PLANT_MANAGER,
            UserRole::QA_MANAGER, UserRole::QA_KABAG,
            UserRole::WAREHOUSE_ADMIN, UserRole::WAREHOUSE_STOCKIST, UserRole::WAREHOUSE_SUPERVISOR,
            UserRole::WAREHOUSE_KABAG, UserRole::WAREHOUSE_MANAGER
        );
    }

    /**
     * @return array
     */
    public static function downgradePallets()
    {
        return array(
            UserRole::SUPERUSER,
            UserRole::ACCOUNTING_MANAGER, UserRole::ACCOUNTING_STAFF,
            UserRole::AUDIT_MANAGER, UserRole::AUDIT_STAFF,
            UserRole::PLANT_MANAGER,
            UserRole::QA_MANAGER, UserRole::QA_KABAG, UserRole::QA_SUPEVISOR, UserRole::QA_STAFF,
            UserRole::WAREHOUSE_ADMIN, UserRole::WAREHOUSE_STOCKIST, UserRole::WAREHOUSE_SUPERVISOR,
            UserRole::WAREHOUSE_KABAG, UserRole::WAREHOUSE_MANAGER
        );
    }

    /**
     * TODO: refactor and simplify
     * @return array
     */
    public static function downgradePalletsModification()
    {
        return array(
            UserRole::SUPERUSER,
            UserRole::PLANT_MANAGER,
            UserRole::QA_MANAGER, UserRole::QA_KABAG, UserRole::QA_SUPEVISOR, UserRole::QA_STAFF
        );
    }

    /**
     * TODO: refactor and simplify
     * @return array
     */
    public static function downgradePalletsApproval()
    {
        return array(
            UserRole::SUPERUSER,
            UserRole::PLANT_MANAGER,
            UserRole::QA_MANAGER, UserRole::QA_KABAG
        );
    }
}
