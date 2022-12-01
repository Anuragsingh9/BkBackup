<?php

namespace Modules\Cocktail\Http\Controllers\Generic\AdminSideControllers;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Cocktail\Http\Controllers\V1\AdminSideControllers\EventSpaceController;
use Modules\Cocktail\Http\Requests\V1\EventSpaceUpdateRequest;
use Modules\Cocktail\Http\Requests\V2\UpdateSpaceRequestV2;
use Validator;
use Modules\Cocktail\Http\Requests\V1\EventSpaceCreateRequest;
use Modules\Cocktail\Http\Requests\V2\CreateSpaceRequestV2;
use Modules\Cocktail\Services\KctService;

class SpaceController extends Controller {
    
    /**
     * @var EventSpaceController
     */
    private $v1SpaceController;
    /**
     * @var \Modules\Cocktail\Http\Controllers\V2\AdminSideControllers\SpaceController
     */
    private $v2SpaceController;
    
    public function __construct() {
        $this->v1SpaceController = app()->make(EventSpaceController::class);
        $this->v2SpaceController = app()->make(\Modules\Cocktail\Http\Controllers\V2\AdminSideControllers\SpaceController::class);
    }
    
    public function store(Request $request) {
        $version = KctService::getInstance()->findEventVersion($request->event_uuid);
        if ($version == 2) {
            $request = $this->validateRequest($request, CreateSpaceRequestV2::class);
            return $this->v2SpaceController->store($request);
        } else {
            $request = $this->validateRequest($request, EventSpaceCreateRequest::class);
            return $this->v1SpaceController->store($request);
        }
    }
    
    public function update(Request $request) {
        $version = KctService::getInstance()->findEventVersionBySpace($request->space_uuid);
        if ($version == 2) {
            $request = $this->validateRequest($request, UpdateSpaceRequestV2::class);
            return $this->v2SpaceController->update($request);
        } else {
            $request = $this->validateRequest($request, EventSpaceUpdateRequest::class);
            return $this->v1SpaceController->update($request);
        }
    }
    
    public function getSpace(Request $request) {
        $version = KctService::getInstance()->findEventVersionBySpace($request->space_uuid);
        if ($version == 2) {
            return $this->v2SpaceController->getSpace($request);
        } else {
            return $this->v1SpaceController->getSpace($request);
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
    
    public function getEventSpacesForAdmin($eventUuid) {
        $version = KctService::getInstance()->findEventVersion($eventUuid);
        if ($version == 2) {
            return $this->v2SpaceController->getEventSpacesForAdmin($eventUuid);
        } else {
            return $this->v1SpaceController->getEventSpacesForAdmin($eventUuid);
        }
    }
}
