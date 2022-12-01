<?php

namespace Modules\Cocktail\Transformers\AdminSide;

use Illuminate\Http\Resources\Json\Resource;
use Modules\Cocktail\Services\KctService;
use Modules\Cocktail\Transformers\OpeningHourResource;

class EventSpaceResource extends Resource {
    
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request) {
        
        $result = [
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
            'users_count'               => isset($this->space_users_count) ? $this->space_users_count : $this->spaceUsers->count(),
            'hosts_count'               => $this->hosts->count(),
            'hosts'                     => UserResource::collection($this->hosts),
            'does_not_follow_main_hour' => $this->follow_main_opening_hours ? 0 : 1,
            'workshop_id'               => $this->event->workshop_id,
            'order_id'                  => $this->order_id,
        ];
        if (isset($this->event->defaultSpace->space_uuid) && $this->event->defaultSpace->space_uuid == $this->space_uuid) {
            $result['is_default'] = 1;
        }
        return $result;
    }
    
}