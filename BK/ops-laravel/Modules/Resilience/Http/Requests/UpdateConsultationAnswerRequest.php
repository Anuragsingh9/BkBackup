<?php

namespace Modules\Resilience\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Modules\Resilience\Rules\AnswerJson;
use Modules\Resilience\Rules\ManualAnswerJson;

class UpdateConsultationAnswerRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $valid = ['nullable', 'string', new AnswerJson($this->consultation_question_id)];

        return [
            'consultation_uuid'         => 'required|string|exists:tenant.consultations,uuid',
            'consultation_answer_id'    => 'required|numeric|exists:tenant.consultation_answers,id',
            'user_workshop_id'          => 'required|numeric|exists:tenant.workshops,id',
            'consultation_question_id'  => 'required|numeric|exists:tenant.consultation_questions,id',
            'is_manual'                 => 'required|numeric|in:0,1',
            'answer'                    => $valid,
            'manual_answer'             => ['required_if:is_manual,1', 'nullable', 'json', new ManualAnswerJson($this->consultation_question_id)],
            'column_data'               => 'sometimes|array',
            'column_data.*'             => 'required_with:column_data|array',
            'column_data.*.*'           => 'required_with:column_data|string',
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
