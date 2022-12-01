<?php

namespace Modules\Cocktail\Transformers\UserSide;

use Illuminate\Http\Resources\Json\Resource;

class EntitySearchResource extends Resource {
    
    public function __construct($resource) {
        parent::__construct($resource);
        static::$wrap = null;
    }
    
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        return [
            
            'id'         => $this->id,
            'long_name'  => $this->long_name,
            'short_name' => $this->short_name,
        
        ];
    }
}
