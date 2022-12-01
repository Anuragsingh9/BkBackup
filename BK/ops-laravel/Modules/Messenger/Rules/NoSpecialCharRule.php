<?php

namespace Modules\Messenger\Rules;

use Illuminate\Contracts\Validation\Rule;

class NoSpecialCharRule implements Rule {
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
    public function passes($attribute, $value) {
        return (boolean)preg_match("/^[a-zA-Z0-9 àâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ'-]*$/", $value);
    }
    
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return __('validation.regex');
    }
}
