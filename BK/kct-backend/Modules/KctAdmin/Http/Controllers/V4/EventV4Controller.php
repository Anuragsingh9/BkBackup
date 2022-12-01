<?php

namespace Modules\KctAdmin\Http\Controllers\V4;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\KctAdmin\Entities\Event;
use Modules\KctAdmin\Entities\EventMeta;
use Modules\KctAdmin\Entities\Moment;
use Modules\KctAdmin\Events\EventDataUpdated;
use Modules\KctAdmin\Exceptions\ZoomAccountExpiredException;
use Modules\KctAdmin\Http\Controllers\V1\BaseController;
use Modules\KctAdmin\Http\Requests\V4\CreateEventV4Request;
use Modules\KctAdmin\Http\Requests\V4\UpdateEventV4Request;
use Modules\KctAdmin\Traits\KctHelper;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\KctAdmin\Transformers\EventSceneryResource;
use Modules\KctAdmin\Transformers\V1\HostResource;
use Modules\KctAdmin\Transformers\V4\EventAnalyticsListResource;
use Modules\KctAdmin\Transformers\V4\EventAnalyticsMinResource;
use Modules\KctAdmin\Transformers\V4\EventAnalyticsResource;
use Modules\KctAdmin\Transformers\V4\EventV4Resource;
use Modules\KctAdmin\Transformers\V4\SpaceV4Resource;
use Nwidart\Modules\Collection;

/**
 * -----------------------------------------------------------------------------------------------------------------
 * @description This class will manage the event related functionality
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class EventV4Controller
 * @package Modules\KctAdmin\Http\Controllers\V4
 */
class EventV4Controller extends BaseController {

    use KctHelper;
    use ServicesAndRepo;

