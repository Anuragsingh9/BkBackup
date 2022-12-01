<?php

namespace Modules\KctAdmin\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\KctAdmin\Services\BusinessServices\factory\BusinessServices\factory\BusinessServices\factory\BusinessServices\factory\BusinessServices\factory\BusinessServices\factory\BusinessServices\factory\KctService;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\KctAdmin\Transformers\EntityResource;

/**
 * @OA\Schema(
 *  title="APIResource: HostResource",
 *  description="Return User Data",
 *  @OA\Property(property="id",type="integer",description="ID of user",example="1"),
 *  @OA\Property(property="lname",type="string",description="Last name of user",example="hello"),
 *  @OA\Property(property="fname",type="string",description="First name of user",example="hello"),
 *  @OA\Property(property="email",type="email",description="Email ID of user",example="hello@xyz.com"),
 *  @OA\Property(property="avatar",type="string",description="Image of user",example="https://[bucket_name].s3.amazonaws.com/a/b.png"),
 *  @OA\Property(property="company",type="object",description="Company of user",ref="#/components/schemas/EntityResource"),
 *  @OA\Property(property="unoins",type="array",description="Unoins of user",@OA\Items(ref="#/components/schemas/EntityResource")),
 * )
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class is responsible for returning host related data
 * ---------------------------------------------------------------------------------------------------------------------
 *
 *
 * Class HostResource
 * @package Modules\KctAdmin\Transformers
 */
class HostResource extends JsonResource {
    use ServicesAndRepo;

    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        return [
            "id"      => $this->resource->id,
            "fname"   => $this->resource->fname,
            "lname"   => $this->resource->lname,
            "email"   => $this->resource->email,
            "avatar"  => $this->resource->avatar ? $this->adminServices()->fileService->getFileUrl($this->resource->avatar) : null,
            "company" => new EntityResource($this->resource->company),
            "union"   => EntityResource::collection($this->resource->unions),
        ];
    }
}
