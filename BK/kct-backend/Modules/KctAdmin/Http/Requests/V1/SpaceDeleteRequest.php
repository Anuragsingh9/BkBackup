<?php

namespace Modules\KctAdmin\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\KctAdmin\Rules\SpaceFutureRule;
use Modules\KctAdmin\Rules\SpaceRule;
use Modules\KctAdmin\Services\BusinessServices\factory\BusinessServices\factory\BusinessServices\factory\BusinessServices\factory\BusinessServices\factory\BusinessServices\factory\BusinessServices\factory\AuthorizationService;

/**
 * @OA\Schema(
 *  title="RequestValidation: Space Delete Request",
 *  description="Validates the request body for space delete",
 *  type="object",
 *  required={"space_uuid"},
 *  @OA\Property(property="space_uuid",type="UUID",description="Space Uuid",example="01493146-d018-11ea-9d2a-b82a72a009b4")
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class validate the space delete request
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class SpaceDeleteRequest
 *
 * @package Modules\KctAdmin\Http\Requests\V1
 */
class SpaceDeleteRequest extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules(): array {
        return [
            'space_uuid' => ['required', new SpaceRule('space_uuid', ['not_default' => true])],
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
