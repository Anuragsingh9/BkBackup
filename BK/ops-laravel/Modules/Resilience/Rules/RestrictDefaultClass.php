<?php

    namespace Modules\Resilience\Rules;

    use Illuminate\Contracts\Validation\Rule;
    use Modules\Resilience\Entities\ConsultationSignUpClass;

    class RestrictDefaultClass implements Rule
    {
        

        /**
         * Determine if the validation rule passes.
         *
         * @param string $attribute
         * @param mixed $value
         * @return bool
         */
        public function passes($attribute, $value)
        {
            $class = ConsultationSignUpClass::where($attribute, $value)->first(['label']);
            return (isset($class->label) && (strtolower($class->label) == strtolower(config('resilience.default_class_name')))) ? FALSE : TRUE;
        }

        /**
         * Get the validation error message.
         *
         * @return string
         */
        public function message()
        {
            return __('resilience::validation.restrict_default_class');
        }
    }
