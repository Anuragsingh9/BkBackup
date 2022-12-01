<?php

namespace Modules\Events\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Events\Service\ValidationService;

class ManualOpeningRule implements Rule {
    private $date;
    private $startTime;
    private $endTime;
    
    /**
     * Create a new rule instance.
     *
     * @param $date
     * @param $startTime
     * @param $endTime
     */
    public function __construct($date, $startTime, $endTime) {
        $this->date = $date;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
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
        if ($value == 1) { // allow user to set manual opening only if event started or have defined time left to start
            if (!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', "$this->date $this->startTime")) {
                return true; // date validation will handle the further error
            }
            if (!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', "$this->date $this->endTime")) {
                return true; // date validation will handle the further error
            }
            if(!ValidationService::getInstance()->isManualOpeningPossible($this->date, $this->startTime, $this->endTime)) {
                $this->msg = __('events::message.manual_not_possible');
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
