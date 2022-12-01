<?php

namespace Modules\KctUser\Rules\V1;

use Illuminate\Contracts\Validation\Rule;

class JsonRGBARule implements Rule {
    /**
     * @var array|string|null
     */
    private $msg;

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
        $value = json_decode($value, 1);
        if ($value) {
            $errors = [];
            $errors = $this->validateColor($value, 'r', $errors);
            $errors = $this->validateColor($value, 'g', $errors);
            $errors = $this->validateColor($value, 'b', $errors);
            $errors = $this->validateAlpha($value, $errors);

            $this->msg = implode(',', $errors);
            if($this->msg) {
                return false;
            }
        } else {
            $this->msg = __('validation.json', ['attribute' => 'value']);
            return false;
        }
        return true;
    }

    public function validateColor($value, $field, $errors) {
        if (!isset($value[$field])) {
            $errors[] = __('validation.required', ['attribute' => "$field"]);
            return $errors;
        }

        if(!is_numeric($value[$field])) {
            $errors[] = __('validation.integer', ['attribute' => $field]);
            return $errors;
        }

        if ($value[$field] < 0 || $value[$field] > 255) {
            $errors[] = __('validation.between.numeric', [
                'attribute' => $field,
                'min'       => 0,
                'max'       => 255,
            ]);
            return $errors;
        }

        return $errors;
    }

    public function validateAlpha($value, $errors) {
        if (!isset($value['a'])) {
            $errors[] = __('validation.required', ['attribute' => "alpha"]);
            return $errors;
        }

        if(!is_float($value['a']) && !is_double($value['a']) && !is_numeric($value['a'])) {

            $errors[] = __('validation.numeric', ['attribute' => 'alpha']);
            return $errors;
        }

        if ($value['a'] < 0 || $value['a'] > 1) {
            $errors[] = __('validation.between.numeric', [
                'attribute' => 'alpha',
                'min'       => 0,
                'max'       => 1,
            ]);
            return $errors;
        }
        return $errors;
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
