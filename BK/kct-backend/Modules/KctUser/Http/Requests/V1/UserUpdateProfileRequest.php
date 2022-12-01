<?php

namespace Modules\KctUser\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @OA\Schema(
 *  title="RequestValidation: User Register Request Validation",
 *  description="Validates the request body for registering a user account ",
 *  type="object",
 *  required={"email", "fname", "lname", "event_uuid", "password"},
 *  @OA\Property(property="fname",type="string",description="First Name",example="Someone"),
 *  @OA\Property(property="lname",type="string",description="Last Name",example="User"),
 *  @OA\Property(property="avatar",type="string",description="Image of user",example="https://[bucket_name].s3.amazonaws.com/a/b.png")
 * )
 */
class UserUpdateProfileRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'fname'  => 'required|string',
            'lname'  => 'required|string',
            'avatar' => 'nullable|image',
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
