<?php

namespace Modules\KctUser\Repositories;

use App\Models\User;

interface IOrgTagUserRepository {
    /**
     * @param $tagId
     * @param $userId
     * @return mixed
     */
    public function findUserTagByTagIdAndUserId($tagId, $userId);

    /**
     * @param $tagId
     * @param $userId
     * @return mixed
     */
    public function create($tagId, $userId);

    /**
     * @param $tagId
     * @param $userId
     * @return mixed
     */
    public function delete($tagId, $userId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method used for get existing user tag
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param User|null $user
     * @param bool $used
     * @param null $eventUuid
     * @return mixed
     */
    public function getExistingTag(?User $user, bool $used = true, $eventUuid = null);
}
