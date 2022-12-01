<?php

namespace Modules\Cocktail\Http\Controllers\V2\UserSideControllers;

use App\User;
use Aws\Chime\Exception\ChimeException;
use Carbon\Carbon;
use Exception;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Cocktail\Entities\EventTag;
use Modules\Cocktail\Entities\EventUser;
use Modules\Cocktail\Http\Requests\EventQuickJoinRequest;
use Modules\Cocktail\Entities\EventUserInvites;
use Modules\Cocktail\Http\Requests\V2\InviteUserRequest;
use Modules\Cocktail\Services\V2Services\DataV2Service;
use Modules\Cocktail\Services\V2Services\ValidationV2Service;
use Modules\Events\Entities\Event;
use Modules\SuperAdmin\Entities\UserTag;
use Validator;
use Modules\Cocktail\Entities\EventSpaceUser;
use Modules\Cocktail\Exceptions\CustomAuthorizationException;
use Modules\Cocktail\Exceptions\CustomValidationException;
use Modules\Cocktail\Exceptions\NotExistsException;
use Modules\Cocktail\Http\Requests\V1\ConversationJoinRequest;
use Modules\Cocktail\Http\Requests\V1\ConversationLeaveRequest;
use Modules\Cocktail\Http\Requests\V1\EventSpaceAddUserRequest;
use Modules\Cocktail\Rules\EventAndSpaceOpenOrNotStarted;
use Modules\Cocktail\Services\AuthorizationService;
use Modules\Cocktail\Services\Contracts\EmailFactory;
use Modules\Cocktail\Services\EventSpaceService;
use Modules\Cocktail\Services\KctEventService;
use Modules\Cocktail\Services\KctService;
use Modules\Cocktail\Services\V2Services\KctCoreService;
use Modules\Cocktail\Transformers\InvitedUserResourceCollection;
use Modules\Cocktail\Transformers\UserSide\ConversationResource;
use Modules\Cocktail\Transformers\V2\UserSide\BadgeV2USResource;
use Modules\Cocktail\Transformers\V2\UserSide\ChimeV2USResource;
use Modules\Cocktail\Transformers\V2\UserSide\EventListV2Resource;
use Modules\Cocktail\Transformers\V2\UserSide\EventV2PublicResource;
use Modules\Cocktail\Transformers\V2\UserSide\EventV2USResource;
use Modules\Cocktail\Transformers\V2\UserSide\EventWithCurrentSpaceResource;
use Modules\Cocktail\Transformers\V2\UserSide\SpaceConvV2USResource;
use Modules\Cocktail\Transformers\V2\UserSide\SpaceV2USCollection;

class KctController extends Controller {
    
    private $emailFactory;
    
    public function __construct(EmailFactory $emailFactory) {
        $this->emailFactory = $emailFactory;
    }
    
