<?php

namespace Modules\Events\Transformers;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Events\Entities\Event;

class EventCollection extends ResourceCollection {
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        if (!($request->transform === 0)) {
            $this->collection->transform(function (Event $event) {
                return (new EventResource($event))->additional($this->additional);
            });
        }
        return parent::toArray($request);
        //        return [
//            'status' => TRUE,
//            'data'   => $this->collection
//        ];
    }
}
