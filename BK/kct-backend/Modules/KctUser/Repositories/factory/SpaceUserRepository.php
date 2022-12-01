<?php


namespace Modules\KctUser\Repositories\factory;

use Illuminate\Support\Facades\Auth;
use Modules\KctUser\Entities\EventSpaceUser;
use Modules\KctUser\Repositories\ISpaceUserRepository;

class SpaceUserRepository implements ISpaceUserRepository {
    public function findSpaceByUserIdAndSpaceUuid($userId, $spaceUuid) {
        return EventSpaceUser::where('user_id', $userId)->where('space_uuid', $spaceUuid)->first();
    }

    public function countSpaceUser($spaceUuid, $userId) {
        return EventSpaceUser::where('space_uuid', $spaceUuid)
            ->where('user_id', $userId)
            ->count();
    }

    public function updateConversationUuid($spaceUuid, $userIds, $conversationUuid) {
        return EventSpaceUser::where('space_uuid', $spaceUuid)
            ->whereIn('user_id', $userIds)
            ->update(['current_conversation_uuid' => $conversationUuid]);
    }

    public function createSpaceUser($userId, $spaceUuid, $role) {
        return EventSpaceUser::updateOrCreate([
            'user_id'    => $userId,
            'space_uuid' => $spaceUuid,
            'role'       => $role,
        ], [
            'user_id'    => $userId,
            'space_uuid' => $spaceUuid,
            'role'       => $role,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function deleteSpaceUser($userId, $spaceUuid) {
        return EventSpaceUser::where('user_id', $userId)->whereIn('space_uuid', $spaceUuid)->delete();
    }
}
