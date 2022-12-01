<?php

namespace Modules\KctUser\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *  title="APIResource: EventSpaceResourcePublic",
 *  description="Event space and moods resource",
 *  @OA\Property(property="space_uuid",type="UUID",description="UUID of space",example="123e4567-e89b-12d3-a456-426614174000"),
 *  @OA\Property(property="space_mood",type="string",description="Space mood value",example="Space Mood 1"),
 *  @OA\Property(property="space_type",type="string",description="Space type",example="VIP"),
 * )
 *
 * Class EventSpaceResourcePublic
 * @package Modules\Cocktail\Transformers
 */
class EventSpaceResourcePublic extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        $spaceType = $this->resource->is_vip_space == 1
            ? config('kctuser.default.space_type_vip')
            : ($this->resource->is_duo_space == 1
                ? config('kctuser.default.space_type_duo')
                : 0);
        return [
            'space_uuid' => $this->space_uuid,
            'space_mood' => $this->space_mood,
            'space_type' => $spaceType,
        ];
    }
}
