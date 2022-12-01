<?php

namespace Modules\KctUser\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\KctUser\Rules\V1\EventAndSpaceOpenOrNotStarted;

/**
 * @OA\Schema(
 *  title="RequestValidation: OTPVerification",
 *  description="Validated the request body for OTP Verification of user email address",
 *  type="object",
 *  required={"otp"},
 *  @OA\Property(property="otp",type="string",
 *     description="OTP - One time password value, which was sent to respective email address",example="123456"),
 *  @OA\Property(property="event_uuid",type="string",
 *     description="Optional: UUID of event to become a participant of event after successfully verifying email address",
 *     example="123e4567-e89b-12d3-a456-426614174000"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will validate request data for verifying OTP data.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class OtpVerifyRequest
 *
 * @package Modules\KctUser\Http\Requests\V1
 */
class OtpVerifyRequest extends FormRequest {
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
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
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Determine if the user is authorized to make this request.
     * -----------------------------------------------------------------------------------------------------------------
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
