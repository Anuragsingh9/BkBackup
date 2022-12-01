<?php

namespace Modules\KctUser\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *  title="APIResource: UserAccessTokenResource",
 *  description="Api resource contains user basic details and the access token for user to get identified in further api's",
 *  @OA\Property(property="id",type="integer",description="The Unique ID of user",example="1"),
 *  @OA\Property(property="fname",type="string",description="First Name",example="Someone"),
 *  @OA\Property(property="lname",type="string",description="Last Name",example="User"),
 *  @OA\Property(property="email",type="string",description="Unique email of user",example="example@example.com"),
 *  @OA\Property(property="validated",type="integer",
 *     description="To indicate if user email is verified or not, 0=Not Verified, 1=Verfied",example="0"),
 *  @OA\Property(property="access_token",type="string",
 *     description="Bearer Access Token for user account, used to authenticate the user",
 *     example="00453b49481563819d9c4bea86b4a738d2eef69c5918ab7259f2e56335635da0028b1b5a531d626d"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will return user basic details with access token.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class UserAccessTokenResource
 *
 * @package Modules\KctUser\Transformers\V1
 */
class UserAccessTokenResource extends JsonResource {

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
            'id'           => $this->resource->id,
            'fname'        => $this->resource->fname,
            'lname'        => $this->resource->lname,
            'email'        => $this->resource->email,
            'access_token' => $this->resource->createToken('check')->accessToken,
            'validated'    => $this->resource->email_verified_at ? 1 : 0,
        ];
    }
}
