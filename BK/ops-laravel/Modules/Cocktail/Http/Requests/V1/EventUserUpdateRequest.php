<?php

namespace Modules\Cocktail\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Modules\Cocktail\Rules\EventAndSpaceMustEndedRule;
use Modules\Cocktail\Rules\EventAndSpaceOpenOrNotStarted;
use Modules\Cocktail\Services\AuthorizationService;

class EventUserUpdateRequest extends FormRequest {
    /**
     * @var array|string|null
     */
    private $authorizationMessage;
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'user_id'    => [
                'required',
                'exists:tenant.users,id',
                Rule::exists('tenant.event_user_data', 'user_id')->where('event_uuid', $this->event_uuid)
            ],
            'event_uuid' => ['required'],
            'field'      => 'required|in:1,2,3,4', // 1. toggle presenter, 2. toggle moderator, 3 - host, 4 - presence
            'space_uuid' => ['required_if:field,3'],
            'presence'   => 'required_if:field,4|nullable|in:P,ANE',
        ];
    }
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        if (AuthorizationService::getInstance()->isUserEventAdmin($this->input('event_uuid'))) {
            return true;
        } else {
            $this->authorizationMessage = __('cocktail::message.not_admin');
            return false;
        }
    }
    
    protected function failedAuthorization() {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => $this->authorizationMessage ? $this->authorizationMessage : "Unauthorised",
        ], 403));
    }
    
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
    }
    
    public function getValidatorInstance() {
        $validator = parent::getValidatorInstance();
        
        $validator->sometimes('event_uuid', ['required', new EventAndSpaceOpenOrNotStarted], function () {
            return $this->input('field') != 4;
        });
        
        $validator->sometimes('event_uuid', ['required', new EventAndSpaceMustEndedRule(__("cocktail::message.presence_can_after_event"))], function () {
            return $this->input('field') == 4;
        });
        
        return $validator;
    }
}

