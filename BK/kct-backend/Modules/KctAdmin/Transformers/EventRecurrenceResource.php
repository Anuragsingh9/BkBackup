<?php

namespace Modules\KctAdmin\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\KctAdmin\Traits\KctHelper;

/**
 * @OA\Schema(
 *  title="APIResource: EventRecurrenceResource",
 *  description="This resource contains event recurrence data",
 *  @OA\Property( property="start_date", type="time", description="Recurrence Start Date", example="2021-12-13"),
 *  @OA\Property( property="end_date", type="time", description="Recurrence End Date", example="2021-12-13"),
 *  @OA\Property( property="recurrence_type",type="integer",description="Recurrence Type, 1 for daily",example="1", enum={"1", "2"}),
 * )
 *
 * Class EventRecurrenceResource
 *
 * @package Modules\KctAdmin\Transformers
 */
class EventRecurrenceResource extends JsonResource {
    use KctHelper;

    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        return [
            'start_date'      => $this->resource->start_date,
            'end_date'        => $this->resource->end_date,
            'recurrence_type' => $this->resource->recurrence_type,
            'is_started'      => Carbon::today()->isAfter($this->resource->start_date) ? 1 : 0,
        ];
    }
}
