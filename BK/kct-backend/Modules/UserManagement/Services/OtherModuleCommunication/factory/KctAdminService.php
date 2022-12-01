<?php

namespace Modules\UserManagement\Services\OtherModuleCommunication\factory;

use Illuminate\Database\Eloquent\Collection;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\UserManagement\Services\OtherModuleCommunication\IKctAdminService;

class KctAdminService implements IKctAdminService {
    use ServicesAndRepo;

    /**
     * @inheritDoc
     */
    public function getLabels($groupId): Collection {
        return $this->adminRepo()->labelRepository->getAll($groupId);
    }

    /**
     * @inheritDoc
     */
    public function getUserCurrentGroup($userId){
        return $this->adminServices()->groupService->getUserCurrentGroup($userId);
    }

    /**
     * @inheritDoc
     */
    public function getGroupData($groupId){
        return $this->adminRepo()->groupRepository->findById($groupId);
    }
}
