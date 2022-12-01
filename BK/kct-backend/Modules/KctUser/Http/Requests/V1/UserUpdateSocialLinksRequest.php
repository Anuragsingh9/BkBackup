<?php

namespace Modules\KctUser\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @OA\Schema(
 *  title="RequestValidation: UserUpdateSocialLinksRequest",
 *  description="Validates the request body for banning a user from event ",
 *  type="object",
 *  required={"user_id", "event_uuid", "ban_reason", "severity"},
 *  @OA\Property(property="field",type="string",description="Field Name",example="field name"),
 *  @OA\Property(property="value",type="string",description="Value of user visibility column",example="value")
 * )
 *
 */
class UserUpdateSocialLinksRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'field' => 'required|in:linkedin,facebook,twitter,instagram',
            'value' => 'required|url',
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
