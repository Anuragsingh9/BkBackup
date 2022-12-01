<?php

namespace Modules\Resilience\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateWelcomeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'           => 'sometimes|string|min:3|max:' . config('resilience.limit.title'),
            'description'     => 'sometimes|string|min:3|max:' . config('resilience.limit.description'),
            'image'           => 'sometimes|image|mimes:jpeg,png,jpg',
            'step_type'       => 'required|numeric|in:1',
            'active'          => 'required|numeric|in:0,1'
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
