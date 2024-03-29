<?php

namespace Modules\Messenger\Http\Requests;

use App\Rules\UUID;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Messenger\Rules\MessageAttachmentUrlRule;
use Modules\Messenger\Service\AuthorizationService;
use Modules\Messenger\Service\ChannelService;

class MessageStoreRequest extends FormRequest {
    /**
     * @return array
     */
    public function rules() {
        return [
            'channel_uuid' => [
                'required',
                Rule::exists('tenant.im_channels', 'uuid')->whereNull('deleted_at')
            ],
            'text'         => 'required_without:url|nullable|string|max:' . config('messenger.validations.message_text'),
            'url'          => ['nullable', 'json', new MessageAttachmentUrlRule],
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
     * @return bool
     */
    public function authorize() {
        return AuthorizationService::getInstance()->isUserBelongsToChannelOrWorkshop($this->channel_uuid, Auth::user());
    }
    
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => FALSE,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
//        parent::failedValidation($validator); // TODO: Change the autogenerated stub
    }
}
