<?php

namespace Modules\KctUser\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\KctUser\Traits\Services;

/**
 * @OA\Schema(
 *  title="APIResource: HostUSResource",
 *  description="Host Resource",
 *  @OA\Property(property="user_id",type="integer",description="ID of user",example="1"),
 *  @OA\Property(property="fname",type="string",description="First name of user",example="hello"),
 *  @OA\Property(property="lname",type="string",description="Last name of user",example="hello"),
 *  @OA\Property(property="email",type="email",description="Email ID of user",example="hello@xyz.com"),
 *  @OA\Property(property="avatar",type="string",description="Image of user",example="https://[bucket_name].s3.amazonaws.com/a/b.png")
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will return data of Host user.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class HostUSResource
 *
 * @package Modules\KctUser\Transformers\V1
 */
class HostUSResource extends JsonResource {
    use Services;

    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        return [
            'user_id' => $this->resource->id,
            'fname'   => $this->resource->fname,
            'lname'   => $this->resource->lname,
            'email'   => $this->resource->email,
            'avatar'  => $this->resource->avatar
                ? $this->userServices()->fileService->getFileUrl($this->resource->avatar)
                : null,
        ];
    }
}
