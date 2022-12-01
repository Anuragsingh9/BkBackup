<?php

namespace Modules\Events\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Modules\Events\Service\EventService;

class UpdateOrganiserRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'fname'   => 'required|string|max:80',
            'lname'   => 'required|string|max:80',
            'company' => 'required|string|max:255',
            'image'   => 'nullable|image',
            'email'   => 'required|email|string|max:255',
            'phone'   => 'nullable|string|max:18',
            'website' => 'nullable|url',
        ];
    }
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        if (EventService::getInstance()->isAdmin()) {
            return true;
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
    
}
