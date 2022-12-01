<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class UUID implements Rule {
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
        return \Ramsey\Uuid\Uuid::isValid($value);
    }
    
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return 'Supplied :attribute is not valid UUID!'; // todo make it translate
    }
}
