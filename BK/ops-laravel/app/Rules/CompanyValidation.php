<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CompanyValidation implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $type, $allVal;

    public function __construct($type, $allVal)
    {
        $this->type = $type;
        $this->allVal = $allVal;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (in_array($value, $this->value)) {
            return true;
        }else{
            return false;
        }
        return preg_match('/(^[\w-]*$)/', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The attribute must be alpha-numeric.';
    }
}
