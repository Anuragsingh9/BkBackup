<?php

    namespace Modules\Resilience\Http\Requests;

    use Illuminate\Contracts\Validation\Validator;
    use Illuminate\Foundation\Http\FormRequest;
    use Illuminate\Http\Exceptions\HttpResponseException;
    use Illuminate\Validation\Rule;

    class UpdateSprintStepQuestionOrderRequest extends FormRequest
    {
        /**
         * Get the validation rules that apply to the request.
         *
         * @return array
         */
        public function rules()
        {
            return [
                'consultation_uuid' => ['required', Rule::exists('tenant.consultations', 'uuid')->whereNull('deleted_at')],
                'type'       => 'required|string|in:sprint,step,question',
                'sprint_id'       => ['required_if:type,step',Rule::exists('tenant.consultation_sprints', 'id')->whereNull('deleted_at')],
                'step_id'       => ['required_if:type,question',Rule::exists('tenant.consultation_steps', 'id')->whereNull('deleted_at')],
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
