<?php

namespace Modules\Cocktail\Http\Controllers\V2\AdminSideControllers;

use Exception;
use App\Http\Controllers\CoreController;
use App\Services\WorkshopService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Cocktail\Exceptions\CustomValidationException;
use Modules\Cocktail\Http\Requests\V1\EventUserUpdateRequest;
use Modules\Cocktail\Http\Requests\V2\CreateVirtualEventRequest;
use Modules\Cocktail\Http\Requests\V2\UpdateVirtualEventRequest;
use Modules\Cocktail\Services\Contracts\ExternalEventFactory;
use Modules\Cocktail\Services\KctEventService;
use Modules\Cocktail\Services\V2Services\DataV2Service;
use Modules\Cocktail\Services\V2Services\KctCoreService;
use Modules\Cocktail\Transformers\AdminSide\EventUserResource;
use Modules\Cocktail\Transformers\V2\AdminSide\VirtualEventResourceV2;
use Modules\Events\Entities\Event;
use Modules\Events\Exceptions\CustomException;
use Modules\Events\Service\EventService;
use Modules\Events\Service\ValidationService;
use Modules\Events\Service\WordPressService;

class EventController extends Controller {
    
    /**
     * @var EventService
     */
    private $eventService;
    /**
     * @var CoreController
     */
    private $core;
    /**
     * @var ValidationService|null
     */
    private $validationService;
    
    /**
     * @var ExternalEventFactory
     */
    private $conferenceService;
    
    public function __construct(ExternalEventFactory $conferenceService) {
        $this->core = app(CoreController::class);
        $this->eventService = EventService::getInstance();
        $this->validationService = ValidationService::getInstance();
        $this->conferenceService = $conferenceService;
    }
    
    /**
     * @OA\POST(
     *  path="api/kct-admin/v2/events",
     *  operationId="store",
     *  tags={"KCT - V2 - Admin Side"},
     *  summary="To create a new Virtual Event",
     *  description="To create a new Virtual Type Event",
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(ref="#/components/schemas/CreateVirtualEventRequest"),
     *      ),
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="",
     *      @OA\JsonContent(
     *          @OA\Property(
     *              property="status",
     *              type="boolean",
     *              description="To indicate server processed request properly",
     *              example="true"
     *          ),
     *          @OA\Property(
     *              property="data",
     *              type="object",
     *              description="Virtual Event Resource",
     *              ref="#/components/schemas/VirtualEventResourceV2"
     *          ),
     *      ),
     *  ),
     *  @OA\Response(
     *      response=403,
     *      description="User Is Unauthorized",
     *      @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),
     *  ),
     *  @OA\Response(
     *      response=422,
     *      description="Data is not valid",
     *      @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),
     *  ),
     *  @OA\Response(
     *      response=500,
     *      description="Some Internal Server Issue Occuerred",
     *      @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  ),
     * ),
     *
     *
     * @param CreateVirtualEventRequest $request
     * @return VirtualEventResourceV2
     */
    public function store(CreateVirtualEventRequest $request) {
        $wpPostId = null;
        try {
            DB::connection('tenant')->beginTransaction();
            
            // adding fields for database column which are not related to kct events
            $request = DataV2Service::getInstance()->addFieldForEventCreate($request);
            
            $param = DataV2Service::getInstance()->eventCreateData($request);
            
            $event = KctCoreService::getInstance()->createEvent($request, $param);
            if (isset($param['eventData']['event_fields']['conference_type']) && $param['eventData']['event_fields']['conference_type']) {
                $conferenceParam = $this->conferenceService->prepareCreateParamFromRequest($request);
                $conference = $this->conferenceService->create($conferenceParam);
                KctCoreService::getInstance()->attachConferenceWithEvent($event, $conference);
            }
            $meta = KctCoreService::getInstance()->metaForEventVersion($event);
            
            DB::connection('tenant')->commit();
            return (new VirtualEventResourceV2($event))->additional(['status' => true, 'meta' => $meta]);
        } catch (CustomValidationException $e) {
            DB::connection('tenant')->rollback();
            $result = $e->render();
        } catch (CustomException $e) {
            DB::connection('tenant')->rollback();
            $result = $e->render();
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            $result = response()->json([
                'status' => false,
                'msg'    => 'Internal server error' . $e->getMessage(),
                'error'  => $e->getTrace(),
            ], 500);
        }
        return $result;
    }
    
