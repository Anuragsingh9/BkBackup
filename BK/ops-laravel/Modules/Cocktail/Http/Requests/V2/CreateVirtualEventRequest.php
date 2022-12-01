<?php

namespace Modules\Cocktail\Http\Requests\V2;

use App\AccountSettings;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Modules\Cocktail\Services\V2Services\KctCoreService;
use Modules\Events\Entities\Event;
use Modules\Events\Rules\EventEndTimeRule;
use Modules\Events\Rules\ImageAspectRatioCheck;
use Modules\Events\Rules\EventStartTimeRule;
use Modules\Events\Rules\EventTimeRule;
use Modules\Events\Rules\OpeningHourAfterRule;
use Modules\Events\Rules\OpeningHourBeforeRule;
use Modules\Events\Service\EventService;


/**
 * @OA\Schema(
 *  title="RequestValidation: User Register Request Validation",
 *  description="Validates the request body for registering a user account ",
 *  type="object",
 *  required={"email", "fname", "lname", "event_uuid", "password"},
 *  @OA\Property(property="title",type="string",description="Title Of Event",example="Title Of Event"),
 *  @OA\Property(property="header_line_one",type="string",description="Header Line One ",example="Header Line One "),
 *  @OA\Property(property="header_line_two",type="string",description="Header Line Two",example="Header Line Two"),
 *  @OA\Property(property="header_text",type="string",description="Header Text",example="Header Text"),
 *  @OA\Property(property="description",type="string",description="Description of event",example="Description of event"),
 *  @OA\Property(property="date",type="date",description="Date of event",example="2020-12-31"),
 *  @OA\Property(property="start_time",type="time",description="Start time of event",example="12:12:59"),
 *  @OA\Property(property="end_time",type="time",description="End time of event",example="12:59:58"),
 *  @OA\Property(property="opening_hours_before",type="integer",description="Opening Before",example="1"),
 *  @OA\Property(property="opening_hours_during",type="integer",description="During event is on or off",example="1"),
 *  @OA\Property(property="opening_hours_after",type="integer",description="Opening after",example="1"),
 * )
 *
 * Class CreateVirtualEventRequest
 *
 * @package Modules\Cocktail\Http\Requests\ApiV2
 */
class CreateVirtualEventRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $validation = config('events.validations');
        $dimension = "dimensions:" .
            "min_width={$validation['image_w']}," .
            "min_height={$validation['image_h']}";

        $validations = [
            'title'             => "required|string|min:{$validation['default_min']}|max:{$validation['title']}",
            'header_line_one'   => "required|string|min:{$validation['default_min']}|max:{$validation['line_one']}",
            'header_line_two'   => "required|string|min:{$validation['default_min']}|max:{$validation['line_two']}",
            'header_text'       => "nullable|string|min:{$validation['default_min']}|max:{$validation['header']}",
            'description'       => "nullable|string|min:{$validation['default_min']}|max:{$validation['description']}",
            'date'              => "required|date|after:yesterday",
            'start_time'        => ['required', new EventStartTimeRule($this->date, null, $this->type)],
            'end_time'          => ['required', 'after:start_time', new EventEndTimeRule($this->date, null, $this->start_time)],
            'is_dummy_event'    => 'nullable|in:1,0',
            'follow_conference' => 'nullable|in:0,1',
            'image'             => ['nullable','image',"$dimension"],
        ];
        
        return $this->addConferenceValidation($validations);
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
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To add the bluejeans conference type event validation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $rules
     * @return mixed
     */
    public function getBJValidations($rules) {
        $options = config('kct_const.bj_options');
        foreach ($options as $option) {
            $rules[$option] = 'required_if:follow_conference,1|in:0,1';
        }
        return $rules;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To add the validations for the zoom conference type event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $rules
     * @return mixed
     */
    public function getZoomValidation($rules) {
        $options = config('kct_const.zoom_options');
        foreach ($options as $option) {
            $rules[$option] = 'required_if:follow_conference,1|in:0,1';
        }
        return $rules;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To put the conference related validation
     * put the validations respective to the current conference type
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $rules
     * @return mixed
     */
    public function addConferenceValidation($rules) {
        $conferenceTypes = config('kct_const.conference_type');
        $currentConference = KctCoreService::getInstance()->getCurrentConference();
        switch ($currentConference) {
            case $conferenceTypes['bj']:
                return $this->getBJValidations($rules);
            case $conferenceTypes['zoom']:
                return $this->getZoomValidation($rules);
            default:
                return $rules;
        }
    }
    
}
