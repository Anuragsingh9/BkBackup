<?php

namespace Modules\Cocktail\Http\Requests\V2;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Cocktail\Rules\EventAndSpaceOpenOrNotStarted;
use Modules\Cocktail\Rules\EventExists;

class InviteUserRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        
        $userVal = config('cocktail.validations.user');
        
        return [
            'user'         => 'required|array|max:10',
            'user.*'       => 'required|array|max:3',
            'user.*.email' => 'required|email',
            'user.*.fname' => "required|string|max:{$userVal['fname_max']}|min:{$userVal['fname_min']}",
            'user.*.lname' => "required|string|max:{$userVal['lname_max']}|min:{$userVal['lname_min']}",
            'event_uuid'   => ["required", new EventExists, new EventAndSpaceOpenOrNotStarted],
        ];
    }
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }
    
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
    }
    
    public function messages() {
        $messages = parent::messages();
        $messages['user.*.fname.required'] = __('validation.required', ['attribute' => 'fname']);
        $messages['user.*.fname.string'] = __('validation.string', ['attribute' => 'fname']);
        $messages['user.*.fname.min'] = __('validation.min.numeric', ['attribute' => 'fname']);
        $messages['user.*.fname.max'] = __('validation.max.numeric', ['attribute' => 'fname']);
        
        $messages['user.*.lname.required'] = __('validation.required', ['attribute' => 'lname']);
        $messages['user.*.lname.string'] = __('validation.string', ['attribute' => 'lname']);
        $messages['user.*.lname.min'] = __('validation.min.numeric', ['attribute' => 'lname']);
        $messages['user.*.lname.max'] = __('validation.max.numeric', ['attribute' => 'lname']);
        
        $messages['user.*.email.required'] = __('validation.required', ['attribute' => 'email']);
        $messages['user.*.email.email'] = __('validation.email', ['attribute' => 'email']);
        return $messages;
    }
}
