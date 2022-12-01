<?php

namespace Modules\Cocktail\Http\Requests\V2;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Events\Entities\Event;
use Modules\Events\Rules\EventEndTimeRule;
use Modules\Events\Rules\EventExistsRule;
use Modules\Events\Rules\EventStartTimeRule;
use Modules\Events\Rules\ImageAspectRatioCheck;
use Modules\Events\Rules\ManualOpeningRule;
use Modules\Events\Service\EventService;

/**
 * @OA\Schema(
 *  title="RequestValidation: Event Update Request Validation",
 *  description="Validates the request body for registering a user account ",
 *  type="object",
 *  required={"id", "title", "header_line_1", "header_line_2", "header_text","date","start_time","end_time",
 *     "opening_hours_before","opening_hours_during","opening_hours_after"},
 *  @OA\Property(
 *      property="id",
 *      type="int",
 *      description="Id of the event",
 *      example="132"
 *  ),
 *  @OA\Property(
 *      property="title",
 *      type="string",
 *      description="Event Title",
 *      example="Test"
 *  ),
 *  @OA\Property(
 *      property="header_line_1",
 *      type="string",
 *      description="First Header Line",
 *      example="Test Header one"
 *  ),
 *  @OA\Property(
 *      property="header_line_2",
 *      type="string",
 *      description="Second Header Line",
 *      example="Test Header Two"
 *  ),*  @OA\Property(
 *      property="header_text",
 *      type="string",
 *      description="Main header",
 *      example="This is main header"
 *  ),
 *     *  @OA\Property(
 *      property="date",
 *      type="date",
 *      description="Date of the event",
 *      example="2021-02-19"
 *  ),*  @OA\Property(
 *      property="start_time",
 *      type="time",
 *      description="Staring time of the event",
 *      example="20:00:00"
 *  ),*  @OA\Property(
 *      property="end_time",
 *      type="time",
 *      description="Staring time of the event",
 *      example="21:00:00"
 *  ),
 *  @OA\Property(
 *      property="opening_hours_before",
 *      type="int",
 *      description="Opening hours before",
 *      example="0"
 *  ),*  @OA\Property(
 *      property="opening_hours_after",
 *      type="int",
 *      description="Opening hours after",
 *      example="15"
 *  ),*  @OA\Property(
 *      property="opening_hours_during",
 *      type="int",
 *      description="Opening hours before",
 *      example="1"
 *  ),
 * )
 *
 * Class UserRegisterRequest
 * @package Modules\Cocktail\Http\Requests\V2
 */
class UpdateVirtualEventRequest extends FormRequest {
    /**
     * @var array
     */
    private $configValidation;
    /**
     * @var string
     */
    private $types;
    
    private $event;
    
    protected function prepareForValidation() {
        $eventId = $this->route('event_id');
        $event = Event::find($eventId);
        $this->merge([
            'id'   => $eventId,
            'type' => $event ? $event->type : '',
        ]);
        $this->event = $event;
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $this->configValidation = config('events.validations');
        $dimension = "dimensions:" .
            "min_width={$this->configValidation['image_w']}," .
            "min_height={$this->configValidation['image_h']}";
        $this->types = config('events.event_type');
        $rules = [
            'id'              => ["required", new EventExistsRule],
            'type'            => 'required|in:' . config('events.event_type.virtual'),
            'title'           => "required|string|min:{$this->configValidation['default_min']}|max:{$this->configValidation['title']}",
            'header_line_one' => "required|string|min:{$this->configValidation['default_min']}|max:{$this->configValidation['line_one']}",
            'header_line_two' => "required|string|min:{$this->configValidation['default_min']}|max:{$this->configValidation['line_two']}",
            'header_text'     => "nullable|string|min:{$this->configValidation['default_min']}|max:{$this->configValidation['header']}",
            'description'     => "nullable|string|min:{$this->configValidation['default_min']}|max:{$this->configValidation['description']}",
            'date'            => 'required|date|after:yesterday',
            'start_time'      => ['required', new EventStartTimeRule($this->date, $this->id)],
            'end_time'        => ['required', 'after:start_time', new EventEndTimeRule($this->date, $this->id, $this->start_time)],
            'manual_opening'  => [
                'nullable',
                'in:1,0',
                new ManualOpeningRule(
                    $this->input('date'),
                    $this->input('start_time'),
                    $this->input('end_time')
                )
            ],
            'image'            => ['nullable','image',"$dimension"],
        ];
        return $this->addConferenceOptions($rules);
    }
    
    public function addConferenceOptions($rules) {
        $keys = array_unique(array_merge(config('kct_const.bj_options'),config('kct_const.zoom_options')));
        foreach ($keys as $key) {
            $rules[$key] = 'nullable|in:0,1';
        }
        return $rules;
    }
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        if (EventService::getInstance()->isAdmin()) {
            return true;
        } else if (Auth::user()->role_commision == 1) {
            return EventService::getInstance()->isEventAdmin($this->route('event_id'));
        }
        return false;
    }
    
    protected function failedAuthorization() {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => __('events::message.admin_only'),
        ], 403));
    }
    
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
    }
    
}
