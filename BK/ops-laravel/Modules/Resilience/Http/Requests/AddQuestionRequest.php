<?php

namespace Modules\Resilience\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Modules\Resilience\Rules\QuestionJson;

class AddQuestionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'consultation_step_id'           => 'required|numeric|exists:tenant.consultation_steps,id',
            'consultation_question_type_id'  => 'required|numeric|exists:tenant.consultation_question_types,id',
            'question'                       => 'required|string|max:' . config('resilience.limit.title'),
            'description'                    => 'sometimes|nullable|string|max:' . config('resilience.limit.description'),
            'comment'                    => 'required_if:consultation_question_type_id,17|string|max:' . config('resilience.limit.description'),
            'is_mandatory'                   => 'required|numeric|in:0,1',
            'allow_add_other_answers'        => 'required|numeric|in:0,1',
            'options'                        => ['nullable', 'json', new QuestionJson],
            'column_data'                    => 'required_if:consultation_question_type_id,16|array',
            'column_data.row'                => 'required_if:consultation_question_type_id,16|array',
            'column_data.row.*'              => 'required_if:consultation_question_type_id,16|array',
            'column_data.row.*.label'        => 'required_if:consultation_question_type_id,16|string',
            'column_data.column'             => 'required_if:consultation_question_type_id,16|array',
            'column_data.column.*'           => 'required_if:consultation_question_type_id,16|array',
            'column_data.column.*.label'     => 'required_if:consultation_question_type_id,16|string'
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
