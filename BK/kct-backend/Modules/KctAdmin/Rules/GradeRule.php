<?php

namespace Modules\KctAdmin\Rules;

use Illuminate\Contracts\Validation\Rule;

class GradeRule implements Rule {
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
    public function passes($attribute, $value): bool {
        // String is require because in import user it use the string
        if ($value && !in_array(strtolower($value), ['employee', 'manager', 'executive', 'other'])) {
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
        return __('validation.in', ['attribute' => 'grade']);
    }
}
