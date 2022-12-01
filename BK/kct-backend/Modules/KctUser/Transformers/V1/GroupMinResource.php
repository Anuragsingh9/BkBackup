<?php

namespace Modules\KctUser\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *  title="APIResource: GroupMinResource",
 *  description="This resource contains group name and group key",
 *  @OA\Property( property="group_key",type="string",description="Group Key",example="grp123"),
 *  @OA\Property( property="group_name",type="string",description="Group Name",example="Humann Connect"),
 * )
 *
 */
class GroupMinResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        return [
            'group_key'  => $this->resource->group_key,
            'group_name' => $this->resource->name,
        ];
    }
}
