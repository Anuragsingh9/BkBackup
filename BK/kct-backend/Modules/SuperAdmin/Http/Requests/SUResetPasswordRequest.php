<?php

namespace Modules\SuperAdmin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * -----------------------------------------------------------------------------------------------------------------
 * @description This class will manage the validation of super admin reset password
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class SUResetPasswordRequest
 * @package Modules\SuperAdmin\Http\Requests
 */
class SUResetPasswordRequest extends FormRequest
{
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email|exists:otp_codes,email',
            'password' => 'required|confirmed',
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Determine if the user is authorized to make this request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
