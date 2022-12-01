<?php

namespace Modules\KctUser\Transformers\V1;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *  title="APIResource: NodeSpaceWithDummyResource",
 *  description="Physical Event Resource",
 *  @OA\Property(property="space_uuid",type="string",description="First Name"),
 *  @OA\Property(property="dummy_users",type="object",description="Title for Event"),
 * )
 *
 * -----------------------------------------------------------------------------------------------------------------
 * @descripiton This class will be used for prepare the data to send node server
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class NodeSpaceWithDummyResource
 *
 * @package Modules\KctUser\Transformers\V1
 */
class NodeSpaceWithDummyResource extends JsonResource {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Transform the resource into an array.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        return [
            'space_uuid'  => $this->resource->space_uuid,
            'dummy_users' => $this->resource->dummyRelations->pluck('dummyUsers')->pluck('id'),
        ];
    }
}
