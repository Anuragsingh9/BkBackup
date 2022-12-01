<?php

namespace Modules\KctAdmin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\KctAdmin\Rules\EventRule;

/**
 * @OA\Schema(
 *     title= "RequestValidation:Update multi User Role",
 *     description= "Update Multi User Role",
 *     type= "object",
 *     required= {"users","role","event_uuid"},
 *     @OA\Property(property="users",
 *          type="array", description="Users",
 *          @OA\Items(type="integer",example="1"),
 *      ),
 *     @OA\Property(property="group_key",
 *          type="string", description="group key",
 *          example="default"
 *      ),
 *      @OA\Property( property="role",type="integer",
 *     description="Role of the user i.e- 0 = event user, 1 = team, 2 = expert, 3 = vip",
 *     example="1",enum={"0","1","2","3"}),
 *      @OA\Property( property="event_uuid",type="string",
 *     description="Event uuid",example="2856e2d0-24d9-11ec-a244-74867a0dc41b"),
 * )
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This method will validate all request data for bulk role update
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class UserBulkRoleUpdateRequest
 */
class UserBulkRoleUpdateRequest extends FormRequest {
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules() {
        return [
            "users"      => "required|array",
            "users.*"    => "required|exists:tenant.users,id",
            "role"       => "required|in:0,1,2,3", // 0 = event user, 1 = team, 2 = expert, 3 = vip
            "event_uuid" => ["required", new EventRule],
            "group_key"  => "required|exists:tenant.groups,group_key",
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
