<?php

namespace Modules\Messenger\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class RemoveUserPanelRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'channel_uuid' => [
                'required',
                Rule::exists('tenant.im_channels', 'uuid')->where(function ($q) {
                    $q->whereNull('deleted_at');
                    $q->where('channel_type', 3);
                })
            ],
        ];
    }
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return TRUE;
    }
    
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => FALSE,
            'msg'    => implode(',', $validator->errors()->all())
        ],422));
//        parent::failedValidation($validator); // TODO: Change the autogenerated stub
    }
}

