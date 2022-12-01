<?php

namespace Modules\KctAdmin\Transformers\V4;
;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\KctAdmin\Traits\KctHelper;

/**
 * @OA\Schema(
 *  title="Resource: EventAnalyticsMinResource",
 *  description="Virtual Event Resource",
 *
 *  @OA\Property( property="recurrence_uuid", type="UUID", format="text", example="d86810a8-9e9d-11eb-948c-149d9980596a"),
 *  @OA\Property( property="event_date", type="date", description="date of event",  example="2021-02-19"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will contain the analytics minimum data resource
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class EventAnalyticsMinResource
 *
 * @package Modules\KctAdmin\Transformers\V4
 */
class EventAnalyticsMinResource extends JsonResource {
    use KctHelper;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Transform the resource into an array.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array {
        return [
            'recurrence_uuid' => $this->resource->recurrence_uuid,
            "recurrence_date" => $this->resource->recurrence_date,
        ];
    }
}
