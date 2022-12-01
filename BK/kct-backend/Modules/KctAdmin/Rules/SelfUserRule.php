<?php

namespace Modules\KctAdmin\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class SelfUserRule implements Rule
{
    private $msg;

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
        $this->msg = '';
        if ($value == Auth::user()->id){
            $username = Auth::user()->fname .' '. Auth::user()->lname ;
            $this->msg = "Self deletion not possible: $username";
            return false;
        }
            return true;
    }



    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->msg;
    }
}
