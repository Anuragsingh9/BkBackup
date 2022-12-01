<?php

    namespace Modules\Resilience\Http\Requests;

    use Illuminate\Contracts\Validation\Validator;
    use Illuminate\Foundation\Http\FormRequest;
    use Illuminate\Http\Exceptions\HttpResponseException;
    use Illuminate\Validation\Rule;
    use Modules\Resilience\Rules\RestrictDefaultClass;

    class GetClassPositions extends FormRequest
    {
        /**
         * Get the validation rules that apply to the request.
         *
         * @return array
         */
        public function rules()
        {

            return [
                'uuid' => ['required', Rule::exists('tenant.consultation_signup_classes', 'uuid')->where(function ($q) {
                    $q->whereNull('deleted_at');
                })],
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

        /**
         * Get data to be validated from the request.
         *
         * @return array
         */
        public function validationData()
        {
            return array_merge(parent::validated(), [
                'uuid' => $this->classUuid,
            ]);
        }
    }
