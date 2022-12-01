<?php

namespace Modules\Cocktail\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Cocktail\Rules\EventAndSpaceOpenOrNotStarted;

/**
 * @OA\Schema(
 *  title="RequestValidation: User Register Request Validation",
 *  description="Validates the request body for registering a user account ",
 *  type="object",
 *  required={"email", "fname", "lname", "event_uuid", "password"},
 *  @OA\Property(
 *      property="email",
 *      type="email",
 *      description="Unique email of user",
 *      example="example@example.com"
 *  ),
 *  @OA\Property(
 *      property="fname",
 *      type="string",
 *      description="First Name",
 *      example="Someone"
 *  ),
 *  @OA\Property(
 *      property="lname",
 *      type="string",
 *      description="Last Name",
 *      example="User"
 *  ),
 *  @OA\Property(
 *      property="event_uuid",
 *      type="uuid",
 *      description="Event UUID for which user wants to register after creating an account",
 *      example="123e4567-e89b-12d3-a456-426614174000"
 *  ),
 *  @OA\Property(
 *      property="password",
 *      type="string",
 *      description="Password for user account to login into account",
 *      example="••••••••"
 *  ),
 * )
 *
 * Class UserRegisterRequest
 * @package Modules\Cocktail\Http\Requests
 */
class UserRegisterRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'email'      => 'required|email',
            'fname'      => 'required|string|max:100',
            'lname'      => 'required|string|max:100',
            'event_uuid' => ['required', new EventAndSpaceOpenOrNotStarted],
            'password'   => 'required|confirmed|string|min:' . config('cocktail.validations.user.password_min')
                . '|max:' . config('cocktail.validations.user.password_max'),
            'lang'       => 'nullable|in:' . implode(',', config('cocktail.available_lang')),
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
