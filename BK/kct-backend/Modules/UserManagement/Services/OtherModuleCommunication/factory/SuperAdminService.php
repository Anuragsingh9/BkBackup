<?php

namespace Modules\UserManagement\Services\OtherModuleCommunication\factory;

use Modules\SuperAdmin\Traits\ServicesAndRepo;
use Modules\UserManagement\Services\OtherModuleCommunication\ISuperAdminService;

class SuperAdminService implements ISuperAdminService{

    use ServicesAndRepo;

    /**
     * @inheritDoc
     */
    public function getAllSuperAdmins(){
        return $this->suRepo()->superAdminRepository->getAllSuperAdmins();
    }

}
