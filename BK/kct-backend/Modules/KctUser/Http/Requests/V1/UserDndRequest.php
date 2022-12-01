<?php

namespace Modules\KctUser\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\KctUser\Rules\V1\EventExists;
use Modules\KctUser\Services\KctUserAuthorizationService;

/**
 * @OA\Schema(
 *  title="RequestValidation: UserDndRequest",
 *  description="Validates the request body for banning a user from event ",
 *  type="object",
 *  required={"user_id", "event_uuid", "ban_reason", "severity"},
 *  @OA\Property(property="field",type="string",description="Field Name",example="field name"),
 *  @OA\Property(property="value",type="string",description="Value of user visibility column",example="value")
 * )
 *
 */
class UserDndRequest extends FormRequest {
    /**
     * @var array|string|null
     */
    private $authorizationMessage;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'event_uuid'   => ['required', new EventExists],
            'active_state' => 'required|in:1,2',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        if (!KctUserAuthorizationService::getInstance()->isUserEventMember($this->input('event_uuid'))) {
            $this->authorizationMessage = __('kctuser::message.not_belongs_event');
            return false;
        }
        return true;
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
    }

    protected function failedAuthorization() {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => $this->authorizationMessage ? $this->authorizationMessage : 'Unauthorized',
        ], 403));
    }
}
