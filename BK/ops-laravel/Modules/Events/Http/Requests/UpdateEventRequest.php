<?php

namespace Modules\Events\Http\Requests;

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

class UpdateEventRequest extends FormRequest {
    
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
        $this->types = config('events.event_type');
        $spaceVal = config('cocktail.validations.space');
        $bluejeans = 'required_if:event_uses_bluejeans_event,1|in:1,0';
        $dimension = "dimensions:" .
            "min_width={$this->configValidation['image_w']}," .
            "min_height={$this->configValidation['image_h']}";
        
        return [
            'id'                         => ["required", new EventExistsRule],
            'title'                      => "required|string|min:{$this->configValidation['default_min']}|max:{$this->configValidation['title']}",
            'header_text'                => "required|string|min:{$this->configValidation['default_min']}|max:{$this->configValidation['header']}",
            'description'                => "nullable|string|min:{$this->configValidation['default_min']}|max:{$this->configValidation['description']}",
            'date'                       => 'required|date|after:yesterday',
            'start_time'                 => ['required', new EventStartTimeRule($this->date, $this->id)],
            'end_time'                   => ['required', 'after:start_time', new EventEndTimeRule($this->date, $this->id, $this->start_time)],
            'image'                      => [
                "nullable",
                "image",
                $dimension,
                new ImageAspectRatioCheck($this->configValidation['image_width_height_ratio'], $this->configValidation['image_height_width_ratio'])
            ],
            // external event specific validations
            'organiser_id'               => 'nullable',
            // internal external specific  validations
            'address'                    => "required_unless:type,virtual|string|min:{$this->configValidation['default_min']}|max:{$this->configValidation['address']}",
            'city'                       => "required_unless:type,virtual|string|min:{$this->configValidation['default_min']}|max:{$this->configValidation['city']}",
            // virtual event validations
            'manual_opening'             => ['nullable', 'in:1,0', new ManualOpeningRule($this->date, $this->start_time, $this->end_time)],
            'opening_hours_before'       => "required_if:type,{$this->types['virtual']}|integer|min:0|max:{$spaceVal['opening_hour_before_max']}",
            'opening_hours_during'       => "required_if:type,{$this->types['virtual']}|in:1,0",
            'opening_hours_after'        => "required_if:type,{$this->types['virtual']}|integer|min:0|max:{$spaceVal['opening_hour_after_max']}",
            'event_uses_bluejeans_event' => "required_if:type,{$this->types['virtual']}|in:1,0",
            'event_chat'                 => $bluejeans,
            'attendee_search'            => $bluejeans,
            'q_a'                        => $bluejeans,
            'allow_anonymous_questions'  => $bluejeans,
            'auto_approve_questions'     => $bluejeans,
            'auto_recording'             => $bluejeans,
            'phone_dial_in'              => $bluejeans,
            'raise_hand'                 => $bluejeans,
            'display_attendee_count'     => $bluejeans,
            'allow_embedded_replay'      => $bluejeans
        
        ];
    }
    
    protected function getValidatorInstance() {
        $validator = parent::getValidatorInstance();
        $extOrg = ['required', Rule::exists('tenant.event_organisers', 'id')->whereNull('deleted_at')];
        $validator->sometimes('organiser_id', $extOrg, function () {
            return $this->type == config('events.event_type.ext');
        });
        return $validator;
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
    
    public function messages() {
        $validation = config('events.validations');
        return [
            'dimensions' => __('events::message.image_validation', [
                'miw' => $validation['image_w'],
                'mih' => $validation['image_h'],
            ]),
        ];
    }
    
}
