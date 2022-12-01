<?php

namespace Modules\KctAdmin\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\KctAdmin\Traits\KctHelper;
use Modules\KctAdmin\Transformers\V1\UserResource;

/**
 * @OA\Schema(
 *  title="APIResource: EventListResource",
 *  description="This resource contains list of events ",
 *  @OA\Property( property="event_uuid",type="string",description="Event uuid",example="0bd13a00-14a0-11ec-a4fa-74867a0dc41b"),
 *  @OA\Property( property="title",type="string",description="Event name",example="My event"),
 *  @OA\Property( property="date", type="string", description="Date of event", example="31-1-2022" ),
 *  @OA\Property( property="start_time", type="time", description="Staring time of the event", example="06:10:00"),
 *  @OA\Property( property="end_time", type="time", description="Staring time of the event", example="07:10:00" ),
 *  @OA\Property( property="type", type="integer", description="To indicate event type, 1=Networking, 2-Content", example="1", enum={"1", "2"} ),
 *  @OA\Property( property="organiser",type="object",description="Orgnisers",ref="#/components/schemas/UserResource"),
 * )
 *
 * Class EventListResource
 *
 * @package Modules\KctAdmin\Transformers
 */
class EventListResource extends JsonResource {
    use KctHelper;

    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        return [
            "event_uuid"      => $this->resource->event_uuid,
            "title"           => $this->resource->title,
            "start_date"      => $this->getEventDateTime($this->resource, 'start_date'),
            "start_time"      => $this->getEventDateTime($this->resource, 'start_time'),
            "end_date"        => $this->getEventDateTime($this->resource, 'end_date'),
            "end_time"        => $this->getEventDateTime($this->resource, 'end_time'),
            'type'            => $this->resource->type,
            'organiser'                => $this->whenLoaded(
                'createdBy',
                $this->resource->relationLoaded('createdBy') && isset($this->resource->createdBy)
                    ? new UserResource($this->resource->createdBy)
                    : null
            ),
            "recurrence_data" => $this->whenLoaded('eventRecurrenceData', $this->resource->relationLoaded('eventRecurrenceData')
                ? [
                    'recurrence_start_date' => $this->getEventDateTime($this->resource, 'recurring_start_date'),
                    'recurrence_end_date'   => $this->getEventDateTime($this->resource, 'recurring_end_date')
                ] : null
            ),
            "display_date"    => $this->resource->eventRecurrenceData
                ? $this->getEventDateTime($this->resource, 'recurring_end_date')
                : $this->getEventDateTime($this->resource, 'end_date'),
            'event_type' => $this->resource->event_type,

        ];
    }
}
