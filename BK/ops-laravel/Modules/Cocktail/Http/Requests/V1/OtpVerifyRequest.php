<?php

namespace Modules\Cocktail\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Cocktail\Rules\EventAndSpaceOpenOrNotStarted;

/**
 * @OA\Schema(
 *  title="RequestValidation: OTPVerification",
 *  description="Validated the request body for OTP Verification of user email address",
 *  type="object",
 *  required={"otp"},
 *  @OA\Property(
 *      property="otp",
 *      type="string",
 *      description="OTP - One time password value, which was sent to respective email address",
 *      example="123456"
 *  ),
 *  @OA\Property(
 *      property="event_uuid",
 *      type="string",
 *      description="Optional: UUID of event to become a participant of event after successfully verifying email address",
 *      example="123e4567-e89b-12d3-a456-426614174000"
 *  ),
 * )
 *
 * Class OtpVerifyRequest
 * @package Modules\Cocktail\Http\Requests
 */
class OtpVerifyRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'otp'        => 'required|string',
            'event_uuid' => ['nullable', new EventAndSpaceOpenOrNotStarted],
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
