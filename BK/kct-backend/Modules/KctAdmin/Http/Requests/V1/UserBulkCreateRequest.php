<?php

namespace Modules\KctAdmin\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\KctAdmin\Rules\EventRule;
use Modules\KctAdmin\Rules\GenderRule;
use Modules\KctAdmin\Rules\GradeRule;
use Modules\KctAdmin\Rules\GroupRule;
use Modules\UserManagement\Rules\EntityRule;
use Modules\UserManagement\Rules\UserRule;

/**
 * @OA\Schema(
 *     title= "RequestValidation:Create multiple user type validation",
 *     description= "Validates the request for adding multiple users",
 *     type= "object",
 *     required= {"user", "user.fname"},
 *     @OA\Property(property="user",
 *          type="array",
 *          description="User",
 *          @OA\Items(
 *             @OA\Property( property="fname",type="string",description="User first name",example="First"),
 *             @OA\Property( property="lname",type="string",description="User last name",example="Name"),
 *             @OA\Property( property="email",type="email",description="User email",example="example@example.com"),
 *             @OA\Property( property="city",type="string",description="city of user",example="City Name"),
 *             @OA\Property( property="country",type="string",description="country of user",example="Country Name"),
 *             @OA\Property( property="address",type="string",description="address of user",example="Full Address"),
 *             @OA\Property( property="postal",type="string",description="postal code of user",example="305060"),
 *             @OA\Property( property="phones",type="array",description="User all landline numbers",
 *                  @OA\Items(
 *                      @OA\Property( property="country_code", type="string",
 *                           description="Country Code For User", example="+91"),
 *                      @OA\Property( property="number", type="string",
 *                           description="Phone Number of User", example="9876543210"),
 *                      @OA\Property( property="is_primary",type="integer",
 *                          description="To indicate the current number is primary",example="1", enum={"0", "1"}),
 *                  ),
 *             ),
 *             @OA\Property( property="mobiles",type="array",description="User all landline numbers",
 *                  @OA\Items(
 *                      @OA\Property( property="country_code", type="string",
 *                           description="Phone Code For User", example="+91"),
 *                      @OA\Property( property="number", type="string",
 *                           description="Phone Number of User", example="9876543210"),
 *                      @OA\Property( property="is_primary",type="integer",
 *                          description="To indicate the current number is primary",example="1", enum={"0", "1"}),
 *                  ),
 *             ),
 *          )
 *     ),
 *     @OA\Property(property="group_key",type="string",
 *          description="Group id in which user need to be added",example="default"),
 *     @OA\Property(property="group_role",type="integer",
 *          description="Group Role, 1 regular, 2 Organiser",example="1", enum={"1", "2"}),
 *     @OA\Property(property="event_uuid",type="UUID",
 *          description="Event Uuid",example="01493146-d018-11ea-9d2a-b82a72a009b4"),
 *     @OA\Property(property="allow_update",type="integer",
 *     description="To allow update if user already exist",example="1"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will validate request data for validating user bulk delete.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class UserBulkCreateRequest
 *
 * @package Modules\KctAdmin\Http\Requests\V1
 */
class UserBulkCreateRequest extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules(): array {
        $validation = config('kctadmin.modelConstants.users.validations');
        if (isset($this->allow_update) && $this->input('allow_update') == 0) {
            $email = ["required", new  UserRule, Rule::unique('tenant.users', 'email')->whereNull('deleted_at')];
        } else {
            $email = ["required", new  UserRule];
        }
        return [
            'user'                          => "required|array|max:{$validation['bulk_insert_max']}",
            // user array validations
            'user.*.fname'                  => ["required", new UserRule],
            'user.*.lname'                  => ["required", new  UserRule],
            'user.*.email'                  => $email,
            "user.*.city"                   => "nullable|string",
            "user.*.country"                => "nullable|string",
            "user.*.address"                => "nullable|string",
            "user.*.postal"                 => "nullable",
            "user.*.phones"                 => "nullable|array",
            "user.*.phones.*.country_code"  => "nullable",
            "user.*.phones.*.number"        => "nullable",
            "user.*.phones.*.is_primary"    => "nullable|in:0,1",
            "user.*.mobiles.*.country_code" => "nullable",
            "user.*.mobiles.*.number"       => "nullable",
            "user.*.company_id"             => ["nullable", new EntityRule("company_id")],
            "user.*.company"                => ["nullable", new EntityRule("company_name")],
            "user.*.company_position"       => ["nullable", new EntityRule("c_position")],
            "user.*.union_id"               => ["nullable", new EntityRule("union_id")],
            "user.*.union"                  => ["nullable", new EntityRule("union_name")],
            "user.*.union_position"         => ["nullable", new EntityRule("u_position")],
            "user.*.gender"                 => ["nullable", new GenderRule],
            "user.*.grade"                  => ["nullable", new GradeRule],

            'group_key'  => ["required", new GroupRule],
            'group_role' => 'nullable|in:1,2,3,4', // 1. Group user 2. Group pilot 3. Group owner 4.Group co-pilot
            'event_uuid' => ['nullable', new EventRule],

            'allow_update' => 'nullable|in:0,1',
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

    public function messages(): array {
        return [
            "user.*.fname.required" => __("validation.required", ['attribute' => 'fname']),

            "user.*.lname.required" => __("validation.required", ['attribute' => 'lname']),

            "user.*.email.unique"   => __("validation.unique", ['attribute' => 'email']),
            "user.*.email.required" => __("validation.required", ['attribute' => 'email']),
        ];
    }
}