    public function initData(Request $request) {
        try {
            $previousVersionData = KctService::getInstance()->getInitData($request);
            $newData = KctCoreService::getInstance()->getInitData($previousVersionData);
            return $newData;
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }
    
    public function getEventData(Request $request, $eventUuid) {
        try {
            DB::connection('tenant')->beginTransaction();
            $event = KctCoreService::getInstance()->getEventDataForUser($eventUuid);
            
            if (!$event) {
                throw new CustomValidationException('exists', 'event');
            }
            
            if (!ValidationV2Service::getInstance()->validateUserPassedJoinEvent($eventUuid)) {
                return response()->json([
                    'status'       => false,
                    'msg'          => 'Complete Join Event Process',
                    'redirect_url' => KctCoreService::getInstance()->getRedirectUrl($request, 'quick_user_info', ['EVENT_UUID' => $event->event_uuid])
                ], 403);
            }
            
            // mark user as present on proper condition
            KctEventService::getInstance()->markUserPresent($eventUuid);
            $userBadge = KctService::getInstance()->getUserBadge(Auth::user()->id, $event->event_uuid);
            
            $meta = KctCoreService::getInstance()->metaForEventVersion($event);
            $meta['time_zone'] = Carbon::now()->timezone->getName();
            $meta['event_status'] = KctEventService::getInstance()->getEventAndSpaceStatus($event);
            $meta['auth'] = $userBadge ? (new BadgeV2USResource($userBadge)) : null;
            $embeddedUrl = KctCoreService::getInstance()->getEmbeddedUrl($event);
            
            if(!$event->image) {
                $path = config('cocktail.s3.v2_event_default_img');
                $event->image = KctService::getInstance()->getCore()->getS3Parameter($path);
            }
            
            DB::connection('tenant')->commit();
            return (new EventV2USResource($event))
                ->additional([
                    'status' => true,
                    'data' => $embeddedUrl ? $embeddedUrl : [],
                    'meta'   => $meta,
                ]);
            
        } catch (NotExistsException $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 422);
        } catch (CustomValidationException $e) {
            DB::connection('tenant')->rollback();
            return $e->render();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }
    
    public function getSpacesAndConversation($eventUuid) {
        try {
            if (!AuthorizationService::getInstance()->isUserEventMember($eventUuid)) {
                throw new CustomAuthorizationException('not_belongs', 'event');
            }
            DB::connection('tenant')->beginTransaction();
            // to check if current chime meeting still exists
//            EventSpaceService::getInstance()->verifyCurrentConversation($eventUuid);
            $event = EventSpaceService::getInstance()->getEventWithSpaceAndConversations($eventUuid);
            $event = KctCoreService::getInstance()->loadUsersTagForSpaceResponse($event);
            if (!$event) {
                throw new CustomValidationException('exists', 'event');
            }
            $event = KctCoreService::getInstance()->getDummyUsers($event);
            $event = KctCoreService::getInstance()->sortConversations($event);
            $result = (new SpaceV2USCollection($event));
            DB::connection('tenant')->commit();
        } catch (CustomAuthorizationException $e) {
            DB::connection('tenant')->rollback();
            $result = $e->render();
        } catch (CustomValidationException $e) {
            DB::connection('tenant')->rollback();
            $result = $e->render();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            $result = response()->json(['status' => false, 'message' => $e->getMessage(), 'msg' => $e->getTrace()], 500);
        }
        
        return $result;
    }
    
    public function conversationJoin(ConversationJoinRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            if (!AuthorizationService::getInstance()
                ->isUserStateAvailable(Auth::user()->id, null, $request->space_uuid)) {
                return response()->json(['status' => false, 'msg' => __('cocktail::message.not_allowed_in_dnd')], 422);
            }
            // if request has dummy user id then replace the user id with dummy so further it can used
            $request = KctCoreService::getInstance()->modifyConvReqForDummy($request);
            
            $result = EventSpaceService::getInstance()
                ->joinWithConversation(
                    $request->user_id,
                    $request->space_uuid,
                    $request->input('dummy_user_id')
                );
            $conversation = KctCoreService::getInstance()->mapDummyUsersToConv($result);
            $allTags = EventTag::all();
            $allPPTags = UserTag::where('status', 1)->get();
            $conversation = KctCoreService::getInstance()->loadTagForConversationModel($conversation, $allTags, $allPPTags);
            DB::connection('tenant')->commit();
            return (new ChimeV2USResource($conversation))->additional(['status' => true]);
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
            $conversation = EventSpaceService::getInstance()->removeUserFromConversation($request->conversation_uuid);
            if ($conversation !== true) {
                // conversation not deleted, there are still more than 1 user left in conversation
                $conversation = KctCoreService::getInstance()->mapDummyUsersToConv($conversation);
                // validate if only dummy users left in conversation then destroy the conversation
                KctCoreService::getInstance()->validateRealUsersInConversation($conversation, $request);
            }
            DB::connection('tenant')->commit();
            if($conversation === true) {
                return response()->json(['status' => true, 'data' => true], 200);
            }
            $allTags = EventTag::all();
            $allPPTags = UserTag::where('status', 1)->get();
            $conversation = KctCoreService::getInstance()->loadTagForConversationModel($conversation, $allTags, $allPPTags);
            return (new ConversationResource($conversation))->additional(['status' => true]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }
    }
    
    public function getCurrentConversation($eventUuid) {
        $event = EventSpaceService::getInstance()->getCurrentConversation($eventUuid);
        if ($event->currentSpace && $event->currentSpace->currentConversation) {
            $allTags = EventTag::all();
            $allPPTags = UserTag::where('status', 1)->get();
            $conversation = $event->currentSpace->currentConversation;
            $conversation = KctCoreService::getInstance()->loadTagForConversationModel($conversation, $allTags, $allPPTags);
            return (new ChimeV2USResource($conversation))->additional(['status' => true]);
        }
        return response()->json(['status' => true, 'data' => null], 200);
    }
    
    public function spaceJoin(EventSpaceAddUserRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            EventSpaceService::getInstance()->addUserToSpace(Auth::user()->id, $request->space_uuid, $request->event_uuid, EventSpaceUser::$ROLE_MEMBER);
            $space = EventSpaceService::getInstance()->getSpace($request->space_uuid);
            $space->load('event');
            // to check if event is dummy then add users data
            if (isset($space->event->event_fields["is_dummy_event"]) && $space->event->event_fields["is_dummy_event"]) {
                $space = KctCoreService::getInstance()->getDummyUsersForSpace($space);
            }
            DB::connection('tenant')->commit();
            return (new SpaceConvV2USResource($space))->additional(['status' => true]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => 'Internal Server Error ', 'error' => $e->getMessage()], 500);
        }
    }
    
    public function getUserEvents(Request $request) {
        try {
            $events = KctEventService::getInstance()->getEventsList($request);
            return EventListV2Resource::collection($events)->additional(['status' => true]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }
    
    public function getCustomGraphics() {
        $setting = KctCoreService::getInstance()->getCustomGraphicsSetting();
        return response()->json([
            'status' => true,
            'data'   => KctCoreService::getInstance()->prepareCustomizationResource($setting),
        ]);
    }
    
    public function sendInvitationEmail(InviteUserRequest $request) {
        try {
            $data = DataV2Service::getInstance()->prepareInviteUsers($request);
            $emails = KctCoreService::getInstance()->separateExistingEmailForInvite($data);
            $event = Event::where('event_uuid', $request->input('event_uuid'))->first();
            foreach ($emails['existingUsers'] as $k=>$user) {
                $this->emailFactory->sendInviteToExistingUser($event, $user);
                // as in current index there is user object added so to insert the data user object will be replace by data to insert
                $emails['existingUsers'][$k] = DataV2Service::getInstance()->prepareUserForInvite($user, 1, $request->input('event_uuid'));
            }
            foreach ($emails['newUsers'] as $user) {
                $inviteUser = new User();
                $inviteUser->fname = $user['first_name'];
                $inviteUser->lname = $user['last_name'];
                $inviteUser->email = $user['email'];
                $this->emailFactory->sendInvitationEmail($event, $inviteUser);
            }
            $dataToInsert = array_merge($emails['existingUsers'], $emails['newUsers']);
            EventUserInvites::insert($dataToInsert);
            $emails = array_pluck($dataToInsert, 'email');
            $data = KctCoreService::getInstance()->getUserEventInvites($request->input('event_uuid'));
            return InvitedUserResourceCollection::collection($data);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ], 500);
        }
    }
    
    
    public function getDataForFirstLogin(Request $request) {
        $validator = Validator::make($request->all(), [
            'event_uuid' => ['required', new EventAndSpaceOpenOrNotStarted],
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msg'    => implode(',', $validator->errors()->all())
            ], 422);
        }
        
        try {
            
            $eventWithSpace = KctCoreService::getInstance()
                ->getEventForQss($request->input('event_uuid'));
            
            $invites = KctCoreService::getInstance()->getUserEventInvites($request->input('event_uuid'));
            $data = [
                'user_badge'     => new BadgeV2USResource(KctService::getInstance()->getUserBadge(Auth::user()->id)),
                'event_resource' => (new EventWithCurrentSpaceResource($eventWithSpace)),
                'invites'        => InvitedUserResourceCollection::collection($invites),
            ];
            return response()->json([
                'status' => true,
                'data'   => $data,
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }
    
    public function joinEvent(EventQuickJoinRequest $request) {
        try {
            $userEventData = EventUser::where('event_uuid', $request->event_uuid)
                ->where('user_id', Auth::user()->id)
                ->first();
            if (!$userEventData) {
                KctEventService::getInstance()->addCurrentUserToEvent($request);
                $userEventData = EventUser::where('event_uuid', $request->event_uuid)
                    ->where('user_id', Auth::user()->id)
                    ->first();
            }
            $updated = $userEventData->update(['is_joined_after_reg' => 1]);
            if (!$updated) {
                throw new Exception();
            }
            return response()->json(['status' => true, 'data' => true], 200);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }
    
    public function getEventBeforeRegister($eventUuid) {
        $event = KctService::getInstance()->getEventBeforeRegistration($eventUuid);
        try {
            if (!$event) {
                throw new NotExistsException('Event Not exists');
            }
            return (new EventV2PublicResource($event))->additional(['status' => true]);
        } catch (NotExistsException $e) {
            return response()->json(['status' => false, 'msg' => 'Event Does not exists'], 422);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => 'Internal Server Error'], 500);
        }
    }
}
