<?php

namespace Modules\Cocktail\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use Modules\Cocktail\Entities\EventSpace;

class SpaceFuture implements Rule {
    private $key;
    /**
     * @var string
     */
    private $time;
    
    /**
     * SpaceFuture constructor.
     * @param string $time
     */
    public function __construct($time = '') {
        $this->time = $time;
    }
    
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
        $eventSpace = EventSpace::with('event')->whereHas('event')->where('space_uuid', $value)->first();
        if (!$eventSpace) {
            $this->key = __('cocktail::message.invalid_space');
            return false;
        }
        $event = $eventSpace ? $eventSpace->event : null;
        if ($event) {
            if ($this->time == 'end') {
                $carbon = Carbon::createFromFormat('Y-m-d H:i:s', "$event->date $event->end_time");
            } else {
                $carbon = Carbon::createFromFormat('Y-m-d H:i:s', "$event->date $event->start_time");
            }
            if ($event->type != 'virtual') {
                $this->key = __('cocktail::message.event_must_virtual');
                return false;
            } else if (Carbon::now()->timestamp >= $carbon->timestamp) {
                $this->key = __('cocktail::message.event_must_future');
                return false;
            }
            return true;
        } else {
            $this->key = __('cocktail::message.invalid_event');
            return false;
        }
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