    /**
     * @OA\POST(
     *  path="events/{event_id}",
     *  operationId="eventUpdate",
     *  tags={"KCT - V2 - Admin Side"},
     *  summary="To update a event",
     *  description="To modify a created event",
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(ref="#/components/schemas/UpdateVirtualEventRequest")
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="Event Updated",
     *      @OA\JsonContent (
     *          @OA\Property(
     *              property="status",
     *              type="boolean",
     *              description="To indicate server processed request properly",
     *              example="true"
     *          )
     *      ),
     *   ),
     *  @OA\Response(
     *      response=422,
     *      description="Data is not valid",
     *      @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),
     *  ),
     *  @OA\Response(
     *      response=500,
     *      description="Some Internal Server Issue Occuerred",
     *      @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  )
     * )
     * @param UpdateVirtualEventRequest $request
     * @param $event_id
     * @return JsonResponse|VirtualEventResourceV2
     * @throws Exception
     */
    public function update(UpdateVirtualEventRequest $request, $event_id) {
        try {
            DB::connection('tenant')->beginTransaction();
            $event = Event::find($event_id);
            
            // to check event must be either running or future in time
            if (!$this->validationService->isEventSpaceOpenOrFuture($event)) {
                return response()->json(['status' => false, 'msg' => __('cocktail::message.event_must_future')], 422);
            }
            
            // storing event data before update
            // to compare the data with new one so if changed then send the event to front users (e.g. via node socket).
            $beforeManualOpening = $event->manual_opening;
            $beforeEndTime = $event->end_time;
            
            $param = DataV2Service::getInstance()->eventUpdateData($request, $event);
            KctCoreService::getInstance()->updateEvent($request, $param, $event);
            
            if (KctCoreService::getInstance()->findEventConferenceType($event) != null) {
                $confParam = $this->conferenceService->prepareUpdateParamFromRequest($request);
                $this->conferenceService->update($event->bluejeans_id, $confParam);
            }
            // the meta to indicate the version of event for front team usage
            $meta = KctCoreService::getInstance()->metaForEventVersion($event);
            DB::connection('tenant')->commit();
            
            // this will emit the event to front if manual opening updated
            $this->eventService->emitManualOpeningEvent($beforeManualOpening, $request, $event);
            // this will emit the event to front if event end time changed during the event running
            KctCoreService::getInstance()->emitEventEndChangeEvent($beforeEndTime, $request, $event);
            
            return (new VirtualEventResourceV2($event))->additional([
                'msg'    => 'Record Updated Successfully',
                'status' => true,
                'meta'   => $meta,
            ]);
        } catch (CustomValidationException $e) {
            DB::connection('tenant')->rollback();
            return $e->render();
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => 'Internal Server Error ' . $e->getMessage(), 'error' => $e->getTrace()], 500);
        }
    }
    
    public function show(Request $request, $event_id) {
        $event = Event::find($event_id);
        if (!$event) {
            return response()->json(['status' => false, 'msg' => __("events::message.invalid_event")], 422);
        }
        
        $additional = KctCoreService::getInstance()->getEventGetAdditional($event, $request);
        $meta = KctCoreService::getInstance()->metaForEventVersion($event);
        return (new VirtualEventResourceV2($event))->additional([
            'data' => $additional,
            'meta' => $meta,
        ]);
    }
    
    public function eventUserUpdateRole(EventUserUpdateRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            // handle the presenter(1) or moderator(2) in new way
            KctCoreService::getInstance()->updateUserRole(
                $request->input('event_uuid'),
                $request->input('user_id'),
                $request->input('field'),
                $request->input('space_uuid'),
                $request->input('presence')
            );
            $result = KctEventService::getInstance()->getEventUsers(
                $request->input('event_uuid'),
                null,
                $request
            );
            $result = $result ? $result : new Collection();
            $additional = KctEventService::getInstance()->getEventUserMeta($request->event_uuid);
            DB::connection('tenant')->commit();
            return EventUserResource::collection($result)->additional([
                'status' => true,
                'meta'   => $additional,
            ]);
        } catch (CustomValidationException $e) {
            DB::connection('tenant')->rollback();
            return $e->render();
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
}
