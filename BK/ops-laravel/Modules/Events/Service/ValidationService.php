<?php


namespace Modules\Events\Service;

use App\Services\Service;
use App\User;
use Carbon\Carbon;
use Modules\Cocktail\Entities\EventSpace;
use Modules\Cocktail\Exceptions\CustomValidationException;
use Modules\Events\Entities\Event;
use Auth;


/**
 * This class is to help to validate the data
 *
 * Class ValidationService
 * @package Modules\Events\Service
 */
class ValidationService extends Service {
    
    private $eventsWithSpaces;
    const DT_FORMAT = 'Y-m-d H:i:s';
    private $manuallyOpenedEvent = [];
    
    /**
     * |---------------------------------*space_open*--------*event_start*------------*event_end*------------*space_end*----------|
     * |-----current time must be here--|
     *
     * This will check the event
     * Event Not started
     * Future
     * No Space open
     *
     * @param $event
     * @return bool
     */
    public function isEventSpaceFuture($event) {
        if ($event) {
            $start = $this->getEventMaxBefore($event);
            $current = Carbon::now()->timestamp;
            return ($current < $start);
        }
        return false;
    }
    
    /**
     * |---------*space_open*----------------*event_start*----------------------*event_end*--------------*space_end*------------------|
     * |------current time must be here------|
     *
     * This will check the event
     * either started
     * or yet to start
     * or space is still opened
     *
     * @param $event
     * @return bool
     */
    public function isEventFuture($event) {
        if ($event) {
            $date = $event->date;
            $start = Carbon::createFromFormat(self::DT_FORMAT, "$date $event->start_time")->timestamp;
            $current = Carbon::now()->timestamp;
            return ($current < $start);
        }
        return false;
    }
    
    /**
     * |---------*space_open*-------------*event_start*---------------*event_end*--------------*space_end*------------------|
     * |---------current time must be here---------------------------------------------------------------|
     *
     * This will check the event
     * either started
     * or yet to start
     * or space is still opened
     *
     * @param $event
     * @return bool
     */
    public function isEventSpaceOpenOrFuture($event) {
        $event = $this->resolveEvent($event);
        if ($event) {
            $end = $this->getEventMaxAfter($event);
            $current = Carbon::now()->timestamp;
            return ($current < $end);
        }
        return false;
    }
    
    /**
     * |---------*space_open*-------------*event_start*---------------*event_end*--------------*space_end*------------------|
     *          |---------current time must be here------------------------------------------------------|
     *
     * This will check the event
     * Event or any its space is opened
     * will return false if event is past (ended) or not yet started (future)
     *
     * @param $event
     * @return bool
     */
    public function isEventOrSpaceRunning($event) {
        $event = $this->resolveEvent($event);
        if ($event) {
            
            if ($this->isManuallyOpen($event)) {
                return true;
            }
            
            $end = $this->getEventMaxAfter($event);
            $start = $this->getEventMaxBefore($event);
            $current = Carbon::now()->timestamp;
            return ($start <= $current && $current < $end);
        }
        return false;
    }
    
    /**
     * |-----*space_open*--------------------------------*event_start*-------*event_end*--------------*space_end*------------------|
     *                  |---current time must be here---|
     *
     * This will check the event
     * either started
     * or yet to start
     * or space is still opened
     *
     * @param $event
     * @return bool
     */
    public function isEventSpaceOpenBefore($event) {
        $event = $this->resolveEvent($event);
        if ($event) {
            $current = Carbon::now()->timestamp;
            $eventStart = Carbon::createFromFormat(self::DT_FORMAT, "$event->date $event->start_time")->timestamp;
            $eventMaxBefore = $this->getEventMaxBefore($event);
            return $eventMaxBefore <= $current && $current < $eventStart;
        }
        return false;
    }
    
