<?php

namespace Modules\Events\Http\Requests;

use App\AccountSettings;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Modules\Events\Entities\Event;
use Modules\Events\Rules\EventEndTimeRule;
use Modules\Events\Rules\ImageAspectRatioCheck;
use Modules\Events\Rules\EventStartTimeRule;
use Modules\Events\Rules\EventTimeRule;
use Modules\Events\Rules\OpeningHourAfterRule;
use Modules\Events\Rules\OpeningHourBeforeRule;
use Modules\Events\Service\EventService;

class CreateEventRequest extends FormRequest {
    /**
     *
     * @return array
     */
    public function rules() {
        $validation = config('events.validations');
        $spaceVal = config('cocktail.validations.space');
        $dimension = "dimensions:" .
            "min_width={$validation['image_w']}," .
            "min_height={$validation['image_h']}";
        $requiredIfVirtual = "required_if:type,virtual";
        return [
            'title'                      => "required|string|min:{$validation['default_min']}|max:{$validation['title']}",
            'header_text'                => "required|string|min:{$validation['default_min']}|max:{$validation['header']}",
            'description'                => "nullable|string|min:{$validation['default_min']}|max:{$validation['description']}",
            'date'                       => "required|date|after:yesterday",
            'start_time'                 => ['required', new EventStartTimeRule($this->date, null, $this->type)],
            'end_time'                   => ['required', 'after:start_time', new EventEndTimeRule($this->date, null, $this->start_time)],
            'address'                    => "required_unless:type,virtual|string|min:{$validation['default_min']}|max:{$validation['address']}",
            'city'                       => "required_unless:type,virtual|string|min:{$validation['default_min']}|max:{$validation['city']}",
            'image'                      => [
                $requiredIfVirtual,
                "image",
                "$dimension",
                new ImageAspectRatioCheck($validation['image_width_height_ratio'], $validation['image_height_width_ratio'])
            ],
            'type'                       => "required|in:int,ext,virtual",
            'organiser_id'               => ['required_if:type,ext',
                'nullable',
                Rule::exists('tenant.event_organisers', 'id')->whereNull('deleted_at'),
            ],
            'event_uses_bluejeans_event' => "$requiredIfVirtual|in:1,0",
            'opening_hours_before'       => ["$requiredIfVirtual", "max:{$spaceVal['opening_hour_before_max']}", 'integer', new OpeningHourBeforeRule($this->date, $this->start_time)],
            'opening_hours_during'       => "$requiredIfVirtual|in:1,0",
            'opening_hours_after'        => [$requiredIfVirtual, "max:{$spaceVal['opening_hour_after_max']}", 'integer', new OpeningHourAfterRule($this->date, $this->start_time)],
        ];
    }
    
    /**
     *
     * @return Validator
     */
    protected function getValidatorInstance() {
        $validator = parent::getValidatorInstance();
        $param = [
            'event_chat', 'attendee_search', 'q_a',
            'allow_anonymous_questions', 'auto_approve_questions',
            'auto_recording', 'phone_dial_in', 'raise_hand',
            'display_attendee_count', 'allow_embedded_replay'
        ];
        $validator->sometimes($param, 'required|in:1,0', function () {
            $tenancy = app(\Hyn\Tenancy\Environment::class);
            $setting = AccountSettings::where('account_id', $tenancy->hostname()->id)->first();
            if ($setting &&
                isset($setting->setting['event_settings']['bluejeans_enabled']) &&
                $setting->setting['event_settings']['bluejeans_enabled'] &&
                $this->event_uses_bluejeans_event == 1
            ) {
                // if enabled from super admin and in event  then required else leave it
                return true;
            }
            return false;
        });
        
        return $validator;
    }
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return EventService::getInstance()->isAdmin();
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
