<?php

namespace Modules\KctAdmin\Http\Controllers\V1;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\KctAdmin\Http\Requests\EventSceneryRequest;
use Modules\KctAdmin\Http\Requests\V1\CreateSpaceRequest;
use Modules\KctAdmin\Http\Requests\V1\SpaceDeleteRequest;
use Modules\KctAdmin\Http\Requests\V1\UpdateSpaceRequest;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\KctAdmin\Transformers\EventSceneryResource;
use Modules\KctAdmin\Transformers\V1\SpaceResource;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will manage the functionality of space
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class SpaceController
 * @package Modules\KctAdmin\Http\Controllers\V1
 */
class SpaceController extends BaseController {
    use ServicesAndRepo;

    /**
     * @OA\Post(
     *  path="/api/v1/admin/spaces",
     *  operationId="storeSpaceV1",
     *  tags={"Space"},
     *  summary="To create a new Space",
     *  description="To create a new Space",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(required=true,@OA\JsonContent(ref="#/components/schemas/CreateSpaceRequest")
     *  ),
     * @OA\Response(
     *      response=200,
     *      description="",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",
     *              description="To indicate server processed request properly",example="true"
     *          ),
     *          @OA\Property(property="data",type="object",description="Space Resource",
     *              ref="#/components/schemas/SpaceResource"
     *          ),
     *      ),
     *  ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *      @OA\JsonContent(ref="#/components/schemas/Doc403Resource")
     *  ),
     *  @OA\Response(response=422,description="Data is not valid",
     *      @OA\JsonContent(ref="#/components/schemas/Doc422Resource")
     *  ),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *      @OA\JsonContent(ref="#/components/schemas/Doc500Resource")
     *  ),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for creating space in an event.It also handles the event venue
     * type(Mono or Multi) according to request parameters provided.
     * @info Venue types:-
     * 1. Mono- If venue type is mono then event will have only one space(Default space).
     * 2. Multi- If venue type is not mono then event can have multiple spaces
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param CreateSpaceRequest $request
     * @return JsonResponse|SpaceResource
     */
    public function store(CreateSpaceRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $event = $this->adminRepo()->eventRepository->findByEventUuid($request->input('event_uuid'));
            // if the FR request space type is mono then create the mono space
            if ($request->input('is_mono') == 1) { // mono space means event will have only one space for its users
                $space = $this->adminRepo()->kctSpaceRepository->getDefaultSpace($request->event_uuid);
                $this->repo->kctSpaceRepository->updateSpace(
                    $space->space_uuid,
                    ['max_capacity' => $request->input('max_capacity')]
                );
                $event = $this->adminRepo()->eventRepository->findByEventUuid($request->event_uuid);
                $event->update(['is_mono_type' => 1]);
                // update the host id and host type for mono space
                $this->repo->eventRepository->updateSpaceHosts($space, $request->input('hosts'));
                $space->refresh();
                $space->load('hosts');
            } else {// if the space is normal
                // prepare information for space
                $param = $this->services->dataFactory->spaceCreateParam($request);
                // creating the space
                $space = $this->repo->kctSpaceRepository->create($param);
                $auth = Auth::user();
                $groupId = $auth->group->id;
                // fetch the group organiser ids
                $organisers = $this->adminRepo()->groupRepository->getGroupUsers(
                    $groupId,
                    2
                )->pluck('id');
                // checking if space host is an organiser
                $isOrganiser = in_array($param['hosts'][0]['id'], $organisers->toArray()) ? 1 : 0;
                // prepare roles for organiser and host
                $roles = [
                    'is_organiser' => $isOrganiser,
                    'is_host'      => 1,
                ];
                // add users into the event
                $this->repo->eventRepository->addUserToEvent(
                    $event->event_uuid,
                    $param['hosts'][0]['id'],
                    $space->space_uuid,
                    $roles
                );
                $event->update(['is_mono_type' => 0]);
                $space->load('event');
            }
            DB::connection('tenant')->commit();
            return (new SpaceResource($space))->additional(['status' => true]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            $result = $this->handleIse($e);
        }
        return $result;
    }

