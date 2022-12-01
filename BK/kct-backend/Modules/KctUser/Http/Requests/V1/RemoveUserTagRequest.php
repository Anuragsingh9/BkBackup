<?php

namespace Modules\KctUser\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @OA\Schema(
 *  title="RequestValidation: RemoveUserTagRequest",
 *  description="To validated the password reset process contains corrent values",
 *  type="object",
 *  required={"tag_id"},
 *  @OA\Property(property="tag_id",type="integer",description="ID of tag",example="1")
 * )
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class validate the remove user tag request
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class RemoveUserTagRequest
 * @package Modules\KctUser\Http\Requests\V1
 */
class RemoveUserTagRequest extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules() {
        return [
            'tag_id' => 'required|exists:user_tags,id',
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
