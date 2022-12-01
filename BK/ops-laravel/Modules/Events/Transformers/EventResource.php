<?php

namespace Modules\Events\Transformers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\Resource;
use Modules\Cocktail\Services\KctEventService;
use Modules\Cocktail\Services\V2Services\KctCoreService;

class EventResource extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        if (!isset($this->id)) {
            return [
                'status' => false,
                'msg'    => 'no data found',
            ];
        }
        $type = [
            'int'     => 'Internal',
            'ext'     => 'External',
            'virtual' => 'Virtual',
        ];
        $organiser = ($this->type != 'ext') ? $this->users->first() : $this->organisers->first();
        $name = ($organiser && isset($organiser->fname)) ? "$organiser->fname $organiser->lname" : '';
        $workshopId = ($this->type != 'ext' && isset($this->workshop->id)) ? $this->workshop->id : null;
        $workshopName = ($this->type != 'ext' && isset($this->workshop->workshop_name)) ? $this->workshop->workshop_name : null;
        $result = [
            "id"              => $this->id,
            "event_uuid"      => $this->event_uuid,
            "title"           => $this->title,
            "header_text"     => $this->header_text,
            "description"     => $this->description,
            "date"            => $this->date,
            "start_time"      => $this->start_time,
            "end_time"        => $this->end_time,
            "address"         => $this->address,
            "city"            => $this->city,
            "image"           => $this->image,
            "type"            => isset($type[$this->type]) ? $type[$this->type] : null,
            "is_territory"    => ($this->territory_value ? true : false),
            "territory_value" => $this->territory_value,
            "organiser_id"    => isset($organiser->pivot->eventable_id) ? $organiser->pivot->eventable_id : null,
            "organiser_name"  => $name,
            "workshop_id"     => $workshopId,
            "workshop_name"   => $workshopName,
        ];
        if ($this->type == 'virtual' || $this->type == 'Virtual') {
            $result['bluejeans_settings'] = $this->bluejeans_settings;
            $result['manual_opening'] = $this->manual_opening;
            $result['opening_hours'] = isset($this->event_fields['opening_hours']) ? $this->event_fields['opening_hours'] : null;
            if ($this->resource->relationLoaded('spaces')) {
                // manipulating as in current date,start_time and end_time are reformatted according to front
                if (isset($this->resource->order_date)
                    && isset($this->resource->s_time)
                    && isset($this->resource->e_time)
                ) {
                    $this->resource->date = $this->resource->order_date;
                    $this->resource->start_time = $this->resource->s_time;
                    $this->resource->end_time = $this->resource->e_time;
                }
                $result['event_status'] = KctEventService::getInstance()->getEventStatus($this->resource);
            }
            $result['conference_type'] = KctCoreService::getInstance()->findEventConferenceType($this->resource);
        }
        return $result;
    }
    
    public function with($request) {
        return [
            'status' => true,
        ];
    }
}
