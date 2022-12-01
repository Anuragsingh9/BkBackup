<?php


namespace Modules\KctAdmin\Transformers\V4;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\KctAdmin\Entities\Moment;
use Modules\KctAdmin\Traits\KctHelper;


/**
 * @OA\Schema(
 *  title="Resource: EventAnalyticsListResource",
 *  description="Virtual Event Resource",
 *
 *  @OA\Property( property="recurrence_uuid", type="UUID", format="text", example="d86810a8-9e9d-11eb-948c-149d9980596a"),
 *  @OA\Property( property="event_uuid", type="UUID", format="text", example="d86810a8-9e9d-11eb-948c-149d9980596a"),
 *  @OA\Property( property="event_title",type="string",description="Title Of Event",example="Title Of Event"),
 *  @OA\Property( property="event_type", type="string", description="Event type", example="Cafeteria event"),
 *  @OA\Property( property="is_zoom_meeting", type="integer", description="To indicate if zoom meeting is present or not
 *      in the event",example="0", enum={"0", "1"}
 *  ),
 *  @OA\Property( property="is_zoom_webinar", type="integer", description="To indicate if zoom webinar is present or not
 *      in the event",example="0", enum={"0", "1"}
 *  ),
 *  @OA\Property( property="media_video", type="integer", description="Count of media video play in event",example="5"),
 *  @OA\Property( property="media_image", type="integer", description="Count of media image play in event",example="6"),
 *  @OA\Property( property="total_attendance", type="integer", description="Count of total attendance of the users in
 *      event",example="5"
 *  ),
 *  @OA\Property( property="total_registration", type="integer", description="Count of registerted users in event",
 *     example="5"
 *  ),
 *  @OA\Property( property="total_conv_count", type="integer", description="Count of total conversation in event",
 *     example="6"
 *  ),
 *  @OA\Property( property="total_duration", type="integer", description="Duration of conversation in the event in seconds",
 *      example="500"
 *  ),
 *  @OA\Property( property="sh_conv_count", type="integer", description="Count of space host conversation in event",
 *     example="6"
 *  ),
 *  @OA\Property( property="event_date", type="date", description="date of event",  example="2021-02-19"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will contain the analytics resource
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class EventAnalyticsListResource
 *
 * @package Modules\KctAdmin\Transformers\V4
 */
class EventAnalyticsListResource extends JsonResource {
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
        $startTime = $this->getCarbonByDateTime($this->resource->recurrence_date);
        $endTime = $this->getCarbonByDateTime($this->resource->event->end_time);
        $totalConversation = $this->calculateTotalConversation($this->resource->eventConversationLog ?? []);

        return [
            'recurrence_uuid'    => $this->resource->recurrence_uuid,
            "event_uuid"         => $this->resource->event_uuid,
            "event_title"        => $this->resource->event->title,
            'event_type'         => $this->resource->event->event_type,
            'is_zoom_meeting'    => $this->resource->event->moments->where('moment_type', Moment::$momentType_meeting)->count() ? 1 : 0,
            'is_zoom_webinar'    => $this->resource->event->moments->where('moment_type', Moment::$momentType_webinar)->count() ? 1 : 0,
            'media_video'        => $this->resource->actionLog->p_video_count,
            'media_image'        => $this->resource->actionLog->p_image_count,
            'total_attendance'   => $this->resource->actionLog->attendee_count,
            'total_registration' => $this->resource->actionLog->reg_count,
            'total_conv_count'   => $this->resource->actionLog->conv_count,

            'total_duration' => $totalConversation,
            'sh_conv_count'  => $this->resource->actionLog->sh_conv_count,

            "event_date"    => $startTime->format('j M Y') . ' at ' . $startTime->format('g:i A') . ' - ' . $endTime->format('g:i A'),
            'is_recurrence' => $this->resource->event->eventRecurrenceData ? 1 : 0,
            'rec_count'     => $this->resource->recurrence_count - 1,
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method returns the total conversation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $resource
     * @return int
     */
    private function calculateTotalConversation($resource) {
        $result = [];
        foreach ($resource as $convState) {
            foreach ($convState->conversationSubState as $convoData) {
                $result[] = collect($convoData);
            }
        }
        $collection = collect($result);
        $allData = $collection->groupBy('users_count')->toArray();
        $duration = 0;
        foreach ($allData as $userCount => $conversationsData) {
            if ((int)$userCount < 2) continue;
            foreach ($conversationsData as $row) {
                if ($row['users_count'] > 1) {
                    $duration += (int)$row['convo_duration'];
                }
            }
        }
        return $duration;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is used to calculate the conversation length
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $spaces
     * @return float|int|string
     */
    private function calculateConversationLength($spaces) {
        $result = 0;
        $eventEnd = Carbon::make($spaces->first()->event->end_time);
        $isEventLive = Carbon::now()->timestamp < $eventEnd->timestamp;
        $recStart = Carbon::make($this->resource->recurrence_date);
        $conversations = [];
        foreach ($spaces as $space) {
            foreach ($space->conversations as $conversation) {
                if (Carbon::make($conversation->created_at)->toDateString() != $recStart->toDateString()) continue;
                if (!$conversation->end_at) {
                    if ($isEventLive) {
                        // not counting the conversation which are still live
                        continue;
                    } else {
                        $conversation->end_at = $eventEnd;
                    }
                }
                $end = Carbon::make($conversation->end_at);
                $start = $conversation->created_at;
                $result += $end->timestamp - $start->timestamp;
                $conversations[] = $conversation['uuid'] . '-->' . $result . '----' . $conversation->created_at;
            }

        }

        return $result;
    }

    public function countAllEventConversation($spaces): int {
        $count = 0;
        foreach ($spaces as $space) {
            $count += count($space->conversations);
        }
        return $count;
    }


    public function countRegisteredUser($eventUsers): int {
        $count = 0;
        foreach ($eventUsers as $user) {
            if ($user->pivot->is_joined_after_reg == 1) {
                $count++;
            }
        }
        return $count;
    }

}
