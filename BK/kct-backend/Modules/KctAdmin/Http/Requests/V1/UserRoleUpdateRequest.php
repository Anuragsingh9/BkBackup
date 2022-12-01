<?php

namespace Modules\KctAdmin\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Modules\UserManagement\Rules\UserRule;

/**
 * @OA\Schema(
 *     title= "RequestValidation:Update User Role",
 *     description= "Update User Role",
 *     type= "object",
 *     required= {"user_id", "role"},
 *     @OA\Property( property="user_id",type="integer",description="User id",example="1"),
 *     @OA\Property( property="role",type="string",
 *     description="event_user,team, expert,vip",example="team", enum={"1", "2"}),
 *     @OA\Property( property="event_uuid",type="string",
 *     description="event uuid",example="6992c1ac-b0bf-11ec-a0ed-f8cab8612f99"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will validate request data for updating user role.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class UserRoleUpdateRequest
 *
 * @package Modules\KctAdmin\Http\Requests\V1
 */
class UserRoleUpdateRequest extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules(): array {
        if (!$this->input('event_uuid')){
            $groupRoles = [1,2,3,4]; // 1 user, 2 organiser 3 owner 4 co-pilot
            $role = implode(',',$groupRoles);
        }else{
            $eventRoles = ['event_user','team','expert','vip'];
            $role = implode(',',$eventRoles);
        }
        return [
            'user_id'    => ['required', new UserRule],
            'role'       => "required|in:$role",
            'event_uuid' => 'nullable|exists:tenant.events,event_uuid',
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
