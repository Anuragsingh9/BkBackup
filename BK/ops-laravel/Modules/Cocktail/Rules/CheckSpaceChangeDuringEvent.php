<?php

namespace Modules\Cocktail\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Events\Service\ValidationService;

class CheckSpaceChangeDuringEvent implements Rule {
    /**
     * @var array|string|null
     */
    private $msg;
    
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $eventUuid) {
        if (ValidationService::getInstance()->isEventRunning($eventUuid)) {
            $this->msg = __("cocktail::message.space_cannot_change_during");
            return false;
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
