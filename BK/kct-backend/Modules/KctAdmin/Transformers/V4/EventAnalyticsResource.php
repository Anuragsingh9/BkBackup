<?php

namespace Modules\KctAdmin\Transformers\V4;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Redis;
use Modules\KctAdmin\Entities\Event;
use Modules\KctAdmin\Traits\KctHelper;

/**
 * @OA\Schema(
 *  title="Resource: EventAnalyticsResource",
 *  description="Virtual Event Resource",
 *
 *  @OA\Property( property="recurrence_uuid", type="UUID", format="text", example="d86810a8-9e9d-11eb-948c-149d9980596a"),
 *  @OA\Property( property="occurrence_start",type="string",description="Date of ocuurence start",example="Ocurrence start time"),
 *  @OA\Property( property="occurrence_end",type="string",description="Date of ocuurence end",example="Ocurrence end time"),
 *  @OA\Property( property="total_attendance", type="integer", description="Count of total attendance of the users in
 *      event",example="5"
 *  ),
 *  @OA\Property( property="total_registration", type="integer", description="Count of registerted users in event",
 *     example="5"
 *  ),
 *  @OA\Property( property="conv_analytics_data", type="object", description="Conversation analytics data", example=" object of Conversation analytics data"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will contain the analytics resource
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class EventAnalyticsResource
 *
 * @package Modules\KctAdmin\Transformers\V4
 */
class EventAnalyticsResource extends JsonResource {
    use KctHelper;

    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        $startTime = $this->getCarbonByDateTime($this->resource->recurrence_date);
        $endTime = $this->getCarbonByDateTime($this->resource->event->end_time)->setDateFrom($startTime);

        $convoData = $this->countConv($this->resource->eventConversationLog ?? []);
        // Find space host conversation
        $spaceHostConversation = $this->resource->eventConversationLog->filter(function ($conversationLog) {
            return $conversationLog->conversation->is_host;
        });
        $SHConvoData = $this->countConv($spaceHostConversation ?? []);
        // prepare attendance for users(Login, count, time)
        $attendanceData = $this->attendanceDataFormat($this->resource->event);
        // prepare average duration
        $averageDuration = $this->averageDuration($this->resource->eventConversationLog ?? []);

        $sendOnline = $startTime->toDateString() == Carbon::now()->toDateString()
            && $this->event->event_type === Event::$eventType_all_day;
        $onlineCount  = 0;
        if ($sendOnline) {
            $onlineCount = Redis::lrange("KCT_EVT_USRS_{$this->resource->event->event_uuid}", 0, -1);
            $onlineCount = count($onlineCount);
            if($this->resource->event->event_settings['is_dummy_event']) {
                // event is dummy
                $dummyCount = $this->resource->event->dummyRelations->count();
                $onlineCount -= $dummyCount ?: 0;
            }
        }

        return [
            'recurrence_uuid'     => $this->resource->recurrence_uuid,
            'occurrence_start'    => $startTime->toDateTimeString(),
            'occurrence_end'      => $endTime->toDateTimeString(),
            'total_attendance'    => $this->resource->actionLog->attendee_count,
            'total_registration'  => $this->resource->actionLog->reg_count,
            'conv_analytics_data' => $convoData,
            'event_type'          => $this->resource->event->event_type,
            'media_video'         => $this->resource->actionLog->p_video_count,
            'media_image'         => $this->resource->actionLog->p_image_count,
            'sh_conv_data'        => $SHConvoData,
            'attendance_data'     => $attendanceData,
            'average_duration'    => $averageDuration,
            'current_online'      => $this->when($sendOnline, $onlineCount)
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method returns the conversation duration with respect to users count
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $resource
     * @return array
     */
    private function countConv($resource): array {
        $result = [];
        foreach ($resource as $convState) {
            foreach ($convState->conversationSubState as $convoData) {
                $result[] = collect($convoData);
            }
        }
        $collection = collect($result);
        $allData = $collection->groupBy('users_count')->toArray();
        $data = [];
        foreach ($allData as $userCount => $conversationsData) {
            if ((int)$userCount < 2) continue;
            $convoCount = 0;
            $duration = 0;
            foreach ($conversationsData as $row) {
                if ($row['users_count'] > 1) {
                    $convoCount += $row['convo_count'];
                    $duration += (int)$row['convo_duration'];
                }
            }
            $data[] = [
                'user_count'     => (int)$userCount,
                'convo_count'    => $convoCount,
                'convo_duration' => $duration,
            ];
        }
        return $data;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is used for prepare the average duration data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $conversationLog
     * @return array
     */
    private function averageDuration($conversationLog): array {
        $result = [];
        foreach ($conversationLog as $convState) {
            foreach ($convState->conversationSubStateForDuration as $convoData) {
                $result[] = [
                    'user_count' => $convoData['users_count'],
                    'start_time' => $convoData['start_time'],
                    'duration'   => $convoData['duration'],
                ];
            }
        }
        return $result;
    }
}
