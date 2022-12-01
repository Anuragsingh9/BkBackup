<?php

namespace Modules\KctUser\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\KctUser\Traits\KctHelper;
use Modules\KctUser\Traits\Services;

/**
 * @OA\Schema(
 *  title="APIResource: EventV2PublicResource",
 *  description="Physical Event Resource",
 *  @OA\Property(property="event_uuid",type="uuid",description="Unique UUID of event",example="123e4567-e89b-12d3-a456-426614174000"),
 *  @OA\Property(property="event_title",type="string",description="Title for Event",example="Event Title"),
 *  @OA\Property(property="date",type="date",description="Date of Event",example="2020-12-31"),
 *  @OA\Property(property="start_time",type="time",description="Start time of event",example="23:59:59"),
 *  @OA\Property(property="end_time",type="time",description="End time of event",example="23:59:59"),
 *  @OA\Property(property="space_moods",type="array",description="Diffrent space moods",@OA\Items(ref="#/components/schemas/EventSpaceResourcePublic")),
 *  @OA\Property(property="header_line_1",type="string",description="Heading line for space",example="Space test"),
 *  @OA\Property(property="header_line_2",type="string",description="Heading line for space",example="Space test"),
 *  @OA\Property(property="agenda",type="string",description="Agenda of the event",example="Event Details"),
 * )
 *
 */
class EventV2PublicResource extends JsonResource {
    use Services, KctHelper;

    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        $h = $this->userServices()->kctService->getEventHeaders($this->resource);
        return [
            'event_uuid'    => $this->resource->event_uuid,
            'event_title'   => $this->resource->title,
            'date'          => $this->getCarbonByDateTime($this->resource->start_time)->toDateString(),
            'start_time'    => $this->getCarbonByDateTime($this->resource->start_time)->toTimeString(),
            'end_time'      => $this->getCarbonByDateTime($this->resource->end_time)->toTimeString(),
            'space_moods'   => EventSpaceResourcePublic::collection($this->spaces),
            'header_line_1' => $h['h1'] ?? null,
            'header_line_2' => $h['h2'] ?? null,
            'agenda'        => $this->whenLoaded('moments',
                          $this->resource->relationLoaded('moments') ? $this->resource->moments : null),
        ];
    }
}
