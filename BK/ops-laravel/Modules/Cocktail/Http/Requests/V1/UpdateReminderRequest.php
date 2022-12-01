<?php

namespace Modules\Cocktail\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UpdateReminderRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        
        $keys = array_keys(config('cocktail.setting_keys.reminder_mails'));
        $validations = [
            'data'                    => 'required|array',
            'data.reminders'          => 'required|array',
        ];
        foreach ($keys as $key) {
            $validations["data.reminders.$key"] = 'required|array';
            $validations["data.reminders.$key.active"] = 'required|numeric|in:0,1';
            $validations["data.reminders.$key.days"]   = 'required|numeric';
        }
        return $validations;
    }
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return Auth::check() && (Auth::user()->role == "M1" || Auth::user()->role == "M0");
    }
    
    
    /**
     * Return the message in custom way if validation failed
     *
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
    }
    
    /**
     * @return JsonResponse|void
     */
    protected function failedAuthorization() {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => "Unauthorised",
        ], 403));
    }
}
