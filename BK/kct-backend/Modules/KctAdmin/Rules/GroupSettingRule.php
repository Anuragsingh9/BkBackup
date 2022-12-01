<?php

namespace Modules\KctAdmin\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class GroupSettingRule implements Rule {
    private ?string $dataSet;
    private ?string $field;
    private ?string $msg = '';
    private ?array $errors = [];

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($field, $dataSet) {
        $this->field = $field;
        $this->dataSet = $dataSet;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool {
        if(!is_array($value)) {
            return true;
        }
        switch ($this->dataSet) {
            case 'array':
                $settings = config("kctadmin.default.group_settings.arrays.$this->field");
                if ($settings) {
                    return $this->validateArray($value)
                        && $this->checkForExtraKey($settings, $value);
                }
                return $this->validateArray($value);
            default:
                return true;
        }
    }

    private function validateArray($value): bool {
        switch ($this->field) {
            // validating array keys
            case 'zoom_default_webinar_settings':
            case 'zoom_webinar_settings':
            case 'zoom_meeting_settings':
                $validator = Validator::make($value, [
                    'enabled'          => 'nullable|in:0,1',
                    'max_participants' => 'nullable|integer|min:1|max:1000',
                    'licenses'         => 'nullable',
                    'is_locked'        => 'nullable|in:0,1',
                    'api_key'          => 'nullable|string',
                    'api_secret'       => 'nullable|string',
                ]);
                if ($validator->fails()) {
                    $this->errors = $validator->errors()->all();
                    return false;
                }
                return true;
        }
        return true;
    }

    private function checkForExtraKey($settings, $value): bool {
        if ($diff = array_diff(array_keys($value), array_keys($settings))) {
            $this->msg = __("validation.in", ['attribute' => implode(',', $diff)]);
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     */
    public function message() {
        return $this->msg ?: $this->errors ?: null;
    }
}
