<?php

namespace Modules\Messenger\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class MessageLikeResource extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        return [
            'status' => TRUE,
            'data'   => [
                'status' => $this->status ? 'Liked' : 'Unliked',
            ]
        ];
    }
}