    /**
     * @OA\Put(
     *  path="/api/v1/admin/spaces",
     *  operationId="update",
     *  tags={"Space"},
     *  summary="To update Space",
     *  description="To update Space",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(required=true,@OA\JsonContent(ref="#/components/schemas/UpdateSpaceRequest"),
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",
     *              description="To indicate server processed request properly",example="true"
     *          ),
     *          @OA\Property(property="data",type="object",description="Space Resource",
     *              ref="#/components/schemas/SpaceResource"
     *          ),
     *      ),
     *  ),
     *  @OA\Response(
     *     response=403,
     *     description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource")
     *  ),
     *  @OA\Response(
     *     response=422,
     *     description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource")
     *  ),
     *  @OA\Response(
     *     response=500,
     *     description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")
     *  ),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will be used for updating the space data and event's header line 1 and header line 2.
     * It also handles the update of event's venue type(Mono or Multi).
     * @info Venue types:-
     * 1. Mono- If venue type is mono then event will have only one space(Default space).
     * 2. Multi- If venue type is not mono then event can have multiple spaces
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param UpdateSpaceRequest $request
     * @return JsonResponse|SpaceResource
     */
    public function update(UpdateSpaceRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            // if request have mono type space
            if ($request->input('is_mono') == 1) {
                $space = $this->adminRepo()->kctSpaceRepository->findSpaceByUuid($request->space_uuid);
                $this->repo->kctSpaceRepository->updateSpace(
                    $space->space_uuid,
                    ['max_capacity' => $request->input('max_capacity')]
                );
                $event = $this->adminRepo()->eventRepository->findByEventUuid($space->event_uuid);
                $event->update(['is_mono_type' => 1]);
                $this->repo->eventRepository->updateSpaceHosts($space, $request->input('hosts'));
                $space->refresh();
                $space->load('hosts');
            } else {
                // to check if user can be the host for the space
                $space = $this->repo->kctSpaceRepository->findSpaceByUuid($request->input('space_uuid'));
                $param = $this->services->dataFactory->spaceUpdateParam($request, $space);
                $this->repo->kctSpaceRepository->updateSpace(
                    $request->input('space_uuid'),
                    $param['spaceData']
                );
                $this->repo->eventRepository->updateSpaceHosts($space, $param['spaceHosts']);
                $event = $space->event;
                if ($request->has('header_line_1') || $request->has('header_line_2')) {
                    $event->header_line_1 = $request->input('header_line_1', $event->header_line_1);
                    $event->header_line_2 = $request->input('header_line_2', $event->header_line_2);
                    // update the event settings
                    $settings = $event->event_settings;
                    $settings['is_self_header'] = (int)$request->input(
                        'is_self_header',
                        $settings['is_self_header'] ?? 0
                    );
                    $event->event_settings = $settings;
                    $event->update(['is_mono_type' => 0]);
                    $event->update();
                }
                $eventGroup = $space->event->group; // Fetching the group related to the event
                $organisers = $this->adminRepo()->groupRepository
                    ->getGroupUsers($eventGroup->id, 2)->pluck('id');
                $isOrganiser = in_array($param['spaceHosts'][0], $organisers->toArray()) ? 1 : 0;
                $roles = [
                    'is_organiser' => $isOrganiser,
                    'is_host'      => 0,
                ];
                $this->repo->eventRepository->addUserToEvent(
                    $event->event_uuid,
                    $param['spaceHosts'][0],
                    $space->space_uuid,
                    $roles
                );
                $space->refresh();
                $space->load('hosts');
            }
            DB::connection('tenant')->commit();
            return (new SpaceResource($space))->additional(['status' => true]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            $result = $this->handleIse($e);
        }
        return $result;
    }

