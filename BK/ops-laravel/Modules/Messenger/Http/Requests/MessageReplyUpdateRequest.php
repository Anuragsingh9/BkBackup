<?php

namespace Modules\Messenger\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Messenger\Entities\MessageReply;

class MessageReplyUpdateRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'message_id'       => [
                'required',
                Rule::exists('tenant.im_messages', 'id')->whereNull('deleted_at'),
                Rule::exists('tenant.im_message_replies', 'message_id')->where('id', $this->message_reply_id),
            ],
            'channel_uuid'     => [
                'required',
                Rule::exists('tenant.im_channels', 'uuid')->whereNull('deleted_at'),
                Rule::exists('tenant.im_messages', 'channel_uuid')->where('id', $this->message_id),
            ],
            'message_reply_id' => [
                'required',
                Rule::exists('tenant.im_message_replies', 'id')->whereNull('deleted_at'),
            ],
            'text'             => 'nullable|string|max:' . config('messenger.validations.message_reply'),
        ];
    }
    
    protected function validationData() {
        $all = parent::validationData();
        //Convert request value to lowercase
        if (isset($all['text']))
            $all['text'] = strip_tags($all['text']);
        return $all;
    }
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        $reply = MessageReply::find($this->message_reply_id);
        return !$reply || ($reply->replied_by == Auth::user()->id);
    }
    
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => FALSE,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
//        parent::failedValidation($validator); // TODO: Change the autogenerated stub
    }
    
}
