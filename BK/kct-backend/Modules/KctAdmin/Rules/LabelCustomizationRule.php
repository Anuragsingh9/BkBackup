<?php

namespace Modules\KctAdmin\Rules;

use Illuminate\Contracts\Validation\Rule;

class LabelCustomizationRule implements Rule {
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
        $decode = json_decode($value, JSON_OBJECT_AS_ARRAY);
        if ($decode) {
            $availableLang = config('kctadmin.available_lang');
            if (count($decode) != count($availableLang)) {
                $this->msg = __('validation.max.array');
                return false;
            }
            foreach ($availableLang as $lang) {
                $lang = strtolower($lang);
                if (!isset($decode[$lang]) || !$decode[$lang]) {
                    $this->msg = __('validation.required', ['attribute' => "$attribute.$lang"]);
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return $this->msg;
    }
}
