<?php

namespace Modules\KctAdmin\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\KctAdmin\Rules\PilotDeleteRule;
use Modules\KctAdmin\Rules\SelfUserRule;

/**
 * @OA\Schema(
 *     title= "RequestValidation:Delete multiple user type validation",
 *     description= "Validates the request for deleting multiple users",
 *     type= "object",
 *     required= {"user","user[0][id]"},
 *     @OA\Property(property="user",
 *          type="array",
 *          description="array of objects, each objec must have id key to delete",
 *          @OA\Items(
 *             @OA\Property(property="id",type="integer",description="User ID to delete",example="1"),
 *          ),
 *     ),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will validate request data for validating user bulk delete.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class UserBulkDeleteRequest
 *
 * @package Modules\KctAdmin\Http\Requests\V1
 */
class UserBulkDeleteRequest extends FormRequest {
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Determine if the user is authorized to make this request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return bool
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules(): array {
        $validation = config('kctadmin.modelConstants.users.validations');
        return [
            'user'      => "required|array|max:{$validation['bulk_delete_max']}",
            'user.*'    => 'required|array|max:1',
            'user.*.id' => ["required", "numeric", "exists:tenant.users,id", new SelfUserRule, new PilotDeleteRule],
        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'message'=> __('kctadmin::messages.cannot_delete_pilot'),
            'errors' => $validator->errors(),
        ], 422));
    }
}
