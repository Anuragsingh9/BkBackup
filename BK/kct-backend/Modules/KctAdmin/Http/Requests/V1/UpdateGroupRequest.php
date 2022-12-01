<?php

namespace Modules\KctAdmin\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     title= "RequestValidation:Update Group",
 *     description= "Validates the request for updating group",
 *     type= "object",
 *     required= {"group_key"},
 *  @OA\Property(property="group_name",type="string",description="Name of group",example="First update"),
 *  @OA\Property(property="group_key",type="string",description="group key for every group that is unique",example="default"),
 *  @OA\Property(property="description",type="string",description="Description of group",example="To update group"),
 *  @OA\Property(property="group_type",type="string",description="Type of group",example="functional_group"),
 *  @OA\Property(property="type_value",type="string",description="Type value of group",example="Location"),
 *  @OA\Property( property="pilot",type="array",description="Group pilot",
 *                  @OA\Items(type="integer",example="1"),
 *              ),
 *  @OA\Property(property="allow_user",type="integer",description="Toggle button for allow user",example="1"),
 *  @OA\Property(property="allow_manage_pilots_owner",type="integer",description="Toggle button for allow manage pilots and owners.",example="1"),
 *  @OA\Property(property="allow_design_setting",type="integer",description="Toggle button for allow design setting.",example="1"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class is used to validate the group update request
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class UpdateGroupRequest
 *
 * @package Modules\KctAdmin\Http\Requests\V1
 */
class UpdateGroupRequest extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules() {
        $validations = config('kctadmin.modelConstants.groups.validations');
        return [
            'new_group_key'             => 'nullable',
            'group_name'                => "nullable|string|min:{$validations['min_name']}|max:{$validations['max_name']}",
            'description'               => "nullable|max:{$validations['max_description']}",
            'type_value'                => "nullable|string|max:{$validations['max_type_value']}",
            'pilot'                     => 'nullable|array|max:1',
            'pilot.*'                   => 'nullable|exists:tenant.users,id',
            'co_pilot'                  => 'nullable|array',
            'co_pilot.*'                => 'nullable|exists:tenant.users,id',
            'allow_user'                => 'nullable|in:0,1',
            'allow_manage_pilots_owner' => 'nullable|in:0,1',
            'allow_design_setting'      => 'nullable|in:0,1',
            'pilot_id'                  => 'nullable|array',
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
}
