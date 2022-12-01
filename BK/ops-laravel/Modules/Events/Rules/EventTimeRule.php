<?php

namespace Modules\Events\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class EventTimeRule implements Rule {
    private $date;
    /**
     * @var array|string|null
     */
    private $msg;
    
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($date) {
        $this->date = $date;
    }
    
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
        if ($this->date) {
            $carbon = Carbon::createFromFormat('Y-m-d H:i:s', "$this->date $value");
            if (Carbon::now()->timestamp < $carbon->timestamp) {
                return true;
            } else {
                $this->msg = __('events::message.past_not_allowed');
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
