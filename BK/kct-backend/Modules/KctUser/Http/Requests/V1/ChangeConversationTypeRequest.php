<?php

namespace Modules\KctUser\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     title= "RequestValidation: ChangeConversationTypeRequest",
 *     description= "Validates the request for changing conversation type",
 *     type= "object",
 *     required= {"conversation_uuid","is_private"},
 *     @OA\Property(property="conversation_uuid",type="string",
 *     description="Unique uuid of the conversation",example="sf7dffdsf7dfsdf7sfds7fsfd44fd",),
 *     @OA\Property(property="is_private",type="integer",description="For private=1 and for normal=0",example="1"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will be used for validating the request data for changing the conversation
 * type(private/public).
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class ChangeConversationTypeRequest
 *
 * @package Modules\KctUser\Http\Requests\V1
 */
class ChangeConversationTypeRequest extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules() {

        return [
            'conversation_uuid' => [
                'required',
                Rule::exists('tenant.kct_conversations', 'uuid')->whereNull('end_at')
            ],
            'is_private'        => 'nullable|in:0,1',
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
}
