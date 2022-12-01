<?php

namespace Modules\Messenger\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Messenger\Entities\Channel;

class DeleteChannelRequest extends FormRequest {
    
    public function rules() {
        return [
            'channel_uuid' => [
                'required',
                Rule::exists('tenant.im_channels', 'uuid')->whereNull('deleted_at')
            ],
        ];
    }
    
    public function authorize() {
        $channel = Channel::find($this->channel_uuid);
        if ($channel) { // if channel found check its auth as owner otherwise validation will do its work
            return Auth::user()->role == 'M0' || Auth::user()->role == 'M1' || $channel->owner_id == Auth::user()->id;
        }
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
