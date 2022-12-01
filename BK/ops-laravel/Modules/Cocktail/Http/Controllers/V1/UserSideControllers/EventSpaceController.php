<?php

namespace Modules\Cocktail\Http\Controllers\V1\UserSideControllers;

use Aws\Chime\Exception\ChimeException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
use Modules\Cocktail\Entities\EventSpaceUser;
use Modules\Cocktail\Exceptions\CustomAuthorizationException;
use Modules\Cocktail\Exceptions\CustomValidationException;
use Modules\Cocktail\Exceptions\NotExistsException;
use Modules\Cocktail\Http\Requests\V1\ConversationJoinRequest;
use Modules\Cocktail\Http\Requests\V1\ConversationLeaveRequest;
use Modules\Cocktail\Http\Requests\V1\EventSpaceAddUserRequest;
use Modules\Cocktail\Services\AuthorizationService;
use Modules\Cocktail\Services\EventSpaceService;
use Modules\Cocktail\Transformers\UserSide\ConversationChimeResource;
use Modules\Cocktail\Transformers\UserSide\ConversationResource;
use Modules\Cocktail\Transformers\UserSide\EventSpaceCollectionResource;
use Modules\Cocktail\Transformers\UserSide\EventSpaceWithConversationResource;
use Modules\Events\Entities\Event;
use Modules\Events\Service\ValidationService;

class EventSpaceController extends Controller {
    
    protected $service;
    
    public function __construct() {
        $this->service = EventSpaceService::getInstance();
    }
    
    /**
     * To get the
     * - all spaces of a particular event + current joined space + all conversation of current joined space + joined conversation (if any)
     * all the space will be have minimal information
     * the current space will also have minimal information
     * the joined space conversation will have minimal information
     * the joined conversation will have full information which include chime + users badge
     *
     *
     * @param $eventUuid
     * @return \Illuminate\Http\JsonResponse|EventSpaceCollectionResource
     */
    public function getEventSpacesForUser($eventUuid) {
        try {
            DB::connection('tenant')->beginTransaction();
            // to check if current chime meeting still exists
            $this->service->verifyCurrentConversation($eventUuid);
            // this will get event with
            // current spaces
            //      with current joined conversation in current space
            //      with all conversation of joined space
            //      with all users of respective conversation
            //      with event user data of each user within respective conversation -> user , to get the user Available|DND state
            $event = $this->service->getEventWithSpaceAndConversations($eventUuid);
            
            if (!$event) {
                throw new CustomValidationException('exists', 'event');
            }
            if (!AuthorizationService::getInstance()->isUserEventMember($eventUuid)) {
                throw new CustomAuthorizationException('not_belongs', 'event');
            }
            $result = (new EventSpaceCollectionResource($event));
            DB::connection('tenant')->commit();
        } catch (CustomAuthorizationException $e) {
            DB::connection('tenant')->rollback();
            $result = $e->render();
        } catch (CustomValidationException $e) {
            DB::connection('tenant')->rollback();
            $result = $e->render();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            $result = response()->json(['status' => false, 'msg' => $e->getTrace()], 500);
        }
        
        return $result;
    }
    
    /**
     * @param $spaceUuid
     * @return \Illuminate\Http\JsonResponse|EventSpaceWithConversationResource
     * @deprecated
     */
    public function getSpaceWithConversation($spaceUuid) {
        try {
            $space = $this->service->getSpace($spaceUuid);
            return (new EventSpaceWithConversationResource($space))->additional(['status' => true]);
        } catch (NotExistsException $e) {
            return response()->json(['status' => false, 'msg' => __('validation.exists', ['attribute' => $e->getMessage()])], 422);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }
    
    public function spaceJoin(EventSpaceAddUserRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $this->service->addUserToSpace(Auth::user()->id, $request->space_uuid, $request->event_uuid, EventSpaceUser::$ROLE_MEMBER);
            $space = $this->service->getSpace($request->space_uuid);
            $space->load('event');
            DB::connection('tenant')->commit();
            return (new EventSpaceWithConversationResource($space))->additional(['status' => true]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => 'Internal Server Error ', 'error' => $e->getMessage()], 500);
        }
    }
    
    
    public function conversationJoin(ConversationJoinRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            if (!AuthorizationService::getInstance()
                ->isUserStateAvailable(Auth::user()->id, null, $request->space_uuid)) {
                return response()->json(['status' => false, 'msg' => __('cocktail::message.not_allowed_in_dnd')], 422);
            }
            $result = $this->service->joinWithConversation($request->user_id, $request->space_uuid);
            DB::connection('tenant')->commit();
            return (new ConversationChimeResource($result))->additional(['status' => true]);
        } catch (CustomValidationException $e) {
            DB::connection('tenant')->rollback();
            $result = $e->render();
        } catch (ChimeException $e) {
            DB::connection('tenant')->rollback();
            $result = response()->json(['status' => false, 'msg' => __('cocktail::message.chime_meeting_expired')], 422);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            $result = response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
        return $result;
    }
    
    public function conversationLeave(ConversationLeaveRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $conversation = $this->service->removeUserFromConversation($request->conversation_uuid);
            DB::connection('tenant')->commit();
            return $conversation === true
                ? response()->json(['status' => true, 'data' => true], 200)
                : (new ConversationResource($conversation))->additional(['status' => true]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }
    }
    
    /**
     * @param $eventUuid
     * @return JsonResponse|ConversationChimeResource
     */
    public function getCurrentConversation($eventUuid) {
        $event = $this->service->getCurrentConversation($eventUuid);
        if ($event->currentSpace && $event->currentSpace->currentConversation) {
            return (new ConversationChimeResource($event->currentSpace->currentConversation))->additional(['status' => true]);
        }
        return response()->json(['status' => true, 'data' => null], 200);
    }
    
}

