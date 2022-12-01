<?php

namespace Modules\Cocktail\Http\Requests\V2;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Modules\Cocktail\Rules\EventOrSpaceOpenRule;
use Modules\Cocktail\Services\AuthorizationService;

class UserQueRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status' => 'required|in:1,2,3',   // 1.Missed, 2.Answered, 3.Rejected
            'event_uuid' => ['required',Rule::exists('tenant.event_info','event_uuid'), new EventOrSpaceOpenRule],
            'from_id' => ['nullable',
                Rule::exists('tenant.users', 'id'),
                Rule::exists('tenant.event_user_data', 'user_id')->where('event_uuid', $this->event_uuid)],
            'conversation_uuid' => ['nullable',Rule::exists('tenant.event_conversation','uuid')->whereNull('end_at')]
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $isUserEventMember = AuthorizationService::getInstance()->isUserEventMember($this->event_uuid);
        if (!$isUserEventMember) {
            $this->authorizationMessage = __('cocktail::message.user_not_member');
            return false;
        }
        return true;
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
    }
}
