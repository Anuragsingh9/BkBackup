<?php

namespace Modules\Events\Transformers;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OrganiserCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return ['status' =>true, 'data' => $this->collection];
    }
}
