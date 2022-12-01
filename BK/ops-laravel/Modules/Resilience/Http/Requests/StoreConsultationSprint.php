<?php

namespace Modules\Resilience\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreConsultationSprint extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'              => ['required', 'string', 'max:' . config('resilience.limit.title'), 'min:3', Rule::unique('tenant.consultation_sprints', 'title')->where('consultation_uuid', $this->consultation_uuid)->whereNull('deleted_at')],
            'description_1'      => 'required|string|min:3|max:' . config('resilience.limit.sprint_description'),
            'description_2'      => 'nullable|string|min:3|max:' . config('resilience.limit.sprint_description'),
            'description_3'      => 'nullable|string|min:3|max:' . config('resilience.limit.sprint_description'),
            'image_non_selected' => 'required|image|mimes:jpeg,png,jpg',
            'image_selected'     => 'required|image|mimes:jpeg,png,jpg',
            'is_accessible'      => 'required|numeric|in:0,1',
            'consultation_uuid'    => 'required|exists:tenant.consultations,uuid',
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
