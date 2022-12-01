<?php

namespace Modules\KctAdmin\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\KctAdmin\Transformers\EntityResource;
use Modules\KctAdmin\Transformers\V1\SpaceResource;


/**
 * @OA\Schema(
 *  title="APIResource: ParticipantResource",
 *  description="Return Participant",
 *  @OA\Property(property="id",type="integer",description="ID of user",example="1"),
 *  @OA\Property(property="fname",type="string",description="First name of user",example="hello"),
 *  @OA\Property(property="lname",type="string",description="Last name of user",example="hello"),
 *  @OA\Property(property="email",type="email",description="Email ID of user",example="hello@xyz.com"),
 *  @OA\Property(property="avatar",type="string",description="Image of user",example="https://[bucket_name].s3.amazonaws.com/a/b.png"),
 *  @OA\Property(property="event_user_role",type="integer",description="User Event Role, 1:Teams, 2:Expert",example="1"),
 *  @OA\Property(property="is_presenter",type="integer",description="To indicate if user is presenter",example="1",enum={"0", "1"}),
 *  @OA\Property(property="is_moderator",type="integer",description="To indicate if user is moderator",example="1",enum={"0", "1"}),
 *  @OA\Property(property="is_vip",type="integer",description="To indicate if user is vip",example="1",enum={"0", "1"}),
 *  @OA\Property(property="is_organiser",type="integer",description="To indicate if user is organiser",example="1",enum={"0", "1"}),
 *  @OA\Property(property="presence",type="integer",description="User presence satus, 1: Present, 2: Absent",example="1",enum={"1", "2"}),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will return all data related to event participants.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class ParticipantResource
 *
 * @package Modules\KctAdmin\Transformers\V1
 */
class ParticipantResource extends JsonResource {
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
        return [
            'id'              => $this->resource->user_id,
            'fname'           => $this->resource->user->fname,
            'lname'           => $this->resource->user->lname,
            'email'           => $this->resource->user->email,
            'avatar'          => $this->adminServices()->fileService->getFileUrl($this->resource->user->avatar),
            'event_user_role' => $this->resource->event_user_role,
            'is_presenter'    => $this->resource->is_presenter,
            'is_moderator'    => $this->resource->is_moderator,
            'is_vip'          => $this->resource->is_vip,
            'is_organiser'    => $this->resource->is_organiser,
            'presence'        => $this->resource->presence,
            'company'         => new EntityResource($this->resource->user->company),
            'union'           => EntityResource::collection($this->resource->user->unions),
            'spaces'          => $this->when($this->relationLoaded('hostSpaces'), $this->getHostSpaces($this->resource)),
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To fetch all space hosts of given space
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $spaces
     * @return AnonymousResourceCollection
     */
    private function getHostSpaces($spaces){
        $spaceData = [];
        foreach ($spaces->hostSpaces as $space){
            $spaceData[] = $space->spaces;
        }
        return SpaceResource::collection($spaceData);
    }
}