    /**
     * |-------*space_open*-----*event_start*----------------------------------*event_end*------*space_end*-----------|
     *                                      |----current time must be here----|
     *
     * This will check the event
     * either started
     * or yet to start
     * or space is still opened
     *
     * @warn handle the event exists this method will return null if event not found
     * @param Event|string|int $event
     * @return bool
     */
    public function isEventRunning($event) {
        $event = $this->resolveEvent($event);
        if ($event) {
            
            $start = Carbon::createFromFormat(self::DT_FORMAT, "{$event->date} {$event->start_time}")->timestamp;
            $end = Carbon::createFromFormat(self::DT_FORMAT, "{$event->date} {$event->end_time}")->timestamp;
            
            $current = Carbon::now()->timestamp;
            return ($start <= $current && $current < $end);
        }
        return null;
    }
    
    /**
     * |-----*space_open*-----*event_start*-------*event_end*---------------------------------*space_end*------------------|
     *                                                       |---current time must be here---|
     *
     * This will check the event
     * either started
     * or yet to start
     * or space is still opened
     *
     * @param $event
     * @return bool
     */
    public function isEventSpaceOpenAfter($event) {
        $event = $this->resolveEvent($event);
        if ($event) {
            $current = Carbon::now()->timestamp;
            $eventEnd = Carbon::createFromFormat(self::DT_FORMAT, "$event->date $event->end_time")->timestamp;
            $eventMaxAfter = $this->getEventMaxAfter($event);
            return $eventEnd <= $current && $current < $eventMaxAfter;
        }
        return false;
    }
    
    /**
     * |---------*space_open*-----------*event_start*---------------*event_end*--------------*space_end*------------------|
     *                                                                                                 |------current time must be here------|
     *
     * This will check the event
     * either started
     * or yet to start
     * or space is still opened
     *
     * @param $event
     * @return bool
     */
    public function isEventEnded($event) {
        $event = $this->resolveEvent($event);
        if ($event) {
            $end = $this->getEventMaxAfter($event);
            $current = Carbon::now()->timestamp;
            return ($end < $current);
        }
        return false;
    }
    
    /**
     * To check that the given date and time belongs to future or not
     *
     * @param $date
     * @param $time
     * @return bool
     */
    public function isFutureTime($date, $time) {
        $todayDate = Carbon::now()->toDateString();
        $result = true;
        if ($date != $todayDate) {
            $result = $date > $todayDate;
        } else {
            $carbon = Carbon::createFromFormat(self::DT_FORMAT, "$date $time");
            if ($carbon->timestamp < Carbon::now()->timestamp) {
                $result = false;
            }
        }
        return $result;
    }
    
    /**
     * @param string $date
     * @param string $time1 // the main time to check
     * @param string $time2 // if this provided that this will check $time1 is not inside the event and there if also no other event between $time2 - $time1
     * @param string $type
     * @param array $excludeEvents
     * @return bool
     */
    public function isTimeSlotAvailable($date, $type, $excludeEvents, $time1, $time2) {
        $events = $this->getEventsOfDate($date, $type, $excludeEvents);
        if ($type == config('events.event_type.virtual')) {
            $time1 = Carbon::createFromFormat(self::DT_FORMAT, "$date $time1")->timestamp;
            $time2 = $time2 ? Carbon::createFromFormat(self::DT_FORMAT, "$date $time2")->timestamp : null;
            foreach ($events as $evt) {
                $s = $this->getEventMaxBefore($evt);
                $e = $this->getEventMaxAfter($evt);
                if (($time1 >= $s && $time1 <= $e) || ($time2 && ($time2 <= $s && $e <= $time1))) {
                    // either time 1 is between another event
                    // or if time2 given time2 - time1 have another event inside
                    // e.g $time2 -> $e started -> $e ended -> $time1
                    return false;
                }
            }
        }
        return true;
    }
    
