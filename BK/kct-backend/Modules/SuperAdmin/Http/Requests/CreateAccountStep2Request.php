<?php

namespace Modules\SuperAdmin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\UserManagement\Rules\UserRule;

/**
 * -----------------------------------------------------------------------------------------------------------------
 * @description This class will manage the validation of create account step 2
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class CreateAccountStep2Request
 * @package Modules\SuperAdmin\Http\Requests
 */
class CreateAccountStep2Request extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules(): array {
        return [
            'first_name'        => ['required', new UserRule],
            'last_name'         => ['required', new UserRule],
            'password'          => ['required', new UserRule],
            'organisation_name' => 'required',
            'fqdn'              => ['required','regex:/^[a-zA-Z0-9\s-]+$/'],
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

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will provide the invalid error message
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return string[]
     */
    public function messages(): array {
        return [
          'fqdn.regex' => 'The account name is invalid',
        ];
    }
}
