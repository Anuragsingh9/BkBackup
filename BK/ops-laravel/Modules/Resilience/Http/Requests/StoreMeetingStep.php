<?php

namespace Modules\Resilience\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreMeetingStep extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'step_id'           => ['required', Rule::exists('tenant.consultation_steps', 'id')->where('step_type', 3)],
            'meeting_id'        => ['required', 'exists:tenant.meetings,id', Rule::unique('tenant.consultation_step_meetings', 'meeting_id')->where('consultation_step_id', $this->step_id)->whereNull('deleted_at')],
            'consultation_uuid' => 'required|exists:tenant.consultations,uuid',
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

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
    }
}
