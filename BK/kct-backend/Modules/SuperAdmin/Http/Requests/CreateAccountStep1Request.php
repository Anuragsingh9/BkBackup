<?php

namespace Modules\SuperAdmin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * -----------------------------------------------------------------------------------------------------------------
 * @description This class will manage the validation of create account step 1
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class CreateAccountStep1Request
 * @package Modules\SuperAdmin\Http\Requests
 */
class CreateAccountStep1Request extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules(): array {
        return [
            'email' => 'required|email',
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
