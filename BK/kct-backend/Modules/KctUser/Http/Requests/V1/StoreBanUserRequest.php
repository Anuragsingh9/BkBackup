<?php

namespace Modules\KctUser\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

//use Modules\KctUser\Services\AuthorizationService;
use Modules\KctUser\Rules\V1\UserExists;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Modules\KctUser\Services\KctUserAuthorizationService;

/**
 * @OA\Schema(
 *  title="RequestValidation: Ban User from Event Request Validation",
 *  description="Validates the request body for banning a user from event ",
 *  type="object",
 *  required={"user_id", "event_uuid", "ban_reason", "severity"},
 *  @OA\Property(property="user_id",type="int",description="Id of the user",example="12"),
 *  @OA\Property(property="event_uuid",type="UUID",description="Event UUID",example="27dfad4e-7739-11eb-bbb9-88d7f6354d4a"),
 *  @OA\Property(property="severity",type="int",description="Severity of ban",example="1"),
 *  @OA\Property(property="ban_reason",type="string",description="Reason of ban",example="Ban Reason")
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will be used for validating the user ban request.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class StoreBanUserRequest
 *
 * @package Modules\KctUser\Http\Requests\V1
 */
class StoreBanUserRequest extends FormRequest {
    public $authorizationMessage;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules(): array {
        $severity = config('kctuser.user_ban.severity');
        return [
            //
            'user_id'    => ['required'],
            'ban_reason' => ['required', 'string', 'nullable'],
            'event_uuid' => ['required'],
            'severity'   => ['required', 'in:' . implode(',', $severity)],
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Determine if the user is authorized to make this request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     *
     * @return bool
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will use for custom error message for authorization failed
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return void
     */
    protected function failedAuthorization() {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => $this->authorizationMessage ? $this->authorizationMessage : "Unauthorised",
        ], 403));
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
