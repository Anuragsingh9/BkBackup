<?php

namespace Modules\Messenger\Rules;

use Illuminate\Contracts\Validation\Rule;
use Validator;
class MessageAttachmentUrlRule implements Rule {
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct() {
        $this->msgs = '';
    }
    
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
        if ($decode = json_decode($value, 1)) {
            $validator = Validator::make(
                ['value' => $decode],
                [
                    'value'        => 'required|array',
                    'value.*'      => 'required|array',
                    'value.*.url'  => 'required|string',
                    'value.*.type' => 'required|string|in:doc,system',
                    'value.*.file_name' => 'nullable|string',
                ]
            );
            if ($validator->fails()) {
                $this->msgs = $validator->errors();
                return FALSE;
            }
            return TRUE;
        }
        return FALSE;
    }
    
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return $this->msgs->toArray();
    }
}
