<?php

namespace Modules\UserManagement\Services\OtherModuleCommunication;

interface ISuperAdminService{

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get all super admin users
     * -----------------------------------------------------------------------------------------------------------------
     * @return mixed
     */
    public function getAllSuperAdmins();
}
