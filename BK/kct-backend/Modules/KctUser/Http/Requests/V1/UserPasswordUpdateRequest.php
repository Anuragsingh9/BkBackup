<?php

namespace Modules\KctUser\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @OA\Schema(
 *  title="RequestValidation: UserEntityDeleteRequest",
 *  description="Validates the request body for banning a user from event ",
 *  type="object",
 *  required={"user_id", "event_uuid", "ban_reason", "severity"},
 *   @OA\Property(property="old_password",type="string",description="old password",example="1"),
 *   @OA\Property(property="password",type="string",description="password",example="1")
 * )
 *
 */
class UserPasswordUpdateRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'old_password' => 'required|string',
            'password'     => 'required|confirmed|string|min:8|max:32'

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
