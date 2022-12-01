<?php

namespace Modules\Cocktail\Transformers\V2\AdminSide;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use Modules\Cocktail\Services\KctService;
use Modules\Cocktail\Services\V2Services\KctCoreService;

/**
 * @OA\Schema(
 *  title="APIResource: VirtualEventResourceV2",
 *  description="Virtual Event V2 Resource",
 *  @OA\Property(
 *     property="id",
 *     type="integer",
 *     description="Id of event",
 *     example="1"
 *  ),
 *  @OA\Property(
 *     property="event_uuid",
 *     type="uuid",
 *     description="Unique UUID of event",
 *     example="123e4567-e89b-12d3-a456-426614174000"
 *  ),
 *  @OA\Property(
 *     property="title",
 *     type="string",
 *     description="Title of event",
 *     example="Event Title"
 *  ),
 *  @OA\Property(
 *     property="header_text",
 *     type="string",
 *     description="Header Text for Event",
 *     example="Event Header"
 *  ),
 *  @OA\Property(
 *     property="header_line_one",
 *     type="string",
 *     description="Header Line One for Event",
 *     example="Event Header Line 1"
 *  ),
 *  @OA\Property(
 *     property="header_line_two",
 *     type="string",
 *     description="Header Line Two for Event",
 *     example="Event Header Line 2"
 *  ),
 *  @OA\Property(
 *     property="description",
 *     type="string",
 *     description="Event Description",
 *     example="Event Description"
 *  ),
 *  @OA\Property(
 *     property="date",
 *     type="date",
 *     description="Date of Event",
 *     example="2020-12-31"
 *  ),
 *  @OA\Property(
 *     property="start_time",
 *     type="time",
 *     description="Start time of event",
 *     example="23:59:59"
 *  ),
 *  @OA\Property(
 *     property="end_time",
 *     type="time",
 *     description="End time of event",
 *     example="23:59:59"
 *  ),
 *  @OA\Property(
 *     property="type",
 *     type="string",
 *     description="Type of event",
 *     example="Virtual",
 *     enum={"Virtual"}
 *      ),
 *  @OA\Property(
 *     property="organiser_id",
 *     type="integer",
 *     description="Event Organiser Id",
 *     example="1"
 *  ),
 *  @OA\Property(
 *     property="organiser_name",
 *     type="string",
 *     description="Full name of event organiser",
 *     example="Organiser Name"
 *  ),
 *  @OA\Property(
 *     property="workshop_id",
 *     type="integer",
 *     description="Event workshop ID",
 *     example="1"
 *  ),
 *  @OA\Property(
 *     property="workshop_name",
 *     type="string",
 *     description="Event workshop Name",
 *     example="Paris - 2020-12-31"
 *  ),
 *  @OA\Property(
 *     property="manual_opening",
 *     type="integer",
 *     description="To indicate if event is currently manually opened",
 *     example="0",
 *     enum={"0", "1"}
 *  ),
 *  @OA\Property(
 *     property="opening_hours",
 *     type="object",
 *     description="Opening Hours for event",
 *     @OA\Property(property="before",type="integer",description="Opening Before",example="1"),
 *     @OA\Property(property="during",type="integer",description="During event is on or off",example="1"),
 *     @OA\Property(property="after",type="integer",description="Opening after",example="1"),
 *  ),
 *  @OA\Property(
 *     property="bluejeans_settings",
 *     type="object",
 *     description="Bluejeans Setting for event",
 *     @OA\Property(property="event_uses_bluejeans_event",type="integer",description="To indicate the event follows bj or not",example="0", enum={"0", "1"}),
 *  ),
 * )
 *
 */
class VirtualEventResourceV2 extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request) {
        
        $organiser = ($this->type != 'ext') ? $this->users->first() : $this->organisers->first();
        $name = ($organiser && isset($organiser->fname)) ? "$organiser->fname $organiser->lname" : '';
        $workshopId = ($this->type != 'ext' && isset($this->workshop->id)) ? $this->workshop->id : null;
        $workshopName = ($this->type != 'ext' && isset($this->workshop->workshop_name)) ? $this->workshop->workshop_name : null;
        
        return [
            "id"                  => $this->id,
            "event_uuid"          => $this->event_uuid,
            "title"               => $this->title,
            "header_text"         => $this->header_text,
            "header_line_one"     => $this->header_line_1,
            "header_line_two"     => $this->header_line_2,
            "description"         => $this->description,
            "date"                => $this->date,
            "start_time"          => $this->start_time,
            "end_time"            => $this->end_time,
            'image'               => $this->image,
            "type"                => 'Virtual',
            "organiser_id"        => isset($organiser->pivot->eventable_id) ? $organiser->pivot->eventable_id : null,
            "organiser_name"      => $name,
            "workshop_id"         => $workshopId,
            "workshop_name"       => $workshopName,
            "manual_opening"      => $this->manual_opening,
            "opening_hours"       => isset($this->event_fields['opening_hours']) ? $this->event_fields['opening_hours'] : null,
            "bluejeans_settings"  => $this->bluejeans_settings,
            "event_version"       => KctService::getInstance()->findEventVersion($this->resource),
            "is_dummy_event"      => isset($this->event_fields["is_dummy_event"]) ? checkValSet($this->event_fields["is_dummy_event"]) : 0,
            "conference_type"     => KctCoreService::getInstance()->findEventConferenceType($this->resource),
            "conference_settings" => $this->getConferenceSetting(),
        ];
    }
    
    public function getConferenceSetting() {
        return isset($this->resource->event_fields['conference_settings'])
            ? $this->resource->event_fields['conference_settings']
            : [];
    }
}
