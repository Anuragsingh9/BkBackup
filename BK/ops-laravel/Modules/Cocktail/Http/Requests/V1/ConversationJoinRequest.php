<?php

namespace Modules\Cocktail\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Modules\Cocktail\Rules\CheckDNDRule;
use Modules\Cocktail\Rules\NoConversationJoinedRule;
use Modules\Cocktail\Rules\SpaceOpen;
use Modules\Cocktail\Services\AuthorizationService;

class ConversationJoinRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'space_uuid'    => ['required', new SpaceOpen],
            'user_id'       => ['required_without:dummy_user_id', 'nullable',
                Rule::exists('tenant.event_space_users', 'user_id'),
                Rule::exists('tenant.users', 'id'),
                new NoConversationJoinedRule($this->space_uuid),
                new CheckDNDRule($this->space_uuid),
            ], // to with conversation starting, check current and user-id both belongs to same space,
            'dummy_user_id' => ['required_without:user_id', 'nullable', 'exists:tenant.dummy_users,id'],
        ];
    }
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return AuthorizationService::getInstance()->isUserBelongsToSpace($this->space_uuid);
    }
    
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
    }
}
