<?php

namespace Modules\Cocktail\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Cocktail\Rules\EventAndSpaceNotStarted;
use Modules\Cocktail\Services\AuthorizationService;

class EventGraphicsLogoDeleteRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
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
            'status' => false,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
    }
}
