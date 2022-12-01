<?php

namespace Modules\Events\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use Modules\Events\Entities\Event;
use Modules\Events\Service\EventService;
use Modules\Events\Service\ValidationService;

class EventEndTimeRule implements Rule {
    private $startTime;
    /**
     * @var string
     */
    private $type;
    private $event;
    private $eventId;
    private $date;
    /**
     * @var array|string|null
     */
    private $msg;
    /**
     * @var ValidationService|null
     */
    private $validation;
    
    /**
     * Create a new rule instance.
     *
     * @param $date
     * @param $eventId
     * @param $start_time
     * @param null $type
     */
    public function __construct($date, $eventId, $start_time, $type = null) {
        $this->date = $date;
        if ($type) {
            $this->type = $type;
        } else {
            $this->event = Event::find($eventId);
            $this->eventId = $eventId;
            $this->type = $this->event ? $this->event->type : '';
        }
        $this->startTime = $start_time;
        $this->validation = ValidationService::getInstance();
    }
    
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $endTime
     * @return bool
     */
    public function passes($attribute, $endTime) {
        $result = true;
        
        if (!$this->isRelativeFieldValid()) {
            return true; // other fields are not valid let there validation handle further
        }
        
        if (!$this->isFormatValid($endTime)) {
            return false;
        }
        
        if ($this->type == config('events.event_type.int') || $this->type == config('events.event_type.ext')) {
            $result = $this->validateIntExtEvent($endTime);
        } else if ($this->type == config('events.event_type.virtual')) {
            $result = $this->validateVirtualEvent($endTime);
        }
        return $result;
    }
    
    /**
     * To check that other relative fields are validated or not
     *
     * @return bool
     */
    private function isRelativeFieldValid() {
        if (!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', "$this->date $this->startTime")) {
            return false; // date validation will handle the further error
        }
        return true;
    }
    
    /**
     * To check that end time has the correct format or not;
     *
     * @param $endTime
     * @return bool
     */
    private function isFormatValid($endTime) {
        if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $endTime)) {
            $this->msg = __('validation.date_format', ['format' => 'H:i:s']);
            return false;
        }
        return true;
    }
    
    /**
     * To validate the internal or external event end time
     *
     * @param $endTime
     * @return bool
     */
    private function validateIntExtEvent($endTime) {
        if (!$this->validation->isFutureTime($this->date, $endTime)) {
            $this->msg = __('events::message.end_time_future');
            return false;
        }
        return true;
    }
    
    
    /**
     * First if event started assume date is of today
     *
     * Check event end date time is future or not
     * Check event end date time slot is available or not
     *
     * @param $endTime
     * @return bool
     */
    private function validateVirtualEvent($endTime) {
        if ($this->validation->isEventOrSpaceRunning($this->eventId)) { // event started so we need to check end time must be future with current date
            // we only allowing to extend the time not reduce
            $this->date = Carbon::now()
                ->toDateString(); // this will protect to during event update admin does not end event suddenly
        }
        
        $result = true;
        if (!$this->validation->isFutureTime($this->date, $endTime)) {
            $this->msg = __('events::message.end_time_future');
            $result = false;
        } else if (!$this->validation->isTimeSlotAvailable($this->date, $this->type, $this->eventId ? [$this->eventId] : [], $endTime, $this->startTime)) {
            $this->msg = __('events::message.end_time_busy');
            $result = false;
        }
        return $result;
    }
    
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return $this->msg;
    }
}
