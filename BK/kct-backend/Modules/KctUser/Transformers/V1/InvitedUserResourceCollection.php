<?php

namespace Modules\KctUser\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *  title="APIResource: InvitedUserResourceCollection",
 *  description="Physical Event Resource",
 *  @OA\Property(property="first_name",type="string",description="First Name"),
 *  @OA\Property(property="last_name",type="string",description="Title for Event"),
 *  @OA\Property(property="email",type="string",description="Date of Event"),
 *  @OA\Property(property="invite_count",type="integer",description=""),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will return data of invited users on HE side.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class InvitedUserResourceCollection
 *
 * @package Modules\KctUser\Transformers\V1
 */
class InvitedUserResourceCollection extends JsonResource {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Transform the resource into an array.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        $data = [
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'email'      => $this->email,
        ];
        if (isset($this->resource->invited_times)) {
            $data['invite_count'] = $this->resource->invited_times;
        }
        return $data;
    }
}
