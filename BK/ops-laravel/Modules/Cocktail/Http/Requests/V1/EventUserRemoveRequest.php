<?php

namespace Modules\Cocktail\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Modules\Cocktail\Rules\EventAndSpaceNotStarted;
use Modules\Cocktail\Services\AuthorizationService;

class EventUserRemoveRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'user_id' => [
                'required',
                'exists:tenant.users,id',
                Rule::exists('tenant.event_user_data', 'user_id')->where('event_uuid', $this->event_uuid)],
            'event_uuid' => ['required', new EventAndSpaceNotStarted],
        ];
    }
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return AuthorizationService::getInstance()->isUserEventAdmin($this->input('event_uuid'));
    }
    
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => FALSE,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
    }
}
