<?php

namespace Modules\Cocktail\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Modules\Cocktail\Rules\DummySpaceMaxCountRule;
use Modules\Cocktail\Rules\EventExists;
use Modules\Cocktail\Rules\NoEventConversationJoinedRule;
use Modules\Cocktail\Rules\SpaceMaxCountRule;
use Modules\Cocktail\Rules\SpaceOpen;
use Modules\Cocktail\Services\AuthorizationService;

class EventSpaceAddUserRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'space_uuid' => ['required', Rule::exists('tenant.event_space', 'space_uuid')
                ->where(function ($q) {
                    $q->where('event_uuid', $this->event_uuid);
                    $q->whereNull('deleted_at');
                }),
                new SpaceOpen,
                new SpaceMaxCountRule,new DummySpaceMaxCountRule,
            ],
            'event_uuid' => ['required', new EventExists, new NoEventConversationJoinedRule]
        ];
    }
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return AuthorizationService::getInstance()->isUserEventMember($this->event_uuid);
    }
    
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
    }
    
    protected function failedAuthorization() {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => __('cocktail::message.not_belongs_event'),
        ], 403));
    }
}
