<?php

namespace Modules\KctUser\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
use Modules\KctUser\Traits\KctHelper;

/**
 * @OA\Schema(
 *  title="RequestValidation: PasswordResetRequest",
 *  description="To validated the password reset process contains corrent values",
 *  type="object",
 *  required={"email", "identifier", "password"},
 *  @OA\Property(property="email",type="email",description="Email of respective user account",
 *     example="example@example.com"
 *  ),
 *  @OA\Property(property="identifier",type="string",
 *     description="String token which is responsible to validate the user is trying to reset password
 *        from the email was sent to the provided email",
 *     example="SE2BwVZmIXBMgFkENHNW93BXSOVtw9zT5SRZ"
 *  ),
 *  @OA\Property(property="password",type="string",description="New password to set for the user account",
 *     example="••••••••"
 *  ),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will be use for validating the reset password request when user try to set your new password
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class PasswordResetRequest
 *
 * @package Modules\KctUser\Http\Requests\V1
 */
class PasswordResetRequest extends FormRequest {
    use KctHelper;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules() {
        return [
            'email'      => [
                'required',
                Rule::exists('tenant.users', 'email')->where('identifier', $this->identifier)
            ],
            'identifier' => 'required|string',
            'password'   => 'required|confirmed|string|min:' . config('kctuser.validations.user.password_min')
                . '|max:' . config('kctuser.validations.user.password_max'),
        ];
    }



    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Determine if the user is authorized to make this request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will be use for custom error message for authorization failed
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will be used for custom error message
     * -----------------------------------------------------------------------------------------------------------------
     */
    protected function prepareForValidation() {
        $this->merge([
            'email' => $this->decryptData($this->email)
        ]);
    }
}
