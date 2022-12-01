<?php

namespace Modules\Events\Rules;

use Illuminate\Contracts\Validation\Rule;

class OpeningHourBeforeRule implements Rule {
    /**
     * Create a new rule instance.
     *
     * @param $date
     * @param $time
     */
    public function __construct($date, $time) {
        $this->date = $date;
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
        return true;
    }
    
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return 'The validation error message.';
    }
}
