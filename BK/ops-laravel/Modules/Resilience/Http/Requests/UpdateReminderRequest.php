<?php

namespace Modules\Resilience\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Modules\Resilience\Rules\QuestionJson;

class UpdateReminderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'data'                      => 'required|array',
            'data.open_consultation'    => 'required|numeric|in:0,1',
            'data.late_participants'    => 'required|numeric|in:0,1',
            'data.reminders'            => 'required|array',
            'data.reminders.*'          => 'required|array',
            'data.reminders.*.active'   => 'required|numeric|in:0,1',
            'data.reminders.*.days'     => 'required|numeric'
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
