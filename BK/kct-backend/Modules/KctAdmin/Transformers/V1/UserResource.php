<?php

namespace Modules\KctAdmin\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\KctAdmin\Traits\ServicesAndRepo;

/**
 * @OA\Schema(
 *  title="APIResource: UserResource",
 *  description="This resource contains records of multiple user created",
 *  @OA\Property(property="id",type="integer",description="ID of user",example="1"),
 *  @OA\Property( property="fname",type="string",description="First Name",example="First"),
 *  @OA\Property( property="lname", type="string", description="Last Name", example="Last" ),
 *  @OA\Property(property="email",type="email",description="Email ID of user",example="hello@xyz.com"),
 *  @OA\Property(property="avatar",type="string",description="Image of user",example="https://[bucket_name].s3.amazonaws.com/a/b.png"),
 *  @OA\Property(property="setting",type="object",description="User personal settings like language setting",example={"lang":"fr"},
 *     @OA\Property(property="lang",type="string",description="Current Language",example="en"),
 *  ),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will return all basic data related to a specific user.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class UserResource
 *
 * @package Modules\KctAdmin\Transformers\V1
 */
class UserResource extends JsonResource {
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
            'id'      => $this->resource->id,
            'fname'   => $this->resource->fname,
            'lname'   => $this->resource->lname,
            'email'   => $this->resource->email,
            'avatar'  => $this->adminServices()->fileService->getFileUrl($this->resource->avatar),
            'setting' => [
                'lang' => $this->resource->setting['lang'] ?? null,
            ]
        ];
    }
}
