<?php

namespace Modules\KctUser\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *  title="RequestValidation: User Register Request Validation",
 *  description="Validates the request body for registering a user account ",
 *  type="object",
 *  required={"email", "fname", "lname", "event_uuid", "password"},
 *  @OA\Property(property="entity_type",type="integer",description="Type of entity"),
 *  @OA\Property(property="entity_id",type="integer",description="Entity id"),
 *  @OA\Property(property="position",type="string",description="Position",),
 *  @OA\Property( property="entity_name", type="string", description="entity name")
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will be use for validating the user update entity request.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class UserUpdateEntityRequest
 *
 * @package Modules\KctUser\Http\Requests\V1
 */
class UserUpdateEntityRequest extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will be used for custom error message
     * -----------------------------------------------------------------------------------------------------------------
     */
    protected function prepareForValidation() {
        $this->merge([
            // if entity type is 2 then change it to 1 (2 previous company, 1 now company)
            'entity_type' => $this->input('entity_type') == 2 ? 1 : 2,
        ]);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules() {

        $entity = ['nullable', Rule::exists('tenant.entities', 'id')->where(function ($q) {
            $q->where('entity_type_id', $this->entity_type);
        })];

        $validation = config('kctuser.validations.entity');

        return [
            'entity_type' => 'required|in:2,1',
            'entity_id'   => $entity,
            'position'    => 'nullable|string',
            'entity_name' => [
                "nullable",
                "required_without:entity_id",
                "string",
                "max:{$validation['long_name_max']}",
                "min:{$validation['long_name_min']}",
                Rule::unique('tenant.entities', 'long_name')->where(function ($q) {
                    $q->where('entity_type_id', $this->entity_type);
                }),
            ],
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
