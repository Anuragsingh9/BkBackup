<?php

namespace Modules\KctAdmin\Transformers\V1;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\KctAdmin\Entities\Event;
use Modules\KctAdmin\Traits\KctHelper;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\KctAdmin\Transformers\PilotResource;

/**
 * @OA\Schema(
 *  title="APIResource: GroupsListResource",
 *  description="This resource contains Multiple Group data",
 *  @OA\Property(property="id",type="integer",description="ID of user",example="1"),
 *  @OA\Property( property="group_type",type="string",description="Group Type",
 *     example="Super Group, Function Group, Topic Group"
 *  ),
 *  @OA\Property( property="group_key",type="string",description="Group Key",example="grp123"),
 *  @OA\Property( property="is_fav",type="integer",description="It retuns the group fav or not"),
 *  @OA\Property( property="group_name",type="string",description="Group Name",example="Humann connect"),
 *  @OA\Property( property="pilot",type="object",description="pilots",ref="#/components/schemas/PilotResource"),
 *  @OA\Property( property="users",type="integer",description="it returns the user count",example="1"),
 *  @OA\Property( property="events_count",type="integer",description="Show the event count",example="1"),
 *  @OA\Property( property="next_event",type="array",description="Show the next event date and time",@OA\Items(
 *      @OA\Property( property="event_name",type="string",description="Event name",example="My event"),
 *      @OA\Property( property="date",type="string",description="Show the next event date",example="2021-12-02"),
 *      @OA\Property( property="start_time",type="string",description="Show the next event start time",example="12:30:00"),
 *      @OA\Property( property="end_time",type="string",description="Show the next event end time",example="12:30:00"),
 *  )),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class is used to contain the group listing related data
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class GroupsListResource
 *
 * @package Modules\KctAdmin\Transformers\V1
 */
class GroupsListResource extends JsonResource {
    use KctHelper;
    use ServicesAndRepo;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Transform the resource into an array.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array {
        $startDate = null;
        $startTime = null;
        $eventName = null;
        $endTime = null;
        $event = $request->order_by == 'next_event' ?
            Event::where('event_uuid', $this->resource->laravel_through_key)->first() :
            $this->resource->events->first();
        if ($event) {
            $eventStartDate = $this->getCarbonByDateTime($event->start_time);
            $eventEndDate = $this->getCarbonByDateTime($event->end_time);
            $eventName = $event->title;
            $startDate = $eventStartDate->toDateString();
            $startTime = $eventStartDate->toTimeString();
            $endTime = $eventEndDate->toTimeString();
        }
        return [
            'id'                        => $this->resource->id,
            'group_type'                => $this->resource->groupType->group_type ?? null,
            'group_key'                 => $this->resource->group_key,
            'is_fav'                    => $this->resource->isFavGroup->count() ? 1 : 0,
            'group_name'                => $this->resource->name,
//            'description'               => $this->resource->description,
            'pilot'                     => $this->whenLoaded('pilots',
                $this->relationLoaded('pilots')
                    ? new PilotResource($this->resource->pilots->first())
                    : []
            ),
            'next_event'                => $this->resource->events->first() ? [
                'event_uuid' => $event->event_uuid,
                'event_name' => $eventName,
                'date'       => $event->event_type === Event::$eventType_all_day ? Carbon::now()->toDateString() : $startDate,
                'start_time' => $startTime,
                'end_time'   => $endTime,
            ] : null,
//            'type_value'                => $this->resource->mainSetting->setting_value['type_value'] ?? "",
//            'allow_user'                => $this->resource->mainSetting->setting_value['allow_user'] ?? 0,
            'allow_manage_pilots_owner' => $this->adminServices()->groupService->isSuperPilotOrOwner() ? 1 : $this->resource->mainSetting->setting_value['allow_manage_pilots_owner'],
            'allow_design_setting'      => $this->adminServices()->groupService->isSuperPilotOrOwner() ? 1 : $this->resource->mainSetting->setting_value['allow_design_setting'],
            'users'                     => $this->resource->groupUser->count(),
            'future_events_count'       => $this->resource->events->count(),
            'all_events_count'          => $this->adminRepo()->groupRepository->getGroupAllEvents($this->resource),
            'published_events_count'    => $this->adminRepo()->groupRepository->getGroupAllDraftEvents($this->resource, 1),
            'draft_events_count'        => $this->adminRepo()->groupRepository->getGroupAllDraftEvents($this->resource, 2),
            'allow_modify'              => $this->adminRepo()->groupUserRepository->isUserSuperPilotOrOwner()
                                                || $this->adminServices()->groupService->isUserGroupAdmin($this->resource, Auth::user()->id)
                                                || $this->adminServices()->groupService->isUserGroupAdmin($this->resource,Auth::id())
                                                ? 1
                                                : 0,
        ];
    }
}
