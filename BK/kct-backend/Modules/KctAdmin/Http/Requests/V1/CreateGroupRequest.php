<?php

namespace Modules\KctAdmin\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Modules\KctAdmin\Rules\GroupCreationLimitRule;

/**
 * @OA\Schema(
 *     title= "RequestValidation:Create Group",
 *     description= "Validates the request for creating group",
 *     type= "object",
 *     required= {"group_name","group_key","group_type","type_value","pilot"},
 *  @OA\Property(property="group_name",type="string",description="Name of group",example="First"),
 *  @OA\Property(property="group_key",type="string",description="group key for every group that is unique",example="Last"),
 *  @OA\Property(property="description",type="string",description="Description of group",example="Description of group"),
 *  @OA\Property(property="group_type",type="string",description="Type of group",example="functional_group"),
 *  @OA\Property(property="type_value",type="string",description="Type value of group",example="location"),
 *  @OA\Property(property="pilot",type="array",description="Add pilots for the group",
 *                  @OA\Items(type="integer",example="1"),),
 *  @OA\Property(property="allow_user",type="integer",description="Toggle button for allow user",example="1"),
 *  @OA\Property(property="allow_manage_pilots_owner",type="integer",description="Toggle button for allow manage pilots and owners.",example="1"),
 *  @OA\Property(property="allow_design_setting",type="integer",description="Toggle button for allow design setting.",example="1"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will be used for validate the group creation request.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class CreateGroupRequest
 *
 * @package Modules\KctAdmin\Http\Requests\V1
 */
class CreateGroupRequest extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules(): array {
        $validations = config('kctadmin.modelConstants.groups.validations');
        return [
            'group_type'                => 'required|string|exists:tenant.group_types,group_type',
            'group_name'                => ["required", "string", "min:{$validations['min_name']}",
                "max:{$validations['max_name']}", new GroupCreationLimitRule()],
            'description'               => "nullable|max:{$validations['max_description']}",
            'type_value'                => "required|string|max:{$validations['max_type_value']}",
            'pilot'                     => 'required|array|max:1',
            'pilot.*'                   => 'required|exists:tenant.users,id',
            'co_pilot'                  => 'nullable|array',
            'co_pilot.*'                => 'nullable|exists:tenant.users,id',
            'allow_user'                => 'nullable|in:0,1',
            'allow_manage_pilots_owner' => 'nullable|in:0,1',
            'allow_design_setting'      => 'nullable|in:0,1',
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
