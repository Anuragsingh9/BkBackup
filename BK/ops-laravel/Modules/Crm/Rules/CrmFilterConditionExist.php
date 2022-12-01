<?php

namespace Modules\Crm\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Crm\Entities\CrmFilterCondition;
use Modules\Crm\Entities\CrmFilterRule;

class CrmFilterConditionExist implements Rule
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
        return CrmFilterRule::where('short_name', $value)->count() > 0 ? true : false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Filter condition not exist.';
    }
}