    /**
     * @OA\Get(
     *  path="/api/v4/admin/events",
     *  operationId="getEventt",
     *  tags={"V4 Event"},
     *  summary="To fetch the event by event uuid",
     *  description="To fetch the event by event uuid",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="event_uuid",in="query",description="Event uuid to fetch",required=true),
     *  @OA\Response(
     *      response=200,
     *      description="Event Details",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="object",
     *     description="Virtual Event Resource",ref="#/components/schemas/EventV4Resource"),
     *      )
     *   ),
     *  @OA\Response(response=403,
     *     description="User Is Unauthorized",@OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=404,
     *     description="Resource not found",@OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,
     *     description="Data is not valid",@OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,
     *     description="Some Internal Server Issue Occuerred",@OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is responsible for fetching single event data of a specific group.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse|mixed
     */
    public function getEvent(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'event_uuid' => 'required|exists:tenant.events,event_uuid',
            ]);
            $validator->validate();
            $event = $this->repo->eventRepository->findByEventUuid($request->input('event_uuid'));
            $links = $this->services->coreService->prepareAccessLinks($event, true);
            if ($event->event_type === Event::$eventType_all_day) {
                unset($links['manual_access']);
            }
            $eventLinks = [];

            foreach ($links as $key => $link) {
                $data[] = [
                    'type' => $key,
                    'link' => $link,
                ];
                $eventLinks = $data;
            }
            $event['links'] = $eventLinks;
            $event->load(['eventRecurrenceData', 'spaces', 'draft']);
            return $this->returnWithStatus(new EventV4Resource($event));
        } catch (ValidationException $e) {
            return $this->send404($e->getMessage(), $e->errors());
        } catch (Exception $e) {
            return $this->handleIse($e);
        }
    }

    /**
     * @OA\Post(
     *  path="/api/v4/admin/events",
     *  operationId="createVirtualEvent",
     *  tags={"V4 Event"},
     *  summary="To create a new Virtual Event",
     *  description="To create a new Virtual Type Event",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(ref="#/components/schemas/CreateEventV4Request"),
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="Virtual Event Created",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="object",
     *     description="Virtual Event Resource",ref="#/components/schemas/EventV4Resource"),
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
     * @descripiton This method is responsible for creating all event related data at one click. Along with event
     * creation it will also handle/create default space,recurrence data,draft data,scenery data and moments data
     * required for an event to occur.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param CreateEventV4Request $request
     * @return EventV4Resource|JsonResponse
     */
    public function createV4Event(CreateEventV4Request $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $group = $this->repo->groupRepository->getGroupByGroupKey($request->input('group_key'));
            $param = $this->services->dataFactory->prepareEventV4Param($request, $group->id);
            $event = $this->repo->eventRepository->create($param['event']);
            $param['draft']['event_uuid'] = $event->event_uuid;

            $isAuthHost = false;

            // Creating the spaces data
            foreach ($param['spaces'] as $key => $space) {
                $space['event_uuid'] = $event->event_uuid;
                $createdSpace = $this->repo->kctSpaceRepository->create($space);
                if ($key === 0) $defaultSpace = $createdSpace;

                if ($space['hosts'] === Auth::user()->id) $isAuthHost = true;
                // if auth user is equal to host then send to organiser value 1
                if ($space['hosts'] === Auth::user()->id) {
                    $isOrganiser = 1;
                } else {
                    $isOrganiser = 0;
                }

                // adding the selected user as space host
                $this->repo->eventRepository->addUserToEvent(
                    $event->event_uuid,
                    $space['hosts'],
                    $createdSpace->space_uuid,
                    ['is_organiser' => $isOrganiser, 'is_host' => 1]
                );
            }

            // storing event recurrence data
            if (in_array(
                $request->event_recurrence['rec_type'] ?? null,
                config('kctadmin.modelConstants.event_recurrence')
            )) {
                $param['recurrence']['event_uuid'] = $event->event_uuid;
                // preparing data for recurrence and making event recurring
                $this->repo->eventRepository->makeEventRecurring($param['recurrence']);
                $event->load('eventRecurrenceData');
            }

            // creating default space data
            if ($event->event_settings['is_dummy_event']) {
                // preparing dummy users data and adding them in default space
                $this->services->dataFactory->prepareDummyUsers($defaultSpace);
            }

            // adding the event draft data
            $this->repo->eventRepository->makeEventAsDraft($param['draft']);

            // storing moment data
            foreach ($param['moments'] as $moment) {
                $moment['event_uuid'] = $event->event_uuid;
                $m = $event->moments()->create($moment);
                if (in_array($moment['moment_type'], [2, 3, 4])) {
                    if (isset($moment['moderator'])) {
                        $this->repo->eventRepository->addUserToEvent(
                            $event->event_uuid,
                            $moment['moderator'],
                            null,
                            ['is_moderator' => $m->id]
                        );
                    }
                    if ($request->input('event_is_published')
                        && in_array(
                            $m->moment_type,
                            [Moment::$momentType_webinar, Moment::$momentType_meeting]
                        )) {
                        $broadcast = $m->moment_type == Moment::$momentType_webinar
                            ? $this->services->zoomService->createWebinar($m)
                            : $this->services->zoomService->createMeeting($m);
                        $m['moment_id'] = $broadcast['moment_id'];
                        $m['moment_settings'] = $broadcast;
                        $m->update();
                    }
                }
            }
            $event->load(['moments']);

            // storing event scenery data
            if ($request->has('event_scenery')) {
                $sceneryParam = $this->adminServices()->dataFactory->prepareParamForV4EventScenery($request, $event);
                $event->update(['event_settings' => $sceneryParam]);
            }

            $links = $this->services->coreService->prepareAccessLinks($event, true);
            $eventLinks = [];
            foreach ($links as $key => $link) {
                $data[] = [
                    'type' => $key,
                    'link' => $link,
                ];
                $eventLinks = $data;
            }
            $event['links'] = $eventLinks;
            if (!$isAuthHost) { // checking if space host id and auth id is not same
                // adding auth user(event creating user) as organiser and host of the default space
                $this->repo->eventRepository->addUserToEvent($event->event_uuid, Auth::user()->id,
                    null,
                    ['is_organiser' => 1, 'is_host' => 0]);
            }
            DB::connection('tenant')->commit();
            return (new EventV4Resource($event))->additional(['status' => true]);
        } catch (ZoomAccountExpiredException $e) {
            return $e->render();
        } catch (Exception $e) {
            DB::connection('tenant')->rollBack();
            return $this->handleIse($e);
        }
    }

    /**
     * @OA\Put(
     *  path="/api/v4/admin/events",
     *  operationId="updateVirtulEvent",
     *  tags={"V4 Event"},
     *  summary="To update a Virtual Event",
     *  description="To update a Virtual Type Event",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(ref="#/components/schemas/UpdateEventV4Request"),
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="Virtual Event Created",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="object",
     *     description="Virtual Event Resource",ref="#/components/schemas/EventV4Resource"),
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
     * @descripiton This method is responsible for updating all event related data at one click. Along with event
     * updating it will also handle the update of  default space,draft data,scenery data and moments data of a given
     * event.
     * @note Some event's data like recurrence,join code and demo users are only updated with certain conditions.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param UpdateEventV4Request $request
     * @return JsonResponse
     */
    public function updateV4Event(UpdateEventV4Request $request): JsonResponse {
        try {

            DB::connection('tenant')->beginTransaction();
            $data = [];
            $event = $this->repo->eventRepository->findByEventUuid($request->input('event_uuid'));
            $eventData = $this->services->dataFactory->prepareV4EventUpdateData($request, $event, null);
            $previousDummy = $event->event_settings['is_dummy_event'];
            if ($request->has('event_title') || $request->has('event_start_date')) {
                $event = $this->repo->eventRepository->updateEvent($event, $eventData['event']);
            }
            $deletedDummyUsers = new Collection();
            $addedDummyUsers = new Collection();
            if ($request->has('event_is_demo') && $previousDummy != $request->input('event_is_demo')) {
                if ($request->input('event_is_demo') == 1) {
                    $this->services->dataFactory->prepareDummyUsers($event->spaces->first());
                    $event->load('dummyRelations');
                    $addedDummyUsers = $event->dummyRelations;
                } else {
                    $deletedDummyUsers = $event->dummyRelations;
                    $event->dummyRelations()->delete();
                }
            }

            // Update the recurrence data
            if (in_array($request->event_recurrence['rec_type'] ?? null, config('kctadmin.modelConstants.event_recurrence'))
                && $request->event_recurrence['rec_type'] == $event->eventRecurrenceData->recurrence_type
            ) {
                $this->repo->eventRepository->updateEventRecurringData($eventData['recurrence'], $event->event_uuid);
                $event->refresh('eventRecurrenceData');
            }
            //if FR request have update the moments
            $event->load('moments');


            foreach ($event->moments as $moment) {
                if (
                    $event->draft->event_status != EventMeta::$eventStatus_live // event not published
                    && !$request->input('event_broadcasting') // event have no broadcasting
                    && in_array($moment->moment_type, [2, 3, 4]) // previously event have broadcasting
                ) {
                    // request doesn't have event broadcasting so deleting the broadcasting moment
                    $moment->delete();
                } else {
                    $moment->moment_name = $eventData['moment']['moment_name'];
                    $moment->moment_description = $eventData['moment']['moment_description'];
                    $moment->start_time = $event->start_time;
                    $moment->end_time = $event->end_time;
                    $moment->update();
                    if (in_array($moment->moment_type, [2, 3, 4])
                        && $moment->moderator->user_id !== $request->input('event_moderator')
                    ) {
                        // making the old moderator as normal user
                        $this->repo->eventRepository->addUserToEvent(
                            $event->event_uuid,
                            $moment->moderator->user_id,
                            null,
                            ['remove_as_moderator' => $moment->id,]
                        );

                        // making the new moderator as host
                        $this->repo->eventRepository->addUserToEvent(
                            $event->event_uuid,
                            $request->input('event_moderator'),
                            null,
                            ['is_moderator' => $moment->id]
                        );
                    }
                }
            }

            if (
                $event->draft->event_status != EventMeta::$eventStatus_live // event not published
                && $request->input('event_broadcasting') // request have broadcasting option
                && !$event->moments()->whereIn('moment_type', [2, 3, 4])->count() // no broadcasting already
            ) {
                $m = $event->moments()->create([
                    'moment_name'        => $eventData['moment']['moment_name'],
                    'moment_description' => $eventData['moment']['moment_description'],
                    'moment_type'        => $request->event_broadcasting == 1
                        ? Moment::$momentType_meeting
                        : Moment::$momentType_webinar,
                    'start_time'         => $event->start_time,
                    'end_time'           => $event->end_time,
                ]);
                $this->repo->eventRepository->addUserToEvent(
                    $event->event_uuid,
                    $request->input('event_moderator'),
                    null,
                    ['is_moderator' => $m->id]
                );
            }

            $event->load('moments');

            //if FR request have update the space data
            $event->load('spaces');

            $this->repo->kctSpaceRepository->shiftSpaceUserToDefaultSpace(
                $event->spaces()->whereIn('space_uuid', $eventData['spaces']['spaceToDelete'])->get(),
                $event->spaces->first()
            );


            foreach ($eventData['spaces']['spaceToUpdate'] as $s) {
                $space = $this->repo->kctSpaceRepository->updateSpace($s['space_uuid'], $s);
                $this->repo->eventRepository->removeAsUserFromSpace($event, $s['hosts']);
                $this->repo->eventRepository->updateSpaceHosts($space, [$s['hosts']]);
            }
            foreach ($eventData['spaces']['spaceToCreate'] as $s) {
                $space = $this->repo->kctSpaceRepository->create($s);
                // adding the selected user as space host
                $this->repo->eventRepository->removeAsUserFromSpace($event, $s['hosts']);

                $this->repo->eventRepository->addUserToEvent(
                    $event->event_uuid,
                    $s['hosts'],
                    $space->space_uuid,
                    ['is_organiser' => 0, 'is_host' => 1]
                );
            }
            $event->spaces()->whereIn('space_uuid', $eventData['spaces']['spaceToDelete'])->forceDelete();

            $event->load('spaces.spaceHost');

            // if FR request have to update scenery then update the scenery data
            if ($request->has('event_scenery')) {
                $settings = $event->event_settings;
                $settings = array_merge($settings, $eventData['event']['event_settings'], $eventData['event_scenery']);
                $event->update(['event_settings' => $settings]);
            }

            // If FR request have to publish the event
            if ($request->has('event_is_published') && $event->draft->event_status != EventMeta::$eventStatus_live) {
                $this->repo->eventRepository->updateOrCreateDraft(
                    $request->event_uuid,
                    $eventData['draft']
                );

                if ($request->event_broadcasting && $request->input('event_is_published')) {
                    $m = $event->moments()->whereIn('moment_type', [2, 3, 4])->first();
                    $broadcast = $request->event_broadcasting == 1
                        ? $this->services->zoomService->createMeeting($m)
                        : $this->services->zoomService->createWebinar($m);
                    $m['moment_id'] = $broadcast['moment_id'];
                    $m['moment_settings'] = $broadcast;
                    $m->update();
                }
                $event->load('moments');

            }

            $event->load('draft');
            if ($event->draft->event_status === EventMeta::$eventStatus_live) {
                $links = $this->services->coreService->prepareAccessLinks($event, true);
                $eventLinks = [];
                foreach ($links as $key => $link) {
                    $eventLinks[] = [
                        'type' => $key,
                        'link' => $link,
                    ];
                }
                $data['event_links'] = $eventLinks;
                $data['event_is_published'] = true;
            }
            $data['event_is_published'] = $event->draft->event_status === EventMeta::$eventStatus_live;
            $data['event_spaces'] = SpaceV4Resource::collection($event->spaces);

            DB::connection('tenant')->commit();

            event(new EventDataUpdated($event, [
                'deleted' => $deletedDummyUsers,
                'added'   => $addedDummyUsers,
            ]));
            return response()->json([
                'status' => true,
                'data'   => $data,
            ]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return $this->handleIse($e);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getEventInitData(Request $request): JsonResponse {
        try {
            $validator = Validator::make($request->all(), [
                'event_uuid' => 'nullable',
            ]);
            if ($validator->fails()) {
                return $this->send422(implode(',', $validator->errors()->all()));
            }
            $allSceneryData = $this->adminServices()->superAdminService->getAllSceneryData();
            if ($request->event_uuid) {
                $eventSceneryData = [
                    'all_scenery_data'     => EventSceneryResource::collection($allSceneryData),
                    // fetching the current event's scenery data
                    'current_scenery_data' => $this->services->dataFactory->fetchEventSceneryData($request->event_uuid),
                ];
            } else {
                $eventSceneryData = [
                    'all_scenery_data' => EventSceneryResource::collection($allSceneryData),
                ];
            }
            $data = [
                'scenery' => $eventSceneryData,
            ];

            $settings = $this->repo->settingRepository->getSettingsByKey(1, $this->getZoomKeys());
            $broadcasting = null;

            foreach ($settings as $setting) {
                if (($setting->setting_value['enabled'] ?? 0) && $setting->setting_value['is_assigned']) {
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

            $data['broadcasting'] = $broadcasting;
            return response()->json(['status' => true, 'data' => $data]);
        } catch (Exception $e) {
            return $this->handleIse($e);
        }
    }


    /**
     * @OA\Get (
     *  path="/api/v4/admin/events/analytics",
     *  operationId="getEventsAnalytics",
     *  tags={"V4 Event"},
     *  summary="To get the analytics data",
     *  description="To get the analytics data for engagement page",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="groupKey[]",in="query",description="group keys of account",required=false, example="default"),
     *  @OA\Parameter(name="row_per_page",in="query",description="row per page",required=false, example="10"),
     *  @OA\Parameter(name="page",in="query",description="page",required=false, example="1"),
     *  @OA\Parameter(name="pagination",in="query",description="pagination",required=false, example="1"),
     *  @OA\Parameter(name="from_date",in="query",description="from date",required=false, example="2022-07-01 13:00:00"),
     *  @OA\Parameter(name="to_date",in="query",description="to date",required=false, example="2022-09-01 13:00:00"),
     *  @OA\Parameter(name="key",in="query",description="Key for searching",required=false),
     *  @OA\Parameter(name="order_by",in="query",description="Order by used for sort the analytics data
     *      ex.:- event_name,event_type,zoom_meeting,zoom_webinar,total_conv_count,total_registration,total_attendees,
     *      media_image,media_video,sh_conv_count,total_duration,event_date",required=false),
     *  @OA\Parameter(name="order",in="query",description="Order of sorting ex:- asc, desc",required=false),
     *  @OA\Response(
     *      response=200,
     *      description="Data fetched",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="object",
     *     description="Virtual Event Resource",ref="#/components/schemas/EventAnalyticsListResource"),
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
     * @descripiton This method is responsible for fetching analytics data of an account according to the given request.
     * This method is also responsible for filtering the data by groups and searching the events by event's title,
     * event type and by event time with different formats like month,date,year etc.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function getEventsAnalytics(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'groupKey'     => ['nullable'],
                'row_per_page' => 'nullable',
                'page'         => 'nullable',
                'pagination'   => 'nullable',
                'from_date'    => 'nullable|date_format:Y-m-d H:i:s',
                'to_date'      => 'nullable|date_format:Y-m-d H:i:s',
                'key'          => 'nullable|min:2',
                'order_by'     => 'nullable|in:event_name,event_type,zoom_meeting,zoom_webinar,total_conv_count,total_registration,total_attendees,media_image,media_video,sh_conv_count,total_duration,event_date',
                'order'        => 'nullable|in:asc,desc'
            ]);
            if ($validator->fails()) {
                return $this->send422($validator->errors());
            }

            $isPaginated = $request->input('pagination');
            $limit = $request->input('row_per_page', 10);
            $startDate = $request->input('from_date');
            $endDate = $request->input('to_date');
            $orderBy = $request->input('order_by', 'event_date');
            $order = $request->input('order', 'desc');
            if ($startDate && $endDate) {
                $startDate = Carbon::make($startDate)->toDateString();
                $endDate = Carbon::make($endDate)->toDateString();
            }

            $requestedGroups = $this->adminRepo()->groupRepository->getGroupsByGroupKeys(
                $request->input('groupKey', []));
            $groupIds = [];
            if ($requestedGroups->isNotEmpty()) { // Request have groups to filter
                $groupIds = $requestedGroups->pluck('id')->toArray();
            } else { // Request does not have groups to filter
                $isSuperPilot = $this->adminRepo()->groupUserRepository->isUserSuperPilotOrOwner();
                if (!$isSuperPilot) {
                    // If user is not super pilot then returning all groups of that user in which user is as a pilot
                    $userGroupIds = $this->adminRepo()->groupUserRepository->getUserPilotGroups(Auth::id())->pluck('group_id')->toArray();
                    $groupIds = $userGroupIds;
                }
            }

            $builder = $this->adminRepo()->eventRepository->getEventFromRecurrence(
                $groupIds,
                $startDate,
                $endDate,
                $request->input('key'),
            );

            $keysAlise = [
                'event_name'         => 'title',
                'event_type'         => 'event_type',
                'zoom_meeting'       => 'is_zoom_meeting',
                'zoom_webinar'       => 'is_zoom_webinar',
                'total_conv_count'   => 'conv_count',
                'total_registration' => 'reg_count',
                'total_attendees'    => 'attendee_count',
                'media_image'        => 'p_image_count',
                'media_video'        => 'p_video_count',
                'sh_conv_count'      => 'sh_conv_count',
                'total_duration'     => 'total_duration',
                'event_date'         => 'recurrence_date',
            ];
            $builder = $this->adminServices()->analyticsService->addSortingToAnalytics($builder, $orderBy, $order, $keysAlise);
            $data = $this->handleDataPagination($builder, $isPaginated, $limit);
            return EventAnalyticsListResource::collection($data)->additional([
                'status' => true,
            ]);
        } catch (Exception $exception) {
            return $this->handleIse($exception);
        }
    }

    /**
     * @OA\Get(
     *  path="/api/v4/admin/events/analytics/single",
     *  operationId="getSingleEventAnalytics",
     *  tags={"V4 Event"},
     *  summary="To fetch the event analytics",
     *  description="To fetch the event analytics",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="event_uuid",in="query",description="Event uuid to fetch",required=true),
     *  @OA\Parameter(name="from_date",in="query",description="Start date of the recurrence event",required=false),
     *  @OA\Parameter(name="to_date",in="query",description="Start date of the recurrence event",required=false),
     *  @OA\Parameter(name="recurrence_uuid",in="query",description="Recurrence uuid",required=false),
     *  @OA\Response(
     *      response=200,
     *      description="Data fetched",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="object",
     *     description="Virtual Event Resource",ref="#/components/schemas/EventAnalyticsResource"),
     *      ),
     *  ),
     *  @OA\Response(response=403,
     *     description="User Is Unauthorized",@OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=404,
     *     description="Resource not found",@OA\JsonContent(ref="#/components/schemas/Doc403Resource")),
     *  @OA\Response(response=422,
     *     description="Data is not valid",@OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,
     *     description="Some Internal Server Issue Occuerred",@OA\JsonContent(ref="#/components/schemas/Doc500Resource")),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is responsible for fetching single event analytics data, and it also returns the event's
     * all recurrences lists in meta
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse|mixed
     */
    public function getSingleEventAnalytics(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'event_uuid' => 'required|exists:tenant.events,event_uuid',
                'from_date'  => 'nullable|date_format:Y-m-d H:i:s,Y-m-d',
                'to_date'    => 'nullable|date_format:Y-m-d H:i:s,Y-m-d',
            ]);
            if ($validator->fails()) {
                return $this->send422($validator->errors());
            }
            $startDate = $request->input('from_date');
            $endDate = $request->input('to_date');
            $builder = $this->adminRepo()->eventRepository->getEventFromRecurrence(null, $startDate, $endDate, null, true);
            $builder->where('event_uuid', $request->input('event_uuid'))->orderBy('recurrence_date', 'asc');
            $occurrences = $builder->get();
            if ($request->has('recurrence_uuid')) {
                $builder->where('recurrence_uuid', $request->input('recurrence_uuid'));
            }
            $result = $builder->get();
            return EventAnalyticsResource::collection($result)->additional([
                'status' => true,
                'meta'   => [
                    'recurrences_list' => EventAnalyticsMinResource::collection($occurrences)
                ],
            ]);
        } catch (Exception $exception) {
            return $this->handleIse($exception);
        }
    }
}

