<?php

namespace Modules\Events\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Modules\Events\Service\EventService;

class UpdateOrgAdminRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'key'   => 'required|in:virtual_do,virtual_prefix',
            'value' => 'required',
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
    
    
    protected function getValidatorInstance() {
        
        $userWhere = function ($q) {
            $q->orWhere('role_commision', '1');
            $q->orWhere('role', 'M1');
            $q->orWhere('role', 'M0');
        };
        
        $validator = parent::getValidatorInstance();
       
        $validator->sometimes(
            'value',
            ['required', Rule::exists('tenant.users', 'id')->where($userWhere)],
            function () {
                return $this->key == 'virtual_do';
            });
        
        $validator->sometimes('value', 'required|string|max:100', function () {
            return $this->key == 'virtual_prefix';
        });
        
        return $validator;
    }
    
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
    }
    
}
