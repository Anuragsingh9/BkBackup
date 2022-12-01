<?php

namespace Modules\KctUser\Http\Controllers\V1;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\KctUser\Entities\EventSpaceUser;
use Modules\KctUser\Exceptions\CustomValidationException;
use Modules\KctUser\Http\Requests\V1\EventSpaceAddUserRequest;
use Modules\KctUser\Traits\KctHelper;
use Modules\KctUser\Traits\Services;
use Modules\KctUser\Transformers\V1\SpaceConvV2USResource;
use Modules\KctUser\Transformers\V1\SpaceUSCollection;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class contain the space related functionality
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class SpaceController
 *
 * @package Modules\KctUser\Http\Controllers\V1
 */
class SpaceController extends BaseController {
    use KctHelper;
    use Services;

    /**
     * @OA\Get(
     *  path="/api/v1/p/event/space/all/{eventUuid}",
     *  operationId="getSpacesAndConversation",
     *  tags={"USAPI1- Space Management"},
     *  summary="To get the all spaces with the conversation data",
     *  description="",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="eventUuid",in="path",description="Event Uuid",required=true),
     *  @OA\Response(
     *      response=200,
     *      description="Event space fetched",
     *      @OA\JsonContent(
     *          @OA\Property(property="data",type="object",description="",ref="#/components/schemas/SpaceUSCollection",),
     *      ),
     *  ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),
     *  ),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),
     *  ),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  ),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This api will return all spaces with all related data like dummy users,tags(organiser and PPT),
     * space hosts,conversations users and single users.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @return JsonResponse|SpaceUSCollection
     */
    public function getSpacesAndConversation($eventUuid) {
        try {
            $participant = $this->repo->eventRepository->findParticipant($eventUuid, Auth::user()->id);
            if (!$participant) {
                return $this->send403(__('kctuser::message.not_belongs_event'));
            }
            DB::connection('tenant')->beginTransaction();
            //check is user in event and space conversation
            $event = $this->services->spaceService->getEventWithSpaceAndConversations($eventUuid);
            if (!$event) {
                // if user in event then throw the exception
                throw new CustomValidationException('exists', 'event');
            }

            //Get the dummy users in event
            $event = $this->services->kctService->getDummyUsers($event);
            // Add the tags for the space response
            $event = $this->services->kctService->loadUsersTagForSpaceResponse($event);
            //Load the space host data in event
            $event = $this->services->dataMapServices->addSpaceHostData($event);
            $event = $this->services->kctService->sortConversations($event);
            $result = (new SpaceUSCollection($event));
            DB::connection('tenant')->commit();
        } catch (CustomValidationException $e) {
            DB::connection('tenant')->rollback();
            $result = $e->render();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            $result = response()->json(['status' => false, 'message' => $e->getMessage(), 'msg' => $e->getTrace()], 500);
        }
        return $result;
    }

    /**
     * @OA\Post(
     *  path="/api/v1/p/event/space/join",
     *  operationId="spaceJoin",
     *  tags={"USAPI1- Space Management"},
     *  summary="To Join The Space",
     *  description="",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(required=true,@OA\JsonContent(ref="#/components/schemas/EventSpaceAddUserRequest")),
     *  @OA\Response(
     *      response=200,
     *      description="Space joined",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",description="To indicate server processed request properly",),
     *          @OA\Property(property="data",type="object",description="",ref="#/components/schemas/SpaceConvV2USResource",),
     *      ),
     *  ),
     *  @OA\Response(response=403,description="User Is Unauthorized",@OA\JsonContent(ref="#/components/schemas/Doc403Resource"),),
     *  @OA\Response(response=422,description="Data is not valid",@OA\JsonContent(ref="#/components/schemas/Doc422Resource"),),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",@OA\JsonContent(ref="#/components/schemas/Doc500Resource"),),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will validate user to join a space and will add the user into the space.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param EventSpaceAddUserRequest $request
     * @return JsonResponse|SpaceConvV2USResource
     */
    public function spaceJoin(EventSpaceAddUserRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $space = $this->services->spaceService->getSpace($request->input('space_uuid'));
            //Validate the user to change the space
            $this->services->authorizationService->validateUserForSpaceChange($space);
            $this->services->spaceService->addUserToSpace(
                Auth::user()->id,
                $request->space_uuid,
                $request->event_uuid,
                EventSpaceUser::$ROLE_MEMBER
            );
            $space->load('event');
            // to check if event is dummy then add users data
            if (isset($space->event->event_settings["is_dummy_event"]) && $space->event->event_settings["is_dummy_event"]) {
                $space = $this->services->kctService->getDummyUsersForSpace($space);
            }
            $space->load("hosts");
            DB::connection('tenant')->commit();
            return (new SpaceConvV2USResource($space))->additional(['status' => true]);
        } catch (CustomValidationException $e) {
            return $e->render();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => 'Internal Server Error ', 'error' => $e->getMessage()], 500);
        }
    }
}