    public function isManualOpeningPossible($date, $startTime, $endTime) {
        $result = true;
        $start = $this->prepareAcceptableStartTime($date, $startTime);
        $end = Carbon::createFromFormat(self::DT_FORMAT, "$date $endTime")->timestamp;
        
        $current = Carbon::now()->timestamp;
        if ($current <= $start || $end <= $current) { // current time is less than start (even allowed before), or current time is after event end
            $result = false;
        }
        return $result;
    }
    
    /**
     * @warn if space not found it will return true so handle space not found
     * @param string|EventSpace $space
     * @return bool
     */
    public function isSpaceOpen($space) {
        $space = $this->resolveSpace($space);
        if ($space) {
            $start = $this->getSpaceStart($space);
            $end = $this->getSpaceEnd($space);
            $during = $this->getSpaceDuring($space);
            
            
            $current = Carbon::now()->timestamp;
            if ($current < $start || $end <= $current) { // either space not started or ended
                return false;
            }
            
            $eventStart = Carbon::createFromFormat(self::DT_FORMAT, "{$space->event->date} {$space->event->start_time}")->timestamp;
            $eventEnd = Carbon::createFromFormat(self::DT_FORMAT, "{$space->event->date} {$space->event->end_time}")->timestamp;
            
            if (($eventStart <= $current && $current < $eventEnd) && !$during) { // current time is in event and during not allowed
                return false;
            }
            return true;
        }
        return false;
    }
    
    /*
     *
     * HELPER Methods only
     *
     *
     */
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to find the event by event object or by event id or uuid
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return mixed|Event
     */
    public function resolveEvent($event) {
        if ($event instanceof Event) {
            return $event;
        } else if (is_numeric($event)) {
            $event = Event::find($event);
        } else if (is_string($event)) {
            $event = Event::where('event_uuid', $event)->first();
        }
        return $event;
    }
    
    /**
     * @param EventSpace $space
     * @return EventSpace|null
     */
    private function resolveSpace($space) {
        if ($space instanceof EventSpace) {
            return $space;
        } else if (is_string($space)) {
            return EventSpace::with('event')->find($space);
        }
        return null;
    }
    
    private function getEventsOfDate($date, $type, $exclude = []) {
        if (!$this->eventsWithSpaces) { // this like caching  so if we need further this result in same request we don't need to hit the whole query
            $this->eventsWithSpaces = Event::with('spaces')
                ->where('type', $type)
                ->where('date', $date)
                ->whereNotIn('id', $exclude)
                ->get();
        }
        return $this->eventsWithSpaces;
    }
    
    /**
     *  To get the maximum before time of the event
     *  when the first space will start
     *
     * @param $event
     * @return int
     */
    public function getEventMaxBefore($event) {
        $startTime = $this->getMaxOpeningSeconds($event, 'before');
        $carbon = Carbon::createFromFormat(self::DT_FORMAT, "$event->date $event->start_time")->timestamp;
        return $carbon - $startTime;
    }
    
    /**
     * To get the maximum after time of the event
     *
     * @param $event
     * @return int
     */
    public function getEventMaxAfter($event) {
        $endTime = $this->getMaxOpeningSeconds($event, 'after');
        $carbon = Carbon::createFromFormat(self::DT_FORMAT, "$event->date $event->end_time")->timestamp;
        return $carbon + $endTime;
    }
    
    
    /**
     * returns the first opening hour of event
     * either event opening hour or any space which is opening first will be calculated first
     *
     * @param Event $event
     * @param string $openingType
     * @return int // return seconds
     */
    public function getMaxOpeningSeconds($event, $openingType) {
        if (!$event->relationLoaded('spaces')) {
            $event->load('spaces');
        }
        $max = isset($event->event_fields['opening_hours'][$openingType])
            ? $event->event_fields['opening_hours'][$openingType]
            : 0;
        
        foreach ($event->spaces as $space) {
            if ($space->opening_hours[$openingType] > $max) {
                $max = $space->opening_hours[$openingType];
            }
        }
        return $max * 60;
    }
    
