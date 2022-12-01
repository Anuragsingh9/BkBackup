<?php

namespace Modules\Cocktail\Http\Controllers\Generic\UserSideControllers;

use Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Cocktail\Http\Controllers\V1\UserSideControllers\EventSpaceController;
use Modules\Cocktail\Http\Controllers\V1\UserSideControllers\KctEventController;
use Modules\Cocktail\Http\Requests\V1\EventSpaceAddUserRequest;
use Modules\Cocktail\Services\KctService;


class KctController extends Controller {
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @version-2 KCT Controller
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @var \Modules\Cocktail\Http\Controllers\V2\UserSideControllers\KctController
     */
    private $kctV2Controller;
    /**
     * @var KctEventController
     */
    private $kctV1EventController;
    /**
     * @var EventSpaceController
     */
    private $kctV1SpaceController;
    
    public function __construct() {
        // version 2 KctController
        $this->kctV2Controller = app()->make(\Modules\Cocktail\Http\Controllers\V2\UserSideControllers\KctController::class);
        $this->kctV1EventController = app()->make(KctEventController::class);
        $this->kctV1SpaceController = app()->make(EventSpaceController::class);
    }
    
    public function initData(Request $request) {
        return $this->kctV2Controller->initData($request);
    }
    
    public function getEventsData(Request $request, $eventUuid) {
        $version = KctService::getInstance()->findEventVersion($eventUuid);
        if ($version == 2) {
            return $this->kctV2Controller->getEventData($request, $eventUuid);
        } else {
            return $this->kctV1EventController->getEventGraphicsDetails($request, $eventUuid);
        }
        
    }
    
    public function getSpacesAndConversation($eventUuid) {
        $version = KctService::getInstance()->findEventVersion($eventUuid);
        if ($version == 2) {
            return $this->kctV2Controller->getSpacesAndConversation($eventUuid);
        } else {
            return $this->kctV1SpaceController->getEventSpacesForUser($eventUuid);
        }
    }
    
    public function spaceJoin(Request $request) {
        $version = KctService::getInstance()->findEventVersionBySpace($request->input('space_uuid'));
        $request = $this->validateRequest($request, EventSpaceAddUserRequest::class);
        if ($version == 2) {
            return $this->kctV2Controller->spaceJoin($request);
        } else {
            return $this->kctV1SpaceController->spaceJoin($request);
        }
    }
    
    public function validateRequest($request, $class) {
        $request = new $class($request->all());
        $instance = Validator::make($request->all(), $request->rules());
        if (!$instance->passes()) {
            throw new HttpResponseException(response()->json([
                'status' => false,
                'msg'    => implode(',', $instance->errors()->all())
            ], 422));
        }
        return $request;
    }
}
