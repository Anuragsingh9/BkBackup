<?php

namespace Modules\KctUser\Http\Controllers\V1;

use Aws\Chime\Exception\ChimeException;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Modules\KctUser\Entities\OrganiserTag;
use Modules\KctUser\Transformers\V1\ChimeUSResource;
use Modules\KctUser\Transformers\V1\ConversationUSResource;
use Modules\KctUser\Transformers\V1\ConversationResource;
use Modules\KctUser\Exceptions\CustomValidationException;
use Modules\KctUser\Http\Requests\V1\KickDummyUserRequest;
use Modules\KctUser\Http\Requests\V1\ConversationJoinRequest;
use Modules\KctUser\Http\Requests\V1\ConversationLeaveRequest;
use Modules\KctUser\Http\Requests\V1\ChangeConversationTypeRequest;
use Modules\KctUser\Transformers\V1\NodeSpaceWithDummyResource;
use Modules\SuperAdmin\Entities\UserTag;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class contain the conversation related functionality
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class ConversationController
 *
 * @package Modules\KctUser\Http\Controllers\V1
 */
class ConversationController extends BaseController {

    /**
     * @OA\Post(
     *  path="api/v1/event/space/conversation/join",
     *  operationId="us-conversationJoin",
     *  tags={"USAPI1- Conversation"},
     *  summary="To join a conversation",
     *  description="",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(ref="#/components/schemas/ConversationJoinRequest"),
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="",
     *      @OA\JsonContent(
     *          @OA\Property(property="data",type="object",description="",ref="#/components/schemas/ChimeUSResource",),
     *      ),
     *  ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will validate(can join or not) user when user tries to start or join conversation and
     * accordingly will create conversation or add user in existing conversation. It also fetches all the user tags data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param ConversationJoinRequest $request
     * @return JsonResponse|ChimeUSResource
     */
    public function conversationJoin(ConversationJoinRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            // if request has dummy user id then replace the user id with dummy so further it can used
            $request = $this->services->kctService->modifyConvReqForDummy($request);
            // if request is coming from space host then can join conversation else throw exception message
            $this->services->validationService->canUserJoinConversation(
                $request->user_id,
                $request->space_uuid,
                $request->input('dummy_user_id')
            );

            $result = $this->services->spaceService->joinWithConversation(
                $request->user_id,
                $request->space_uuid,
                $request->input('dummy_user_id')
            );

            $result = $this->services->spaceService->markConversationHost($result);

            // add the dummy user in conversation
            $conversation = $this->services->kctService->mapDummyUsersToConv($result);
            $allTags = $this->services->adminService->getAllEventTag();
            $allPPTags = UserTag::where('status', 1)->get();
            // load tag into the conversation modal
            $conversation = $this->services->kctService->loadTagForConversationModel(
                $conversation,
                $allTags,
                $allPPTags
            );
            DB::connection('tenant')->commit();
            return (new ChimeUSResource($conversation))->additional(['status' => true]);
        } catch (CustomValidationException $e) {
            DB::connection('tenant')->rollback();
            $result = $e->render();
        } catch (ChimeException $e) {
            DB::connection('tenant')->rollback();
            $result = response()->json([
                'status' => false,
                'msg'    => __('kctuser::message.chime_meeting_expired'),
                'm'      => $e->getMessage(),
                'trace'  => $e->getTrace(),
            ], 422);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            $result = response()->json([
                'status' => false,
                'msg'    => $e->getMessage(),
                'track'  => $e->getTrace()
            ], 500);
        }
        return $result;
    }

    /**
     * @OA\Delete(
     *  path="api/v1/event/space/conversation/leave",
     *  operationId="us-conversationLeave",
     *  tags={"USAPI1- Conversation"},
     *  summary="To Leave a conversation",
     *  description="",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(ref="#/components/schemas/ConversationLeaveRequest"),
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="",
     *      @OA\JsonContent(
     *          @OA\Property(property="data",type="object",description="",ref="#/components/schemas/ChimeUSResource",),
     *      ),
     *  ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To allow user to leave conversation or automatically end conversation if single user left in the
     * conversation. It also loads all tags(Organiser and User tags) for the conversation.
     * @note If only dummy users left in the conversation then conversation will be ended automatically.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param ConversationLeaveRequest $request
     * @return JsonResponse|ConversationResource
     */
    public function conversationLeave(ConversationLeaveRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $conversation = $this->services->spaceService->removeUserFromConversation(
                $request->conversation_uuid
            );
            if ($conversation !== true) {
                // conversation not deleted, there are still more than 1 user left in conversation
                $conversation = $this->services->kctService->mapDummyUsersToConv($conversation);
                // validate if only dummy users left in conversation then destroy the conversation
                $this->services->kctService->validateRealUsersInConversation($conversation, $request);
            }
            DB::connection('tenant')->commit();
            if ($conversation === true) {
                return response()->json(['status' => true, 'data' => true], 200);
            }
            $allTags = $this->repo->userTagsRepository->allTags();
            $allPPTags = $this->services->superAdminService->getAllTags(1);
            $conversation = $this->services->kctService->loadTagForConversationModel(
                $conversation,
                $allTags,
                $allPPTags
            );
            return (new ConversationResource($conversation))->additional(['status' => true]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json([
                'status' => false,
                'msg'    => $e->getMessage(),
                'trace'  => $e->getTrace()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *  path="api/v1/event/space/conversation/{eventUuid}",
     *  operationId="us-getCurrentConversation",
     *  tags={"USAPI1- Conversation"},
     *  summary="To get the current conversation",
     *  description="",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="eventUuid",in="path",description="Event Uuid",required=true),
     *  @OA\Response(
     *      response=200,
     *      description="",
     *      @OA\JsonContent(
     *          @OA\Property(property="data",type="object",description="",ref="#/components/schemas/ChimeUSResource",),
     *      ),
     *  ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get the current conversation of auth user and load all the tags(Organiser and User Tags) for that
     * conversation.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @return JsonResponse|ChimeUSResource
     */
    public function getCurrentConversation($eventUuid) {
        $event = $this->services->spaceService->getCurrentConversation($eventUuid);
        // checking if user exist in current conversation
        if ($event->currentSpace && $event->currentSpace->currentConversation) {
            $allTags = OrganiserTag::all();
            $allPPTags = UserTag::where('status', 1)->get();
            $conversation = $event->currentSpace->currentConversation;
            $conversation = $this->services->kctService->loadTagForConversationModel(
                $conversation,
                $allTags,
                $allPPTags
            );
            return (new ChimeUSResource($conversation))->additional(['status' => true]);
        }
        return response()->json(['status' => true, 'data' => null], 200);
    }

    /**
     * @OA\Post(
     *  path="api/v1/change/conversion/type",
     *  operationId="us-changeConversationType",
     *  tags={"USAPI1- Conversation"},
     *  summary="To get the current conversation",
     *  description="",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(ref="#/components/schemas/ChangeConversationTypeRequest"),
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="",
     *      @OA\JsonContent(
     *          @OA\Property(property="data",type="object",
     *     description="",ref="#/components/schemas/ConversationUSResource"),
     *      ),
     *  ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To change the conversation type from normal to private and vice-versa.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param ChangeConversationTypeRequest $request
     * @return JsonResponse|ConversationUSResource
     */
    public function changeConversationType(ChangeConversationTypeRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $result = $this->services->kctService->changeConversationType($request);
            DB::connection('tenant')->commit();
            return new ConversationUSResource($result);
        } catch (CustomValidationException $e) {
            DB::connection('tenant')->rollback();
            return $e->render();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 500]);
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To add the user in conversation.When a user is asked to join the conversation if user click on join
     * then this api will be executed from node side to add the user in that conversation.
     * It will be same for dummy user also.
     * @note This method also check the conversation user count like
     *      without host = 4
     *      with host = 5 users in conversation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function addUserInConversation(Request $request): JsonResponse {
        $conversation = $this->repo->convRepository->getConversation($request->input('conversationUuid'));
        if ($conversation) {
            $conversationUsersCount = $this->services->spaceService
                ->getConversationUserCount($conversation);
            $hosts = $conversation->space->hosts;
            // this contains the id of host which are in conversation
            $hostsInConversation = count(array_intersect(
                $hosts->pluck('id')->toArray(),
                $conversation->users->pluck('id')->toArray()
            ));

            $maxUsers = $this->services->kctService->getEventMaxConvCount($conversation->space->event) + $hostsInConversation;
            if ($conversationUsersCount >= $maxUsers) {
                return response()->json([
                    'status' => false,
                    'msg'    => __('kctuser::message.conversation_member_limit',
                        ['attribute' => $maxUsers])
                ]);
            }
            $space = $conversation->space;
            if ($space) {
                $this->repo->eventRepository->updateDummyUser(
                    $request->input('dummyUserId'),
                    $conversation->uuid,
                    $space->event_uuid
                );
            }
        }
        return response()->json([
            'data'   => $request->all(),
            'status' => true,
        ]);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is used to check if the event has dummy Users option selected by the organiser.
     * If present, it fetches the dummy user's id and send the information to node server.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function getEventDummyUsers(Request $request) {
        $event = $this->repo->eventRepository->findByEventUuid($request->input('eventId'));
        $event->load('spaces.dummyRelations.dummyUsers');
        $data = $event->spaces;
        return NodeSpaceWithDummyResource::collection($data)->additional([
            'status' => true,
        ]);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will be used for remove the dummy user from a conversation
     * and also remove the dummy user data from the conversation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param KickDummyUserRequest $request
     * @return JsonResponse
     */
    public function removeDummyUser(KickDummyUserRequest $request): JsonResponse {
        try {
            DB::connection('tenant')->beginTransaction();

            $eventUuid = $request->input('eventUuid');
            $dummyUserId = $request->input('dummyUserId');

            // find dummy user which are in conversation

            $dummyUser = $this->services->adminService->getDummyUserDataInsideConv(
                $eventUuid,
                $dummyUserId
            );
            if (!$dummyUser->conversation) {
                return response()->json([
                    'data'   => $request->all(),
                    'status' => true,
                ]);
            }
            $conversation = $dummyUser->conversation;

            // fetching space host, users, dummy users
            $spaceHosts = $conversation->space->hosts;
            $conversationUsers = $conversation->users;
            $dummyUsers = $conversation->space->event->dummyRelations;

            // this contains the ids of hosts which are in conversation
            $hostsInConversation = count(array_intersect(
                $spaceHosts->pluck('id')->toArray(),
                $conversation->users->pluck('id')->toArray()
            ));

            if (!$hostsInConversation) {
                response()->json([
                    'status' => false,
                    'msg'    => 'There is no host found in conversation',
                ]);
            }
            if ($conversationUsers->count() == 1 && $dummyUsers->count() == 1) {
                // conversation ended automatically if the last user is a dummy user
                $this->services->spaceService->deleteConversation($conversation);
                $conversationState = 'delete';
            } else {
                $conversationState = 'remove';
            }
            $dummyUser->current_conv_uuid = null;
            $dummyUser->update();

            DB::connection('tenant')->commit();
            return response()->json([
                'status' => true,
                'data'   => [
                    'conversation' => [
                        'uuid'   => $conversation->uuid,
                        'action' => $conversationState,
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json([
                'status' => false,
                'msg'    => $e->getMessage(),
                'trace'  => $e->getTrace()
            ], 500);
        }
    }
}
