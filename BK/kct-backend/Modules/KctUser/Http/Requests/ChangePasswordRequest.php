<?php

namespace Modules\KctUser\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *  title="RequestValidation: PasswordResetRequest",
 *  description="To validated the password reset process contains corrent values",
 *  type="object",
 *  required={"email", "identifier", "password"},
 *  @OA\Property(property="current_password",type="string",description="current password of user account",example="••••••••"),
 *  @OA\Property(property="password",type="string",description="New password to set for the user account",example="••••••••"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will be use for validating the change password request when user try to change our password
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class ChangePasswordRequest
 *
 * @package Modules\KctUser\Http\Requests
 */
class ChangePasswordRequest extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules() {
        $validation = config('kctuser.validations.user');
        return [
            'current_password' => 'required|string|current_password:api',
            'password'         => "required|confirmed|string|min:{$validation['password_min']}
                                    |max:{$validation['password_max']}",
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
}
