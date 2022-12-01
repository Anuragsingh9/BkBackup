<?php

namespace Modules\Cocktail\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *  title="RequestValidation: PasswordResetRequest",
 *  description="To validated the password reset process contains corrent values",
 *  type="object",
 *  required={"email", "identifier", "password"},
 *  @OA\Property(
 *      property="email",
 *      type="email",
 *      description="Email of respective user account",
 *      example="example@example.com"
 *  ),
 *  @OA\Property(
 *      property="identifier",
 *      type="string",
 *      description="String token which is responsible to validate the user is trying to reset password from the email was sent to the provided email",
 *      example="SE2BwVZmIXBMgFkENHNW93BXSOVtw9zT5SRZ"
 *  ),
 *  @OA\Property(
 *      property="password",
 *      type="string",
 *      description="New password to set for the user account",
 *      example="••••••••"
 *  ),
 * )
 *
 * Class PasswordResetRequest
 * @package Modules\Cocktail\Http\Requests
 */
class PasswordResetRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'email'      => ['required', Rule::exists('tenant.users', 'email')->where('identifier', $this->identifier)],
            'identifier' => 'required|string',
            'password'   => 'required|confirmed|string|min:' . config('cocktail.validations.user.password_min')
                . '|max:' . config('cocktail.validations.user.password_max'),
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
