<?php

namespace Modules\Cocktail\Services\V2Services;

use Carbon\Carbon;
use App\Services\Service;
use Illuminate\Support\Facades\Auth;
use Modules\Cocktail\Entities\EventSpace;
use Modules\Cocktail\Entities\EventUser;
use Modules\Events\Service\ValidationService;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class job is to validate the things are correct for further processing or not
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class ValidationV2Service
 * @package Modules\Cocktail\Services\V2Services
 */
class ValidationV2Service extends Service {
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To find the type of param and return the space accordingly
     * -----------------------------------------------------------------------------------------------------------------
     * @param $space
     * @return EventSpace|null
     */
    public function resolveSpace($space) {
        if ($space instanceof EventSpace) {
            // space object  passed so just return it
            return $space;
        } else if (is_string($space)) {
            // space uuid is passed so fetch and return it
            return EventSpace::find($space);
        }
        return null;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To find if space is future or not
     * @warn if space not found it will return @true;
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $space
     * @return boolean
     */
    public function isSpaceFuture($space) {
        $space = $this->resolveSpace($space);
        if ($space) {
            $space->load("event");
            return $this->isEventFuture($space->event);
        }
        return true;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To find if event is future or not
     * @warn if event not found it will return @true;
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return boolean
     */
    public function isEventFuture($event) {
        $event = ValidationService::getInstance()->resolveEvent($event);
        if ($event) {
            $start = Carbon::createFromFormat(ValidationService::DT_FORMAT, "{$event->date} {$event->start_time}")->timestamp;
            $current = Carbon::now()->timestamp;
            return ($current < $start);
        }
        return true;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To find if there is already a vip space created in event or not
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @return bool
     */
    public function isVipCreated($eventUuid, $exclude) {
        return !!EventSpace::where('event_uuid', $eventUuid)
            ->where('is_vip_space', 1)
            ->where('space_uuid', '!=', $exclude)
            ->count();
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To find if there is already a duo space created in event or not
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @param null $exclude
     * @return bool
     */
    public function isDuoCreated($eventUuid, $exclude = null) {
        return (bool) EventSpace::where('event_uuid', $eventUuid)
            ->where('is_duo_space', 1)
            ->where('space_uuid', '!=', $exclude)
            ->count();
    }
    
    public function validateUserPassedJoinEvent($eventUuid) {
        return EventUser::where('event_uuid', $eventUuid)->where('user_id', Auth::user()->id)->where('is_joined_after_reg', 1)->count();
    }

}
