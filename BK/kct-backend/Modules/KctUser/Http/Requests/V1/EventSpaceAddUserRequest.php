<?php

namespace Modules\KctUser\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Modules\KctUser\Rules\V1\DummySpaceMaxCountRule;
use Modules\KctUser\Rules\V1\EventExists;
use Modules\KctUser\Rules\V1\NoEventConversationJoinedRule;
use Modules\KctUser\Rules\V1\SpaceMaxCountRule;
use Modules\KctUser\Rules\V1\SpaceOpen;
use Modules\KctUser\Services\KctUserAuthorizationService;
use Modules\KctUser\Traits\Services;

/**
 * @OA\Schema(
 *  title="RequestValidation: EventSpaceAddUserRequest",
 *  description="Validates the request body for adding a user in space ",
 *  type="object",
 *  required={
 *     "space_uuid",
 *     "event_uuid"
 * },
 *  @OA\Property(property="space_uuid",type="uuid",description="UUID of Space",example="123e4567-e89b-12d3-a456-426614174000"),
 *  @OA\Property(property="event_uuid",type="uuid",description="UUID of Event",example="123e4567-e89b-12d3-a456-426614174000"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will validate the add user in event space request
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class EventSpaceAddUserRequest
 *
 * @package Modules\KctUser\Http\Requests\V1
 */
class EventSpaceAddUserRequest extends FormRequest {
    use Services;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules() {
        return [
            'space_uuid' => ['required', Rule::exists('tenant.event_spaces', 'space_uuid')
                ->where(function ($q) {
                    $q->where('event_uuid', $this->event_uuid);
                    $q->whereNull('deleted_at');
                }),
                new SpaceOpen,
                new SpaceMaxCountRule, new DummySpaceMaxCountRule,
            ],
            'event_uuid' => ['required', new EventExists, new NoEventConversationJoinedRule]
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Determine if the user is authorized to make this request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return bool
     */
    public function authorize(): bool {
        return $this->userServices()->authorizationService->isUserEventMember($this->event_uuid);
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
            'msg'    => __('kctuser::message.not_belongs_event'),
        ], 403));
    }
}
