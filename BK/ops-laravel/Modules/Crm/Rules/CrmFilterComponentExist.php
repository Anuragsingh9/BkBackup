<?php

namespace Modules\Crm\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Crm\Entities\CrmFilterType;

class CrmFilterComponentExist implements Rule
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
        $filterTypes = CrmFilterType::all('component');

        $flag = false;
        foreach ($filterTypes as $filterType){
            $component = $filterType->component;
            $component = json_decode($component);
            $flag = property_exists($component,$value);
            if($flag)
                break;
        }
        return $flag;

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Filter component not exist.';
    }
}
