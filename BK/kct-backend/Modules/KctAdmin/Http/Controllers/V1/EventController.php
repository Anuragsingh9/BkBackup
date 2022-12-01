<?php

namespace Modules\KctAdmin\Http\Controllers\V1;

use Carbon\Carbon;
use Exception;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Models\Website;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\KctAdmin\Entities\Event;
use Modules\KctAdmin\Exceptions\CustomValidationException;
use Modules\KctAdmin\Exceptions\ZoomGrantException;
use Modules\KctAdmin\Http\Requests\EventInviteEmailRequest;
use Modules\KctAdmin\Http\Requests\EventUploadsUpdateRequest;
use Modules\KctAdmin\Http\Requests\MomentCreateRequest;
use Modules\KctAdmin\Http\Requests\UpdateDraftEventRequest;
use Modules\KctAdmin\Http\Requests\UpdateEventRequest;
use Modules\KctAdmin\Http\Requests\UserBulkRoleRemoveRequest;
use Modules\KctAdmin\Http\Requests\UserBulkRoleUpdateRequest;
use Modules\KctAdmin\Http\Requests\V1\CreateEventRequest;
use Modules\KctAdmin\Http\Requests\V1\DeleteEventRequest;
use Modules\KctAdmin\Http\Requests\V1\UpdateVirtualEventRequest;
use Modules\KctAdmin\Rules\EventRule;
use Modules\KctAdmin\Rules\GroupRule;
use Modules\KctAdmin\Traits\KctHelper;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\KctAdmin\Transformers\DraftEventResource;
use Modules\KctAdmin\Transformers\EventListResource;
use Modules\KctAdmin\Transformers\EventLiveImagesResource;
use Modules\KctAdmin\Transformers\EventLiveVideoResource;
use Modules\KctAdmin\Transformers\EventMinListResource;
use Modules\KctAdmin\Transformers\V1\EventSpaceHostResource;
use Modules\KctAdmin\Transformers\V1\EventUserResource;
use Modules\KctAdmin\Transformers\V1\HostResource;
use Modules\KctAdmin\Transformers\V1\MomentResource;
use Modules\KctAdmin\Transformers\V1\ParticipantResource;
use Modules\KctAdmin\Transformers\V1\VirtualEventResource;
use Ramsey\Uuid\Uuid;
use When\When;

/**
 * -----------------------------------------------------------------------------------------------------------------
 * @description This class will manage the event related functionality
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class EventController
 * @package Modules\KctAdmin\Http\Controllers\V1
 */
class EventController extends BaseController {
    use ServicesAndRepo;
    use KctHelper;
    use \Modules\SuperAdmin\Traits\ServicesAndRepo;
    use \Modules\UserManagement\Traits\ServicesAndRepo;

    /**
     * @OA\Post(
     *  path="/api/v1/admin/events",
     *  operationId="createVirtualEvent",
     *  tags={"Event"},
     *  summary="To create a new Virtual Event",
     *  description="To create a new Virtual Type Event",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(ref="#/components/schemas/CreateEventRequest"),
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="Virtual Event Created",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="object",
     *     description="Virtual Event Resource",ref="#/components/schemas/VirtualEventResource"),
     *      ),
     *  ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To create a virtual event.
     *
     * @note:- This method prepares and create all necessary data which are required for an event. They are-
     * 1. Draft event
     * 2. Default space
     * 3. Key moments
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param CreateEventRequest $request
     * @return VirtualEventResource|JsonResponse
     */
    public function createEvent(CreateEventRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $group = $this->repo->groupRepository->getGroupByGroupKey($request->group_key);
            // preparing the event data
            $param = $this->services->dataFactory->prepareEventCreateData($request, $group->id);

            $event = $this->repo->eventRepository->create($param);
            if (in_array($request->event_recurrence['recurrence_type'] ?? null, config('kctadmin.modelConstants.event_recurrence'))) {
                // preparing data for recurrence and making event recurring
                $recurringParam = $this->services->dataFactory->prepareRecurringEventData($request, $event);
                $this->repo->eventRepository->makeEventRecurring($recurringParam);
                $event->load('eventRecurrenceData');
            }

            if ($request->has('draft')) { // checking if event is a draft event
                $draftParam = $this->services->dataFactory->prepareDraftEventData($event, $request->draft);
                $this->repo->eventRepository->makeEventAsDraft($draftParam);
            }
            $spaceParam = $this->services->dataFactory->prepareDefaultSpace($request, $event, $group->id);
            $space = $this->repo->kctSpaceRepository->create($spaceParam);
            // adding group's first pilot as space host of the default space
            $this->repo->eventRepository->addUserToEvent($event->event_uuid, $spaceParam['hosts'], null, ['is_organiser' => 0, 'is_host' => 1]);

            $moments = $this->services->dataFactory->prepareMomentData($request);
            foreach ($moments as $moment) {
                $event->moments()->create($moment);
            }
            $event->load('moments');
            if ($event->event_settings['is_dummy_event']) {
                // preparing dummy users data and adding them in default space
                $this->services->dataFactory->prepareDummyUsers($space);
            }
            // adding auth user(event creating user) as organiser and host of the default space
            $this->repo->eventRepository->addUserToEvent($event->event_uuid, Auth::user()->id, null, ['is_organiser' => 1, 'is_host' => 0]);
            DB::connection('tenant')->commit();
            return (new VirtualEventResource($event))->additional(['status' => true]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return $this->handleIse($e);
        }
    }


    public function createNewEvent(CreateEventRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $group = $this->repo->groupRepository->getGroupByGroupKey($request->group_key);
            $param = $this->services->dataFactory->prepareEventCreateData($request, $group->id);
            $event = $this->repo->eventRepository->create($param);
            // storing event recurrence data
            if (in_array($request->event_recurrence['recurrence_type'] ?? null, config('kctadmin.modelConstants.event_recurrence'))) {
                // preparing data for recurrence and making event recurring
                $recurringParam = $this->services->dataFactory->prepareRecurringEventData($request, $event);
                $this->repo->eventRepository->makeEventRecurring($recurringParam);
                $event->load('eventRecurrenceData');
            }
            // creating default space data
            $spaceParam = $this->services->dataFactory->prepareDefaultSpace($request, $event, $group->id);
            $defaultSpace = $this->repo->kctSpaceRepository->create($spaceParam);
            if ($event->event_settings['is_dummy_event']) {
                // preparing dummy users data and adding them in default space
                $this->services->dataFactory->prepareDummyUsers($defaultSpace);
            }
            // adding group's first pilot as space host of the default space
            $this->repo->eventRepository->addUserToEvent($event->event_uuid, $spaceParam['hosts'], null, ['is_organiser' => 0, 'is_host' => 1]);
            if ($request->has('draft')) { // checking if event is a draft event
                $draftParam = $this->services->dataFactory->prepareDraftEventData($event, $request->draft);
                $this->repo->eventRepository->makeEventAsDraft($draftParam);
            }
            // storing moment data
            $moments = $this->services->dataFactory->prepareMomentData($request);
            foreach ($moments as $moment) {
                $event->moments()->create($moment);
            }
            $event->load('moments');
            // storing event scenery data
            if (isset($request->scenery_data['asset_id']) && $request->scenery_data['asset_id'] != null) {
                $sceneryParam = $this->adminServices()->dataFactory->prepareParamForEventScenery($request, $event);
                $event->update(['event_settings' => $sceneryParam]);
            }
            // adding auth user(event creating user) as organiser and host of the default space
            $this->repo->eventRepository->addUserToEvent($event->event_uuid, Auth::user()->id, null, ['is_organiser' => 1, 'is_host' => 0]);
            DB::connection('tenant')->commit();
            return (new VirtualEventResource($event))->additional(['status' => true]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollBack();
            return $this->handleIse($e);
        }
    }


