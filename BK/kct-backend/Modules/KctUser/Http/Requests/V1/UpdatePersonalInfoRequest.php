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
 *  required={"field", "value"},
 *  @OA\Property(property="field",type="enum",description="Field to update",example="field_1",
 *     enum={"field_1","field_2","field_3"}
 *  ),
 *  @OA\Property(property="value",type="string",description="Value of field",example="Field")
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class validate the update personal information request.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class UpdatePersonalInfoRequest
 *
 * @package Modules\KctUser\Http\Requests\V1
 */
class UpdatePersonalInfoRequest extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules() {
        return [
            'field' => "required|in:field_1,field_2,field_3",
            'value' => "nullable|string|max:100",
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Determine if the user is authorized to make this request.
     * -----------------------------------------------------------------------------------------------------------------
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
