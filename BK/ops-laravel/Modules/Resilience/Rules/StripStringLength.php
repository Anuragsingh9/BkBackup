<?php

namespace Modules\Resilience\Rules;

use Illuminate\Contracts\Validation\Rule;

class StripStringLength implements Rule
{
    public $length;

    /**
     * Create a new rule instance.
     *
     * @param $length
     */
    public function __construct($length)
    {
        $this->length = $length;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return strlen(strip_tags($value)) > $this->length ? false : true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute length is more';
    }
}
