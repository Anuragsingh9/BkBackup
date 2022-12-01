<?php

namespace Modules\Cocktail\Http\Controllers\V1\UserSideControllers;

use App\AccountSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Cocktail\Exceptions\CustomValidationException;
use Modules\Cocktail\Exceptions\NotExistsException;
use Modules\Cocktail\Http\Requests\V1\EventJoinRequest;
use Modules\Cocktail\Http\Requests\V1\UserDndRequest;
use Modules\Cocktail\Services\KctEventService;
use Exception;
use Modules\Cocktail\Transformers\AdminSide\EventUserResource;
use Modules\Cocktail\Transformers\UserSide\EventGraphicsResource;
use Auth;
use Modules\Cocktail\Transformers\UserSide\UserEventResource;
use Modules\Events\Service\ValidationService;

/**
 * Class KctEventController
 * @package Modules\Cocktail\Http\Controllers
 */
class KctEventController extends Controller {
    
    private $service;
    
    public function test() {
        return AccountSettings::where('account_id', 1)->first()->setting;
        
    }
    
    public function __construct() {
        $this->service = KctEventService::getInstance();
    }
    
    public function getEventGraphicsDetails(Request $request, $eventUuid) {
        try {
            DB::connection('tenant')->beginTransaction();
            $event = $this->service->getKeepContactCustomization($eventUuid);

            // mark user as present on proper condition
            $this->service->markUserPresent($eventUuid);
            
            $additional = $this->service->getGraphicsAdditional($event);
            
            DB::connection('tenant')->commit();
            return (new EventGraphicsResource($event))->additional(['status' => true, 'data' => $additional,]);
            
        } catch (NotExistsException $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 200);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }
    
    public function getEventEmbeddedUrl($eventUuid) {
        try {
            return response()->json(['status' => true, 'data' => $this->service->getEventEmbeddedUrl($eventUuid)], 200);
        } catch (CustomValidationException $e) {
            return $e->render();
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }
    
    public function getUserEvents(Request $request) {
        try {
            $events = $this->service->getEventsList($request);
            return UserEventResource::collection($events)->additional(['status' => true]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }
    
    public function joinEvent(EventJoinRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            
            if($request->has('space_uuid')){
                ValidationService::getInstance()->isSpaceHaveSeat(
                    $request->input('event_uuid'),
                    $request->input('space_uuid'),
                    true,
                    true
                );
            }
            
            $eventUser = $this->service->addCurrentUserToEvent($request);
            DB::connection('tenant')->commit();
            return (new EventUserResource($eventUser))->additional(['status' => true]);
        } catch (CustomValidationException $e) {
            return $e->render();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'error' => $e->getTrace()], 500);
        }
    }
    
    public function toggleDnd(UserDndRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $status = $this->service->updateUserDnd($request->input('active_state'), $request->input('event_uuid'));
            DB::connection('tenant')->commit();
            return response()->json(['status' => true, 'data' => ['status' => $status]], 200);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'error' => $e->getTrace()], 500);
        }
    }
    
}
