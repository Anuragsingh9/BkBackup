<?php

namespace Modules\UserManagement\Services\OtherModuleCommunication;

use Illuminate\Database\Eloquent\Collection;

interface IKctAdminService {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get all the labels
     * -----------------------------------------------------------------------------------------------------------------
     * @param $groupId
     * @return mixed
     */
    public function getLabels($groupId): Collection;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get the user current group Id
     * -----------------------------------------------------------------------------------------------------------------
     * @param $userId
     * @return mixed
     */
    public function getUserCurrentGroup($userId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the group data by group_id.
     * -----------------------------------------------------------------------------------------------------------------
     * @param $groupId
     * @return mixed
     */
    public function getGroupData($groupId);
    }

