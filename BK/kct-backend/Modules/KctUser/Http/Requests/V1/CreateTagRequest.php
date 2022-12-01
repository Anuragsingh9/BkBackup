<?php

namespace Modules\KctUser\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *  title="RequestValidation: CreateTagRequest",
 *  description="Validates the request body for adding a user in space ",
 *  type="object",
 *  required={
 *     "tag_id"
 * },
 *  @OA\Property(property="tag_name",type="string",description="ID of Tag",example="testing"),
 *  @OA\Property(property="tag_type",type="integer",description="tag type",example="1")
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class use for validate the create user tag request
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class CreateTagRequest
 *
 * @package Modules\KctUser\Http\Requests\V1
 */
class CreateTagRequest extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules() {
        return [
            'tag_name' => 'required|string',
            'tag_type' => 'required|in:1,2',
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
}
