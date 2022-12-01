<?php

namespace Modules\KctAdmin\Transformers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Modules\KctAdmin\Traits\KctHelper;
use Modules\KctAdmin\Traits\ServicesAndRepo;

/**
 * @OA\Schema(
 *  title="Resource: SpaceResource",
 *  description="Space Resource",
 *
 *  @OA\Property(property="space_uuid",type="uuid",description="Unique UUID of Space",
 *     example="123e4567-e89b-12d3-a456-426614174000"
 *  ),
 *  @OA\Property(property="space_name",type="string",description="Name of space",example="Space Name"),
 *  @OA\Property(property="space_short_name",type="string",description="Space Short Name",example="Space Short Name"),
 *  @OA\Property(property="space_mood",type="string",description="Space Mood",example="Space Mood"),
 *  @OA\Property(property="max_capacity",type="integer",description="Maximum Capacity for space",example="1"),
 *  @OA\Property(property="is_vip_space",type="integer",description="To indicate if space is vip",example="1",
 *     enum={"0", "1"}
 *  ),
 *  @OA\Property(property="is_duo_space",type="integer",description="To indicate if space is duo",example="1",
 *      enum={"0", "1"}
 *  ),
 *  @OA\Property(property="is_mono_space",type="integer",description="To indicate if space is mono",example="1",
 *      enum={"0", "1"}
 *  ),
 *  @OA\Property(property="event_uuid",type="uuid",description="UUID of Event",
 *     example="123e4567-e89b-12d3-a456-426614174000"
 *  ),
 *  @OA\Property(property="order_id",type="string",description="Sorting ID",example="a"),
 *  @OA\Property(property="space_hosts",type="array",description="Space hosts",
 *     @OA\Items(ref="#/components/schemas/HostResource")
 *  ),
 *  @OA\Property(property="header_line_1",type="string",description="Header Line 1 of event",example="a"),
 *  @OA\Property(property="header_line_2",type="string",description="Header Line 2 of event",example="b"),
 *  @OA\Property(property="is_self_header",type="integer",description="To indicate if event has own header",
 *     example="1", enum={"0", "1"}
 *  ),
 * ),
 *
 * -----------------------------------------------------------------------------------------------------------------
 * @description This class will manage the resource of the space
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class SpaceResource
 * @package Modules\KctAdmin\Transformers\V1
 */
class SpaceResource extends JsonResource {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Transform the resource into an array.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request
     * @return array
     */
    public function toArray($request): array {
        $host = $this->resource->spaceHost ?? [];
        return [
            'space_uuid'       => $this->resource->space_uuid,
            'space_name'       => $this->resource->space_name,
            'space_short_name' => $this->resource->space_short_name,
            'space_mood'       => $this->resource->space_mood,
            'max_capacity'     => $this->resource->max_capacity,
            'is_vip_space'     => $this->resource->is_vip_space,
            'is_duo_space'     => $this->resource->is_duo_space,
//            'is_mono_space'    => $this->resource->is_mono_space,
            'event_uuid'       => $this->event_uuid,
            'order_id'         => $this->resource->order_id,
            'space_hosts'      => isset($host->user) ? new HostResource($host->user) : [],
            'header_line_1'    => $this->resource->event->header_line_1 ?: "",
            'header_line_2'    => $this->resource->event->header_line_2 ?: "",
            'is_self_header'   => $this->resource->event->event_settings['is_self_header'] ?? 0,
//            'is_member'        => $this->isSpaceUser($this->resource->space_uuid),
            'is_default'       => (int)($this->resource->event->spaces->first()->space_uuid
                == $this->resource->space_uuid),
        ];
    }
}
