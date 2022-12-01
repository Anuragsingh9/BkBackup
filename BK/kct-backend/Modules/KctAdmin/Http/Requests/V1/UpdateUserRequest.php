<?php

namespace Modules\KctAdmin\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\KctAdmin\Rules\CompanyNameRule;
use Modules\KctAdmin\Rules\EntityPositionRule;
use Modules\KctAdmin\Rules\GenderRule;
use Modules\KctAdmin\Rules\GradeRule;
use Modules\UserManagement\Entities\Entity;
use Modules\UserManagement\Rules\EntityRule;
use Modules\UserManagement\Rules\IsEmailEditableRule;
use Modules\UserManagement\Rules\UserRule;

/**
 * @OA\Schema(
 *     title= "RequestValidation:Update user profile",
 *     description= "Validates the request for updating user profiles",
 *     type= "object",
 *     required= {"id","fname","lname","email"},
 *     @OA\Property( property="id",type="integer",description="User Id",example="63"),
 *     @OA\Property( property="fname",type="string",description="First Name",example="First"),
 *     @OA\Property( property="lname", type="string", description="Last Name", example="Last" ),
 *     @OA\Property( property="email", type="string", description="Email of the user", example="abc@mailinator.com" ),
 *     @OA\Property( property="phone_code", type="string", description="Phone Code For User", example="+91"),
 *     @OA\Property( property="phone_number", type="string", description="Phone Number of User", example="9876543210"),
 *     @OA\Property( property="mobile_code", type="string", description="Mobile Code For User", example="+91"),
 *     @OA\Property( property="mobile_number", type="string",
 *          description="Mobile q Number of User", example="9876543210"),
 *     @OA\Property( property="company_id", type="string",
 *          description="Id of company if adding in existsting", example="1"),
 *     @OA\Property( property="company_name", type="string",
 *          description="Name of Company if creating new", example="Company"),
 *     @OA\Property( property="c_position", type="string", description="Postition in company", example="Position"),
 *     @OA\Property( property="unions", type="array", description="Unions",
 *          @OA\Items(
 *               @OA\Property( property="union_id", type="integer",
 *                  description="Id of union if adding in existsting", example="1"),
 *               @OA\Property( property="union_old_id", type="integer",
 *                  description="Id of union if replacing", example="1"),
 *               @OA\Property( property="union_name", type="string",
 *                  description="Name of Union if creating new", example="Company"),
 *               @OA\Property( property="position", type="string",
 *                  description="Postition in Union", example="Position"),
 *          ),
 *     ),
 *     @OA\Property( property="internal_id",type="string",description="User Internal Id",example="1234ABCD"),
 * )
 *
 * -----------------------------------------------------------------------------------------------------------------
 * @descripiton This class will validate request for user profile update request fields
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class UpdateUserRequest
 *
 * @package Modules\KctAdmin\Http\Requests\V1
 */
class UpdateUserRequest extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules(): array {
        return [
            'id'                    => "nullable|exists:tenant.users,id",
            'fname'                 => ["required", new UserRule],
            'lname'                 => ["required", new UserRule],
            'email'                 => [new UserRule, new IsEmailEditableRule($this->id), Rule::unique('tenant.users', 'email')->whereNot('id', $this->input('id'))],
            // contact data
            'phone_code'            => 'nullable|string',
            'phone_number'          => 'nullable|string',
            'mobile_code'           => 'nullable|string',
            'mobile_number'         => 'nullable|string',
            'company_id'            => ["nullable", new EntityRule],
            'company_name'          => ["nullable", new EntityRule],
            'c_position'            => ["nullable", new EntityRule],
            'unions'                => 'nullable|array',
            'unions.*.union_id'     => ["nullable", new EntityRule],
            'unions.*.union_name'   => ["nullable", new EntityRule],
            'unions.*.position'     => ["nullable", new EntityRule],
            'unions.*.union_old_id' => 'nullable|exists:tenant.entities,id',
            'internal_id'           => 'nullable|string',
            'gender'                => ['nullable', new GenderRule],
            'grade'                => ['nullable', new GradeRule],
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
