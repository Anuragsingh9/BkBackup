<?php

namespace Modules\Cocktail\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use Modules\Events\Entities\Event;
use Modules\Events\Service\ValidationService;

class EventAndSpaceOpenOrNotStarted implements Rule {

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $eventKey;
    
    /**
     * @var bool
     */
    private $isIntAllowed;
    
    /**
     * EventAndSpaceOpenOrNotStarted constructor.
     * @param string $eventKey
     * @param bool $isIntAllowed
     */
    public function __construct($eventKey = 'event_uuid', $isIntAllowed = false) {
        $this->eventKey = $eventKey;
        $this->isIntAllowed = $isIntAllowed;
    }
    
    /**
     * Check
     * event exists
     * event virtual
     * event is either started or future
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
        $event = Event::where($this->eventKey, $value)->first();
        $result = true;
        if ($event) {
            if ($event->type != 'virtual') {
                if ($this->isIntAllowed) {
                    if (!ValidationService::getInstance()->isEventFuture($event)) {
                        $this->key = __('cocktail::message.event_must_future');
                        return false;
                    }
                } else {
                    $this->key = __('cocktail::message.event_must_virtual');
                    $result = false;
                }
            } else if (!ValidationService::getInstance()->isEventSpaceOpenOrFuture($event)) {
                $this->key = __('cocktail::message.event_must_future');
                $result = false;
            }
        } else {
            $this->key = __('cocktail::message.invalid_event');
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
        return $this->key;
    }
}
