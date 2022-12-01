<?php

namespace Modules\KctAdmin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *  title="RequestValidation: Crete scenery data",
 *  description="Validates the request body for create scenery data",
 *  type="object",
 *  required={"event_uuid"},
 *  @OA\Property(property="event_uuid",type="UUID",description="Event Uuid",example="01493146-d018-11ea-9d2a-b82a72a009b4"),
 *  @OA\Property(property="asset_id",type="integer",description="asset id",example="1"),
 *  @OA\Property(property="top_background_color",type="string",description="Color of the top background",
 *     example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"
 *  ),
 *  @OA\Property(property="component_opacity",type="integer",description="Capacity of component",example=14)
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will validate the event scenery request
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class EventSceneryRequest
 * @package Modules\KctAdmin\Http\Requests
 */
class EventSceneryRequest extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules(): array {
        return [
            'event_uuid'           => "required|exists:tenant.events,event_uuid",
            'asset_id'             => "nullable|integer",
            'top_background_color' => "required",
            'component_opacity'    => "required"
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
