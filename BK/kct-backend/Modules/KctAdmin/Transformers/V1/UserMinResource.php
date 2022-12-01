<?php

namespace Modules\KctAdmin\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\KctAdmin\Transformers\EntityResource;

/**
 * @OA\Schema(
 *  title="APIResource: UserMinResource",
 *  description="This resource contains records of multiple user created",
 *  @OA\Property(property="id",type="integer",description="ID of user",example="1"),
 *  @OA\Property( property="fname",type="string",description="First Name",example="First"),
 *  @OA\Property( property="lname", type="string", description="Last Name", example="Last" ),
 *  @OA\Property( property="email",type="email",description="Email ID of user",example="hello@xyz.com"),
 * )
 *
 * Class UserFullResource
 *
 * @package Modules\KctAdmin\Transformers\V1
 */
class UserMinResource extends JsonResource {
    use ServicesAndRepo;

    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        return [
            'id'    => $this->resource->id,
            'fname' => $this->resource->fname,
            'lname' => $this->resource->lname,
            'email' => $this->resource->email,
        ];
    }
}
