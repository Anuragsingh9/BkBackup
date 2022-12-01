<?php

namespace Modules\Cocktail\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use Modules\Events\Entities\Event;
use Modules\Events\Service\ValidationService;

/**
 *
 * |---------------------------------*space_open*--------*event_start*------------*event_end*------------*space_end*----------|
 * |-----current time must be here--|
 *
 * To check that event and space both should not be started yet
 *
 * Class EventAndSpaceNotStarted
 * @package Modules\Cocktail\Rules
 */
class EventAndSpaceNotStarted implements Rule {
    
    /**
     * @var string
     */
    private $key;
    
    /**
     * @var string
     */
    private $eventKey;
    
    /**
     * EventAndSpaceOpenOrNotStarted constructor.
     * @param string $eventKey
     */
    public function __construct($eventKey = 'event_uuid') {
        $this->eventKey = $eventKey;
    }
    
    /**
     * Checks the following
     * 1. Event exists
     * 2. Events is virtual type
     * 3. Event or its any space not yet started
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
                $this->key = __('cocktail::message.event_must_virtual');
                $result = false;
            } else if (!ValidationService::getInstance()->isEventSpaceFuture($event)) {
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
