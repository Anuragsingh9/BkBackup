<?php

namespace Modules\KctUser\Rules\V1;

use Illuminate\Contracts\Validation\Rule;
use Modules\KctUser\Services\KctUserValidationService;

class EventHostRule implements Rule {
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($event_id = null) {
        $this->event_id = $event_id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
        if (is_array($value)) {
            $isAlreadyHost = KctUserValidationService::getInstance()->isUsersAlreadySpaceHost($this->event_id, $value, 'id');
            if ($isAlreadyHost) {
                $this->msg = __('kctuser::message.user_already_host_of_space');
                return false;
            }
        }
        return true;
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