    /**
     * @OA\Put(
     *  path="/api/v1/admin/events",
     *  operationId="updateVirtualEvent",
     *  tags={"Event"},
     *  summary="To update a virtual event",
     *  description="To modify a created virtual event",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(ref="#/components/schemas/UpdateVirtualEventRequest")
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="Event Updated",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="object",
     *     description="Event Resource",ref="#/components/schemas/VirtualEventResource"),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update virtual event related data like event basic details,recurring event data,draft data and
     * moments data.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param UpdateVirtualEventRequest $request
     * @return JsonResponse|VirtualEventResource
     */
    public function updateVirtualEvent(UpdateVirtualEventRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $event = $this->repo->eventRepository->findByEventUuid($request->input('event_uuid'));
            $eventData = $this->services->dataFactory->prepareEventUpdateData($request, $event, null);
            $event = $this->repo->eventRepository->updateEvent($event, $eventData['event']);
            if (in_array($request->event_recurrence['recurrence_type'] ?? null, config('kctadmin.modelConstants.event_recurrence'))) {
                $recurringParam = $this->services->dataFactory->prepareRecurringUpdateData($request, $event);
                $this->repo->eventRepository->updateEventRecurringData($recurringParam, $event->event_uuid);
                $event->refresh('eventRecurrenceData');
            }
            // if event is networking or if event is content type with auto moment creation then synchronising the
            // moment start and end time with event time
            if ($event->type == Event::$type_networking ||
                ($event->type == Event::$type_content &&
                    isset($event->event_settings['is_auto_key_moment_event']) && $event->event_settings['is_auto_key_moment_event'])) {
                $this->adminRepo()->eventRepository->updateOrCreateDraft(
                    $request->event_uuid, [
                    'reg_start_time' => $eventData['event']['start_time'],
                    'reg_end_time'   => $eventData['event']['end_time'],
                    'event_status'   => $eventData['draft']['event_status'],
                    'is_reg_open'    => $eventData['draft']['is_reg_open'],
                    'share_agenda'   => $eventData['draft']['share_agenda']
                ]);
                $event->load('moments');
                foreach ($event->moments as $moment) {
                    $moment->start_time = $event->start_time;
                    $moment->end_time = $event->end_time;
                    $moment->update();
                }
            }
            DB::connection('tenant')->commit();
            return (new VirtualEventResource($event))->additional(['status' => true,]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return $this->handleIse($e);
        }
    }

