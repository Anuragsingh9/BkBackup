<?php

namespace Modules\Cocktail\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Cocktail\Rules\EventAndSpaceOpenOrNotStarted;


/**
 * @OA\Schema(
 *  title="RequestValidation: EventJoinRequest",
 *  description="To validate the request before adding user to event/space",
 *  type="object",
 *  required={"event_uuid"},
 *  @OA\Property(
 *      property="event_uuid",
 *      type="string",
 *      description="UUID of future event",
 *      example="123456"
 *  ),
 *  @OA\Property(
 *      property="space_uuid",
 *      type="string",
 *      description="Optional: UUID of space to join of that event",
 *      example="123e4567-e89b-12d3-a456-426614174000"
 *  ),
 * )
 *
 * Class EventJoinRequest
 * @package Modules\Cocktail\Http\Requests
 */
class EventJoinRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'event_uuid' => ['required', new EventAndSpaceOpenOrNotStarted],
            'space_uuid' => ['nullable'],
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
