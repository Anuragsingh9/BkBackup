<?php

namespace Modules\KctUser\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *  title="RequestValidation: UserEntityDeleteRequest",
 *  description="Validates the request body for banning a user from event ",
 *  type="object",
 *  required={"user_id", "event_uuid", "ban_reason", "severity"},
 *   @OA\Property(property="entity_id",type="id",description="ID of Entity",example="1")
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will be used to validating user delete entity FR request
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class UserEntityDeleteRequest
 *
 * @package Modules\KctUser\Http\Requests\V1
 */
class UserEntityDeleteRequest extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules(): array {
        return [
            'entity_id' => ['required', Rule::exists('tenant.entity_users', 'entity_id')->where(function ($q) {
                $q->where('user_id', Auth::user()->id);
            })],
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Determine if the user is authorized to make this request.
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
