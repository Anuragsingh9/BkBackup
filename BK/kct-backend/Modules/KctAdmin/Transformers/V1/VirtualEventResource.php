<?php

namespace Modules\KctAdmin\Transformers\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\KctAdmin\Services\BusinessServices\factory\BusinessServices\factory\BusinessServices\factory\BusinessServices\factory\BusinessServices\factory\BusinessServices\factory\BusinessServices\factory\KctCoreService;
use Modules\KctAdmin\Services\BusinessServices\factory\BusinessServices\factory\BusinessServices\factory\BusinessServices\factory\BusinessServices\factory\BusinessServices\factory\BusinessServices\factory\KctService;
use Modules\KctAdmin\Traits\KctHelper;
use Modules\KctAdmin\Transformers\DraftEventResource;

/**
 * @OA\Schema(
 *  title="Resource: VirtualEventResource",
 *  description="Virtual Event Resource",
 *
 *  @OA\Property( property="event_uuid", type="UUID", format="text", example="d86810a8-9e9d-11eb-948c-149d9980596a"),
 *  @OA\Property( property="title",type="string",description="Title Of Event",example="Title Of Event"),
 *  @OA\Property( property="header_text", type="string", description="Header Text for Event", example="Event Header" ),
 *  @OA\Property( property="header_line_1", type="string", description="Header Line One for Event",
 *      example="Event Header Line 1"
 *  ),
 *  @OA\Property( property="header_line_2", type="string", description="Header Line Two for Event",
 *      example="Event Header Line 2"
 *  ),
 *  @OA\Property( property="description", type="string", description="Event Description", example="Event Description"),
 *  @OA\Property( property="date", type="date", description="Date of the event", example="2021-02-19" ),
 *  @OA\Property( property="start_time", type="time", description="Staring time of the event", example="20:00:00"),
 *  @OA\Property( property="end_time", type="time", description="Staring time of the event", example="21:00:00" ),
 *  @OA\Property( property="manual_opening", type="integer", description="To indicate if event is currently manually opened",
 *      example="0", enum={"0", "1"}
 *  ),
 *  @OA\Property( property="is_dummy_event", type="integer", description="To indicate if event follows dummy users or not",
 *      example="0", enum={"0", "1"}
 *  ),
 *  @OA\Property( property="type", type="integer", description="To indicate event type, 1=Networking, 2-Content",
 *      example="1", enum={"1", "2"}
 *  ),
 *  @OA\Property( property="organiser", type="object", description="Organiser user resource",
 *     ref="#/components/schemas/UserResource"
 *  ),
 *  @OA\Property( property="time_state", type="object", description="To indicate if event is live",
 *      @OA\Property( property="is_past", type="integer", description="To indicate if event is past", example="0",
 *      enum={"0", "1"}
 *      ),
 *      @OA\Property( property="is_live", type="integer", description="To indicate if event is live", example="0",
 *      enum={"0", "1"}
 *      ),
 *      @OA\Property( property="is_future", type="integer", description="To indicate if event is future", example="0",
 *      enum={"0", "1"}
 *      ),
 *  ),
 *  @OA\Property(property="is_self_header",type="integer",description="To indicate if event has own header",example="1",
 *      enum={"0", "1"}
 *  ),
 *  @OA\Property( property="event_draft", type="object", description="Draft event resource",
 *     ref="#/components/schemas/DraftEventResource"
 *  ),
 *  @OA\Property(property="is_auto_key_moment_event",type="integer",description="This indicate the event is auto create",
 *     example="1", enum={"0", "1"}
 *  ),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will contain the event resource
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class VirtualEventResource
 *
 * @package Modules\KctAdmin\Transformers\V1
 */
class VirtualEventResource extends JsonResource {
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
        $startTime = $this->getCarbonByDateTime($this->resource->start_time);
        $endTime = $this->getCarbonByDateTime($this->resource->end_time);
        $now = Carbon::now();
        return [
            "event_uuid"               => $this->resource->event_uuid,
            "title"                    => $this->resource->title,
            "header_text"              => $this->resource->header_text,
            "header_line_1"            => $this->resource->header_line_1,
            "header_line_2"            => $this->resource->header_line_2,
            "description"              => $this->resource->description,
            "date"                     => $startTime->toDateString(),
            "start_time"               => $startTime->toTimeString(),
            "end_time"                 => $endTime->toTimeString(),
            "join_code"                => $this->resource->join_code,
//            'image'           => $filesService->getFileUrl($this->resource->image),
            "manual_opening"           => $this->resource->manual_opening,
            "is_dummy_event"           => (int)$this->resource->event_settings["is_dummy_event"] ?? 0,
            'type'                     => $this->resource->type,
            'organiser'                => $this->whenLoaded(
                'createdBy',
                $this->resource->relationLoaded('createdBy') && isset($this->resource->createdBy)
                    ? new UserResource($this->resource->createdBy)
                    : null
            ),
            'event_recurrence'         => $this->whenLoaded('eventRecurrenceData', $this->resource->relationLoaded('eventRecurrenceData') && $this->resource->eventRecurrenceData ? [
                'recurrence_type'              => $this->resource->eventRecurrenceData->recurrence_type,
                'recurrence_start_date'        => $this->resource->eventRecurrenceData->start_date,
                'recurrence_end_date'          => $this->resource->eventRecurrenceData->end_date,
//                'recurrence_settings'   => [
//                    'weekdays'        => $this->resource->eventRecurrenceData->recurrences_settings['weekdays'] ?? [],
//                    'month_date'      => $this->resource->eventRecurrenceData->recurrences_settings['month_date'] ?? [],
//                    'repeat_interval' => $this->resource->eventRecurrenceData->recurrences_settings['repeat_interval'] ?? [],
//                ],
                "rec_weekdays"                 => $this->resource->eventRecurrenceData->recurrences_settings['weekdays'] ?? 0,
                "recurrence_ondays"            => $this->resource->eventRecurrenceData->recurrences_settings['month_date'] ?? [],
                "repeat_interval"              => $this->resource->eventRecurrenceData->recurrences_settings['repeat_interval'] ?? 1,
                "recurrence_month_type"        => $this->resource->eventRecurrenceData->recurrences_settings['recurrence_month_type'] ?? 1,
                "recurrence_on_month_week"     => $this->resource->eventRecurrenceData->recurrences_settings['recurrence_on_month_week'] ?? 1,
                "recurrence_on_month_week_day" => $this->resource->eventRecurrenceData->recurrences_settings['recurrence_on_month_week_day'] ?? 'Monday',
            ] : []),
            'time_state'               => [
                'is_part'   => (int)($now->timestamp < $startTime->timestamp),
                'is_live'   => (int)($startTime->timestamp <= $now->timestamp && $now->timestamp <= $endTime->timestamp),
                'is_future' => (int)($endTime->timestamp <= $now->timestamp),
            ],
            'is_self_header'           => $this->resource->event_settings['is_self_header'] ?? 0,
            "event_draft"              => new DraftEventResource($this->resource),
            'is_auto_key_moment_event' => (isset($this->resource->event_settings['is_auto_key_moment_event'])
                && $this->resource->event_settings['is_auto_key_moment_event'])
                ? 1
                : 0
        ];
    }
}
