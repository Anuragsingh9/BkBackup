<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class FrEn implements Rule {
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
        // if input is other then English and French alphabets throw validation error
        return preg_match("/^[a-zA-ZàâäèéêëîïôœùûüÿçÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ.\-' _]*$/", $value);
    }
    
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return __('validation.frenchChar');
    }
}
