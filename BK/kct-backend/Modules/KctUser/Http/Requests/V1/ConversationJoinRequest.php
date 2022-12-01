<?php

namespace Modules\KctUser\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Modules\KctUser\Rules\V1\CheckDNDRule;
use Modules\KctUser\Rules\V1\NoConversationJoinedRule;

//use Modules\KctUser\Services\AuthorizationService;
use Modules\KctUser\Rules\V1\SpaceOpen;
use Modules\KctUser\Services\KctUserAuthorizationService;

/**
 * @OA\Schema(
 *     title= "RequestValidation: ConversationJoinRequest",
 *     description= "Validates the request for changing conversation type",
 *     type= "object",
 *     required= {"conversation_uuid","is_private"},
 *     @OA\Property(property="space_uuid",type="string",description="Unique uuid of the conversation",
 *          example="sf7dffdsf7dfsdf7sfds7fsfd44fd"
 *     ),
 *     @OA\Property(property="user_id",type="integer",description="For private=1 and for normal=0",example="1"),
 *     @OA\Property(property="dummy_user_id",type="integer",description="For private=1 and for normal=0",example="1"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will be validating the conversation join request
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class ConversationJoinRequest
 * @package Modules\SuperAdmin\Services\BusinessServices\factory
 */
class ConversationJoinRequest extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules() {
        return [
            'space_uuid'    => ['required', new SpaceOpen],
            'user_id'       => ['required_without:dummy_user_id', 'nullable',
                Rule::exists('tenant.kct_space_users', 'user_id'),
                Rule::exists('tenant.users', 'id'),
                new NoConversationJoinedRule($this->space_uuid),
            ], // to with conversation starting, check current and user-id both belongs to same space,
            'dummy_user_id' => ['required_without:user_id', 'nullable', 'exists:tenant.dummy_users,id'],
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Determine if the user is authorized to make this request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will be use for custom error message for authorization failed
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
    }
}
