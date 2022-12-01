<?php

namespace Modules\KctAdmin\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\KctAdmin\Traits\ServicesAndRepo;

/**
 * @OA\Schema(
 *  title="APIResource: UserFullResource",
 *  description="This resource contains records of multiple user created",
 *  @OA\Property( property="country_code", type="string", description="Phone Code For User", example="+91"),
 *  @OA\Property( property="number", type="string", description="Phone Number of User", example="9876543210"),
 *  @OA\Property( property="is_primary",type="integer",description="To indicate the current number is primary",example="1", enum={"0", "1"}),
 *  @OA\Property( property="type",type="integer",description="To indicate the type of number, 1 Mobile, 2 Landline/Phone",example="1", enum={"1", "2"}),
 * )
 *
 * Class UserMobileResource
 *
 * @package Modules\KctAdmin\Transformers\V1
 */
class UserMobileResource extends JsonResource {
    use ServicesAndRepo;

    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        return [
            'country_code' => $this->resource->country_code,
            'number'       => $this->resource->number,
            'is_primary'   => $this->resource->is_primary,
            'type'         => $this->resource->type, // 1 mobile, 2 landline
        ];
    }
}
