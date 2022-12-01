<?php

namespace Modules\Cocktail\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Events\Entities\Event;

class EventExists implements Rule {
    
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
        return (boolean)Event::where('event_uuid', $value)->whereIn('type', ['virtual'])->count();
    }
    
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return $this->key = __('cocktail::message.invalid_event');
    }
}
