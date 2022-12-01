<?php

namespace Modules\Resilience\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CreateConsultationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'is_reinvent'           => 'required|numeric|in:0,1',
            'public_reinvent'       => 'required_if:is_reinvent,1|numeric|in:0,1',
            'user_id'               => 'required|numeric|exists:tenant.users,id',
            'workshop_id'           => 'required|numeric|exists:tenant.workshops,id',
            'name'                  => 'required|string|max:' . config('resilience.limit.title'),
            'long_name'                  => 'required|string|max:' . config('resilience.limit.title'),
            'internal_name'         => ['required', 'string', 'max:' . config('resilience.limit.title'), 'min:3', Rule::unique('tenant.consultations', 'internal_name')->whereNull('deleted_at')],
            'start_date'            => 'required|date_format:d/m/Y|after_or_equal:today',
            'end_date'              => 'required|date_format:d/m/Y|after:start_date',
            'display_results_until' => 'required|date_format:d/m/Y|after:end_date',
            'allow_to_go_back'      => 'required|numeric|in:0,1'
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
