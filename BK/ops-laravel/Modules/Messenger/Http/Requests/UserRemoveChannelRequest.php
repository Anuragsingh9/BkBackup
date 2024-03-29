<?php

namespace Modules\Messenger\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Messenger\Entities\Channel;

class UserRemoveChannelRequest extends FormRequest {
    
    public function rules() {
        return [
            'channel_uuid' => [
                'required',
                Rule::exists('tenant.im_channels', 'uuid')->where(function ($q) {
                    $q->whereNull('deleted_at');
                    $q->where('owner_id', '!=', $this->user_id);
                })
            ],
            'user_id'      => 'required|exists:tenant.users,id',
        ];
    }
    
    public function authorize() {
        $channel = Channel::find($this->channel_uuid);
        if ($channel && !in_array(Auth::user()->role, ['M0', 'M1'])) {
            return (boolean)$channel->owner_id == Auth::user()->id;
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
