<?php

namespace Modules\Cocktail\Transformers\V2\UserSide;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\App;

class UserTagV2USResource extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        
        $lang = "tag_";
        $lang .= strtoupper(App::getLocale());
        return [
            "id"   => $this->resource->id,
            "name" => $this->resource->$lang,
        ];
    }
}
