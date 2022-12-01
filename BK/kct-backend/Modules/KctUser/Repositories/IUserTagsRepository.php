<?php

namespace Modules\KctUser\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\KctUser\Entities\UserTag;

interface IUserTagsRepository {

    /**
     * @param $userId
     * @param $tagId
     * @return mixed
     */
    public function updateOrCreateUserPPTag($userId, $tagId);

    /**
     * @param $userId
     * @return mixed
     */
    public function getUserTagId($userId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the all tags
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param null $status
     * @return Collection
     */
    public function allTags($status = null): Collection;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To add the tag to user
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $userId
     * @param $tagId
     * @return UserTag|null
     */
    public function addTagToUser($userId, $tagId): ?UserTag;

}
