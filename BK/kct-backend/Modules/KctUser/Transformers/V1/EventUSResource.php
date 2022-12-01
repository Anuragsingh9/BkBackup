<?php

namespace Modules\KctUser\Transformers\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Modules\KctAdmin\Entities\GroupSetting;
use Modules\KctAdmin\Transformers\EventLiveImagesResource;
use Modules\KctAdmin\Transformers\EventLiveVideoResource;
use Modules\KctAdmin\Transformers\V1\MomentResource;
use Modules\KctUser\Services\EventTimeService;
use Modules\KctUser\Services\KctCoreService;
use Modules\KctUser\Traits\Services;

/**
 * @OA\Schema(
 *  title="APIResource: EventResource",
 *  description="Physical Event Resource",
 *  @OA\Property(property="event_uuid",type="uuid",description="Unique UUID of event",example="123e4567-e89b-12d3-a456-426614174000"),
 *  @OA\Property(property="event_title",type="string",description="Title for Event",example="Event Title"),
 *  @OA\Property(property="date",type="date",description="Date of Event",example="2020-12-31"),
 *  @OA\Property(property="start_time",type="time",description="Start time of event",example="23:59:59"),
 *  @OA\Property(property="end_time",type="time",description="End time of event",example="23:59:59"),
 *  @OA\Property(property="is_participant",type="boolean",description="Is Event territory",example="0"),
 *  @OA\Property(property="organiser_fname",type="string",description="Full name of event organiser",example="Organiser Name"),
 *  @OA\Property(property="organiser_lname",type="string",description="Full name of event organiser",example="Organiser Name"),
 *  @OA\Property(property="is_presenter",type="integer",description="To check if user is presenter",example="0"),
 *  @OA\Property(property="is_moderator",type="integer",description="To check if user is moderator",example="0"),
 *  @OA\Property(property="is_host",type="integer",description="To check if user is host",example="0"),
 *  @OA\Property(property="event_version",type="boolean",description="Is Event territory",example="0"),
 *  @OA\Property(property="conference_type",type="string",description="Header Text for Event",example="Event Header"),
 *  @OA\Property(property="is_banned",type="integer",description="To check if user is banned",example="0"),
 *  @OA\Property(property="is_mono_present",type="integer",description="To indicate if mono space present or not in event",example="0"),
 * )
 *
 */
class EventUSResource extends JsonResource {
    use Services;

    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        $start = Carbon::createFromFormat('Y-m-d H:i:s', $this->resource->start_time);
        $end = Carbon::createFromFormat('Y-m-d H:i:s', $this->resource->end_time);

        $h = $this->userServices()->kctService->getEventHeaders($this->resource);

//        $image = $this->userServices()->adminService->getEventImage();
        $image = $this->getGroupEventImage($this->resource);
        $result = [
            'event_uuid'             => $this->event_uuid,
            'event_title'            => $this->title,
            'event_header_text'      => $this->header_text,
            'event_description'      => $this->description,
            'event_date'             => $start->toDateString(),
            'event_start_time'       => $start->toTimeString(),
            'event_end_time'         => $end->toTimeString(),
            'event_end_date'         => $end->toDateString(),
            'event_type'             => $this->resource->event_type,
            "header_line_one"        => $h['h1'] ?? null,
            "header_line_two"        => $h['h2'] ?? null,
            "manual_opening"         => $this->manual_opening,
            "is_dummy_event"         => (int)$this->event_settings["is_dummy_event"] ?? 0,
            "event_image"            => $image,
            "conference_type"        => $this->resource->type == 2 ? 'zoom' : null,
            'opening_before'         => 0,
            'opening_after'          => 0,
            'opening_during'         => 1,
            'is_mono_present'        => $this->resource->is_mono_type ? 1 : 0,
            'moments'                => $this->whenLoaded('moments',
                $this->resource->relationLoaded('moments') ? MomentResource::collection($this->resource->moments) : []
            ),
            'event_live_images'      => isset($this->resource->event_settings['event_images']) ? EventLiveImagesResource::collection($this->resource->event_settings['event_images']) : [],
            'event_live_video_links' => isset($this->resource->event_settings['event_video_links']) ? EventLiveVideoResource::collection($this->resource->event_settings['event_video_links']) : [],
            'pilot_panel'            => $this->when(Auth::user()->id == $this->userServices()->validationService->getEventCreateByUserId($this->resource->event_uuid), true),
            'is_auto_created'        => $this->resource->event_settings['is_auto_key_moment_event'] ?? 0,
            'event_conv_limit'       => (int)($this->resource->event_settings['event_conv_limit'] ?? 4),
            'event_grid_rows'        => $this->resource->event_settings['event_grid_rows'] ?? 4,
            'virtual_backgrounds'    => [
                'system_backgrounds' => $this->resource->system_backgrounds,
        ],
            'current_system_background_id' => $this->resource->current_system_background_id  ? (int)$this->resource->current_system_background_id : null,
        ];

        return $result;
    }

    public function getGroupEventImage($event) {
        $key = 'event_image';
        $groupId = $event->group->id;
        $data = GroupSetting::where('group_id', $groupId)->where('setting_key', $key)->first();
        if ($data->setting_value['event_image'] ?? null) {
            return $data->setting_value['event_image'] ?
                $this->userServices()->fileService->getFileUrl($data->setting_value['event_image']) : null;
        }
        return null;
    }
}
