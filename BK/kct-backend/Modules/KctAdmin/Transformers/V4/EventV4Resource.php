<?php

namespace Modules\KctAdmin\Transformers\V4;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\KctAdmin\Entities\Moment;
use Modules\KctAdmin\Traits\KctHelper;
use Modules\KctAdmin\Traits\ServicesAndRepo;

/**
 * @OA\Schema(
 *  title="Resource: V4VirtualEventResource",
 *  description="Virtual Event Resource",
 *
 *  @OA\Property( property="event_uuid", type="UUID", format="text", example="d86810a8-9e9d-11eb-948c-149d9980596a"),
 *  @OA\Property( property="event_title",type="string",description="Title Of Event",example="Title Of Event"),
 *  @OA\Property( property="event_description", type="string", description="Event Description", example="Event Description"),
 *  @OA\Property( property="event_start_date", type="date", description="Date of the event", example="2021-02-19" ),
 *  @OA\Property( property="event_start_time", type="time", description="Staring time of the event", example="20:00:00"),
 *  @OA\Property( property="event_end_time", type="time", description="Staring time of the event", example="21:00:00" ),
 *  @OA\Property( property="event_is_demo", type="integer", description="To indicate if event follows dummy users or not",
 *      example="0", enum={"0", "1"}
 *  ),
 *  @OA\Property( property="event_spaces", type="object", description="Organiser user resource",
 *     ref="#/components/schemas/SpaceV4Resource"
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
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will contain the event resource
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class EventV4Resource
 *
 * @package Modules\KctAdmin\Transformers\V4
 */
class EventV4Resource extends JsonResource {
    use ServicesAndRepo;
    use KctHelper;

    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        $startTime = $this->getCarbonByDateTime($this->resource->start_time);
        $endTime = $this->getCarbonByDateTime($this->resource->end_time);
        $now = Carbon::now();

        $broadcast = 0;
        $moderator = 0;

        if ($this->resource->event_type === 2 || $this->resource->event_type === 3) {
            $moment = $this->resource->moments()->whereIn('moment_type', [Moment::$momentType_meeting, Moment::$momentType_webinar])->first();
            if ($moment) {
                $broadcast = $moment->moment_type == Moment::$momentType_meeting ? 1 : 2;
                $moderator = $moment->moderator->user_id;
            }
        }
        return [
            'event_uuid'          => $this->resource->event_uuid,
            'event_title'         => $this->resource->title,
            'event_start_date'    => $startTime->toDateString(),
            'event_start_time'    => $startTime->toTimeString(),
            'event_end_time'      => $endTime->toTimeString(),
            'custom_link'         => [
                'code'     => $this->resource->join_code,
                'full_url' => $this->adminServices()->coreService->prepareParticipantsLink($this->resource),
            ],
            'event_description'   => $this->resource->description,
            'event_spaces'        => SpaceV4Resource::collection($this->spaces),
            'timestamp'           => [
                'now'   => $now->toDateTimeString(),
                'start' => $startTime->toDateTimeString(),
                'end'   => $endTime->toDateTimeString()
            ],
            'event_state'         => [
                'is_past'   => (int)($endTime->timestamp <= $now->timestamp),
                'is_live'   => (int)($startTime->timestamp <= $now->timestamp && $now->timestamp <= $endTime->timestamp),
                'is_future' => (int)($now->timestamp < $startTime->timestamp),
            ],
            'event_is_published'  => $this->resource->draft->event_status === 1 ? 1 : 0,
            'event_is_demo'       => $this->resource->event_settings['is_dummy_event'] ? 1 : 0,
            'is_recurrence'       => $this->resource->eventRecurrenceData ? 1 : 0,
            'event_recurrence'    => $this->whenLoaded('eventRecurrenceData', $this->resource->relationLoaded('eventRecurrenceData') && $this->resource->eventRecurrenceData ? [
                'rec_start_date'        => $this->resource->eventRecurrenceData->start_date,
                'rec_type'              => $this->resource->eventRecurrenceData->recurrence_type,
                'rec_end_date'          => $this->resource->eventRecurrenceData->end_date,
                'rec_weekdays'          => $this->resource->eventRecurrenceData->recurrences_settings['weekdays'] ?? 0,
                'rec_month_date'        => $this->resource->eventRecurrenceData->recurrences_settings['month_date'] ?? [],
                'rec_interval'          => $this->resource->eventRecurrenceData->recurrences_settings['repeat_interval'] ?? 1,
                'rec_month_type'        => $this->resource->eventRecurrenceData->recurrences_settings['recurrence_month_type'] ?? 1,
                'rec_on_month_week'     => $this->resource->eventRecurrenceData->recurrences_settings['recurrence_on_month_week'] ?? 1,
                'rec_on_month_week_day' => $this->resource->eventRecurrenceData->recurrences_settings['recurrence_on_month_week_day'] ?? 'Monday',
            ] : []),
            'event_links'         => $this->when($this->resource->draft->event_status === 1 && $this->resource->links, $this->resource->links),
            'event_scenery'       => $this->resource->event_settings['event_scenery']['category_type_id'] ?? null,
            'event_scenery_asset' => $this->resource->event_settings['event_scenery']['asset_id'] ?? null,
            'event_top_bg_color'  => $this->hexToRgba($this->resource->event_settings['event_scenery']['top_bg_color'] ?? null),
            'event_component_op'  => $this->resource->event_settings['event_scenery']['component_opacity'] ?? null,
            'event_type'          => $this->resource->event_type,
            'event_broadcasting'  => $broadcast,
            'event_moderator'     => $moderator,
            'event_conv_limit'    => $this->resource->event_settings['event_conv_limit'] ?? 4,
            'event_grid_rows'     => $this->resource->event_settings['event_grid_rows'] ?? 4,
        ];
    }
}
