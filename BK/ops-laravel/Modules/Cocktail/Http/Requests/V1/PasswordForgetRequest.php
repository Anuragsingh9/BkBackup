<?php

namespace Modules\Cocktail\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @OA\Schema(
 *  title="RequestValidation: PasswordForgetRequest",
 *  description="Validated the email address asking for reset password is valid or not",
 *  type="object",
 *  required={"email"},
 *  @OA\Property(
 *      property="email",
 *      type="string",
 *      description="Email Address to send the forget password",
 *      example="example@example.com"
 *  ),
 * )
 * Class PasswordForgetRequest
 * @package Modules\Cocktail\Http\Requests
 */
class PasswordForgetRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'email' => 'required|exists:tenant.users,email',
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
