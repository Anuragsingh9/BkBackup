<?php

namespace Modules\KctAdmin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\KctAdmin\Rules\DraftEventRule;

/**
 * @OA\Schema(
 *  title="RequestValidation: Registration window request validation Validation",
 *  description="Validates the request body for updating event registeration opening and closing time ",
 *  type="object",
 *  required={"event_uuid", "reg_start_date", "reg_start_time", "reg_end_date","reg_end_time","share_agenda","event_status"},
 *  @OA\Property(property="event_uuid", type="UUID", format="text", example="d86810a8-9e9d-11eb-948c-149d9980596a"),
 *  @OA\Property(property="reg_start_date",type="date",description="Registration start date of event",example="2020-12-31"),
 *  @OA\Property(property="reg_start_time",type="time",description="Registration start time of event",example="12:12:59"),
 *  @OA\Property(property="reg_end_date",type="date",description="Registration start date of event",example="2020-12-31"),
 *  @OA\Property(property="reg_end_time",type="integer",description="Registration end time of event",example="12:12:59",
 *      enum={"0", "1"}
 *  ),
 *  @OA\Property(property="share_agenda",type="integer",description="To share agenda on HCT side 0=hide, 1=share",
 *     example="1", enum={"0", "1"}
 *  ),
 *  @OA\Property(property="event_status",type="integer",description="Status of event, 1= Live, 2= draft",example="2"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will validate the update draft event requests
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class UpdateDraftEventRequest
 *
 * @package Modules\KctAdmin\Http\Requests
 */
class UpdateDraftEventRequest extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules(): array {
        return [
            'event_uuid'     => ["required", new DraftEventRule($this->event_uuid)],
            'reg_start_date' => ["nullable", new DraftEventRule($this->event_uuid, $this->reg_end_date)],
            'reg_start_time' => ["nullable", new DraftEventRule($this->event_uuid, $this->reg_start_date)],
            'reg_end_date'   => ["nullable", new DraftEventRule($this->event_uuid)],
            'reg_end_time'   => ["nullable", new DraftEventRule($this->event_uuid, $this->reg_end_date)],
            'share_agenda'   => ["nullable", "in:0,1"],
            'event_status'   => ["nullable", "in:1,2"], // 1 for live, 2 for draft
            'is_reg_open'    => ["nullable", "in:0,1,2"] // 1 for open, 2 for close
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Determine if the user is authorized to make this request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return bool
     */
    public function authorize(): bool {
        return true;
    }
}
