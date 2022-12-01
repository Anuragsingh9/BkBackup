<?php

namespace Modules\KctAdmin\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\KctAdmin\Services\BusinessServices\factory\BusinessServices\factory\BusinessServices\factory\BusinessServices\factory\BusinessServices\factory\BusinessServices\factory\BusinessServices\factory\KctService;
use Modules\KctAdmin\Traits\ServicesAndRepo;

/**
 * @OA\Schema(
 *  title="APIResource: BroadcastHostResource",
 *  description="Return User Data",
 *  @OA\Property(property="id",type="integer",description="ID of user",example="1"),
 *  @OA\Property(property="lname",type="string",description="Last name of user",example="hello"),
 *  @OA\Property(property="fname",type="string",description="First name of user",example="hello"),
 *  @OA\Property(property="email",type="email",description="Email ID of user",example="hello@xyz.com"),
 *  @OA\Property(property="number_of_participants",type="integer",description="Participant Limit",example="100")
 * )
 *
 * Class UserResource
 * @package Modules\KctAdmin\Transformers
 */
class BroadcastHostResource extends JsonResource {
    use ServicesAndRepo;

    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        return [
            "id"      => $this->resource['id'],
            "fname"   => $this->resource['fname'],
            "lname"   => $this->resource['lname'],
            "email"   => $this->resource['email'],
            'number_of_participants' => $this->resource['max_participant'],
        ];
    }
}
