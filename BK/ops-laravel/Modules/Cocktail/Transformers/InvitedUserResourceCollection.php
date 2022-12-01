<?php

namespace Modules\Cocktail\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class InvitedUserResourceCollection extends Resource {
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
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
