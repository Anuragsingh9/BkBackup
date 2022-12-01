<?php

namespace Modules\Cocktail\Transformers\AdminSide;

use Illuminate\Http\Resources\Json\Resource;
use Modules\Cocktail\Services\KctEventService;
use Modules\Cocktail\Services\KctService;

class EventGraphicsResource extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        $pageCustomization = (isset($this->event_fields['keepContact']['page_customisation'])
            ? $this->event_fields['keepContact']['page_customisation']
            : null);
        $logoPath = (isset($pageCustomization['keepContact_page_logo'])
            ? $pageCustomization['keepContact_page_logo']
            : null);
        $logo = $logoPath ? KctService::getInstance()->getCore()->getS3Parameter($logoPath) : null;
        $graphics = (isset($this->event_fields['keepContact']['graphics_setting'])
            ? array_map(KctEventService::getInstance()->graphicsFilter(), $this->event_fields['keepContact']['graphics_setting']) : null);
        if ($pageCustomization) {
            $pageCustomization = [
                'keepContact_page_title'       => (isset($pageCustomization['keepContact_page_title']) ? $pageCustomization['keepContact_page_title'] : ''),
                'keepContact_page_description' => (isset($pageCustomization['keepContact_page_description']) ? $pageCustomization['keepContact_page_description'] : ''),
                'website_page_link'            => (isset($pageCustomization['website_page_link']) ? $pageCustomization['website_page_link'] : ''),
                'twitter_page_link'            => (isset($pageCustomization['twitter_page_link']) ? $pageCustomization['twitter_page_link'] : ''),
                'linkedIn_page_link'           => (isset($pageCustomization['linkedIn_page_link']) ? $pageCustomization['linkedIn_page_link'] : ''),
                'facebook_page_link'           => (isset($pageCustomization['facebook_page_link']) ? $pageCustomization['facebook_page_link'] : ''),
                'instagram_page_link'          => (isset($pageCustomization['instagram_page_link']) ? $pageCustomization['instagram_page_link'] : ''),
                'keepContact_page_logo'        => $logo,
            ];
        }
        return [
            'event_uuid'         => $this->event_uuid,
            'page_customization' => $pageCustomization,
            'graphics_setting'   => $graphics,
            'section_text'       => (isset($this->event_fields['keepContact']['section_text']) ? $this->event_fields['keepContact']['section_text'] : null),
        ];
    }
}
