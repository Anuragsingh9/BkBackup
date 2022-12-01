<?php

namespace Modules\Events\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use Modules\Events\Entities\Event;
use Modules\Events\Service\EventService;
use Modules\Events\Service\ValidationService;

class EventStartTimeRule implements Rule {
    
    /**
     * @var string
     */
    private $date;
    /**
     * @var string
     */
    private $type;
    /**
     * @var Event
     */
    private $event;
    /**
     * @var array|string|null
     */
    private $msg;
    /**
     * @var int
     */
    private $eventId;
    /**
     * @var ValidationService|null
     */
    private $validation;
    
    /**
     * Create a new rule instance.
     *
     * @param string $date
     * @param int $eventId
     * @param string $type
     */
    public function __construct($date, $eventId, $type = null) {
        $this->date = $date;
        if ($type) {
            $this->type = $type;
        } else {
            $this->event = Event::find($eventId);
            $this->eventId = $eventId;
            $this->type = $this->event ? $this->event->type : '';
        }
        $this->validation = ValidationService::getInstance();
    }
    
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
        $result = true;
        if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $value)) {
            $this->msg = __('validation.date_format', ['format' => 'H:i:s']);
            return false;
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->date)) {
            return true; // date validation will handle the further error
        }
        if ($this->type == config('events.event_type.int') || $this->type == config('events.event_type.ext')) {
            $result = $this->validateIntExtEvent($value);
        } else if ($this->type == config('events.event_type.virtual')) {
            $result = $this->validateVirtualEvent($value);
        }
        return $result;
    }
    
    /**
     * @param $value
     * @return bool
     */
    public function validateIntExtEvent($value) {
        $result = true;
        if (!$this->validation->isFutureTime($this->date, $value)) {
            $this->msg = __('events::message.date_must_future');
            $result = false;
        }
        return $result;
    }
    
    /**
     * @param $value
     * @return bool
     */
    public function validateVirtualEvent($value) {
        $result = true;
        if ($this->event && $this->validation->isEventOrSpaceRunning($this->event)) {
            return true;
        }
        if (!$this->validation->isFutureTime($this->date, $value)) {
            $this->msg = __('events::message.date_must_future');
            $result = false;
        } else {
            if (!$this->validation->isTimeSlotAvailable($this->date, $this->type, $this->eventId ? [$this->eventId] : [], $value, null)) {
                $this->msg = __('events::message.start_time_busy');
                $result = false;
            }
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
