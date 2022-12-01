<?php

namespace Modules\Crm\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Crm\Entities\CrmFilterType;

class CrmFilterTypeExist implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        return CrmFilterType::find($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Filter type ID not exist.';
    }
}
