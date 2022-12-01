<?php

namespace Modules\Cocktail\Transformers\UserSide;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;
use Modules\Cocktail\Services\KctService;
use Modules\Cocktail\Transformers\UserBadgeResource;

class EventGraphicsResource extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        if (isset($this->event_fields['keepContact']['page_customisation'])) {
            $pageCustomization = $this->event_fields['keepContact']['page_customisation'];
            if(isset($pageCustomization['keepContact_page_logo']) && $pageCustomization['keepContact_page_logo']) {
                $pageCustomization['keepContact_page_logo'] = KctService::getInstance()->getCore()->getS3Parameter($pageCustomization['keepContact_page_logo']);
            }
        }else {
            $pageCustomization = null;
        }
        return [
            'event_uuid'            => $this->event_uuid,
            'event_title'           => $this->title,
            'event_header_text'     => $this->header_text,
            'event_description'     => $this->description,
            'event_date'            => $this->date,
            'event_start_time'      => $this->start_time,
            'event_end_time'        => $this->end_time,
            'event_address'         => $this->address,
            'event_city'            => $this->city,
            'event_image'           => $this->image,
            'page_customization'    => $pageCustomization,
            'graphics_setting'      => (isset($this->event_fields['keepContact']['graphics_setting']) ? $this->event_fields['keepContact']['graphics_setting'] : null),
            'section_text'          => (isset($this->event_fields['keepContact']['section_text']) ? $this->event_fields['keepContact']['section_text'] : null),
            'allow_embedded_replay' => isset($this->bluejeans_settings['allow_embedded_replay']) ? $this->bluejeans_settings['allow_embedded_replay'] : 0,
        ];
    }
}
