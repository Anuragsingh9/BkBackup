<?php

namespace Modules\KctUser\Rules\V1;

use Illuminate\Contracts\Validation\Rule;
use Modules\KctAdmin\Entities\Event;

class EventExists implements Rule {

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
        return (boolean)Event::where('event_uuid', $value)->count();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return $this->key = __('kctuser::message.invalid_event');
    }
}
