<?php


namespace Modules\KctUser\Repositories\factory;


use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Modules\KctUser\Entities\OrganiserTag;
use Modules\KctUser\Entities\UserTag;
use Modules\KctUser\Repositories\IUserTagsRepository;

class UserTagsRepository implements IUserTagsRepository {

    /**
     * @inheritDoc
     */
    public function updateOrCreateUserPPTag($userId, $tagId) {
        EventUserTagRelation::updateOrCreate([ // todo EventUserTagRelation
            'user_id' => $userId,
            'tag_id'  => $tagId,
        ], [
            'user_id' => $userId,
            'tag_id'  => $tagId,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getUserTagId($userId) {
        return UserTag::where('user_id', Auth::user()->id)->pluck('tag_id');
    }

    /**
     * @inheritDoc
     */
    public function allTags($status = null): Collection {
        return OrganiserTag::all();
    }

    /**
     * @inheritDoc
     */
    public function addTagToUser($userId, $tagId): ?UserTag {
        return UserTag::create([
            'user_id' => $userId,
            'tag_id'  => $tagId,
        ]);
    }
}
