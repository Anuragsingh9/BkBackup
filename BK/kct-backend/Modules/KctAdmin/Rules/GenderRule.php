<?php

namespace Modules\KctAdmin\Rules;

use Illuminate\Contracts\Validation\Rule;

class GenderRule implements Rule {
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct() {

    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
        if ($value && !in_array(strtolower($value), ['m', 'f', 'o', 'male', 'female', 'other'])) {
            $this->value = $value;
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
        return __('validation.in', ['attribute' => 'gender']);
    }
}
