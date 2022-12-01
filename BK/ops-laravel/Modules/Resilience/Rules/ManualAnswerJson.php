<?php

namespace Modules\Resilience\Rules;

use Illuminate\Contracts\Validation\Rule;
use Validator;
use Modules\Resilience\Entities\ConsultationQuestion;

class ManualAnswerJson implements Rule
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
        if(!$this->isJson($value)) {
            return false;
        }
        $value = json_decode($value,true);
        $validator = Validator::make($value, [
            'value'             => 'array',
            'value.*'           => 'required|json',
        ]);

        if ($validator->fails()) {
            return false;
        }
        $questionType = ConsultationQuestion::with('consultationQuestionType')->find($this->type);
        if(!$questionType) {
            return false;
        }
        if(in_array($questionType->consultationQuestionType->id, ['13', '14'])) {
            foreach ($value as $k => $v) {
                if(!(isset($v['scale_value']) && isset($v['id']) && isset($v['label']) && is_string($v['label']) && is_string($v['scale_value']) && is_string($v['id']))) {
                    return false;
                }
            }
        }
        if(in_array($questionType->consultationQuestionType->id, ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '15'])) {
            foreach ($value as $k => $v) {
                if(!(isset($v['label']) && isset($v['id']) && is_string($v['label']) && is_string($v['id']))) {
                    return false;
                }
            }
        }

        foreach ($value as $k => $v) {
            if(!(isset($v['label']) && isset($v['id']) && is_string($v['label']) && is_string($v['id']))) {
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
        return 'The manual answer should array json';
    }

    /**
     * Get the json validation.
     *
     * @return string
     */
    public function isJson($string) {
        return (is_string($string) && (is_object(json_decode($string)) || is_array(json_decode($string)))) ? true : false;
    }
}
