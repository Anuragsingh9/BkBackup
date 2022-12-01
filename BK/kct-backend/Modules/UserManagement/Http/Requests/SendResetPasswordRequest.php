<?php

namespace Modules\UserManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     title="RequestValidation: send reset password link validation",
 *     description="Validate the reset password link for updating user password",
 *     type="object",
 *     required={"email"},
 *      @OA\Property( property="email",type="email",description="User email",example="example@example.com"),
 *)
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will validate request data for sending Password reset link to user's email.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 */
class SendResetPasswordRequest extends FormRequest
{
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
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
