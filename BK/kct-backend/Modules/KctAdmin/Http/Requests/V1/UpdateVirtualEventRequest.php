<?php

namespace Modules\KctAdmin\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\KctAdmin\Rules\EventRule;
use Modules\KctAdmin\Rules\EventUpdateRule;

/**
 * @OA\Schema(
 *  title="RequestValidation: Virtual Event Update Request Validation",
 *  description="Validates the request body for updating the virtual event ",
 *  type="object",
 *  required={"id", "title", "header_line_1", "header_line_2", "header_text","date","start_time","end_time",
 *     "opening_hours_before","opening_hours_during","opening_hours_after"},
 *
 *  @OA\Property(property="event_uuid", type="UUID", format="text", example="d86810a8-9e9d-11eb-948c-149d9980596a"),
 *  @OA\Property(property="title",type="string",description="Title Of Event",example="Title Of event updated"),
 *  @OA\Property(property="description",type="string",description="Description Of Event",example="Event description"),
 *  @OA\Property( property="date", type="date", description="Date of the event", example="2021-02-19" ),
 *  @OA\Property( property="start_time", type="time", description="Staring time of the event", example="20:00:00"),
 *  @OA\Property( property="end_time", type="time", description="Staring time of the event", example="21:00:00" ),
 *  @OA\Property(property="image",type="image",description="Event Image",example="Header Text"),
 *  @OA\Property( property="manual_opening", type="integer",
 *     description="To indicate if event is currently manually opened", example="0", enum={"0", "1"} ),
 *  @OA\Property(property="is_mono_event",type="integer",description="Event have multi space or not",example="1"),
 *  @OA\Property(property="header_line_1",type="string",description="Event header line 1",example="Header line one"),
 *  @OA\Property(property="header_line_2",type="string",description="Event header line 1",example="Header line two"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will validate request data for updating an event.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class UpdateVirtualEventRequest
 *
 * @package Modules\KctAdmin\Http\Requests
 */
class UpdateVirtualEventRequest extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules(): array {
        $validation = config('kctadmin.modelConstants.events.validations');
        return [
            'event_uuid'     => ["required", new EventRule],
            'title'          => "required|string",
            'date'           => ["required", new EventRule],
            'start_time'     => ['required', 'date_format:H:i:s'],
            'end_time'       => 'required|after:start_time|date_format:H:i:s',
            'manual_opening' => ['nullable', 'in:1,0'],
            'description'    => 'nullable|string',
            'is_mono_event' => 'nullable',
            'is_self_header' => 'nullable|in:0,1',
            'header_line_1' => 'nullable|string',
            'header_line_2' => 'nullable|string',
            'join_code'      => ["required", "regex:/^[a-zA-Z0-9-]+$/", "max:{$validation['join_code_max']}",
                "min:{$validation['join_code_min']}"]
        ];
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton  Determine if the user is authorized to make this request.
     * -----------------------------------------------------------------------------------------------------------------
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
            'msg'    => __('kctadmin::messages.admin_only'),
        ], 403));
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will use for custom error message for authorization failed
     * -----------------------------------------------------------------------------------------------------------------
     *
     */
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
    }

}
