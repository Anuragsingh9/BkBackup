<?php

    namespace Modules\Resilience\Http\Requests;

    use Illuminate\Contracts\Validation\Validator;
    use Illuminate\Foundation\Http\FormRequest;
    use Illuminate\Http\Exceptions\HttpResponseException;
    use Illuminate\Validation\Rule;

    class UpdateClass extends FormRequest
    {
        protected $CLASS_TYPE = [
            'union'   => 1,
            'company' => 2,
        ];

        /**
         * Get the validation rules that apply to the request.
         *
         * @return array
         */
        public function rules()
        {

            return [
                'label' => ['required', 'string', 'max:' . config('resilience.limit.title'), 'min:3', Rule::unique('tenant.consultation_signup_classes', 'label')->where('class_type', $this->CLASS_TYPE[$this->class_type])->whereNot('uuid', $this->route('class'))->whereNull('deleted_at')],
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