    /**
     * @OA\Delete(
     *  path="/api/v1/admin/events",
     *  operationId="deleteEvent",
     *  tags={"Event"},
     *  summary="To delete a virtual event",
     *  description="To delete a created virtual event",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(ref="#/components/schemas/DeleteEventRequest")
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="Event Updated",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * ),
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To delete an event of a given event_uuid
     *
     * @note This will only delete future events
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param DeleteEventRequest $request
     * @return JsonResponse
     */
    public function deleteEvent(DeleteEventRequest $request): JsonResponse {
        try {
            DB::connection('tenant')->beginTransaction();
            $event = $this->repo->eventRepository->findByEventUuid($request->input('event_uuid'));
            if ($this->isLiveEvent($event) && $event->draft->event_status === 1) { // checking if event is running
                return $this->send422(__("kctadmin::messages.event_must_future"), [
                    'event_uuid' => [
                        __("kctadmin::messages.event_must_future")
                    ]
                ]);
            }
            $event->delete();
            DB::connection('tenant')->commit();
            return response()->json([
                'status' => true,
                'data'   => true,
            ]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return $this->handleIse($e);
        }
    }

    /**
     * @OA\Get(
     *  path="/api/v1/admin/events/find",
     *  operationId="findEvent",
     *  tags={"Event"},
     *  summary="To fetch a virtual event",
     *  description="To fetch a created virtual event",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="event_uuid",in="query",description="Event Uuid",required=true),
     *  @OA\Response(
     *      response=200,
     *      description="Event Details",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="object",
     *     description="Virtual Event Resource",ref="#/components/schemas/VirtualEventResource"),
     *      )
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
     * @descripiton To fetch a specific event's data by event_uuid
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse|VirtualEventResource
     */
    public function find(Request $request) {
        $event = $this->repo->eventRepository->findByEventUuid($request->input('event_uuid'));
        if (!$event) {
            return $this->send422(__('validation.exists', ['attribute' => 'event']));
        }
        $event->load(['organiser', 'draft', 'eventRecurrenceData']);
        return (new VirtualEventResource($event))->additional(['status' => true]);
    }

    /**
     * @OA\Get(
     *  path="/api/v1/admin/events/links",
     *  operationId="getAccessLink",
     *  tags={"Event"},
     *  summary="To Get The Events Access Links",
     *  description="To fetch events access links",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="event_uuid",in="query",description="Event Uuid",required=true),
     *  @OA\Response(
     *      response=200,
     *      description="Event Details",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="object",description="Links",
     *              @OA\Property(property="participants_link",type="string",
     *     description="Participant Link",
     *     example="https://sub_domain.domain.com/e/quick-login/95639e06-3265-11ec-a636-149d9980596a"),
     *              @OA\Property(property="moderator_links",type="array",
     *     description="Moderator Links, In which user is currently as moderator",
     *                  @OA\Items(
     *                      @OA\Property(property="id",type="integer",description="Moment ID",example="1"),
     *                      @OA\Property(property="moment_name",type="string",
     *     description="Moment Name",example="Name Of Moment"),
     *                      @OA\Property(property="link",type="string",
     *     description="Participant Link",
     *     example="https://sub_domain.domain.com/e/quick-login/95639e06-3265-11ec-a636-149d9980596a"),
     *                  ),
     *              ),
     *              @OA\Property(property="speaker_links",type="array",
     *     description="Moderator Links, In which user is currently as moderator",
     *                  @OA\Items(
     *                      @OA\Property(property="id",type="integer",description="Moment ID",example="1"),
     *                      @OA\Property(property="moment_name",type="string",
     *     description="Moment Name",example="Name Of Moment"),
     *                      @OA\Property(property="link",type="string",
     *     description="Participant Link",
     *     example="https://sub_domain.domain.com/e/quick-login/95639e06-3265-11ec-a636-149d9980596a"),
     *                  ),
     *              ),
     *          ),
     *      )
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
     * @descripiton To prepare all type of access links with respect to different kinds of event
     * roles(event attendee,moderator, speaker).
     *
     * @note:- Different types of links are:-
     * 1. Moderator link
     * 2. Speaker link
     * 3. Attendee link
     * 4. Manual access link
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAccessLink(Request $request): JsonResponse {
        try {
            $event = $this->repo->eventRepository->findByEventUuid($request->input('event_uuid'));
            if (!$event) {
                return response()->json(['status' => false, 'message' => __('validations.exists', ['attribute' => 'event'])], 422);
            }
            $links = $this->services->coreService->prepareAccessLinks($event, true);
            return response()->json(['status' => true, 'data' => $links], 200);
        } catch (ZoomGrantException $e) {
            return $e->render();
        } catch (Exception $exception) {
            return $this->handleIse($exception);
        }
    }


    /**
     * @OA\Get(
     *  path="/api/v1/admin/events/list/{groupKey}",
     *  operationId="getEvents",
     *  tags={"Event"},
     *  summary="To fetch events list",
     *  description="To fetch events list, if limits not assigned default will be taken i.e. 50",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="groupKey",in="path",description="Group key",required=true),
     *  @OA\Parameter(name="limit",in="query",description="Number of events in list",required=false),
     *  @OA\Parameter(name="event_type",in="query",description="Event type in event list",required=false),
     *  @OA\Response(
     *      response=200,
     *      description="Event Details",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="object",description="Events list",
     *              @OA\Property(property="future_events",type="array",
     *     description="Future Events list",@OA\Items(ref="#/components/schemas/VirtualEventResource")),
     *              @OA\Property(property="past_events",type="array",
     *     description="Future Events list",@OA\Items(ref="#/components/schemas/VirtualEventResource")),
     *          ),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is responsible for fetching and returning the requested type of event list of a
     * particular group.
     *
     * @info The event list may be of three types- 1. Future event 2. Past event 3. Draft event
     * It will return the list as per the request sent from front.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @param $groupKey
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function getEvents(Request $request, $groupKey) {
        try {
            $request->merge(['groupKey' => $groupKey]);
            $validator = Validator::make($request->all(), [
                'groupKey'   => ['required', new GroupRule],
                'limit'      => 'nullable|integer',
                'event_type' => 'nullable|string'
            ]);
            $validator->validate();
            $group = $this->repo->groupRepository->getGroupByGroupKey($request->groupKey);
            if ($request['event_type'] == "draft") {
                $events = $this->repo->eventRepository->getGroupDraftEvents(
                    $request->input('limit', 10),
                    $request->has('isPaginated'),
                    $group->id,
                );
            } elseif ($request['event_type'] == "past") {
                $events = $this->repo->eventRepository->getEvents(
                    'past',
                    $request->input('limit'),
                    null,
                    $request->has('isPaginated'),
                    $group->id,
                );
            } else {
                $events = $this->repo->eventRepository->getEvents(
                    'future',
                    $request->input('limit'),
                    null,
                    $request->has('isPaginated'),
                    $group->id,
                );
            }
            $events->load('eventRecurrenceData');
            return EventListResource::collection($events)->additional([
                'status' => true,
            ]);
        } catch (ValidationException $e) {
            return $this->send422($e->getMessage(), $e->errors());
        }
    }

    /**
     * @OA\Get(
     *  path="/api/v1/admin/events/users",
     *  operationId="getParticipants",
     *  tags={"Event"},
     *  summary="To fetch events users list",
     *  description="To fetch event users list",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="event_uuid",in="query",description="Event Uuid",required=true),
     *  @OA\Parameter(name="key",in="query",
     *     description="Key for searching participants type ex:- event_user,vip,team,expert,space_host",
     *     required=false,example="vip"),
     *  @OA\Response(
     *      response=200,
     *      description="Event participants details",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",ref="#/components/schemas/ParticipantResource"),
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
     * @descripiton This method will fetch and filter the event's participants according to the requested role for a
     * given event.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function getParticipants(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'event_uuid' => ['required', new EventRule],
                'key'        => "nullable|in:event_user,vip,team,expert,space_host,moderator,speaker",
            ]);
            if ($validator->fails()) {
                return $this->send422($validator->errors());
            }
            if (in_array($request->input('key'), ['moderator', 'speaker'])) {
                // fetching moderator and speaker with respect to the requested key
                $filteredUsers = $this->repo->eventRepository->getEventUsers($request->input('event_uuid'), $request->input('key'));
            } else {
                // fetching space host,vip,team,expert and attendee with respect to the requested key
                $users = $this->repo->eventRepository->getEventUsers($request->input('event_uuid'));
                $filteredUsers = $this->services->dataFactory->filterParticipantsByKey($request, $users);
            }
            return ParticipantResource::collection($filteredUsers)->additional([
                'status' => true,
            ]);
        } catch (Exception $exception) {
            return $this->handleIse($exception);
        }

    }

    /**
     * @OA\Post(
     *  path="/api/v1/admin/events/moments",
     *  tags={"Event"},
     *  summary="To add multiple moments",
     *  description="To add multiple moments at one click",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(required=true,@OA\JsonContent(ref="#/components/schemas/MomentCreateRequest")),
     *  @OA\Response(
     *      response=200,
     *      description="Moment created",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="object",ref="#/components/schemas/MomentResource"),
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
     * @descripiton To create or update different kinds of moments for an event.
     * @note:- Types of moments:-
     * 1. Networking
     * 2. Default zoom webinar
     * 3. Zoom webinar
     * 4. Zoom meeting
     * 5. YouTube pre recorded
     * 6. Vimeo pre recorded
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param MomentCreateRequest $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function updateMoments(MomentCreateRequest $request) {
        try {
            $this->services->validationService->validateMoments($request->input('moments'));
            DB::connection('tenant')->beginTransaction();
            $event = $this->adminRepo()->eventRepository->findByEventUuid(request()->input('event_uuid'));
            $event->load('moments');
            $startTime = $this->getCarbonByDateTime($event->start_time);
            $moments = $request->input('moments');
            // finding the deleted moments id
            $momentsToDelete = array_values(
                array_diff(
                    $event->moments->pluck('id')->toArray(), Arr::pluck($moments, 'id')
                )
            );
            foreach ($moments as $moment) {
                $setting = [];
                if (in_array($moment['moment_type'], [5, 6])) { // 5. YouTube link  6. Vimeo link
                    $setting = ['pre_recorded_url' => $moment['video_url']];
                }
                $dataToInsert = [
                    'moment_name'        => $moment['name'] ?? null,
                    'moment_description' => $moment['description'] ?? null,
                    'moment_settings'    => $setting,
                    'start_time'         => $startTime->toDateString() . " {$moment['moment_start']}",
                    'end_time'           => $startTime->toDateString() . " {$moment['moment_end']}",
                    'moment_type'        => $moment['moment_type'],
                    'event_uuid'         => $request->input('event_uuid'),
                ];

                if (isset($moment['id'])) {
                    // moment id exists means we need to update the moments
                    $this->repo->momentRepository->update($moment['id'], $dataToInsert);
                    $dataToInsert['moderator'] = $moment['moderator'] ?? null;
                    $momentId = $moment['id'];
                } else {
                    // creating new moments
                    $dataToInsert['moderator'] = $moment['moderator'] ?? null;
                    $m = $this->repo->momentRepository->create($dataToInsert);
                    $momentId = $m->id;
                }
                if (in_array($moment['moment_type'], [2, 3, 4])) {
                    if (isset($moment['moderator'])) {
                        $this->repo->eventRepository->addUserToEvent($request->input('event_uuid'), $moment['moderator'], null, ['is_moderator' => $momentId]);
                    }
                    if (count($moment['speakers'] ?? [])) {
                        foreach ($moment['speakers'] as $speaker) {
                            $this->repo->eventRepository->addUserToEvent($request->input('event_uuid'), $speaker, null, ['is_speaker' => $momentId]);
                        }
                    }
                }
            }
            $this->adminRepo()->eventRepository->storeEventAutoKeyMoment($request->input('is_auto_key_moment_event', 0), $request->input('event_uuid'));
            $this->repo->momentRepository->delete($momentsToDelete);
            $event->refresh();
            $event->load('moments', 'moments.moderator.user', 'moments.speakers.user');
            DB::connection('tenant')->commit();
            return MomentResource::collection($event->moments)->additional([
                'status' => true,
                'meta'   => [
                    'is_auto_key_moment_event' => $event->event_settings['is_auto_key_moment_event']
                ],
            ]);
        } catch (CustomValidationException $exception) {
            return $exception->render();
        } catch (Exception $exception) {
            DB::connection('tenant')->rollBack();
            return $this->handleIse($exception);
        }
    }

    /**
     * @OA\Get(
     *  path="/api/v1/admin/events/moments",
     *  tags={"Event"},
     *  summary="To get moments",
     *  description="To get the moments for an event",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="event_uuid",in="query",description="Event Uuid",required=true),
     *  @OA\Response(
     *      response=200,description="Moment fetched successfully",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="object",ref="#/components/schemas/MomentResource"),
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
     *------------------------------------------------------------------------------------------------------------------
     * @descripiton To fetch all moments related data for a specific event
     *------------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function getMoments(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'event_uuid' => 'required|exists:tenant.events,event_uuid',
            ]);
            if ($validator->fails()) {
                return $this->send422(implode(',', $validator->errors()->all()));
            }
            $event = $this->adminRepo()->eventRepository->findByEventUuid($request->input('event_uuid'));
            // loading moments with moderators and speakers if any
            $event->load(['moments' => function ($q) {
                $q->orderBy('start_time');
            }, 'moments.moderator.user', 'moments.speakers.user']);
            // synchronising the settings
            $this->services->groupService->syncGroupSettings(1);
            $settings = $this->repo->settingRepository->getSettingsByKey(1, $this->getZoomKeys());
            $broadcasting = null;
            foreach ($settings as $setting) {
                if ($setting->setting_value['enabled'] ?? 0 && $setting->setting_value['is_assigned']) {
                    $broadcasting = [
                        'broadcast_key'      => $setting->setting_key,
                        'webinar_moderators' => HostResource::collection(
                            $this->services->userService->getUsersById(Arr::pluck($setting->setting_value['webinar_data']['hosts'] ?? [], 'id'))
                        ),
                        'meeting_moderators' => HostResource::collection(
                            $this->services->userService->getUsersById(Arr::pluck($setting->setting_value['meeting_data']['hosts'] ?? [], 'id'))
                        ),
                    ];
                    break;
                }
            }
            $event->refresh();
            return MomentResource::collection($event->moments)->additional([
                'status'                   => true,
                'available_broadcast'      => $broadcasting,
                'is_auto_key_moment_event' => (isset($event->event_settings['is_auto_key_moment_event'])
                    && $event->event_settings['is_auto_key_moment_event']) ? 1 : 0
            ]);
        } catch (Exception $exception) {
            return $this->handleIse($exception);
        }
    }

    /**
     * @OA\Put(
     *  path="/api/v1/admin/events/participants",
     *  tags={"Event"},
     *  summary="To update multi user role",
     *  description="To update the multi user role",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(required=true,@OA\JsonContent(ref="#/components/schemas/UserBulkRoleUpdateRequest")),
     *  @OA\Response(
     *      response=200,
     *      description="User updated successfully",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="array",
     *     description="To indicate server processed request properly",
     *     @OA\Items(ref="#/components/schemas/EventUserResource")),
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
     * @descripiton To update event's multiple users role at once.
     * @note :- Types of role to update are:-
     * 1. Team          = (1)
     * 2. Expert        = (2)
     * 3. VIP           = (3)
     * 4. Participant   = (0)
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param UserBulkRoleUpdateRequest $request
     * @return JsonResponse|AnonymousResourceCollection
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To update multiple users event's role at a time.
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function updateMultiUserRole(UserBulkRoleUpdateRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $requestUsers = $request->input('users');
            $users = new Collection();
            $group = $this->repo->groupRepository->getGroupByGroupKey($request->input('group_key'));
            if ($group) { // checking if auth user belongs to any group
                foreach ($requestUsers as $userId) {
                    $groupUser = $group->groupUser()->where('user_id', $userId)->first();
                    if ($groupUser) { // checking if requested user belongs to that group
                        $event = $this->repo->eventRepository->findByEventUuid($request->event_uuid);
                        $userRoleData = $this->services->dataFactory->prepareMultiUserRoleUpdate($userId, $request->role);
                        $event->eventUserRelation()->updateOrCreate(['user_id' => $userId, 'event_uuid' => $request->event_uuid], $userRoleData);
                        $updatedUser = $this->services->userService->findById($userId);
                        $updatedUser->load(['eventUser' => function ($q) use ($event) {
                            $q->where('event_uuid', $event->event_uuid);
                        }, 'group']);
                        $users->push($updatedUser);
                    } else {
                        return $this->send422(__('validation.exists', ['attribute' => 'group']));
                    }
                }
                DB::connection('tenant')->commit();
                return EventUserResource::collection($users)->additional(['status' => true]);
            }
        } catch (Exception $exception) {
            DB::connection('tenant')->rollBack();
            return $this->handleIse($exception);
        }
    }

    /**
     * @OA\Delete (
     *  path="/api/v1/admin/events/participants",
     *  tags={"Event"},
     *  summary="To remove multi user role",
     *  description="To remove the multi user role",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(required=true,@OA\JsonContent(ref="#/components/schemas/UserBulkRoleRemoveRequest")),
     *  @OA\Response(
     *      response=200,
     *      description="User updated successfully",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="array",
     *     description="To indicate server processed request properly",
     *     @OA\Items(ref="#/components/schemas/EventUserResource")),
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
     * @descripiton To remove multiple users from an event at once.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param UserBulkRoleRemoveRequest $request
     * @return JsonResponse|AnonymousResourceCollection
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To remove multiple event's users from an event.
     * -----------------------------------------------------------------------------------------------------------------
     *
     */
    public function removeMultiUserRole(UserBulkRoleRemoveRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $requestUsers = $request->input('users');
            $users = new Collection();
            $event = $this->repo->eventRepository->findByEventUuid($request->event_uuid);
            // deleting the users from event
            foreach ($requestUsers as $userId) {
                $eventUser = $event->eventUserRelation()->where('user_id', $userId)->first();
                $eventUser->delete();
            }
            // Fetching all users left in the event
            $event->load('eventUsers', 'group');
            $eventUsers = $event->eventUsers->pluck('id');
            // loading event users after deleting the users from event
            foreach ($eventUsers as $userId) {
                $updatedUser = $this->services->userService->findById($userId);
                $updatedUser->load(['eventUser' => function ($q) use ($event) {
                    $q->where('event_uuid', $event->event_uuid);
                }, 'group']);
                $users->push($updatedUser);
            }
            DB::connection('tenant')->commit();
            return EventUserResource::collection($users)->additional(['status' => true]);
        } catch (CustomValidationException $exception) {
            DB::connection('tenant')->rollback();
            return $exception->render();
        } catch (Exception $exception) {
            DB::connection('tenant')->rollBack();
            return $this->handleIse($exception);
        }
    }

