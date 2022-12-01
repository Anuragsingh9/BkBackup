<?php

namespace Modules\KctAdmin\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *  title="APIResource: EntityResource",
 *  description="This resource contains entity data",
 *  @OA\Property(property="id",type="integer",description="ID of entity",example="1"),
 *  @OA\Property(property="entity_type_id",type="integer",
 *     description="ID of type of entity, 1 Company, 2 Union",example="1"),
 *  @OA\Property( property="long_name",type="string",description="Name of Entity",example="First"),
 *  @OA\Property( property="position",type="string",description="Position in the Entity",example="Owner"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will return all data related to an entity(1.Company and 2.Union) like entity id,entity name,
 * entity type.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class EntityResource
 *
 * @package Modules\KctAdmin\Transformers
 */
class EntityResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        if (isset($this->pivot->position)) {
            $position = $this->pivot->position;
        } else if (isset($this->position)) {
            $position = $this->position;
        } else {
            $position = null;
        }
        return [
            'id'             => $this->resource->id,
            'entity_type_id' => $this->resource->entity_type_id,
            'long_name'      => $this->resource->long_name,
            'position'       => $position
        ];
    }
}
