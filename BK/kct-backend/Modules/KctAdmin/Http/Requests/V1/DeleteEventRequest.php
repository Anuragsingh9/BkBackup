<?php

namespace Modules\KctAdmin\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Modules\KctAdmin\Rules\EventRule;


/**
 * @OA\Schema(
 *  title="RequestValidation: To Validate event before delete",
 *  description="Validates the request body for updating the virtual event ",
 *  type="object",
 *  required={"id", "title", "header_line_1", "header_line_2", "header_text","date","start_time","end_time",
 *     "opening_hours_before","opening_hours_during","opening_hours_after"},
 *
 *  @OA\Property(property="event_uuid", type="UUID", format="text", example="d86810a8-9e9d-11eb-948c-149d9980596a"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This method will validate request for deleting an event.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class UpdateVirtualEventRequest
 *
 * @package Modules\KctAdmin\Http\Requests
 */
class DeleteEventRequest extends FormRequest {
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules(): array {
        return [
            'event_uuid' => ['required', new EventRule],
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
        return true;
    }
}
