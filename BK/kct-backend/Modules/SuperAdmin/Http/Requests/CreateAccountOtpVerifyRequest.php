<?php

namespace Modules\SuperAdmin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * -----------------------------------------------------------------------------------------------------------------
 * @description This class will manage the validation of create account verify by otp
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class CreateAccountOtpVerifyRequest
 * @package Modules\SuperAdmin\Http\Requests
 */
class CreateAccountOtpVerifyRequest extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules(): array {
        return [
            'code_1' => 'required|string|min:1|max:1',
            'code_2' => 'required|string|min:1|max:1',
            'code_3' => 'required|string|min:1|max:1',
            'code_4' => 'required|string|min:1|max:1',
            'code_5' => 'required|string|min:1|max:1',
            'code_6' => 'required|string|min:1|max:1',
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Determine if the user is authorized to make this request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return bool
     */
    public function authorize(): bool {
        return true;
    }
}
