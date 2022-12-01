<?php

namespace Modules\Cocktail\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Events\Service\ValidationService;

class EventAndSpaceMustEndedRule implements Rule {
    /**
     * @var string
     */
    private $msg;
    
    
    public function __construct($msg) {
        $this->msg = $msg;
    }
    
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param $eventUuid
     * @return bool
     */
    public function passes($attribute, $eventUuid) {
        if (ValidationService::getInstance()->isEventEnded($eventUuid)) {
            return true;
        }
        return false;
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
