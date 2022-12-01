<?php

namespace Modules\SuperAdmin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Cocktail\Services\AuthorizationService;
use Illuminate\Contracts\Validation\Validator;
use Modules\SuperAdmin\Rules\UserTagExists;

/**
 * @OA\Schema(
 *  title="RequestValidation: User Tags Update Request Validation",
 *  description="Validates the request body for updating a user tag ",
 *  type="object",
 *  required={"user_id"},
 *  @OA\Property(
 *      property="user_id",
 *      type="int",
 *      description="Id of the user",
 *      example="12"
 *  ),
 *  @OA\Property(
 *      property="tag_en",
 *      type="string",
 *      description="Tag's english name",
 *      example="test"
 *  ),
 *  @OA\Property(
 *      property="tag_fr",
 *      type="string",
 *      description="Tag's french name",
 *      example="test"
 *  ),
 * )
 *
 * Class UpdateTagRequest
 * @package Modules\SuperAdmin\Http\Requests
 */
class UpdateTagRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
            'id'   => ['required', new UserTagExists]
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }
    
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
    }
}
