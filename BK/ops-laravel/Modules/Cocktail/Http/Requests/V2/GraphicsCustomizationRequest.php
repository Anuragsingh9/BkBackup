<?php

namespace Modules\Cocktail\Http\Requests\V2;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Cocktail\Rules\ColorRGBA;
use Modules\Cocktail\Rules\V2\JsonRGBARule;
use Modules\Cocktail\Services\V2Services\KctCoreService;

class GraphicsCustomizationRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $config = config('cocktail.default.custom_graphics');
        $fields = array_merge(
            array_keys($config['colors']),
            array_keys($config['checkboxes']),
            array_keys($config['urls'])
        );
        $rules = [
            'field' => 'required|in:' . implode(',', $fields),
        ];
        return $rules;
    }
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }
    
    protected function getValidatorInstance() {
        $validator = parent::getValidatorInstance();
        $fieldType = KctCoreService::getInstance()->findFieldType($this->input('field'));
        $validator->sometimes('value', ['required', 'in:0,1'], function ($q) use ($fieldType) {
            return $fieldType == 'checkbox';
        });
        
        $validator->sometimes('value', [new JsonRGBARule], function () use ($fieldType) {
            return $fieldType == 'color';
        });
    
        $validator->sometimes('value', 'required|url', function () use ($fieldType) {
            return $fieldType == 'urls';
        });
        return $validator;
    }
    
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
    }
    
}
