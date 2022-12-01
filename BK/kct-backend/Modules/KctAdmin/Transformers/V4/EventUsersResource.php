<?php

namespace Modules\KctAdmin\Transformers\V4;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\KctAdmin\Transformers\EntityResource;


/**
 * @OA\Schema(
 *  title="APIResource: EventUsersResource",
 *  description="Return Event users",
 *  @OA\Property(property="id",type="integer",description="ID of user",example="1"),
 *  @OA\Property(property="name",type="string",description="Name of user",example="John"),
 *  @OA\Property(property="email",type="email",description="Email ID of user",example="hello@xyz.com"),
 *  @OA\Property(property="avatar",type="string",description="Image of user",example="https://[bucket_name].s3.amazonaws.com/a/b.png"),
 *  @OA\Property(property="event_user_role",type="integer",description="User Event Role, 1:Teams, 2:Expert",example="1"),
 *  @OA\Property(property="is_presenter",type="integer",description="To indicate if user is presenter",example="1",enum={"0", "1"}),
 *  @OA\Property(property="is_moderator",type="integer",description="To indicate if user is moderator",example="1",enum={"0", "1"}),
 *  @OA\Property(property="is_vip",type="integer",description="To indicate if user is vip",example="1",enum={"0", "1"}),
 *  @OA\Property(property="is_organiser",type="integer",description="To indicate if user is organiser",example="1",enum={"0", "1"}),
 *  @OA\Property(property="registration",type="integer",description="To indicate if user is register or not",example="1",enum={"0", "1"}),
 *  @OA\Property(property="attendance",type="integer",description="attendance of user",example="3"),
 *  @OA\Property(property="company",type="object",description="Company of user",ref="#/components/schemas/EntityResource"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will return all data related to event users
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class EventUsersResource
 *
 * @package Modules\KctAdmin\Transformers\V4
 */
class EventUsersResource extends JsonResource {
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

        if ($request->event_participants == 1 && ($request->order_by == 'lname' || $request->order_by == 'email')) {
            $sortedByUserModel = [
                'id'              => $this->resource->id,
                'name'            => $this->resource->fname . ' ' . $this->resource->lname,
                'email'           => $this->resource->email,
                'avatar'          => $this->adminServices()->fileService->getFileUrl($this->resource->avatar),
                'event_user_role' => $this->resource->eventUser->event_user_role,
                'is_presenter'    => $this->resource->eventUser->eventUserRole->where('role', 4)->count() ? 1 : 0,
                'is_moderator'    => $this->resource->eventUser->eventUserRole->where('role', 3)->count() ? 1 : 0,
                'is_vip'          => $this->resource->eventUser->is_vip,
                'is_organiser'    => $this->resource->eventUser->is_organiser,
                'is_space_host'   => count($this->resource->eventUser->isHost->toArray()) ? 1 : 0,
                'registration'    => $this->resource->eventUser->is_joined_after_reg,
                'attendance'      => count($this->resource->eventUser->eventUserJoinReport->toArray()),
                'company'         => new EntityResource($this->resource->company),
            ];
            $result = $sortedByUserModel;
        }else{

            $allData =  [
                'id'              => $this->resource->user_id,
                'name'            => $this->resource->user->fname . ' ' . $this->resource->user->lname,
                'email'           => $this->resource->user->email,
                'avatar'          => $this->adminServices()->fileService->getFileUrl($this->resource->user->avatar),
                'event_user_role' => $this->resource->event_user_role,
                'is_presenter'    => $this->resource->eventUserRole->where('role', 4)->count() ? 1 : 0,
                'is_moderator'    => $this->resource->eventUserRole->where('role', 3)->count()  ? 1 : 0,
                'is_vip'          => $this->resource->is_vip,
                'is_organiser'    => $this->resource->is_organiser,
                'is_space_host'   => count($this->resource->isHost->toArray()) ? 1 : 0,
                'registration'    => $this->resource->is_joined_after_reg,
                'attendance'      => count($this->resource->eventUserJoinReport->toArray()),
                'company'         => new EntityResource($this->resource->user->company),
            ];
            $result = $allData;

        }
        return $result;
    }
}
