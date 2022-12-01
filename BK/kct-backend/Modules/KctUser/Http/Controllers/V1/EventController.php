<?php

namespace Modules\KctUser\Http\Controllers\V1;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\KctAdmin\Transformers\V1\LabelResource;
use Modules\KctUser\Exceptions\CustomValidationException;
use Modules\KctUser\Http\Requests\V1\EventJoinRequest;
use Modules\KctUser\Http\Requests\V1\EventQuickJoinRequest;
use Modules\KctUser\Http\Requests\V1\StoreBanUserRequest;
use Modules\KctUser\Rules\UpdatePanelRule;
use Modules\KctUser\Traits\KctHelper;
use Modules\KctUser\Traits\Repo;
use Modules\KctUser\Traits\Services;
use Modules\KctUser\Transformers\V1\BadgeUSResource;
use Modules\KctUser\Transformers\V1\EventGroupSettingResource;
use Modules\KctUser\Transformers\V1\EventListV2Resource;
use Modules\KctUser\Transformers\V1\EventUserUSResource;
use Modules\KctUser\Transformers\V1\EventUSResource;
use Modules\KctUser\Transformers\V1\EventV2PublicResource;
use Modules\KctUser\Transformers\V1\GroupMinResource;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class handles all event related logics and functionalities of HE(attendee) side.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class EventController
 * @package Modules\KctUser\Http\Controllers\V1;
 */
class EventController extends BaseController {
    use KctHelper;
    use Repo;
    use ServicesAndRepo;
    use \Modules\UserManagement\Traits\ServicesAndRepo;
    use Services;

