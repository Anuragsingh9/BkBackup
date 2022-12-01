<?php

namespace Modules\KctAdmin\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\KctAdmin\Traits\KctHelper;

/**
 * @OA\Schema(
 *  title="APIResource: EventMinListResource",
 *  description="This resource contains minimum information about the events ",
 *  @OA\Property( property="title",type="string",description="Event name",example="My event"),
 *  @OA\Property( property="date", type="string", description="Date of event", example="31-1-2022" ),
 *  @OA\Property( property="start_time", type="time", description="Staring time of the event", example="06:10:00"),
 *  @OA\Property( property="end_time", type="time", description="Staring time of the event", example="06:10:00" ),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class contain the event data with minimal information
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class EventMinListResource
 *
 * @package Modules\KctAdmin\Transformers
 */
class EventMinListResource extends JsonResource
{
    use KctHelper;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Transform the resource into an array.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param  Request
     * @return array
     */
    public function toArray($request): array
    {
        $startTime = $this->getCarbonByDateTime($this->resource->start_time);
        $endTime = $this->getCarbonByDateTime($this->resource->end_time);
        return [
            "event_uuid" => $this->resource->event_uuid,
            "title"      => $this->resource->title,
            'is_recurrence'       => $this->resource->eventRecurrenceData ? 1 : 0,
            "date"       => $startTime->toDateString(),
            "start_time" => $startTime->toTimeString(),
            "end_time"   => $endTime->toTimeString(),
        ];
    }
}
