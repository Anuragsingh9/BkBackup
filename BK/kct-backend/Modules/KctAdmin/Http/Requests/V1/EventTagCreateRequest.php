<?php

namespace Modules\KctAdmin\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @OA\Schema(
 *  title="RequestValidation: Event Tag Request Validation",
 *  description="Validates the request body for event tag ",
 *  type="object",
 *  required={"tag_name"},
 *  @OA\Property(property="tag_name",type="string",description="Name of Event Tag",example="Name of Event Tag"),
 *  @OA\Property(property="group_key",type="int",description="Group key for the tag",example="default"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class validate the event tag FR request
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class EventTagRequest
 * @package Modules\KctAdmin\Http\Requests\V1
 */
class EventTagCreateRequest extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules(): array {
        return [
            'tag_name' => 'required|min:2|max:30|unique:tenant.organiser_tags,name',
            'group_key' => 'required|exists:tenant.groups,group_key',
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Determine if the user is authorized to make this request
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return bool
     */
    public function authorize(): bool {
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
