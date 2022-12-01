<?php

namespace Modules\KctUser\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *  title="RequestValidation: AttachUserTagRequest",
 *  description="Validates the request body for adding a user in space ",
 *  type="object",
 *  required={
 *     "tag_id"
 * },
 *  @OA\Property(property="tag_id",type="integer",description="ID of Tag",example="1")
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class used for validate the user attach request
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class AttachUserTagRequest
 *
 * @package Modules\KctUser\Http\Requests\V1
 */
class AttachUserTagRequest extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules() {
        return [
            'tag_id' => ['required', Rule::exists('user_tags', 'id')->where('status', 1),],
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
