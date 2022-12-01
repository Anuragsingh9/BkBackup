<?php

namespace Modules\Cocktail\Http\Requests\V2;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BadgeUpdateV2Request extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'field' => 'required|in:fname,lname,avatar',
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
    
    protected function getValidatorInstance() {
        $validator = parent::getValidatorInstance();
        $validator->sometimes('value', "required|string|max:100|regex:/^[a-zàâçéèêëîïôûùüÿñæœ' .-]*$/i", function () {
            return in_array($this->field, ['fname', 'lname']);
        });
        $validator->sometimes('value', 'required|image', function () {
            return $this->field == 'avatar';
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
