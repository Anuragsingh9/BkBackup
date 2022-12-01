<?php

namespace Modules\KctAdmin\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\KctAdmin\Entities\EventMeta;
use Modules\KctAdmin\Traits\KctHelper;
use Modules\KctAdmin\Transformers\V1\MomentResource;

/**
 * @OA\Schema(
 *  title="APIResource: DraftEventResource",
 *  description="This resource contains events which are in invitation plan phase ",
 *  @OA\Property( property="event_uuid",type="string",description="Event uuid",
 *     example="0bd13a00-14a0-11ec-a4fa-74867a0dc41b"
 *  ),
 *  @OA\Property( property="name",type="string",description="Event name",example="My event"),
 *  @OA\Property( property="description", type="string", description="Event description", example="Internal meetings" ),
 *  @OA\Property( property="start_time", type="time", description="Staring time of the event",
 *      example="2021-12-13 06:10:00"
 *  ),
 *  @OA\Property( property="end_time", type="time", description="Staring time of the event", example="2021-12-13 06:10:00"),
 *  @OA\Property( property="type", type="integer", description="To indicate event type, 1=Networking, 2-Content",
 *      example="1", enum={"1", "2"}
 *  ),
 *  @OA\Property( property="reg_start", type="time", description="Event registration opening time",
 *      example="2021-12-13 06:10:00"
 *  ),
 *  @OA\Property( property="reg_end", type="time", description="Event registration closing time",
 *     example="2021-12-13 06:10:00"
 *  ),
 *  @OA\Property( property="agenda",type="object",description="Current Group Object",
 *     ref="#/components/schemas/MomentResource"
 *  ),
 *  @OA\Property( property="share_agenda",type="integer",description="Privacy of agenda",example="1"),
 *  @OA\Property( property="status",type="integer",description="Status of the eventr",example="2"),
 *  @OA\Property(property="is_reg_open",type="integer",description="To indicate is registration open or not",
 *     example="1", enum={"0", "1"}
 *  ),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will be used for returning the draft event resource
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class DraftEventResource
 *
 * @package Modules\KctAdmin\Transformers
 */
class DraftEventResource extends JsonResource {
    use KctHelper;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Transform the resource into an array.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        if ($this->whenLoaded('draft') && $this->resource->draft && isset($this->resource->draft->reg_start_time)) {
            $startTime = $this->getCarbonByDateTime($this->resource->start_time);
            $endTime = $this->getCarbonByDateTime($this->resource->end_time);
            $resource = [
                'event_uuid'   => $this->resource->event_uuid,
                'name'         => $this->resource->title,
                'description'  => $this->resource->description,
                "start_time"   => $startTime->toDateTimeString(),
                "end_time"     => $endTime->toDateTimeString(),
                "type"         => $this->resource->type,
                'reg_start'    => $this->resource->draft->reg_start_time,
                'reg_end'      => $this->resource->draft->reg_end_time,
                'agenda'       => $this->when($this->resource->type == 2,
                    MomentResource::collection($this->resource->moments)),
                'share_agenda' => $this->when($this->resource->type == 2,
                    $this->resource->draft->share_agenda),
                'status'       => $this->resource->draft->event_status,
                'is_reg_open'  => $this->resource->draft->is_reg_open,
            ];
        } else {
            $startTime = $this->getCarbonByDateTime($this->resource->start_time);
            $endTime = $this->getCarbonByDateTime($this->resource->end_time);
            $resource = [
                'event_uuid'  => $this->resource->event_uuid,
                'name'        => $this->resource->title,
                'description' => $this->resource->description,
                "start_time"  => $startTime->toDateTimeString(),
                "end_time"    => $endTime->toDateTimeString(),
                "type"        => $this->resource->type,
                'reg_start'   => $this->resource->start_time,
                'reg_end'     => $this->resource->end_time,
                'status'      => EventMeta::$eventStatus_live,
                'is_reg_open' => 0,
            ];
        }
        $resource['recur_data'] = new EventRecurrenceResource($this->resource->eventRecurrenceData);
        return $resource;
    }
}
