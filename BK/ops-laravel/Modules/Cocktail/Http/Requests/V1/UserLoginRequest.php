<?php

namespace Modules\Cocktail\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Cocktail\Rules\EventExists;

/**
 * @OA\Schema(
 *  title="RequestValidation: UserLoginRequest",
 *  description="Validates the request body for logining user with email and password ",
 *  type="object",
 *  required={"email", "password"},
 *  @OA\Property(
 *      property="email",
 *      type="email",
 *      description="Registered Email Address",
 *      example="example@example.com"
 *  ),
 *  @OA\Property(
 *      property="password",
 *      type="string",
 *      description="Password for resprective email account",
 *      example="••••••••"
 *  ),
 *  @OA\Property(
 *      property="event_uuid",
 *      type="string",
 *      description="Optional Event UUID if user want to login directly to a event",
 *      example="123e4567-e89b-12d3-a456-426614174000"
 *  ),
 * )
 *
 * Class UserLoginRequest
 * @package Modules\Cocktail\Http\Requests
 */
class UserLoginRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'email'      => 'required|email',
            'password'   => 'required|string',
            'event_uuid' => ['nullable', new EventExists]
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
