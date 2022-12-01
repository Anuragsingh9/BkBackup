<?php


namespace Modules\KctUser\Repositories\factory;

use Carbon\Carbon;
use Modules\KctUser\Entities\ConversationUser;
use Modules\KctUser\Repositories\IConversationUserRepository;


class ConversationUserRepository implements IConversationUserRepository {

    /**
     * @inheritDoc
     */
    public function createUserConv($conversationUuid, $userId, $userChime) {
        return ConversationUser::create([
            'conversation_uuid' => $conversationUuid,
            'user_id'           => $userId,
            'chime_attendee'    => $userChime,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getConversationByUserId($userIds, $conversationUuid) {
        return ConversationUser::whereIn('user_id', $userIds)
            ->where('conversation_uuid', $conversationUuid)
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function getConversationByConvUuid($conversationUuid) {
        return ConversationUser::where('conversation_uuid', $conversationUuid)->get();
    }

    /**
     * @inheritDoc
     */
    public function convLeaveUpdate($conversationUuid, $conversationUsers) {
        ConversationUser::where('conversation_uuid', $conversationUuid)
            ->whereIn('user_id', $conversationUsers->pluck('user_id')->toArray())
            ->update(['leave_at' => Carbon::now()]);
    }

    /**
     * @inheritDoc
     */
    public function updateLeaveConversation($conversationIds, $userId) {
        ConversationUser::whereIn('conversation_uuid', $conversationIds) // todo conversation
        ->where('user_id', $userId)
            ->whereNull('leave_at')
            ->update(['leave_at' => Carbon::now()]);
    }


}
