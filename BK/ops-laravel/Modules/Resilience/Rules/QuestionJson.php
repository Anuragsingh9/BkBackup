<?php

namespace Modules\Resilience\Rules;

use Illuminate\Contracts\Validation\Rule;
use Validator;

class QuestionJson implements Rule
{

    /**
     * Create a new rule instance.
     *
     * @param $questionTypeId
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
        $value = json_decode($value,true);
        $validator = Validator::make($value, [
            'value'             => 'array',
            'value.*'           => 'required|json',
        ]);

        if ($validator->fails()) {
            return false;
        }

        foreach ($value as $k => $v) {
            if(!(isset($v['label']) && isset($v['is_manual']) && is_string($v['label']) && is_integer($v['is_manual']))) {
                return false;
            }
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
        return 'The :attribute format is not valid.';
    }
}
