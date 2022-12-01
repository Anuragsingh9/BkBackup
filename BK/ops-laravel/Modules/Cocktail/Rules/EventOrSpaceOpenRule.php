<?php

namespace Modules\Cocktail\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use Modules\Events\Entities\Event;
use Modules\Events\Service\ValidationService;

class EventOrSpaceOpenRule implements Rule {
    
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
     */
    public function __construct() {
    
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
        $event = Event::where('event_uuid', $value)->first();
        $result = true;
        if ($event) {
            if ($event->type != 'virtual') {
                $this->key = __('cocktail::message.event_must_virtual');
                $result = false;
            } else if (!ValidationService::getInstance()->isEventOrSpaceRunning($event)) {
                $this->key = __('cocktail::message.must_during');
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
