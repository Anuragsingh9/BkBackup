<?php

namespace Modules\KctUser\Transformers\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\KctUser\Traits\KctHelper;

/**
 * @OA\Schema(
 *  title="APIResource: EventWithCurrentSpaceResource",
 *  description="Physical Event Resource",
 *  @OA\Property(property="event_uuid",type="uuid",description="Unique UUID of event",example="123e4567-e89b-12d3-a456-426614174000"),
 *  @OA\Property(property="event_title",type="string",description="Title for Event",example="Event Title"),
 *  @OA\Property(property="date",type="date",description="Date of Event",example="2020-12-31"),
 *  @OA\Property(property="start_time",type="time",description="Start time of event",example="23:59:59"),
 *  @OA\Property(property="end_time",type="time",description="End time of event",example="23:59:59"),
 *  @OA\Property(property="current_space",type="object",description="Different Spaces",ref="#/components/schemas/SpaceUSResource"),
 *  @OA\Property(property="spaces",type="array",description="Different Spaces",@OA\Items(ref="#/components/schemas/SpaceUSResource")),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will return data basic event data and user current space data.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class EventWithCurrentSpaceResource
 *
 * @package Modules\KctUser\Transformers\V1
 */
class EventWithCurrentSpaceResource extends JsonResource {

    use KctHelper;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Transform the resource into an array.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        return [
            'event_title'     => $this->resource->title,
            'event_uuid'      => $this->resource->event_uuid,
            'date'            => $this->getCarbonByDateTime($this->resource->start_time)->toDateString(),
            'start_time'      => $this->getCarbonByDateTime($this->resource->start_time)->toTimeString(),
            'end_time'        => $this->getCarbonByDateTime($this->resource->end_time)->toTimeString(),
            'spaces'          => SpaceUSResource::collection($this->resource->spaces),
            'current_space'   =>
                (isset($this->resource->currentSpace) && $this->resource->currentSpace)
                    ? new SpaceUSResource($this->resource->currentSpace)
                    : null,
            'event_role'      => $this->resource->eventUserRelation->first() ? $this->resource->eventUserRelation->first()->event_user_role : 0,
            'is_vip'          => $this->resource->eventUserRelation->first() ? $this->resource->eventUserRelation->first()->is_vip : 0,
            'is_mono_present' => $this->resource->is_mono_type ? 1 : 0,
            'event_end_date' => Carbon::make($this->resource->end_time)->toDateString(),

        ];
    }
}
