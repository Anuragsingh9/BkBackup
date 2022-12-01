<?php

namespace Modules\Resilience\Rules;

use Illuminate\Contracts\Validation\Rule;
use Validator;
use Modules\Resilience\Entities\ConsultationQuestion;

class AnswerJson implements Rule
{
    public $type;

    /**
     * Create a new rule instance.
     *
     * @param $type
     */
    public function __construct($type)
    {
        $this->type = $type;
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
        if(!$this->isJson($value))
        {
            return true;
        }
        $value = json_decode($value,true);
        if(is_null($value)) {
            return true;
        }
        $questionType = ConsultationQuestion::with('consultationQuestionType')->find($this->type);
        if(!$questionType) {
            return false;
        }
        if(in_array($questionType->consultationQuestionType->id, ['13', '14'])) {
            foreach ($value as $k => $v) {
                if(!(isset($v['scale_value']) && isset($v['id']) && is_integer($v['scale_value']) && is_string($v['id']))) {
                    return false;
                }
            }
        }
        if(in_array($questionType->consultationQuestionType->id, ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '15'])) {
            foreach ($value as $k => $v) {
                if(!(isset($v) && is_string($v))) {
                    return false;
                }
            }
        }
        return true;
    }

    public function isJson($string) {
        return (is_string($string) && (is_object(json_decode($string)) || is_array(json_decode($string)))) ? true : false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The answer should be in proper format.';
    }
}
