<?php

namespace Modules\SuperAdmin\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * -----------------------------------------------------------------------------------------------------------------
 * @description This class will return the user tag resource
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class UserTagResource
 * @package Illuminate\Http\Resources\Json\JsonResource
 */
class UserTagResource extends JsonResource {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Transform the resource into an array.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request): array {
        return [
            'tag_id'   => $this->id,
            'tag_EN'   => $this->resource->locales->where('locale', 'en')->first()->value,
            'tag_FR'   => $this->resource->locales->where('locale', 'fr')->first()->value,
            'tag_type' => $this->tag_type,
        ];
    }
}
