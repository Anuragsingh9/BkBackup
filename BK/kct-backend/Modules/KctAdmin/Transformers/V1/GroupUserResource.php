<?php

namespace Modules\KctAdmin\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\KctAdmin\Transformers\EntityResource;

/**
 * @OA\Schema(
 *  title="APIResource: GroupUserResource",
 *  description="This resource contains group user data",
 *  @OA\Property(property="id",type="integer",description="ID of Group",example="1"),
 *  @OA\Property( property="fname",type="string",description="User first name",example="Abc"),
 *  @OA\Property( property="lname", type="string", description="User last name", example="Xyz" ),
 *  @OA\Property( property="email", type="string", description="Email of the user", example="abc@mailinator.com"),
 *  @OA\Property( property="avatar", type="string", description="Image of user",
 *     example="https://s3.eu-west-2.amazonaws.com/kct-dev/nitinhumann.humannconnect.dev/users/avatar/1651665543.jpg"),
 *  @OA\Property(property="setting",type="object",
 *     description="User personal settings like language setting",example={"lang":"fr"},
 *     @OA\Property(property="lang",type="string",description="Current Language",example="en"),
 *  ),
 *  @OA\Property( property="role", type="string", description="Group role of the user", example="1"),
 *  )
 *
 * Class GroupUserResource
 *
 * @package Modules\KctAdmin\Transformers\V1
 */
class GroupUserResource extends JsonResource {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Transform the resource into an array.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        $HStatusColorCode = "#808080";
        return [
            'id'      => $this->resource->id,
            'fname'   => $this->resource->fname,
            'lname'   => $this->resource->lname,
            'HStatus' => $HStatusColorCode,
            'email'   => $this->resource->email,
            'avtar'   => $this->avtar,
            'setting' => $this->setting,
            'role'    => $this->getUserGroupRole($this->resource)->role,
            'company' => new EntityResource($this->resource->company),
            'union'   => EntityResource::collection($this->resource->unions),
        ];
    }

    private function getUserGroupRole($resource) {
        $group = $resource->group;
        $groupData = $group->load(['groupUser' => function ($q) use ($resource) {
            $q->where('user_id', $resource->id);
        }]);
        return $groupData->groupUser[0];
    }

//    public function getUser(){
//        foreach ($this->groupUser as $userData){
//            $user = $userData->users;
//            $user['role'] = $userData->role;
//            $data [] = $user;
//        }
//        return UsersResource::collection($data);
//    }
}
