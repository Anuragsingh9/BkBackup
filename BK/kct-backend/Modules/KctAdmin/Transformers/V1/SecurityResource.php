<?php

namespace Modules\KctAdmin\Transformers\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class SecurityResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        return [
            'name' => $this->name,
        ];
    }
}
