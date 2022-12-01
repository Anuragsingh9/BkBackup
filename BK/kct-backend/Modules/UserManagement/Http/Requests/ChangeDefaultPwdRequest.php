<?php

namespace Modules\UserManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\UserManagement\Rules\OldPasswordRule;
use Modules\UserManagement\Rules\UserRule;

/**
 * @OA\Schema(
 *  title="RequestValidation: Change default password validation",
 *  description="Validates the request body for changing the user's default password",
 *  type="object",
 *  required={"email", "password"},
 *  @OA\Property(property="email",type="string",description="email of user",example="example@example.com"),
 *  @OA\Property(property="password",type="password",description="Password for the user account",example="********"),
 *  @OA\Property(property="password_confirmation",type="password",
 *     description="Password for the user account",example="********"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will validate request data for changing user's default password.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class ChangeDefaultPwdRequest
 * @package Modules\UserManagement\Http\Requests
 */
class ChangeDefaultPwdRequest extends FormRequest {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'email'    => ["required", "email", "exists:tenant.users,email"],
            'password' => ["required", "confirmed", new UserRule, new OldPasswordRule($this->email)]
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
