<?php


namespace Modules\KctUser\Repositories\factory;


use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\KctAdmin\Entities\Event;
use Modules\KctUser\Entities\Conversation;
use Modules\KctUser\Entities\SystemVirtualBackgrounds;
use Modules\KctUser\Repositories\IConversationRepository;
use Modules\KctUser\Traits\Services;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will be managing the conversation management
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class ConversationRepository
 * @package Modules\KctUser\Repositories\factory
 */
class ConversationRepository implements IConversationRepository {
    use Services;
    /**
     * @inheritDoc
     */
    public function createConversation($spaceUuid) {
        return Conversation::create([
            'space_uuid' => $spaceUuid,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getUserConversation($userId, $spaceUuid) {
        return Conversation::whereHas("userRelation", function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->where('space_uuid', $spaceUuid)->first();
    }

    /**
     * @inheritDoc
     */
    public function getConversation($uuid) {
        return Conversation::where('uuid', $uuid)->first();
    }

    /**
     * @inheritDoc
     */
    public function getUserCurrentConversation($eventUuid, $eventUser) {
        return Event::with([
            'currentSpace',
            'currentSpace.currentConversation.users.eventUser' => $eventUser,
        ])->where('event_uuid', $eventUuid);
    }

    /**
     * @inheritDoc
     */
    public function getConversationUserToRemove($conversationUuid) {
        return Conversation::with(['userRelation', 'dummyRelation.dummyUsers'])
            ->whereHas('userRelation', function ($q) {
                $q->where('user_id', Auth::user()->id);
            })->where('uuid', $conversationUuid)->first();
    }

    /**
     * @inheritDoc
     */
    public function getSpaceAllConversation($spaceUuid) {
        return Conversation::whereIn('space_uuid', $spaceUuid)->get();
    }

    /**
     * @inheritDoc
     */
    public function getSystemVirtualBGImages(){
        return SystemVirtualBackgrounds::all();
    }

}