    /**
     * @OA\Delete(
     *  path="/api/v1/admin/spaces",
     *  operationId="destroy",
     *  tags={"Space"},
     *  summary="To delete a space",
     *  description="To delete a space",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(required=true,@OA\JsonContent(ref="#/components/schemas/SpaceDeleteRequest")),
     *  @OA\Response(
     *      response=200,
     *      description="Deleted Space",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",
     *              description="To indicate server processed request properly",example="true"
     *          ),
     *          @OA\Property( property="data",type="boolean",
     *              description="To indicate server processed request properly",example="true"
     *          ),
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
     * @description This method is used for deleting a space from an event.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param SpaceDeleteRequest $request
     * @return JsonResponse
     */
    public function delete(SpaceDeleteRequest $request): JsonResponse {
        try {
            DB::connection('tenant')->beginTransaction();
            $space = $this->adminRepo()->kctSpaceRepository->findSpaceByUuid($request->space_uuid);
            $space->delete();
            DB::connection('tenant')->commit();
            return response()->json(['status' => true, 'data' => true,]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return $this->handleIse($e);
        }
    }

    /**
     * @OA\Get(
     *  path="/api/v1/admin/spaces",
     *  operationId="getEventSpaces",
     *  tags={"Space"},
     *  summary="To get all spaces of event",
     *  description="To get all the spaces of specific event",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="event_uuid",in="query",description="Event Uuid",required=true),
     *  @OA\Parameter(name="key",in="query",
     *     description="Key for filtering space type ex:- mono,normal",required=false,example="normal"),
     *  @OA\Response(
     *      response=200,
     *      description="Event Spaces",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",
     *              description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="array",description="To indicate server processed request properly",
     *              @OA\Items(ref="#/components/schemas/SpaceResource")),
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
     * @description This method is used for getting the event spaces and also fetches the scenery data for the specified
     * event.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function getEventSpaces(Request $request) {
        $validator = Validator::make($request->all(), [
            'event_uuid' => 'required|exists:tenant.events,event_uuid',
            'key'        => 'nullable|in:mono,normal'
        ]);
        if ($validator->fails()) {
            return $this->send422(implode(',', $validator->errors()->all()));
        }
        $event = $this->repo->eventRepository->findByEventUuid($request->input('event_uuid'));
        $spaces = $this->repo->eventRepository->getEventSpaces($request->input('event_uuid'));
        // add the space users
        $spaces->load('spaceUsers');
        if ($request->input('key') == 'mono') {
            $spaces = $spaces->slice(0, 1);
        }
        // prepare the scenery data for the event
        $allSceneryData = $this->adminServices()->superAdminService->getAllSceneryData();
        $eventSceneryData = [
            'all_scenery_data'     => EventSceneryResource::collection($allSceneryData),
            // fetching the current event's scenery data
            'current_scenery_data' => $this->services->dataFactory->fetchEventSceneryData($request->event_uuid),
            'header_info'          => [
                'header_line_1'  => $event->header_line_1,
                'header_line_2'  => $event->header_line_2,
                'is_mono_space'  => $event->is_mono_type,
                'is_self_header' => $event->event_settings['is_self_header'] ?? 0,
            ],
        ];
        return SpaceResource::collection($spaces)->additional([
            'status' => true,
            'meta'   => array_merge($this->adminServices()->validationService
                ->getEventState($request->event_uuid),
                $this->adminServices()->validationService->isEventMonoType(
                    $request->event_uuid), $eventSceneryData,
            ),
        ]);
    }

    /**
     * @OA\Post (
     *  path="/api/v1/admin/spaces/scenery",
     *  operationId="createSceneryData",
     *  tags={"Space"},
     *  summary="Create scenery data for the event",
     *  description="To create scenery data for the space",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(required=true,@OA\JsonContent(ref="#/components/schemas/EventSceneryRequest")),
     *  @OA\Response(
     *      response=200,
     *      description="Scenery data creted",
     *      @OA\JsonContent(
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
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for creating the scenery data and event related settings like header line 1,
     * header line 2,event venue type etc.
     * @note This scenery data will be used in HE(attendee) side for background image and colors on Event's dashboard
     * page.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param EventSceneryRequest $request
     * @return JsonResponse
     */
    public function createSceneryData(EventSceneryRequest $request): JsonResponse {
        try {
            DB::connection('tenant')->beginTransaction();
            $event = $this->repo->eventRepository->findByEventUuid($request->event_uuid);
            // update the header line mono type if present in FR request
            $event->header_line_1 = $request->input('header_line_1', $event->header_line_1);
            $event->header_line_2 = $request->input('header_line_2', $event->header_line_2);
            $event->is_mono_type = $request->input('is_mono_event', $event->is_mono_type);
            $settings = $event->event_settings;
            if ($request->has('is_self_header')) {
                $settings['is_self_header'] = $request->input('is_self_header');
            }
            $event->event_settings = $settings;
            $event->update();
            $param = $this->services->dataFactory->prepareDataForEventScenery($request, $event);
            $event->update(['event_settings' => $param]);
            DB::connection('tenant')->commit();
            return response()->json(['status' => true, 'data' => true], 201);
        } catch (Exception $exception) {
            DB::connection('tenant')->rollBack();
            return $this->handleIse($exception);
        }
    }
}
