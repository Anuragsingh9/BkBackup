<?php

namespace Modules\Resilience\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateConsultationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'is_reinvent'           => 'sometimes|numeric|in:0,1',
            'public_reinvent'       => 'sometimes:is_reinvent,1|numeric|in:0,1',
            'name'                  => 'sometimes|string|max:' . config('resilience.limit.title'),
            'long_name'                  => 'sometimes|string|max:' . config('resilience.limit.title'),
            'internal_name'              => ['sometimes', 'string', 'max:' . config('resilience.limit.title'), 'string', Rule::unique('tenant.consultations', 'internal_name')->whereNull('deleted_at')->whereNot('uuid', $this->route('consultationId'))],
            'start_date'            => 'required_with:end_date|date_format:d/m/Y',
            'end_date'              => 'required_with:start_date|date_format:d/m/Y|after:start_date',
            'display_results_until' => 'required_with:start_date,end_date|date_format:d/m/Y|after:end_date',
            'allow_to_go_back'      => 'sometimes|numeric|in:0,1'
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
    }
}
