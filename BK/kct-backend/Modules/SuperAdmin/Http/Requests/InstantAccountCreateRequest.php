<?php

namespace Modules\SuperAdmin\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * -----------------------------------------------------------------------------------------------------------------
 * @description This class will manage the validation of instant account creation
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class InstantAccountCreateRequest
 * @package Modules\SuperAdmin\Http\Requests
 */
class InstantAccountCreateRequest extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method used for apply validation on request data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function validationData(): array {
        $data = parent::validationData();
        // this will modify the acc name with suffix but this will not remain in controller,
        // i.e. in controller its actual value will present
        if (isset($data['accName'])) {
            $data['accName'] = $this->input('accName') . '.' . env('HOST_SUFFIX');
        }
        return $data;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules(): array {
        return [
            'orgFname' => ['required'],
            'orgLname' => ['required'],
            'orgEmail' => 'required|email',
            'orgName'  => 'required|unique:organisations,name_org',
            // custom added key for the validation of unique account name
            'accName'  => 'required|string|unique:hostnames,fqdn',
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
     * @description To send the custom validation when request is failed
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode(',', $validator->errors()->all()),
            'errors' => $validator->errors(),
        ], 422));
    }
}
