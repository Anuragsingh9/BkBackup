<?php

namespace Modules\KctAdmin\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *  title="Resource: TagResource",
 *  description="Virtual Event Resource",
 *
 *  @OA\Property(property="tag_id", type="integer", format="text", example="1"),
 *  @OA\Property(property="tag_name", type="string", format="text", example="Tag Name"),
 *  @OA\Property(property="is_display", type="integer", format="integer", example="To indicate for displaying tag or not"),
 * )
 *
 * -----------------------------------------------------------------------------------------------------------------
 * @description This class will contain tag resource
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class TagResource
 * @package Modules\KctAdmin\Transformers\V1
 */
class TagResource extends JsonResource {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Transform the resource into an array.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        return [
            'tag_id'     => $this->resource->id,
            'tag_name'   => $this->resource->name,
            'is_display' => $this->resource->is_display,
        ];
    }
}
