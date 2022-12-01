<?php

namespace Modules\KctUser\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *  title="APIResource: EntityUSResource",
 *  description="",
 *  @OA\Property(property="entity_id",type="integer",description="ID of Entity",example="1"),
 *  @OA\Property(property="long_name",type="string",description="Long name of entity",example="hello"),
 *  @OA\Property(property="short_name",type="string",description="Short Name of Entity",example="hello"),
 *  @OA\Property(property="position",type="string",description="Position of user in entity",example="hello"),
 * )
 *
 *---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will return all data related to an entity(1.Company and 2.Union) like entity id,entity name,
 * entity type.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class EntityUSResource
 *
 * @package Modules\KctUser\Transformers\V1
 */
class EntityUSResource extends JsonResource {

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
        } else if (isset($this->pivot->entity_label)) {
            $position = $this->pivot->entity_label;
        } else {
            $position = null;
        }
        return [
            'entity_id'  => $this->id,
            'long_name'  => $this->long_name,
            'short_name' => $this->short_name,
            'position'   => $position,
        ];
    }
}
