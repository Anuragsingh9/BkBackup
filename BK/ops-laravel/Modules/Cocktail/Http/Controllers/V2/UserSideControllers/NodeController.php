<?php

namespace Modules\Cocktail\Http\Controllers\V2\UserSideControllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Cocktail\Entities\Conversation;
use Modules\Cocktail\Entities\EventDummyUser;
use Modules\Cocktail\Entities\EventSpace;
use Modules\Cocktail\Services\EventSpaceService;
use Modules\Cocktail\Services\V2Services\KctCoreService;
use Modules\Cocktail\Transformers\V2\UserSide\NodeSpaceWithDummyResource;

class NodeController extends Controller {
    public function addUserInConversation(Request $request) {
        $conversation = Conversation::where('uuid', $request->conversationUuid)->first();
        if ($conversation) {
            $conversationUsersCount = EventSpaceService::getInstance()
                ->getConversationUserCount($conversation);
            if ($conversationUsersCount >= config('cocktail.conversation_max_member')) {
                return response()->json([
                    'status' => false,
                    'msg'    => __('cocktail::message.conversation_member_limit', config('cocktail.conversation_max_member'))
                ]);
            }
            $space = EventSpace::find($conversation->space_uuid);
            if ($space) {
                $eventUuid = $space->event_uuid;
                EventDummyUser::where('dummy_user_id', $request->dummyUserId)
                    ->where('event_uuid', $eventUuid)
                    ->update([
                        'current_conv_uuid' => $request->conversationUuid
                    ]);
            }
        }
        return response()->json([
            'data'   => $request->all(),
            'status' => true,
        ]);
    }
    
    public function getEventDummyUsers(Request $request) {
        $data= KctCoreService::getInstance()->getEventDummyUsers($request->input('eventId'));
        return  NodeSpaceWithDummyResource::collection($data)->additional([
            'status' => true,
        ]);
    }
}
