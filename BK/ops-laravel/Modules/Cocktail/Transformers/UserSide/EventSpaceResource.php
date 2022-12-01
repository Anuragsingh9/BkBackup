<?php

namespace Modules\Cocktail\Transformers\UserSide;

use Illuminate\Http\Resources\Json\Resource;
use Modules\Cocktail\Services\KctService;
use Modules\Cocktail\Transformers\OpeningHourResource;
use Modules\Events\Service\ValidationService;

class EventSpaceResource extends Resource {
    
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request) {
        $validation = ValidationService::getInstance();
        $isOpen = null;
        if ($this->relationLoaded('event')) {
            $isOpen = $validation->isManuallyOpen($this->event) || $validation->isSpaceOpen($this->resource);
        }
        $userCount = $isOpen ? 0 : null;
        
        return [
            'space_uuid'                => $this->space_uuid,
            'space_name'                => $this->space_name,
            'space_short_name'          => $this->space_short_name,
            'space_mood'                => $this->space_mood,
            'max_capacity'              => $this->max_capacity,
            'space_image_url'           => $this->space_image_url
                ? KctService::getInstance()->getCore()->getS3Parameter($this->space_image_url)
                : null,
            'space_icon_url'            => $this->space_icon_url
                ? KctService::getInstance()->getCore()->getS3Parameter($this->space_icon_url)
                : null,
            'is_vip_space'              => $this->is_vip_space,
            'opening_hours'             => new OpeningHourResource($this->opening_hours),
            'event_uuid'                => $this->event_uuid,
            'users_count'               => $userCount,
            'does_not_follow_main_hour' => $this->follow_main_opening_hours ? 0 : 1,
            'is_open'                   => $isOpen,
        ];
    }
    
}