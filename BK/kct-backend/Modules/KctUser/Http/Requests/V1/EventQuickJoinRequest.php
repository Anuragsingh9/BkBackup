<?php

namespace Modules\KctUser\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Modules\KctUser\Rules\EventRegisterRule;
use Modules\KctUser\Rules\V1\EventAndSpaceOpenOrNotStarted;
use Modules\KctUser\Rules\IsFutureEvent;

/**
 * @OA\Schema(
 *  title="RequestValidation: EventQuickJoinRequest",
 *  description="To validate the request before adding user to event/space",
 *  type="object",
 *  required={"event_uuid"},
 *  @OA\Property(property="event_uuid",type="string",
 *     description="UUID of future event",example="123e4567-e89b-12d3-a456-426614174000")
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will validate request data for user to event quick join(from QSS page)
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class EventQuickJoinRequest
 *
 * @package Modules\KctUser\Http\Requests\V1
 */
class EventQuickJoinRequest extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules() {
        return [
            'event_uuid' => ['required', new EventAndSpaceOpenOrNotStarted, new EventRegisterRule],
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Determine if the user is authorized to make this request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }
}
