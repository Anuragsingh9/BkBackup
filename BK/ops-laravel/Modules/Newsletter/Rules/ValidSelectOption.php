<?php

namespace Modules\Newsletter\Rules;

use App\Model\SelectOption;
use Illuminate\Contracts\Validation\Rule;

class ValidSelectOption implements Rule
{
    private $val;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($id = 0)
    {
        $this->val = $id;
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
        if ($this->val > 0) {
            $true = SelectOption::where('option_value', $value)->count();
            return ($true > 0)??0;
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Selected Option is Invalid.';
    }
}
