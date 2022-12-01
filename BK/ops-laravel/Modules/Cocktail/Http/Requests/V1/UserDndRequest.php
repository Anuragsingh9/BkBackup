<?php

namespace Modules\Cocktail\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Cocktail\Rules\EventExists;
use Modules\Cocktail\Services\AuthorizationService;

class UserDndRequest extends FormRequest {
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
            'event_uuid'   => ['required', new EventExists],
            'active_state' => 'required|in:1,2',
        ];
    }
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        if (!AuthorizationService::getInstance()->isUserEventMember($this->input('event_uuid'))) {
            $this->authorizationMessage = __('cocktail::message.not_belongs_event');
            return false;
        }
        return true;
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
            'msg'    => $this->authorizationMessage ? $this->authorizationMessage : 'Unauthorized',
        ], 403));
    }
}
