<?php

namespace Modules\KctAdmin\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\KctAdmin\Traits\ServicesAndRepo;

/**
 * @OA\Schema(
 *  title="APIResource: EventUserResource",
 *  description="This resource contains user role in event",
 *  @OA\Property( property="fname",type="string",description="First Name",example="First"),
 *  @OA\Property( property="lname", type="string", description="Last Name", example="Last" ),
 *  @OA\Property( property="email",type="email",description="Email ID of user",example="hello@xyz.com"),
 *  @OA\Property( property="avatar",type="string",description="Image of user",example="https://[bucket_name].s3.amazonaws.com/a/b.png"),
 *  @OA\Property( property="setting",type="object",description="User personal settings like language setting",example={"lang":"fr"},
 *     @OA\Property( property="lang",type="string",description="Current Language",example="en"),
 *  ),
 *  @OA\Property( property="current_group",type="object",description="Current Group Object",ref="#/components/schemas/GroupResource"),
 *  @OA\Property( property="event_uuid",type="string",description="Event uuid",example="0bd13a00-14a0-11ec-a4fa-74867a0dc41b"),
 *  @OA\Property( property="event_role",type="integer",description="Role of the user in event",example="1"),
 *  @OA\Property( property="is_vip",type="integer",description="User is vip user",example="1"),
 *  @OA\Property( property="is_presenter",type="integer",description="User is presenter",example="1"),
 *  @OA\Property( property="is_moderator",type="integer",description="User is is_moderator",example="1"),
 *  @OA\Property( property="is_organiser",type="integer",description="User is is_organiser",example="1"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will return all data related to event users.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class EventUserResource
 *
 * @package Modules\KctAdmin\Transformers\V1
 */
class EventUserResource extends JsonResource {
    use ServicesAndRepo;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Transform the resource into an array.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        $group = $this->whenLoaded(
            'group',
            new GroupResource(
                $this->resource->relationLoaded('group') ? $this->resource->group : null
            )
        );
        return [
            'id'            => $this->resource->id,
            'fname'         => $this->resource->fname,
            'lname'         => $this->resource->lname,
            'email'         => $this->resource->email,
            'avatar'        => $this->adminServices()->fileService->getFileUrl($this->resource->avatar),
            'setting'       => [
                'lang' => $this->resource->setting['lang'] ?? null,
            ],
            'current_group' => $group,
            'event_uuid'    => $this->resource->eventUser->event_uuid,
            'event_role'    => $this->resource->eventUser->event_user_role,
            'is_vip'        => $this->resource->eventUser->is_vip,
            'is_presenter'  => $this->resource->eventUser->is_presenter,
            'is_moderator'  => $this->resource->eventUser->is_moderator,
            'is_organiser'  => $this->resource->eventUser->is_organiser
        ];
    }
}
