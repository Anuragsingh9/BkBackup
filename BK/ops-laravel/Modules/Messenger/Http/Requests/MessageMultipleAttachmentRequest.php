<?php

namespace Modules\Messenger\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class MessageMultipleAttachmentRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $extensions = array_merge(...array_values(config('messenger.validations.extensions')));
        return [
            'channel_uuid'    => [
                'required',
                Rule::exists('tenant.im_channels', 'uuid')->whereNull('deleted_at')
            ],
            'system_upload'   => 'required|array',
            'system_upload.*' => 'required|file|mimes:' . implode(',', $extensions),
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
