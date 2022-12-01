<?php

namespace Modules\KctUser\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @OA\Schema(
 *  title="RequestValidation: BadgeUpdateUSRequest",
 *  description="Validates the request body for adding a user in space ",
 *  type="object",
 *  required={
 *     "field"
 * },
 *  @OA\Property(property="field",type="string",description="Field Value",example="fname"),
 *  @OA\Property(property="value",type="string",description="Value of user visibility column",example="shubham")
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will be validating the update user badge request
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class BadgeUpdateUSRequest
 *
 * @package Modules\KctUser\Http\Requests\V1
 */
class BadgeUpdateUSRequest extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules() {
        return [
            'field' => 'required|in:fname,lname,avatar,bg_image',
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
     * @description This method will be used for validate the instance
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return Validator
     */
    protected function getValidatorInstance() {
        $validator = parent::getValidatorInstance();
        $validator->sometimes('value', "required|string|max:100|regex:/^[a-zàâçéèêëîïôûùüÿñæœ' .-]*$/i", function () {
            return in_array($this->field, ['fname', 'lname']);
        });
        $validator->sometimes('value', 'required|image', function () {
            return $this->field == 'avatar';
        });
        return $validator;
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
