<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class FrenchName implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
    
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
        $reg='/^[0-9a-zA-Zu00E0-u00FC&àâäèéêëîïôœùûüÿççÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ _\'\ "-]*$/m';
        return preg_match($reg,$value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.frenchName');
    }
}
