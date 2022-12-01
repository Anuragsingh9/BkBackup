<?php

namespace Modules\KctUser\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @OA\Schema(
 *  title="RequestValidation: Ban User from Event Request Validation",
 *  description="Validates the request body for banning a user from event ",
 *  type="object",
 *  required={"user_id", "event_uuid", "ban_reason", "severity"},
 *  @OA\Property(property="field",type="string",description="Field Name",example="user_lname"),
 *  @OA\Property(property="value",type="string",description="Value of user visibility column",example="1")
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will be validating the update user visibility request.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class UpdateUserVisibilityRequest
 *
 * @package Modules\KctUser\Http\Requests\V1
 */
class UpdateUserVisibilityRequest extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules() {
        return [
            'field' => 'required|in:user_lname,unions,company,p_field_1,p_field_2,p_field_3,personal_tags,professional_tags',
            'value' => 'in:0,1',
        ];
    }

    /**
     * ---------------------------------------------------------------------------------------------------------------------
     * @descripiton Determine if the user is authorized to make this request.
     * ---------------------------------------------------------------------------------------------------------------------
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will be use for custom error message for authorization failed
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
    }
}
