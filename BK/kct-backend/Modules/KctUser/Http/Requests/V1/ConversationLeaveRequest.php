<?php

namespace Modules\KctUser\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     title= "RequestValidation: ConversationLeaveRequest",
 *     description= "Validates the request for changing conversation type",
 *     type= "object",
 *     required= {"conversation_uuid","is_private"},
 *     @OA\Property(property="conversation_uuid",type="string",description="Unique uuid of the conversation")
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will validate request data for user leaving a conversation.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class ConversationLeaveRequest
 *
 * @package Modules\KctUser\Http\Requests\V1
 */
class ConversationLeaveRequest extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules() {
        return [
            'conversation_uuid' => ['required']
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Determine if the user is authorized to make this request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
    }
}
