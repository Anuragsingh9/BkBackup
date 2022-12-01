<?php

namespace Modules\Cocktail\Http\Controllers\Generic\AdminSideControllers;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Cocktail\Http\Requests\V2\UpdateVirtualEventRequest;
use Modules\Cocktail\Services\KctService;
use Modules\Events\Entities\Event;
use Modules\Events\Http\Requests\UpdateEventRequest;
use Validator;
use Modules\Cocktail\Http\Requests\V2\CreateVirtualEventRequest;
use Modules\Events\Http\Controllers\EventsController;
use Modules\Events\Http\Requests\CreateEventRequest;

class EventController extends Controller {
    
    /**
     * @var EventsController
     */
    private $eventsEventController;
    
    /**
     * @var \Modules\Cocktail\Http\Controllers\V2\AdminSideControllers\EventController
     */
    private $kctV2EventController;
    
    public function __construct() {
        $this->eventsEventController = app()->make(EventsController::class);
        $this->kctV2EventController = app()->make(\Modules\Cocktail\Http\Controllers\V2\AdminSideControllers\EventController::class);
    }
    
    public function store(Request $request) {
        if ($request->input('type') == config('events.event_type.virtual')
            // commented this condition as both bj , non bj and zoom event will be created in new interface now so,
            // if the event is virtual create it with this new interface
            // && !$request->input('event_uses_bluejeans_event')
        ) {
            // event has virtual type and does not use bluejeans
            // send to v2
            $request = $this->validateRequest($request, CreateVirtualEventRequest::class);
            return $this->kctV2EventController->store($request);
        } else {
            // event is v1 type and send to Events controller
            $request = $this->validateRequest($request, CreateEventRequest::class);
            return $this->eventsEventController->store($request);
        }
    }
    
    public function show(Request $request, $event_id) {
        $version = KctService::getInstance()->findEventVersion($event_id);
        if ($version == 2) {
            return $this->kctV2EventController->show($request, $event_id);
        } else {
            return $this->eventsEventController->show($request, $event_id);
        }
    }
    
    
    public function update(Request $request, $event_id) {
        $version = KctService::getInstance()->findEventVersion($event_id);
        $event = Event::find($event_id);
        $request->merge([
            'id'   => $event_id,
            'type' => $event ? $event->type : '',
        ]);
        if ($version == 2) {
            $request = $this->validateRequest($request, UpdateVirtualEventRequest::class);
            return $this->kctV2EventController->update($request, $event_id);
        } else {
            $request = $this->validateRequest($request, UpdateEventRequest::class);
            return $this->eventsEventController->update($request, $event_id);
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

