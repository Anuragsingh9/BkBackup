<?php

namespace Modules\Cocktail\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Cocktail\Rules\CheckAlreadyEventMemberRule;
use Modules\Cocktail\Rules\EventAndSpaceOpenOrNotStarted;
use Modules\Cocktail\Rules\MemberAddDataRule;
use Modules\Cocktail\Services\AuthorizationService;

class EventUserAddRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return AuthorizationService::getInstance()->isUserEventAdmin($this->input('event_uuid'));
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'event_uuid'   => ['required_without:workshop_id', new EventAndSpaceOpenOrNotStarted('event_uuid', true), new CheckAlreadyEventMemberRule('event_uuid', $this->input('data'))],
            'workshop_id'  => ['required_without:event_uuid', new EventAndSpaceOpenOrNotStarted('workshop_id', true), new CheckAlreadyEventMemberRule('workshop_id', $this->input('data'))],
            'data'         => ['nullable', 'json', new MemberAddDataRule],
            'email'        => 'required_without:data|nullable|max:200',
            'firstname'    => 'nullable|string|max:200',
            'lastname'     => 'nullable|string|max:200',
            'member_type'  => 'nullable',
            'is_presenter' => 'nullable|in:0,1',
            'is_moderator' => 'nullable|in:0,1',
        ];
        
    }
    
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
    }
}
