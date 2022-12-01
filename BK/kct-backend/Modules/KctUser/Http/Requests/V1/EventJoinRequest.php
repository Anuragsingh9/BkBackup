<?php

namespace Modules\KctUser\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\KctAdmin\Rules\EventTimeRule;
use Modules\KctUser\Rules\EventRegisterRule;
use Modules\KctUser\Rules\V1\EventAndSpaceOpenOrNotStarted;


/**
 * @OA\Schema(
 *  title="RequestValidation: EventJoinRequest",
 *  description="To validate the request before adding user to event/space",
 *  type="object",
 *  required={"event_uuid"},
 *  @OA\Property(property="event_uuid",type="uuid",
 *     description="UUID of future event",example="123e4567-e89b-12d3-a456-426614174000"),
 *  @OA\Property(property="space_uuid",type="string",
 *     description="Optional: UUID of space to join of that event",example="123e4567-e89b-12d3-a456-426614174000")
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will validate request data for joining an user into an event.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class EventJoinRequest
 *
 * @package Modules\KctUser\Http\Requests\V1
 */
class EventJoinRequest extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules() {
        return [
            'event_uuid' => ['required', new EventTimeRule(), new EventRegisterRule],
            'space_uuid' => ['nullable'],
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