    private function prepareAcceptableStartTime($date, $startTime) {
        $start = Carbon::createFromFormat(self::DT_FORMAT, "$date $startTime");
        return $start->timestamp - config('events.validations.manual_opening_possible'); // reducing seconds so we can allow to set to 1 if event have defined time left to start
    }
    
    
    /**
     * To get the start time in time format of space by opening hour
     *
     * @param EventSpace $space
     * @return int
     */
    private function getSpaceStart($space) {
        $openingBefore = isset($space->opening_hours['before']) ? $space->opening_hours['before'] : 0;
        $openingBefore *= 60; // converting to seconds
        $start = Carbon::createFromFormat(self::DT_FORMAT, "{$space->event->date} {$space->event->start_time}")->timestamp;
        return $start - $openingBefore; // reducing opening before from event start time
    }
    
    /**
     * To get the start time in time format of space by opening hour
     *
     * @param EventSpace $space
     * @return int
     */
    private function getSpaceEnd($space) {
        $openingBefore = isset($space->opening_hours['after']) ? $space->opening_hours['after'] : 0;
        $openingBefore *= 60; // converting to seconds
        $end = Carbon::createFromFormat(self::DT_FORMAT, "{$space->event->date} {$space->event->end_time}")->timestamp;
        return $end + $openingBefore; // reducing opening before from event start time
    }
    
    private function getSpaceDuring($space) {
        return isset($space->opening_hours['during']) ? $space->opening_hours['during'] : 0;
    }
    
    /**
     * To check the space is full or not
     *
     * @param $eventUuid
     * @param $spaceUuid
     * @param bool $allowDefault
     * @param bool $allowException
     * @return bool
     * @throws CustomValidationException
     */
    public function isSpaceHaveSeat($eventUuid, $spaceUuid, $allowDefault = false, $allowException = false) {
        
        if ($allowDefault) {
            $defaultSpace = EventSpace::where('event_uuid', $eventUuid)->orderBy('created_at', 'asc')->first();
            if ($defaultSpace && $spaceUuid == $defaultSpace->space_uuid) {
                return true;
            }
        }
        
        $space = EventSpace::with(['spaceUsers' => function ($q) {
            $q->where('user_id', '!=', Auth::user()->id);
        }])->where('space_uuid', $spaceUuid)->first();
        
        if ($space && $space->max_capacity && $space->spaceUsers->count() >= $space->max_capacity) {
            // if space not found other validation handling
            if ($allowException) {
                throw new CustomValidationException('space_full', '', 'message');
            } else {
                return false;
            }
        }
        return true;
    }
    
    /**
     *
     * To check if event is manually opened within appropriate timing
     *
     * @warn it will return true if event not found
     *
     * @param $event
     * @return bool
     */
    public function isManuallyOpen($event) {
        $event = $this->resolveEvent($event);
        
        if (!$event) {
            return true;
        }
        
        if (isset($this->openEvent["$event->id"])) {
            return (bool)$this->manuallyOpenedEvent["$event->id"];
        }
        
        $this->manuallyOpenedEvent["$event->id"] = 0;
        $result = false;
        
        if ($event->manual_opening) {
            if ($this->isEventRunning($event)) {
                $this->manuallyOpenedEvent["$event->id"] = 1;
                $result = true;
            } else {
                $start = Carbon::createFromFormat('Y-m-d H:i:s', "$event->date $event->start_time");
                
                $timeBetweenEventStartAndCurrent = $start->timestamp - Carbon::now()->timestamp;
                
                if ($timeBetweenEventStartAndCurrent <= config('events.validations.manual_opening_possible') && $timeBetweenEventStartAndCurrent >= 0) {
                    $this->manuallyOpenedEvent["$event->id"] = 1;
                    $result = true;
                }
            }
        }
        return $result;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description this will try to resolve the user variable and return the User object
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param User|string $user
     * @return User
     */
    public function resolveUser($user) {
        if($user instanceof User) {
            return $user;
        } else if (is_numeric($user)) {
            return User::find($user);
        }
        return $user;
    }
}
