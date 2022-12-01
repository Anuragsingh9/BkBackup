<?php

namespace Modules\UserManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\UserManagement\Rules\UserRule;

/**
 * @OA\Schema(
 *     title="RequestValidation: send reset password link validation",
 *     description="Validate the reset password link for updating user password",
 *     type="object",
 *     required={"email", "password", "password_confirmation", "identifier"},
 *      @OA\Property( property="email",type="email",description="User email",example="example@example.com"),
 *      @OA\Property(property="password",type="password",description="Password for the user account",example="********"),
 *      @OA\Property(property="password_confirmation", type="string",
 *     description="Password Confirmation",example="••••••••"),
 *      @OA\Property(property="identifier",type="string",
 *     description="identifier",example="bJvfXpmLUUn8rgpz6WvKB2SMfro0z5hZu6wqrl6z6FEQllOfxgy1qPuedRUV"),
 *  )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will validate request data for reset password.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class ResetPasswordRequest
 *
 * @package Modules\UserManagement\Http\Requests
 */
class ResetPasswordRequest extends FormRequest {
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules() {
        return [
            'email'      => ['required', 'email'],
            'password'   => ['required', 'confirmed', 'string', 'min:6', new UserRule("password_change")],
            'identifier' => 'required|string',
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Determine if the user is authorized to make this request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return bool
     */
    public function authorize(): bool {
        return true;
    }
}
