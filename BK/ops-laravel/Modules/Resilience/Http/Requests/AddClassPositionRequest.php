<?php

    namespace Modules\Resilience\Http\Requests;

    use Illuminate\Contracts\Validation\Validator;
    use Illuminate\Foundation\Http\FormRequest;
    use Illuminate\Http\Exceptions\HttpResponseException;
    use Illuminate\Validation\Rule;

    class AddClassPositionRequest extends FormRequest
    {
        /**
         * Get the validation rules that apply to the request.
         *
         * @return array
         */
        public function rules()
        {
            return [
                'class_uuid' => ['required', Rule::exists('tenant.consultation_signup_classes', 'uuid')->whereNull('deleted_at')],
                'positions'  => ['required', 'string', 'max:' . config('resilience.limit.title'), 'min:3', Rule::unique('tenant.consultation_signup_class_positions', 'positions')->where('consultation_sign_up_class_uuid', $this->class_uuid)->whereNull('deleted_at')],
            ];
        }

        /**
         * Determine if the user is authorized to make this request.
         *
         * @return bool
         */
        public function authorize()
        {
            return TRUE;
        }

        protected function failedValidation(Validator $validator)
        {
            throw new HttpResponseException(response()->json([
                'status' => FALSE,
                'msg'    => implode(',', $validator->errors()->all()),
            ], 422));
        }
    }
