<?php

namespace Modules\KctAdmin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\KctAdmin\Rules\EventRule;
use Modules\KctAdmin\Rules\RemoveEventUserRule;

/**
 * @OA\Schema(
 *     title= "RequestValidation:Remove multi User Role",
 *     description= "Remove Multi User Role",
 *     type= "object",
 *     required= {"users","users.user_id","event_uuid"},
 *     @OA\Property(property="users",
 *          type="array", description="Users",
 *             @OA\Items(type="integer",example="1"),
 *      ),
 *      @OA\Property( property="event_uuid",type="string",
 *     description="Event uuid",example="2856e2d0-24d9-11ec-a244-74867a0dc41b"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This method will validate all request data for bulk role remove from event
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class UserBulkRoleRemoveRequest
 */
class UserBulkRoleRemoveRequest extends FormRequest {
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     * @return array
     */
    public function rules(): array {
        return [
            "users"      => "required|array",
            "users.*"    => ["required", "exists:tenant.users,id", new RemoveEventUserRule($this->event_uuid)],
            "event_uuid" => ["required", new EventRule]
        ];
    }

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
}
