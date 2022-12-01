<?php

namespace Modules\Crm\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Crm\Entities\CrmFilter;
use Modules\Crm\Entities\CrmFilterType;

class CrmFilterNameUnique implements Rule
{
    protected $id = null;

    /**
     * Create a new rule instance.
     *
     * @param $id
     * @return void
     */
    public function __construct($id = null)
    {
        //
        $this->id = $id;
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
        if($this->id){
            return CrmFilter::where('name', $value)->where('id', '!=', $this->id)->count() > 0 ? false : true;
        }else{
            return CrmFilter::where('name', $value)->count() > 0 ? false : true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Filter name must be unique.';
    }
}
