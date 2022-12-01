<?php

namespace Modules\KctUser\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *  title="RequestValidation: EventSpaceAddUserRequest",
 *  description="Validates the request body for adding a user in space ",
 *  type="object",
 *  required={
 *     "space_uuid",
 *     "event_uuid"
 * },
 *  @OA\Property(property="dummyUserId",type="integer",description="ID of user",example="1"),
 *  @OA\Property(property="eventuuid",type="uuid",description="UUID of Event",example="123e4567-e89b-12d3-a456-426614174000"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will be use for validating the request of remove dummy user from conversation.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class KickDummyUserRequest
 *
 * @package Modules\KctUser\Http\Requests\V1
 */
class KickDummyUserRequest extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules() {
        return [
            'eventUuid'   => 'required|exists:tenant.events,event_uuid',
            'dummyUserId' => [
                'required',
                Rule::exists('tenant.event_dummy_users', 'dummy_user_id')
                    ->where('event_uuid', $this->input('eventUuid'))
            ]
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Determine if the user is authorized to make this request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }
}
