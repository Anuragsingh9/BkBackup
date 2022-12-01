<?php

namespace Modules\UserManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\UserManagement\Rules\OrganiserRule;

/**
 * @OA\Schema(
 *  title="RequestValidation: Login Request Validation",
 *  description="Validates the request body signin to workspace",
 *  type="object",
 *  required={"email", "password"},
 *  @OA\Property(property="email",type="string",description="email of user",example="example@example.com"),
 *  @OA\Property(property="password",type="password",description="Password for the user account",example="********"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will validate request data for user login.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class UserLoginRequest
 * @package Modules\UserManagement\Http\Requests
 */
class UserLoginRequest extends FormRequest
{
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => ["required","email",new OrganiserRule($this->email)],
            'password' => ["required",new OrganiserRule($this->email)],
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Determine if the user is authorized to make this request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