    /**
     * @OA\Get (
     *  path="/api/v1/admin/events/draft/all",
     *  tags={"Event"},
     *  summary="To get all draft events",
     *  description="To get all draft events ",
     *  security={{"api_key": {}}},
     *  @OA\Response(
     *      response=200,
     *      description="Event Details",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",description="To indicate server processed request properly",
     *              example="true"
     *          ),
     *          @OA\Property(property="data",type="array",description="To indicate server processed request properly",
     *              @OA\Items(ref="#/components/schemas/VirtualEventResource")
     *          ),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")
     *  ),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")
     *  ),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To fetch all draft events for a specific group
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return JsonResponse
     */
    public function getDraftEvents(): JsonResponse {
        try {
            $draftEvents = $this->repo->eventRepository->getDraftEvents();
            $eventUuid = $draftEvents->pluck('event_uuid');
            $events = $this->repo->eventRepository->getEvents('', 50, $eventUuid);
            return response()->json([
                'status' => true,
                'data'   => ['draft_events' => VirtualEventResource::collection($events)]
            ]);
        } catch (Exception $error) {
            return $this->handleIse($error);
        }

    }

    /**
     * @OA\Get(
     *  path="/api/v1/admin/events/draft/find",
     *  tags={"Event"},
     *  summary="To finding the draft event ",
     *  description="To fetch event, which are for invitation plan",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="event_uuid",in="query",description="Event uuid of the draft event",required=true),
     *  @OA\Response(
     *      response=200,
     *      description="Event Details",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",
     *              description="To indicate server processed request properly",example="true"
     *          ),
     *          @OA\Property(property="data",type="array",description="To indicate server processed request properly",
     *              @OA\Items(ref="#/components/schemas/DraftEventResource")
     *          ),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")
     *  ),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")
     *  ),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will be used for finding a specific draft event in a group
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse|DraftEventResource
     */
    public function findDraftEvent(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'event_uuid' => 'required|exists:tenant.events,event_uuid',
            ]);
            if ($validator->fails()) {
                return $this->send422(implode(',', $validator->errors()->all()));
            }
            $event = $this->repo->eventRepository->findByEventUuid($request->event_uuid, ['eventRecurrenceData']);
            $event->load('draft');
            return (new DraftEventResource($event))->additional(['status' => true]);
        } catch (Exception $error) {
            return $this->handleIse($error);
        }
    }

    /**
     * @OA\Put (
     *  path="/api/v1/admin/events/draft/update",
     *  tags={"Event"},
     *  summary="To update the draft event's data",
     *  description="To update the data for a draft event",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(required=true,@OA\JsonContent(ref="#/components/schemas/UpdateDraftEventRequest")),
     *  @OA\Response(
     *      response=200,
     *      description="Event Details",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",
     *              description="To indicate server processed request properly",example="true"
     *          ),
     *          @OA\Property(property="data",type="array",description="To indicate server processed request properly",
     *              @OA\Items(ref="#/components/schemas/DraftEventResource")
     *          ),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")
     *  ),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")
     *  ),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will update the draft event's data and send the emails as per the changes in the event
     * status to organiser of the event.
     * This method also creates the zoom broadcast for every moment.
     * Type's of moments:-
     *       1 = networking
     *       2 = default zoom webinar
     *       3 = zoom webinar
     *       4 = zoom meeting
     *       5 = youtube pre-recorded
     *       6 = vimeo pre-recorded
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param UpdateDraftEventRequest $request
     * @return JsonResponse|DraftEventResource
     */
    public function updateDraftEvent(UpdateDraftEventRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $event = $this->repo->eventRepository->findByEventUuid($request->input('event_uuid'));
            $draft = $event->draft;
            $dataToUpdate = $this->services->dataFactory->prepareDraftEventUpdateData($request, $draft, $event);
            $data = $this->repo->eventRepository->updateOrCreateDraft(
                $request->event_uuid,
                $dataToUpdate
            );
            $event->load('moments');
            // sending emails related to invitation plan
            if ($request->event_status == 1) {
                $this->adminServices()->emailService->sendInvitationPlanEmails($request, $event);
            }
            //Creating the zoom broadcast for every moment
            foreach ($event->moments as $moment) {
                if (isset($moment['moment_id']) && $moment['moment_id']) {
                    //if event have moment_id, that means zoom and webinar already created.
                    continue;
                }
                if ($moment->moment_type == 2) {
                    $broadcast = $this->services->zoomService->createWebinar($moment);
                    $moment['moment_id'] = $broadcast['moment_id'];
                    $moment['moment_settings'] = $broadcast;
                } else if ($moment->moment_type == 4 || $moment->moment_type == 3) {
                    $broadcast = $this->services->zoomService->createMeeting($moment);
                    $moment['moment_type'] = 4;
                    $moment['moment_id'] = $broadcast['moment_id'];
                    $moment['moment_settings'] = $broadcast;
                }
                $moment->update();
            }
            $event->load('draft');
            DB::connection('tenant')->commit();
            return (new DraftEventResource($event))->additional(['status' => true]);
        } catch (ZoomGrantException $e) {
            return $e->render();
        } catch (Exception $exp) {
            DB::connection('tenant')->rollBack();
            return $this->handleIse($exp);
        }
    }

    /**
     * @OA\Get(
     *  path="/api/v1/admin/events/min/list/{groupKey}",
     *  operationId="getMinEvents",
     *  tags={"Event"},
     *  summary="To fetch minimum information of events list",
     *  description="To fetch minimum information of events list, if limits not assigned default will be taken i.e. 50",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="groupKey",in="path",description="Group Key for the event",required=true),
     *  @OA\Parameter(name="limit",in="query",description="Number of events in list",required=false),
     *  @OA\Response(
     *      response=200,
     *      description="Event Details",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",description="To indicate server processed request properly",
     *              example="true"
     *          ),
     *          @OA\Property(property="data",ref="#/components/schemas/EventMinListResource"),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")
     *  ),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")
     *  ),
     * )
     *
     * ----------------------------------------------------------------------------------------------------------------
     * @descripiton This method fetches the different kinds of event lists with minimal amount of data to show event list
     * in Event drown list.
     * @note Different types of event lists are 1. Future Event List 2. Past Event List 3. Draft Event List
     * ----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @param $groupKey
     * @return JsonResponse
     */
    public function getMinEvents(Request $request, $groupKey): JsonResponse {
        try {
            $request->merge(['groupKey' => $groupKey]);
            $validator = Validator::make($request->all(), [
                'groupKey' => ['required', new GroupRule],
                'limit'    => 'nullable|integer',
            ]);
            $validator->validate();
            $group = $this->repo->groupRepository->getGroupByGroupKey($request->groupKey);
            $pastEvents = $this->repo->eventRepository->getEvents(
                'past',
                $request->input('limit'),
                null,
                $request->has('isPaginated'),
                $group->id,
            );
            $futureEvents = $this->repo->eventRepository->getEvents(
                'future',
                $request->input('limit'),
                null,
                $request->has('isPaginated'),
                $group->id,
            );
            $draftEvents = $this->repo->eventRepository->getGroupDraftEvents(
                $request->input('limit', 10),
                $request->has('isPaginated'),
                $group->id,
            );
            return response()->json([
                'status'        => true,
                'past_events'   => EventMinListResource::collection($pastEvents),
                'future_events' => EventMinListResource::collection($futureEvents),
                'draft_events'  => EventMinListResource::collection($draftEvents),
            ]);
        } catch (ValidationException $e) {
            return $this->send422($e->getMessage(), $e->errors());
        }

    }

    /**
     * @OA\Post(
     *  path="/api/v1/admin/events/live/settings",
     *  operationId="createLiveSettingData",
     *  tags={"Event"},
     *  summary="To create Live page setting data for event",
     *  description="To create live page setting data for auto-create event",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(required=true,
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(ref="#/components/schemas/EventUploadsUpdateRequest"),
     *      )
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="Live page setting data created successfully",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *      ),
     *  ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description To create or update live setting (Live Tab) data for an event conditionally.
     * @note :- The data contains videos and images
     * CASE:- 1. Request has old_event_uuid- Copy all live setting data from old event to a given event
     * CASE:- 2. Request doesn't have old_event_uuid- Upload new videos and images as per the request data.
     *          a. If request has is_default_image or is_default_video key's value as 1, then we need to copy
     *              demo(default) data into the event live setting data.
     *          b. Else upload the new image or video according to request parameter
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param EventUploadsUpdateRequest $request
     * @return JsonResponse
     */
    public function createLiveSettingData(EventUploadsUpdateRequest $request): JsonResponse {
        try {
            DB::connection('tenant')->beginTransaction();
            $event = $this->repo->eventRepository->findByEventUuid($request->event_uuid);
            $settings = $event->event_settings;
            $uuid = $this->generateUuid();
            if ($request->old_event_uuid) { // if request has old_event_uuid means we have to copy live page data
                $oldEvent = $this->repo->eventRepository->findByEventUuid($request->old_event_uuid);
                $settings = $this->services->dataFactory->getEventLivePageData($oldEvent, $event);
                $return = ['data' => true];
            } else {
                if ($request->is_default_image == 1 || $request->is_default_video == 1) {
                    // is_default_image or is_default_video has value = 1 means we need to
                    // copy default demo images or videos
                    $settings = $request->is_default_image ? $this->services->dataFactory->copyDemoLiveImages($event) :
                        $this->services->dataFactory->copyDemoLiveVideos($event);
                    $return = ['data' => true];
                } else {
                    if ($request->has('event_live_image') && $request->event_live_image) {
                        $imageValidation = config('kctadmin.modelConstants.event_live_image');
                        // resizing image for actual image to be displayed on HE side
                        $image = $this->resizeImage($request->event_live_image, $imageValidation['max_width'], $imageValidation['max_height'])->stream();
                        // resizing image for thumbnail or preview of the image to be displayed
                        $thumbnail = $this->resizeImage($request->event_live_image, $imageValidation['thumbnail_max_width'], $imageValidation['thumbnail_max_height'])->stream();
                        $originalName = Uuid::uuid1()->toString() . "." . $request->file('event_live_image')->clientExtension();
                        $folder = config('kctadmin.constants.storage_paths.live_event_image');
                        // path of the actual image
                        $path = "$folder/$event->event_uuid/$originalName";
                        // path of the thumbnail of the image
                        $thumbnailPath = "$folder/$event->event_uuid/" . '_thumbnail' . "$originalName";
                        // uploading the actual image
                        $this->services->fileService->storeFile($image->__toString(), $path);
                        // uploading the thumbnail for the image
                        $this->services->fileService->storeFile($thumbnail->__toString(), $thumbnailPath);
                        $settings['event_images'][] = [
                            'key'            => $uuid,
                            'path'           => $path,
                            'thumbnail_path' => $thumbnailPath,
                        ];
                        $filepath = $this->services->fileService->getFileUrl($path);
                        $thumbnailFilepath = $this->services->fileService->getFileUrl($thumbnailPath);
                        $return = [
                            'key'            => $uuid,
                            'path'           => $filepath,
                            'thumbnail_path' => $thumbnailFilepath,
                        ];
                    }
                    if ($request->has('event_live_video_link') && $request->event_live_video_link) {
                        $path = $request->event_live_video_link;
                        if ($request->input('video_type') == 1) {
                            // Fetching the You Tube video Id from You Tube video url
                            $targetId = $this->getYoutubeIdByUrl($path);
                        } else {
                            // Fetching the Vimeo video Id from Vimeo video url
                            $targetId = $this->getVimeoIdByUrl($path);
                        }
                        $folder = config('kctadmin.constants.storage_paths.live_event_video_thumbnails');
                        $fqdn = $this->umServices()->tenantService->getFqdn();
                        $uploadPath = "$fqdn/$folder/$event->event_uuid/$uuid.jpg";
                        $thumbnailUrl = $this->getVideoThumbnailUrl($request->input('video_type'), $targetId);
                        $this->services->fileService->uploadImageByUrl($thumbnailUrl, $uploadPath);
                        $settings['event_video_links'][] = [
                            'key'            => $uuid,
                            'value'          => $path,
                            'video_type'     => $request->input('video_type'),
                            'thumbnail_path' => $uploadPath,
                        ];
                        $url = $this->services->fileService->getFileUrl($uploadPath);
                        $return = [
                            'key'            => $uuid,
                            'value'          => $path,
                            'video_type'     => $request->input('video_type'),
                            'thumbnail_path' => $url,
                        ];
                    }
                }

            }
            $event->update(['event_settings' => $settings]);
            DB::connection('tenant')->commit();
            return response()->json(array_merge(['status' => true,], $return), 201);
        } catch (Exception $exception) {
            DB::connection('tenant')->rollBack();
            return $this->handleIse($exception);
        }
    }

    /**
     * @OA\Get (
     *  path="/api/v1/admin/events/live/settings",
     *  tags={"Event"},
     *  summary="To get the live page setting data for the evnet",
     *  description="To get the data for live page setting for auto-create event",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="event_uuid",in="query",description="Event uuid for the event",required=true),
     *  @OA\Response(
     *      response=200,
     *      description="Event Live Page Setting Data",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="event_live_images",type="array",
     *     description="To indicate server processed request properly",
     *              @OA\Items(ref="#/components/schemas/EventLiveImagesResource"),
     *          ),
     *          @OA\Property(property="event_live_video_links",type="array",
     *     description="To indicate server processed request properly",
     *              @OA\Items(ref="#/components/schemas/EventLiveVideoResource"),
     *          ),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To fetch all data related to event's live setting page for an event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getLiveSettingData(Request $request): JsonResponse {
        try {
            $validator = Validator::make($request->all(), [
                'event_uuid' => 'required|exists:tenant.events,event_uuid',
            ]);
            if ($validator->fails()) {
                return $this->send422($validator->errors());
            }
            $event = $this->adminRepo()->eventRepository->findByEventUuid($request->event_uuid);
            return response()->json([
                'status' => true,
                'data'   => [
                    'event_live_images'      => isset($event->event_settings['event_images']) ? EventLiveImagesResource::collection($event->event_settings['event_images']) : [],
                    'event_live_video_links' => isset($event->event_settings['event_video_links']) ? EventLiveVideoResource::collection($event->event_settings['event_video_links']) : [],
                    'is_default_image'       => $this->services->validationService->checkIsDefaultAsset($event, 'image'),
                    'is_default_video'       => $this->services->validationService->checkIsDefaultAsset($event, 'video')
                ],
            ], 201);
        } catch (Exception $exception) {
            return $this->handleIse($exception);
        }
    }

    /**
     * @OA\Delete(
     *  path="/api/v1/admin/events/live/settings",
     *  operationId="deleteLiveSettingData",
     *  tags={"Event"},
     *  summary="To delete the live page setting data",
     *  description="To delete the live page setting data of the auto-create event",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="event_uuid",in="query",description="Event uuid for the event",required=true),
     *  @OA\Parameter(name="key",in="query",description="Name of the key",required=true),
     *  @OA\Parameter(name="type",in="query",description="Type of the content 1. Image, 2. Video",required=true),
     *  @OA\Response(
     *      response=200,
     *      description="Data Deleted",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * ),
     *
     * ----------------------------------------------------------------------------------------------------------------
     * @descripiton To delete the live page setting data of an event.
     * ----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteLiveSettingData(Request $request): JsonResponse {
        try {
            $validator = Validator::make($request->all(), [
                'event_uuid' => 'required|exists:tenant.events,event_uuid',
                'key'        => 'required|string',
                'type'       => 'required|string',
            ]);
            if ($validator->fails()) {
                return $this->send422($validator->errors());
            }
            $event = $this->adminRepo()->eventRepository->findByEventUuid($request->event_uuid);
            $eventSettings = $event->event_settings;

            //set the value from request type which type of data needs to be deleted
            if ($request->type == 'image') {
                $settingKey = 'event_images';
            } else {
                $settingKey = 'event_video_links';
            }
            $eventImages = $eventSettings[$settingKey];
            $newEventImages = [];
            //append the only require images/videos
            foreach ($eventImages as $image) {
                if ($image['key'] != $request->key) {
                    $newEventImages[] = $image;
                }
            }
            //put all require image/videos for update
            $eventSettings[$settingKey] = $newEventImages;
            $event->event_settings = $eventSettings;
            $event->update();
            return response()->json([
                'data'   => true,
                'status' => true,
            ]);
        } catch (Exception $exception) {
            return $this->handleIse($exception);
        }
    }

    /**
     * @OA\Post(
     *  path="/api/v1/admin/events/invite/email",
     *  operationId="inviteEventUsers",
     *  tags={"Event"},
     *  summary="Mail send to the event users ",
     *  description="Mail send to the event users according to user role",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="event_uuid",in="query",description="Event uuid for the event",required=false),
     *  @OA\Response(
     *      response=200,
     *      description="Mail send to the event users",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",
     *              description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="boolean",
     *              description="To indicate server processed request properly",example="true"),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To send event invitation email to all event members according to their event's role.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function inviteEventUsers(Request $request): JsonResponse {
        try {
            DB::connection('tenant')->beginTransaction();
            // get the event for sending email
            $event = $this->repo->eventRepository->findByEventUuid($request->event_uuid);
            $data = $this->services->dataFactory->prepareDataForInviteEmail($request, $event);
            $this->services->emailService->sendEventInviteEmail($request, $data, $event);
            DB::connection('tenant')->commit();
            return response()->json(['status' => true, 'data' => true], 200);
        } catch (Exception $exception) {
            DB::connection('tenant')->rollBack();
            return $this->handleIse($exception);
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will use for redirect to the user with help of join code
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @param $joinCode
     * @return RedirectResponse
     */
    public function redirectUsingJoinCode(Request $request, $joinCode): RedirectResponse {
        $domain = $request->getHost();
        $host = env('APP_HOST');
        $dns = explode('.', $domain)[0];
        $fqdn = $dns . '.' . $host;
        $hostname = Hostname::where('fqdn', $fqdn)->first();
        if ($hostname) {
            $website = Website::find($hostname->website_id);
            if ($website) {
                $this->services->superAdminService->setTenant($website);
                $event = $this->repo->eventRepository->getEventByJoinCode($joinCode, [], true);
                if ($event) {
                    $prefix = config('kctadmin.event_front_prefix.no_recurring');
                    // preparing front side dashboard url
                    $url = env('HOST_TYPE') . $fqdn . "/$prefix/" . "dashboard/" . $event->event_uuid;
                    return redirect()->to($url);
                }
            }
        }
        return redirect()->back();
    }

    /**
     * @OA\Post(
     *  path="/api/v1/admin/events/validate/join-code",
     *  operationId="validateJoinCode",
     *  tags={"Event"},
     *  summary="To validate the custom or join url of the event is avialable or not ",
     *  description="Custom url from front side (relative) will be validated to check if the url is available or not by checking if it's taken by other event or not to show the real time validations to user" ,
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(required=true,@OA\JsonContent(
     *          @OA\Property(property="current_event",type="string", description="Event uuid for the event to exclude from check"),
     *          @OA\Property(property="key",type="string", description="User input custom url key"),
     *      ),
     *   ),
     *  @OA\Response(
     *      response=200,
     *      description="Mail send to the event users",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",
     *              description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="boolean",
     *              description="To indicate server processed request properly",example="true"),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Custom url from front side (relative) will be validated to check if the url is available or not by
     * checking if it's taken by other event or not to show the real time validations to user
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function validateJoinCode(Request $request): JsonResponse {
        $event = null;
        if ($request->has("current_event")) {
            $event = $this->repo->eventRepository->findByEventUuid($request->input('current_event'));
        }
        // checking if event is water fountain or all day type event then allowing to take the reserved join code
        if ((!$event || ($event && $event->event_type != Event::$eventType_all_day))
            && in_array($request->input('key'), config('kctadmin.constants.reservedJoinCode'))
        ) {
            return response()->json([
                'status' => true,
                'data'   => [
                    'available' => false,
                ],
            ]);
        }

        $event = $this->repo->eventRepository->getEventByJoinCode(
            $request->input('key'),
            $request->has('current_event') ? [$request->input('current_event')] : []
        );

        return response()->json([
            'status' => true,
            'data'   => [
                'available' => !$event,
            ],
        ]);
    }

    /**
     * @OA\Post(
     *  path="/api/recur/updateEvent",
     *  operationId="updateRecurEvent",
     *  tags={"Event"},
     *  summary="To validate the custom or join url of the event is avialable or not ",
     *  description="Custom url from front side (relative) will be validated to check if the url is available or not by checking if it's taken by other event or not to show the real time validations to user" ,
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(required=true,@OA\JsonContent(
     *          @OA\Property(property="start_date",type="string", description="Date to set"),
     *          @OA\Property(property="event_uuid",type="string", description="Event Uuid"),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Custom url from front side (relative) will be validated to check if the url is available or not by
     * checking if it's taken by other event or not to show the real time validations to user
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     */
    public function updateRecurEvent(Request $request) {
        $event = Event::find($request->event_uuid);
        $carbon = Carbon::make($request->start_date);
        $event->start_time = Carbon::make($event->start_time)->setDate($carbon->year, $carbon->month, $carbon->day);
        $event->end_time = Carbon::make($event->end_time)->setDate($carbon->year, $carbon->month, $carbon->day);
        foreach ($event->moments as $moment) {
            $moment->start_time = Carbon::make($moment->start_time)->setDate($carbon->year, $carbon->month, $carbon->day);
            $moment->end_time = Carbon::make($moment->end_time)->setDate($carbon->year, $carbon->month, $carbon->day);
            $moment->update();
        }

        $event->update();

        $rec = $event->eventRecurrenceData;
        $rec->start_date = $carbon->toDateString();
        $rec->end_date = $request->input('end_date')
            ? Carbon::make($request->input('end_date'))->toDateString()
            : Carbon::now()->addDays(40)->toDateString();
        $rec->update();

        return Event::with(['moments', 'eventRecurrenceData'])->find($request->event_uuid);
    }

    /**
     * @OA\Post(
     *  path="/api/recur/createTestEvent",
     *  operationId="createTestEvents",
     *  tags={"Event"},
     *  summary="To validate the custom or join url of the event is avialable or not ",
     *  description="Custom url from front side (relative) will be validated to check if the url is available or not by checking if it's taken by other event or not to show the real time validations to user" ,
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(required=true,@OA\JsonContent(
     *          @OA\Property(property="daily",type="string", description="To include daily events"),
     *          @OA\Property(property="weekly",type="string", description="To include week events"),
     *          @OA\Property(property="monthlyByDay",type="string", description="To include the months by day events"),
     *          @OA\Property(property="monthlyByWeek",type="string", description="To include the months by week events"),
     *      ),
     *   ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Custom url from front side (relative) will be validated to check if the url is available or not by
     * checking if it's taken by other event or not to show the real time validations to user
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     */
    public function createTestEvents(Request $request) {

        $prepare3Daily = function ($previousDays, $interval, $shouldOccur = false) {
            $shouldOccur = !$shouldOccur ? 'X- ' : '';
            return [
                'title'            => "$shouldOccur Every $interval Days, $previousDays Past Day",
                'description'      => "$shouldOccur Every $interval Days, $previousDays Past Day",
                'date'             => Carbon::now()->subDays($previousDays)->toDateString(),
                "rec_start_date"   => Carbon::now()->subDays($previousDays)->toDateString(),
                "event_recurrence" => [
                    "recurrence_type"     => 1,
                    "recurrence_end_date" => "2022-12-01",
                    "recurrence_interval" => $interval,
                ],
            ];
        };

        $prepareMonthly = function ($preMonth, $onDay, $interval, $shouldOccur = false, $onDays = 1) {
            $shouldOccur = !$shouldOccur ? 'X- ' : '';
            $start = Carbon::now()->subMonths($preMonth)->day($onDay)->toDateString();
            return [
                'title'            => "$shouldOccur Every $interval month, on $onDays, prev $preMonth months $start",
                'description'      => "$shouldOccur Every $interval month, on $onDays, prev $preMonth months $start",
                'date'             => $start,
                "rec_start_date"   => Carbon::now()->subMonths($preMonth)->day($onDay)->toDateString(),
                "event_recurrence" => [
                    "recurrence_type"              => 5,
                    "recurrence_end_date"          => "2022-12-01",
                    "recurrence_interval"          => $interval,
                    "recurrence_month_type"        => 1,
                    "recurrence_on_month_week"     => 1,
                    "recurrence_on_month_week_day" => "Monday",
                    "recurrence_ondays"            => $onDays,
                ],
            ];
        };


        $prepareMonthlyWeek = function ($preMonth, $onDay, $interval, $shouldOccur = false, $week, $weekDay) {
            $shouldOccur = !$shouldOccur ? 'X- ' : '';
            $start = Carbon::now()->subMonths($preMonth)->day($onDay)->toDateString();
            return [
                'title'            => "$shouldOccur Every $interval month, on $week week's $weekDay, prev $preMonth months $start",
                'description'      => "$shouldOccur Every $interval month, on $week week's $weekDay, prev $preMonth months $start",
                'date'             => $start,
                "rec_start_date"   => Carbon::now()->subMonths($preMonth)->day($onDay)->toDateString(),
                "event_recurrence" => [
                    "recurrence_type"              => 5,
                    "recurrence_end_date"          => "2022-12-01",
                    "recurrence_interval"          => $interval,
                    "recurrence_month_type"        => 2,
                    "recurrence_on_month_week"     => $week,
                    "recurrence_on_month_week_day" => $weekDay,
                ],
            ];
        };

        //  64 32 16 8 4 2 1
        //  M  T  W  T F S S

        $prepareWeekly = function ($interval, $weekDays, $prev, $shouldOccur = false) {
            $shouldOccur = !$shouldOccur ? 'X- ' : '';

            return [
                'title'            => "$shouldOccur Every $interval week, on $weekDays, prev $prev week",
                'description'      => "$shouldOccur Every $interval week, on $weekDays, prev $prev week",
                'date'             => Carbon::now()->subWeeks($prev)->toDateString(),
                "rec_start_date"   => Carbon::now()->subWeeks($prev)->toDateString(),
                "event_recurrence" => [
                    "recurrence_type"     => 3,
                    "recurrence_end_date" => "2022-12-01",
                    "recurrence_interval" => $interval,
                    "rec_weekdays"        => $weekDays,
                ],
            ];
        };

        $daily = [
            // 3 days interval event
            $prepare3Daily(6, 3, true),
            $prepare3Daily(5, 3),
            $prepare3Daily(4, 3),
            $prepare3Daily(3, 3, true),
            $prepare3Daily(2, 3),
            $prepare3Daily(1, 3),

            // 2 days interval event
            $prepare3Daily(4, 2, true),
            $prepare3Daily(3, 2),
            $prepare3Daily(2, 2, true),
            $prepare3Daily(1, 2),

            $prepare3Daily(2, 1, true),
            $prepare3Daily(1, 1, true),
        ];

        $monthlyDay = [
            // 1 month
            $prepareMonthly(1, Carbon::today()->subDays(2)->day, 2, false, 4),
            $prepareMonthly(2, Carbon::today()->subDays(2)->day, 2, false, 4),
            $prepareMonthly(3, Carbon::today()->subDays(2)->day, 2, false, 4),
            $prepareMonthly(4, Carbon::today()->subDays(2)->day, 2, false, 4),

            $prepareMonthly(1, Carbon::today()->day, 2, false, 5),
            $prepareMonthly(2, Carbon::today()->day, 2, true, 5),
            $prepareMonthly(3, Carbon::today()->day, 2, false, 5),
            $prepareMonthly(4, Carbon::today()->day, 2, true, 5),

            $prepareMonthly(1, Carbon::today()->addDays(2)->day, 2, false, 6),
            $prepareMonthly(2, Carbon::today()->addDays(2)->day, 2, false, 6),
            $prepareMonthly(3, Carbon::today()->addDays(2)->day, 2, false, 6),
            $prepareMonthly(4, Carbon::today()->addDays(2)->day, 2, false, 6),
        ];

        $monthlyByWeek = [
            $prepareMonthlyWeek(1, Carbon::today()->subDays(2)->day, 2, false, 2, Carbon::today()->subDay()->dayName),
            $prepareMonthlyWeek(2, Carbon::today()->subDays(2)->day, 2, false, 2, Carbon::today()->subDay()->dayName),
            $prepareMonthlyWeek(3, Carbon::today()->subDays(2)->day, 2, false, 2, Carbon::today()->subDay()->dayName),
            $prepareMonthlyWeek(4, Carbon::today()->subDays(2)->day, 2, false, 2, Carbon::today()->subDay()->dayName),

            $prepareMonthlyWeek(1, Carbon::today()->day, 2, false, 1, Carbon::today()->dayName),
            $prepareMonthlyWeek(2, Carbon::today()->day, 2, true, 1, Carbon::today()->dayName),
            $prepareMonthlyWeek(3, Carbon::today()->day, 2, false, 1, Carbon::today()->dayName),
            $prepareMonthlyWeek(4, Carbon::today()->day, 2, true, 1, Carbon::today()->dayName),

            $prepareMonthlyWeek(1, Carbon::today()->addDays(2)->day, 2, false, 2, Carbon::today()->addDay()->dayName),
            $prepareMonthlyWeek(2, Carbon::today()->addDays(2)->day, 2, false, 2, Carbon::today()->addDay()->dayName),
            $prepareMonthlyWeek(3, Carbon::today()->addDays(2)->day, 2, false, 2, Carbon::today()->addDay()->dayName),
            $prepareMonthlyWeek(4, Carbon::today()->addDays(2)->day, 2, false, 2, Carbon::today()->addDay()->dayName),
        ];

        $weekly = [
            $prepareWeekly(1, 3, 1, false),
            $prepareWeekly(1, 3, 2, false),
            $prepareWeekly(1, 6, 1, true),
            $prepareWeekly(1, 6, 2, true),
            $prepareWeekly(1, 16, 1, false),
            $prepareWeekly(1, 16, 2, false),

            $prepareWeekly(2, 3, 1, false),
            $prepareWeekly(2, 3, 2, false),
            $prepareWeekly(2, 6, 1, false),
            $prepareWeekly(2, 6, 2, true),
            $prepareWeekly(2, 16, 1, false),
            $prepareWeekly(2, 16, 2, false),
        ];

        $data = [];
        if ($request->has('daily')) {
            $data = $daily;
        }
        if ($request->has('weekly')) {
            $data = array_merge($data, $weekly);
        }
        if ($request->has('monthlyByDay')) {
            $data = array_merge($data, $monthlyDay);
        }
        if ($request->has('monthlyByWeek')) {
            $data = array_merge($data, $monthlyByWeek);
        }

        $common = [
            "group_key"           => $request->group_key,
            'start_time'          => '18:00:00',
            'end_time'            => '19:00:00',
            "join_code"           => "",
            "is_dummy_event"      => 1,
            "type"                => 1,
            "draft"               => 0,
            "recurrence_end_date" => "2022-12-01",
        ];

        $group = $this->repo->groupRepository->getGroupByGroupKey($request->input('group_key', 'default'));

        $user = $group->groupUser()->where('role', 2)->first();
        Auth::loginUsingId($user->user_id);
        $event = [];
        foreach ($data as $r) {
            $request->merge(array_merge($r, $common));
            DB::connection('tenant')->beginTransaction();
            $event[] = $this->createTest($request, $group);
            DB::connection('tenant')->commit();
        }
        return $event;
    }

    /**
     * @OA\Post(
     *  path="/api/recur/deleteTestEvent",
     *  operationId="deleteTestEvent",
     *  tags={"Event"},
     *  summary="To validate the custom or join url of the event is avialable or not ",
     *  description="Custom url from front side (relative) will be validated to check if the url is available or not by checking if it's taken by other event or not to show the real time validations to user" ,
     *  security={{"api_key": {}}},
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Custom url from front side (relative) will be validated to check if the url is available or not by
     * checking if it's taken by other event or not to show the real time validations to user
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return int
     */
    public function deleteTestEvents() {
        $events = Event::all();
        foreach ($events as $event) {
            if (isset($event->event_settings['isTest'])) {
                $event->delete();
            }
        }
        return $events->count();
    }

    private function createTest($request, $group) {
        $param = $this->services->dataFactory->prepareEventCreateData($request, $group->id);
        $param['event_settings']['isTest'] = 1;
        $event = $this->repo->eventRepository->create($param);
        if (in_array($request->event_recurrence['recurrence_type'] ?? null, config('kctadmin.modelConstants.event_recurrence'))) {
            // preparing data for recurrence and making event recurring
            $recurringParam = $this->services->dataFactory->prepareRecurringEventData($request, $event);
            $this->repo->eventRepository->makeEventRecurring($recurringParam);
            $event->load('eventRecurrenceData');
        }

        if ($request->has('draft')) { // checking if event is a draft event
            $draftParam = $this->services->dataFactory->prepareDraftEventData($event, $request->draft);
            $draftParam['reg_start_time'] = Carbon::now()->toDateString();
            $draftParam['reg_end_time'] = Carbon::now()->toDateString();
            $this->repo->eventRepository->makeEventAsDraft($draftParam);
        }
        $spaceParam = $this->services->dataFactory->prepareDefaultSpace($request, $event, $group->id);
        $space = $this->repo->kctSpaceRepository->create($spaceParam);
        // adding group's first pilot as space host of the default space
        $this->repo->eventRepository->addUserToEvent($event->event_uuid, $spaceParam['hosts'], null, ['is_organiser' => 0, 'is_host' => 1]);

        $moments = $this->services->dataFactory->prepareMomentData($request);
        foreach ($moments as $moment) {
            $event->moments()->create($moment);
        }
        $event->load('moments');
        if ($event->event_settings['is_dummy_event']) {
            // preparing dummy users data and adding them in default space
            $this->services->dataFactory->prepareDummyUsers($space);
        }
        // adding auth user(event creating user) as organiser and host of the default space
        $this->repo->eventRepository->addUserToEvent($event->event_uuid, Auth::user()->id, null, ['is_organiser' => 1, 'is_host' => 0]);
        return $event;
    }

    public function test() {
        $r = new When();
        $r->RFC5545_COMPLIANT = When::IGNORE;

        $r->startDate(Carbon::today())
            ->freq('weekly')
            ->byDay(['fr', 'sa'])
            ->interval(2)
            ->until(Carbon::today()->addMonths(4))
            ->generateOccurrences();

        dd($r->occurrences);

    }

}
