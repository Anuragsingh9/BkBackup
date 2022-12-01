<?php

namespace Modules\Cocktail\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UserUpdateEntityRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        
        $entity = ['nullable', Rule::exists('tenant.entities', 'id')->where(function ($q) {
            $q->where('entity_type_id', $this->entity_type);
        })];
        
        $validation = config('cocktail.validations.entity');
        
        return [
            'entity_type' => 'required|in:2,3',
            'entity_id'   => $entity,
            'position'    => 'nullable|string',
            'entity_name' => [
                "nullable",
                "required_without:entity_id",
                "string",
                "max:{$validation['long_name_max']}",
                "min:{$validation['long_name_min']}",
                Rule::unique('tenant.entities', 'long_name')->where(function ($q) {
                    $q->where('entity_type_id', $this->entity_type);
                }),
            ],
        ];
    }
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }
    
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
    }
}
