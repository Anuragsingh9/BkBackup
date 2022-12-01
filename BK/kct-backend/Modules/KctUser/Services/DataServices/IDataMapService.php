<?php

namespace Modules\KctUser\Services\DataServices;

use App\Models\User;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will be managing the data map integration
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IDataMapService
 * @package Modules\KctUser\Services\DataServices
 */
interface IDataMapService {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To load the tags for user
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $user
     * @param null $allTags
     * @return mixed
     */
    public function loadPPTagsForUser($user, $allTags = null);

}
