<?php

namespace Modules\Cocktail\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Cocktail\Rules\EventAndSpaceNotStarted;
use Modules\Cocktail\Services\AuthorizationService;

class EventRegistrationFormRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $validation = config('cocktail.validations');
        return [
            'event_uuid' => ['required', new EventAndSpaceNotStarted],
            'display'    => 'required|in:0,1',
            'title'      => "required|string|min:{$validation['default_min']}|max:{$validation['registration']['title_max']}",
            'points'     => "required|string|min:{$validation['default_min']}|max:{$validation['registration']['points_max']}",
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
