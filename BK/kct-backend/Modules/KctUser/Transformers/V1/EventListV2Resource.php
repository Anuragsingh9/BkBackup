<?php

namespace Modules\KctUser\Transformers\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\KctUser\Traits\Services;

/**
 * @OA\Schema(
 *  title="APIResource: EventListV2Resource",
 *  description="Physical Event Resource",
 *  @OA\Property(property="event_uuid",type="uuid",description="Unique UUID of event",
 *     example="123e4567-e89b-12d3-a456-426614174000"
 *  ),
 *  @OA\Property(property="event_title",type="string",description="Title for Event",example="Event Title"),
 *  @OA\Property(property="event_date",type="date",description="Date of Event",example="2020-12-31"),
 *  @OA\Property(property="event_start_time",type="time",description="Start time of event",example="23:59:59"),
 *  @OA\Property(property="event_end_time",type="time",description="End time of event",example="23:59:59"),
 *  @OA\Property(property="is_participant",type="boolean",description="Is Event territory",example="0"),
 *  @OA\Property(property="organiser_fname",type="string",description="Full name of event organiser",
 *     example="Organiser Name"
 *  ),
 *  @OA\Property(property="organiser_lname",type="string",description="Full name of event organiser",
 *     example="Organiser Name"
 *  ),
 *  @OA\Property(property="is_presenter",type="integer",description="To check if user is presenter",example="0"),
 *  @OA\Property(property="is_moderator",type="integer",description="To check if user is moderator",example="0"),
 *  @OA\Property(property="is_host",type="integer",description="To check if user is host",example="0"),
 *  @OA\Property(property="event_version",type="boolean",description="Is Event territory",example="0"),
 *  @OA\Property(property="conference_type",type="string",description="Header Text for Event",example="Event Header"),
 *  @OA\Property(property="is_banned",type="integer",description="To check if user is banned",example="0"),
 *  @OA\Property(property="links",type="string",description="Event broadCast links",example="https://www.google.co.in"),
 *  @OA\Property(property="type",type="string",description="Type of Event",example="past, future"),
 *  @OA\Property(property="agenda",type="string",description="Agenda of Event",example="Agenda of event"),
 *  @OA\Property(property="registration_open",type="integer",description="To check if registration is open",example="0"),
 *  @OA\Property(property="registration_closed",type="integer",description="To check if registration is closed",
 *     example="0"
 *  ),
 *  @OA\Property(property="registration_not_open",type="integer",description="To check if registration is not open",
 *     example="1"
 *  ),
 *  @OA\Property(property="share_agenda",type="string",description="Share the agenda of Event",
 *     example="event name, event date and etc."
 *  ),
 *  @OA\Property(property="event_role",type="integer",description="User role in Event",example="0"),
 *  @OA\Property(property="is_vip",type="integer",description="User is VIP or not in the event",example="0"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will return all data related to event.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class EventListV2Resource
 *
 * @package Modules\KctUser\Transformers\V1
 */
class EventListV2Resource extends JsonResource {
    use Services;
    use ServicesAndRepo;

    /**
     * @var mixed
     */
    private $shareAgenda;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Transform the resource into an array.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        if ($this->whenLoaded('draft')) {
            $registration_status = $this->resource->draft;
            $open = $registration_status->event_status == 1 && $registration_status->is_reg_open == 1;
            $notOpen = $registration_status->event_status == 1 && !$registration_status->is_reg_open;
            $closed = $registration_status->event_status == 1 && $registration_status->is_reg_open == 2;
            $this->shareAgenda = $this->resource->draft->share_agenda;
            if ($open) {
                $open = (int)$this->isCurrentlyRegPossible($this->resource->draft);
                $notOpen = $open ? 0 : 1;
            }
        } else {
            $open = 0;
            $notOpen = 1;
            $closed = 0;
        }

        $eventUser = $this->eventUsers->count() ? $this->eventUsers->first() : false;

        $start = Carbon::createFromFormat('Y-m-d H:i:s', $this->resource->start_time);
        $end = Carbon::createFromFormat('Y-m-d H:i:s', $this->resource->end_time);
        $current = Carbon::now();

        $links = null;

        if ($this->resource->isAdmin && isset($this->resource->event_settings['manual_access_code'])) {
            $links = [
                'access_code' => $this->resource->event_settings['manual_access_code'],
            ];
        }

        return [
            'event_uuid'  => $this->resource->event_uuid,
            'event_title' => $this->resource->title,

            'event_date'       => $start->toDateString(),
            'event_start_time' => $start->toTimeString(),
            'event_end_time'   => $end->toTimeString(),

            'is_participant' => (boolean)$this->resource->eventUsers->count(),

            'organiser_fname'       => $this->resource->organiser->user->fname ?? null,
            'organiser_lname'       => $this->resource->organiser->user->lname ?? null,
            'is_presenter'          => $eventUser ? $eventUser->pivot->is_presenter : 0,
            'is_moderator'          => $eventUser ? $eventUser->pivot->is_moderator : 0,
            'is_host'               => $this->resource->isHostOfAnySpace->count() ? 1 : 0,
            'event_version'         => 2,
            'conference_type'       => null,
            'is_banned'             => $this->selfUserBanStatus ? 1 : 0,
//            'links'                 => $this->whenLoaded('moderatorMoments',
//                $this->resource->relationLoaded('moderatorMoments')
//                    ? $this->userServices()->adminService->getEventBroadcastingLinks($this->resource)
//                    : null
//            ),
            'type'                  => $this->resource->type,
            'agenda'                => $this->whenLoaded('moments',
                $this->resource->relationLoaded('moments') ? $this->resource->moments : null),
            'registration_open'     => $current->timestamp > $end->timestamp ? 0 : (int)$open,
            'registration_closed'   => $current->timestamp > $end->timestamp ? 1 : (int)$closed,
            'registration_not_open' => $current->timestamp > $end->timestamp ? 0 : (int)$notOpen,
            'share_agenda'          => $this->when($this->resource->type == 2, $this->shareAgenda),
            'event_role'            => $this->resource->eventUserRole->first()
                ? $this->resource->eventUserRole->first()->event_user_role
                : 0,
            'is_vip'                => $this->resource->eventUserRole->first()
                ? $this->resource->eventUserRole->first()->is_vip
                : 0,
            'links'                 => $this->when($links, $links),
            'event_type'            => $this->resource->event_type,
        ];
    }

    // This will return true if registration is open and current time is between registration start and end time
    private function isCurrentlyRegPossible($draftEvent): bool {
        $regStart = Carbon::createFromFormat('Y-m-d H:i:s', $draftEvent->reg_start_time);
        $regEnd = Carbon::createFromFormat('Y-m-d H:i:s', $draftEvent->reg_end_time);
        $currentTime = Carbon::now();
        return $currentTime->timestamp >= $regStart->timestamp && $currentTime->timestamp <= $regEnd->timestamp;
    }
}
