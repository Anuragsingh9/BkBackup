<?php

namespace Modules\Resilience\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'           => 'sometimes|string|max:' . config('resilience.limit.title'),
            'description'     => 'sometimes|string|max:' . config('resilience.limit.description'),
            'step_type'       => 'required|numeric|in:2',
            'active'          => 'required|numeric|in:0,1',
            'answerable'      => 'required|numeric|in:0,1'
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
}