    /**
     * @OA\Get(
     *  path="/api/v1/p/events",
     *  operationId="getEventsList",
     *  tags={"USAPI1- Event API"},
     *  summary="To get the events list for future and past",
     *  description="Event list of the HE side",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="item_per_page",in="query",description="Number of events in each page",required=false),
     *  @OA\Parameter(name="order_by",in="query",description="Which column we want to sort. Example: fname, lname",required=false),
     *  @OA\Parameter(name="order",in="query",description="In which order we want to arrenge. Example: acending, decending",required=false),
     *  @OA\Parameter(name="key",in="query",description="Search in event list by key",required=false),
     *  @OA\Parameter(name="tense",in="query",description="Event types. Example: future, past",required=false),
     *  @OA\Parameter(name="group_key",in="query",description="Group's unique key. Example:any001",required=false),
     *  @OA\Response(
     *     response=200,description="",
     *     @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",),
     *          @OA\Property(property="data",type="object",
     *     description="",ref="#/components/schemas/EventListV2Resource",),
     *      ),
     *  ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To fetch the event list according to request parameters provided.
     * @note:- The event list varies as per the values of below request fields
     * 1. item_per_page = whether the list should be server side paginated or not
     * 2. order_by      = by which column the list should be sorted
     * 3. order         = ascending or descending
     * 4. key           = searching value
     * 5. tense         = future or past
     * 6. group_key     = Unique key of the group from which we need fetch the event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function getEventsList(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'item_per_page' => 'nullable|integer',
                'order_by'      => 'nullable|string',
                'order'         => 'nullable|in:desc,asc',
                'key'           => 'nullable|string',
                'tense'         => 'nullable|string',
            ]);
            if ($validate->fails()) {
                return $this->send422(implode(',', $validate->errors()->all()));
            }
            // handling order by column
            $orderBy = $this->services->eventService->getEventsListOrderBy($request);
            // // handling order(asc/desc) for event list
            $order = $this->services->eventService->getEventsListOrder($request);
            $op = $request->has('tense') && $request->input('tense') == 'past' ? '<' : '>';
            $group = $this->services->adminService->getGroupIdByGroupKey($request->group_key);
            $superAdmins = $this->adminServices()->superAdminService->getAllSuperAdmins();
            $superAdminsEmail = $superAdmins->pluck('email')->toArray();
            $isSuperPilots = $this->adminServices()->groupService->isSuperPilotOrOwner();
            // Prepare group id according to user role.
            // 1.If request have group_key then return the id of that specific group only
            // 2.If user is super user(default group's pilot,co-pilot and owner) or super-admin return all groups ids
            // 3.ElseIf return ids of the group in which user is member of the group
            if (in_array(Auth::user()->email, $superAdminsEmail) || $isSuperPilots) {
                $userGroupIds = $this->services->adminService->getAllGroup()->pluck('id')->toArray();
            } else {
                $userGroupIds = $this->services->adminService->getCurrentUserGroupIds()->toArray();
            }
            $grpIds = $group ? [$group->id] : $userGroupIds;
            $meta['groups'] = GroupMinResource::collection($this->services->adminService->getGroupByIds($userGroupIds));
            $builder = $this->repo->eventRepository->getEventListBuilder($op, $grpIds);

            if ($request->has('key')) {
                $builder = $this->services->eventService->addSearchParamToEventListBuilder(
                    $builder,
                    $request->input('key')
                );
            }
            if ($request->has('key') && !$request->has('order_by')) {
                $builder = $this->services->eventService->addSearchDefaultOrderToEventList(
                    $builder,
                    $request->input('key')
                );
            } else {
                $builder = $builder->orderBy($orderBy, $order);
                if ($orderBy != 'start_time') {
                    $builder = $builder->orderBy('start_time', $order);
                }
            }
            // if request has item_per_page field means event list needs to be server side paginated
            if ($request->has('item_per_page')) {
                $events = $builder->paginate($request->input('item_per_page'));
            } else {
                $events = $builder->get();
            }
            // load the events with moments, draft and event user relation
            $events->load('moments', 'draft', 'eventUserRelation', 'isAdmin');
            return EventListV2Resource::collection($events)->additional([
                'status' => true,
                'meta'   => $meta,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'msg'    => $e->getMessage(),
                'trace'  => $e->getTrace()
            ], 500);
        }
    }

    /**
     * @OA\Post (
     *  path="/api/v1/p/events/join",
     *  operationId="eventJoin",
     *  tags={"USAPI1- Event API"},
     *  summary="To Join the event",
     *  description="To join the event",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(
     *      required=true,
     *      description="Request body",
     *      @OA\JsonContent(ref="#/components/schemas/EventJoinRequest")
     *  ),
     *  @OA\Response(
     *     response=200,
     *     description="Event join successfully",
     *     @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly", example=true),
     *          @OA\Property(property="data",type="boolean",
     *     description="To indicate server processed request properly", example=true),
     *      ),
     *  ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method allow user to join the event and validates the user while switching between the spaces
     * as per there different roles in the event.
     * @note Maximum number of participants in space is 1000.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param EventJoinRequest $request
     * @return JsonResponse
     */
    public function eventJoin(EventJoinRequest $request): JsonResponse {
        try {
            DB::connection('tenant')->beginTransaction();
            if ($request->has('space_uuid')) { // request has space_uuid means user trying to switch space
                $this->services->validationService->isSpaceHaveSeat(
                    $request->input('event_uuid'),
                    $request->input('space_uuid'),
                    true,
                    true
                );
                $space = $this->services->adminService->findSpaceByUuid($request->input('space_uuid'));
                // Validate the new user for space change
                $validated = $this->services->authorizationService->validateNewUserForSpaceChange($space);
                if ($validated) {
                    $event = $space->event;
                    $userCurrentSpace = $event->currentSpace->space_uuid;
                    // removing user from currently joined space
                    $this->userRepo()->spaceUserRepository->deleteSpaceUser(
                        Auth::user()->id,
                        [$userCurrentSpace]
                    );
                }
            }
            $event = $this->repo->eventRepository->findByEventUuid($request->input('event_uuid'));
            // adding user to the event
            $eventUser = $this->services->adminService->addUserToEvent(
                $request->input('event_uuid'),
                Auth::user()->id,
                $request->input('space_uuid')
            );
            $eventUser->load(['isHost' => function ($q) use ($event) {
                $q->whereIn('space_uuid', $event->spaces->pluck('space_uuid'));
            }]);
            DB::connection('tenant')->commit();
            return response()->json([
                'status' => true,
                'data'   => true,
            ]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json([
                'status' => false,
                'msg'    => $e->getMessage(),
                'error'  => $e->getTrace()
            ], 500);
        }
    }


    /**
     * @OA\Get(
     *  path="/api/v1/event/kct-customization/{eventUuid}",
     *  operationId="US-getEventsData",
     *  tags={"USAPI1- Event API"},
     *  summary="To get the event and the customization data for the event",
     *  description="",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="eventUuid",in="path",description="Event Uuid",required=true),
     *  @OA\Response(
     *     response=200,description="",
     *     @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",description="To indicate server processed request properly",),
     *          @OA\Property(property="data",type="object",description="",ref="#/components/schemas/EventUSResource"),
     *      ),
     *  ),
     *  @OA\Response(response=403,description="User Is Unauthorized",@OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",@OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",@OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the events data
     * This method provide the user badge, event and space status
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @param $eventUuid
     * @return JsonResponse|EventUSResource
     */
    public function getEventsData(Request $request, $eventUuid) {
        try {
            DB::connection('tenant')->beginTransaction();
            $accessCodeValid = false;
            $event = $this->repo->eventRepository->findByEventUuid($eventUuid);
            if (!$event) {
                throw new CustomValidationException('exists', 'event');
            }
            $eventUser = $this->repo->eventRepository->findParticipant($eventUuid, Auth::user()->id);

            // Event actual time for rehearsal mode
            $startTime = Carbon::createFromFormat('Y-m-d H:i:s', $event->start_time);
            $endTime = Carbon::createFromFormat('Y-m-d H:i:s', $event->end_time);
            $meta['event_actual_start_time'] = $startTime->toTimeString();
            $meta['event_actual_end_time'] = $endTime->toTimeString();
            $meta['event_actual_date'] = $startTime->toDateString();
            $meta['event_actual_end_date'] = $endTime->toDateString();
            if ($this->services->kctService->eventCheckAccessCode($event, $request->input('access_code'))) {

                $event->start_time = Carbon::now()->sub('hour', 1)->setSeconds(0);
                $event->end_time = Carbon::now()->endOfDay()->setSeconds(0);
                $event->moments = $event->moments->map(function ($moment) {
                    $moment->start_time = Carbon::now()->sub('hour', 1)->setSeconds(0);
                    $moment->end_time = Carbon::now()->endOfDay()->setSeconds(0);
                    return $moment;
                });
                $meta['is_rehearsal_mode'] = 1;
                if (!$eventUser || !$event->currentSpace) {
                    $this->repo->eventRepository->addUserToEvent($eventUuid, Auth::user()->id);
                }
                $eventUser = $this->repo->eventRepository->findParticipant($eventUuid, Auth::user()->id);
                $accessCodeValid = true;
            } else {
                if (!$eventUser || !$event->currentSpace) {
                    $this->services->adminService->addUserToEvent($event->event_uuid, Auth::user()->id);
                    $eventUser = $this->repo->eventRepository->findParticipant($eventUuid, Auth::user()->id);
                } else if ($eventUser->is_joined_after_reg == 0) {
                    $eventUser->is_joined_after_reg = 1;
                    $eventUser->update();
                }
            }
            $event->load('hosts');
            // mark user as present on proper condition
            $eventUser->presence = 1;
            $eventUser->update();

            $userBadge = $this->services->userService->getUserBadge(Auth::user()->id, $event->event_uuid);
            $fqdn = $this->umServices()->tenantService->getFqdn();

            $meta['time_zone'] = Carbon::now()->timezone->getName();
            $meta['event_status'] = $this->services->eventService->getEventAndSpaceStatus($event);
            $meta['auth'] = $userBadge ? (new BadgeUSResource($userBadge)) : null;
            $meta['current_scenery_data'] = $this->adminServices()->dataFactory->fetchEventSceneryData($eventUuid, true);
            $meta['join_link'] = $this->services->kctService->eventCheckAccessCode($event, $request->input('access_code'))
                ? "https://" . $fqdn . "/e/dashboard/" . $event->event_uuid . "?access_code=" . $event->event_settings['manual_access_code']
                : "https://" . $fqdn . "/j/" . $event->join_code;
            $meta['short_join_link'] = "https://" . $fqdn . "/j/" . $event->join_code;

            $event->load('moments');

            $key = $this->getGraphicKeys();
            $settings = $this->services->kctService->getDataByEvent($event, $key);
            $meta['groupSettings'] = new EventGroupSettingResource($settings);

            $embeddedData = $this->services->kctService->getEmbeddedUrl($event);
            if ($embeddedData) {
                if ($moment = $this->services->kctService->getEventCurrentMoment($event)) {
                    $embeddedData = [
                        'embedded_url'    => $embeddedData,
                        'conf_user_name'  => "",
                        'conf_meeting_id' => $moment ? $moment->moment_id : null,
                        'conf_user_email' => env("ZM_DEFAULT_APP_EMAIL"),
                        'conf_api_key'    => env('ZM_DEFAULT_APP_KEY'),
                    ];
                }
            }
            $meta['access_code_valid'] = $accessCodeValid ? 1 : 0;

            // get all the virtual background images
            $data = $this->repo->convRepository->getSystemVirtualBGImages();
            $images = [];
            foreach ($data as $d => $value){
                $images[$d]['id'] = $value['id'];
                $images[$d]['image_url'] = $this->userServices()->fileService->getFileUrl($value['image_url']);
            }
            $event['system_backgrounds'] = $images;

            // Get the current virtual background id
            $userData = $this->repo->userRepository->getUserById(Auth::id());
            $event['current_system_background_id'] = $userData->setting['bg_image'] ?? null;

            DB::connection('tenant')->commit();
            return (new EventUSResource($event))
                ->additional([
                    'status' => true,
                    'data'   => $embeddedData ? [
                        'embedded_url' => $embeddedData
                    ] : [],
                    'meta'   => $meta,
                ]);

        } catch (CustomValidationException $e) {
            DB::connection('tenant')->rollback();
            return $e->render();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }
    }

    /**
     * @OA\Get(
     *  path="/api/v1/event/embedded-url/{eventUuid}",
     *  operationId="US-getEventsData",
     *  tags={"USAPI1- Event API"},
     *  summary="To get embedded URL for the event conference live",
     *  description="",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="eventUuid",in="path",description="Event Uuid",required=true),
     *  @OA\Response(
     *      response=200,
     *      description="",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",description="To indicate server processed request properly",example=true),
     *          @OA\Property(property="data",type="sting",description="Embedded URL Of current conference for event"),
     *      ),
     *  ),
     *  @OA\Response(response=403,description="User Is Unauthorized",@OA\JsonContent(ref="#/components/schemas/Doc403Resource"),),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",@OA\JsonContent(ref="#/components/schemas/Doc500Resource"),),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get embedded URL for the event conference live
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @return JsonResponse
     */
    public function getEventEmbeddedUrl($eventUuid): JsonResponse {
        try {
            $event = $this->services->validationService->resolveEvent($eventUuid);
            return response()->json(['status' => true, 'data' => $this->services->kctService->getEmbeddedUrl($event)], 200);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'msg' => $e->getTrace()], 500);
        }
    }

    /**
     * @OA\Post(
     *  path="/api/v1/p/quick-join-event",
     *  operationId="US-joinEvent",
     *  tags={"USAPI1- Event API"},
     *  summary="To allow user to join into the event.",
     *  description="To allow user to join event,mark that user has registered for the event",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(
     *      required=true,
     *      description="Request body",
     *      @OA\JsonContent(ref="#/components/schemas/EventQuickJoinRequest")
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example=true),
     *          @OA\Property(property="data",type="boolean",
     *     description="To indicate if user added to event", example=true),
     *      ),
     *  ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To allow user to join event,mark that user has registered for the event and send successful
     * registration email to user.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param EventQuickJoinRequest $request
     * @return JsonResponse
     */
    public function joinEvent(EventQuickJoinRequest $request): JsonResponse {
        try {
            $event = $this->repo->eventRepository->findByEventUuid($request->event_uuid);
            $userEventData = $this->repo->eventRepository->findParticipant(
                $request->input('event_uuid'),
                Auth::user()->id
            );
            if (!$userEventData) { // checking if user hadn't joined the event
                // adding the user to the event
                $userEventData = $this->repo->eventRepository->addUserToEvent(
                    $request->input('event_uuid'),
                    Auth::user()->id
                );
            }
            $userEventData->is_joined_after_reg = 1; // marking user as registered into the event
            $updated = $userEventData->update();
            if (!$updated) {
                throw new Exception();
            }
            $this->services->emailService->sendEventRegSuccess($event);
            return response()->json(['status' => true, 'data' => true], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'msg'    => $e->getMessage(),
                'error'  => $e->getTrace()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *  path="/api/v1/p/event/user/ban",
     *  operationId="eventUserBan",
     *  tags={"USAPI1- Miscellaneous"},
     *  summary="To ban user from an event",
     *  description="To ban the user from an event",
     *  @OA\RequestBody(required=true,
     *     @OA\MediaType(mediaType="multipart/form-data",@OA\Schema(ref="#/components/schemas/StoreBanUserRequest"))
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="Success message",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example=true),
     *          @OA\Property(property="msg",type="string",
     *     description="To indicate if user banned", example="User has been banned from the event"),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description To store the data for banning an user from event and send an email containing information of banned
     * user to the space host(authorized user to ban anyone in the space).
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param StoreBanUserRequest $request
     * @return JsonResponse
     */
    public function storeUserBan(StoreBanUserRequest $request): JsonResponse {
        try {
            DB::connection('tenant')->beginTransaction();
            $event = $this->repo->eventRepository->findByEventUuid($request->input('event_uuid'));
            $alreadyBanned = $this->repo->banUserRepository->getBanUserByIdAndBanableId(
                $request->input('user_id'),
                $request->input('event_uuid')
            );
            // If the user is already banned then send the msg
            if ($alreadyBanned) {
                return response()->json([
                    'status' => false,
                    'msg'    => __('kctuser::message.already_banned_user'),
                ], 422);
            } else {
                // else prepare the ban user details
                $data = $this->services->dataService->prepareBanUserDetails(
                    $request->input('user_id'),
                    $request->severity,
                    $request->ban_reason
                );
                $store = $event->banUser()->create($data);
                if ($store) {
                    // send the user detials to the event
                    $banUser = $this->services->userManagementService->findById($request->input('user_id'));
                    $this->services->emailService->sendBanUserFromEvent($event, $banUser, $request);
                    DB::connection('tenant')->commit();
                    return response()->json([
                        'status' => true,
                        'msg'    => __('kctuser::message.user_event_ban'),
                    ], 200);
                } else {
                    return response()->json([
                        'status' => false,
                        'msg'    => 'Internal Server Error',
                    ], 500);
                }
            }
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json([
                'status' => false,
                'msg'    => 'Internal Server Error',
                'error'  => $e->getMessage(),
                'trace'  => $e->getTrace()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *  path="/api/v1/p/event/{eventUuid}",
     *  operationId="US-getEventBeforeRegister",
     *  tags={"USAPI1- Event API"},
     *  summary="To get event before the register",
     *  description="Get event before the register and load all moments related to the event",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="eventUuid",in="path",description="Event Uuid",required=true),
     *  @OA\Response(
     *      response=200,
     *      description="Event fetched successfully",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example=true),
     *          @OA\Property(property="data",type="oj=bject",
     *     description="To indicate event is get before the register",ref="#/components/schemas/EventV2PublicResource" ),
     *      ),
     *  ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get event before the registration and prepare the moments data of the event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @return JsonResponse|EventV2PublicResource
     */
    public function getEventBeforeRegister($eventUuid) {
        //Fetch the event details
        $event = $this->repo->eventRepository->findByEventUuid($eventUuid);
        $event->load('moments');
        try {
            if (!$event) {
                return $this->send422(__('validation.exists', ['attribute' => 'event']));
            }
            $event->load('moments');
            return (new EventV2PublicResource($event))->additional(['status' => true]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => 'Internal Server Error'], 500);
        }
    }

    /**
     * @OA\Post (
     *  path="/api/v1/p/panel/current/section",
     *  operationId="updatePanelCurrentSection",
     *  tags={"USAPI1- Event API"},
     *  summary="To update the pilot panel section",
     *  description="To update the pilot panel section",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="event_uuid",in="query",description="event uuid of the event",required=true),
     *  @OA\Parameter(name="type",in="query",
     *     description="Type of the content. example 1 for image type, 2 for video type 3 for zoom type",required=true),
     *  @OA\Parameter(name="uuid",in="query",description="uuid of the asset",required=false),
     *  @OA\Response(
     *     response=200,description="",
     *     @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example=true),
     *          @OA\Property(property="data",type="boolean",
     *     description="To indicate server processed request properly",example=true),
     *      ),
     *  ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will update the pilot panel section according to request and return the current section
     * data.
     * @note Different types of sections are- 1. image 2. video 3. zoom
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updatePanelCurrentSection(Request $request): JsonResponse {
        try {
            DB::connection('tenant')->beginTransaction();
            $validator = Validator::make($request->all(), [
                'event_uuid' => ["required", "exists:tenant.events,event_uuid", new UpdatePanelRule],
                'type'       => ["required", "in:1,2,3"], // 1 for image type, 2 for video type 3 for zoom type
                'uuid'       => ["nullable"],
            ]);
            if ($validator->fails()) {
                return $this->send422($validator->errors());
            }
            $currentSectionData = [
                'type' => $request->input('type', 3),
                'uuid' => $request->input('uuid'),
            ];
            $event = $this->repo->eventRepository->findByEventUuid($request->event_uuid);
            $settings = $event->event_settings;
            $settings['current_section_data'] = $currentSectionData;
            //update the setting of event
            $check = $event->update(['event_settings' => $settings]);
            //if data is updated then send the current section data else send empty array
            $returnData = $check ? $currentSectionData : [];
            DB::connection('tenant')->commit();
            return response()->json(['status' => true, 'data' => $returnData], 201);
        } catch (Exception $exception) {
            DB::connection('tenant')->rollBack();
            return $this->handleIse($exception);
        }
    }

    /**
     * @OA\Get(
     *  path="/api/v1/p/graphics/event/{eventUuid}",
     *  operationId="US-getGroupSettingByEvent",
     *  tags={"USAPI1- Event API"},
     *  summary="To get the event and the customization data for the event",
     *  description="",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="eventUuid",in="path",description="Event Uuid",required=true),
     *  @OA\Response(
     *     response=200,description="",
     *     @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",description="To indicate server processed request properly",),
     *          @OA\Property(property="data",type="object",description="",ref="#/components/schemas/EventUSResource"),
     *      ),
     *  ),
     *  @OA\Response(response=403,description="User Is Unauthorized",@OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",@OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",@OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the events data
     * This method provide the user badge, event and space status
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @param $eventUuid
     * @return JsonResponse|EventUSResource
     */
    public function getGroupSettingByEvent($eventUuid) {
        $event = $this->adminRepo()->eventRepository->findByEventUuid($eventUuid);
        if (!$event) {
            return response()->json(['status' => false, 'data' => false], 422);
        }
        $graphics_data = $this->services->kctService->prepareGraphicsData($event->group->id);
        if (isset($graphics_data['group_has_own_customization']) && $graphics_data['group_has_own_customization'] == 0) {
            $group = $this->services->adminService->getDefaultGroup();
            $graphics_data = $this->services->kctService->prepareGraphicsData($group->id);
        }
        $labels = $this->services->adminService->getLabels($event->group->id);
        $labels = ['labels' => LabelResource::collection($labels)];
        return response()->json([
            'status' => true,
            'data'   => array_merge($graphics_data, $labels),
        ]);
    }
}
