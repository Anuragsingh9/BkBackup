<?php

namespace Modules\KctAdmin\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *  title="RequestValidation: Import Users Step 2",
 *  description="Validates the request body for actual importing users ",
 *  type="object",
 *  required={"tag_name"},
 *  @OA\Property(property="file_name",type="string",description="Name of  File",example="Name of File"),
 *  @OA\Property(property="group_key",type="string",description="Group key",example="default"),
 *  @OA\Property(property="aliases",type="object",
 *     description="Aliases of excel headings and system headings object keys are system heading and there value is
 *     heading name in excel sheet",
 *               @OA\Property(property="fname",type="string",
 *     description="REQUIRED: Column name in sheet which belongs to first name",example="first_name"),
 *               @OA\Property(property="lname", type="string",
 *     description="REQUIRED: Column name in sheet which belongs to last name", example="last_name"),
 *               @OA\Property(property="email", type="string",
 *     description="REQUIRED: Column name in sheet which belongs to email", example="email"),
 *               @OA\Property(property="city",type="string",
 *     description="Column name in sheet which belongs to city",example="city"),
 *               @OA\Property(property="country",type="string",
 *     description="Column name in sheet which belongs to country",example="country"),
 *               @OA\Property(property="company", type="string",
 *     description="Column name in sheet which belongs companyr", example="company"),
 *               @OA\Property(property="company_position", type="string",
 *     description="Column name in sheet which belongs company position", example="company_position"),
 *              @OA\Property(property="union", type="string",
 *     description="Column name in sheet which belongs to union", example="union"),
 *               @OA\Property(property="union_position", type="string",
 *     description="Column name in sheet which belongs to union position", example="union_position"),
 *
 *  ),
 *
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will validate request for import step 2.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class EventTagRequest
 *
 * @package Modules\KctAdmin\Http\Requests
 */
class ImportUserStep2Request extends FormRequest {
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules(): array {
        return [
            'file_name' => 'required|string',

            'aliases'       => 'required|array',
            'aliases.fname' => 'required|string',
            'aliases.lname' => 'required|string',
            'aliases.email' => 'required|string',

            'aliases.city'             => 'nullable|string',
            'aliases.country'          => 'nullable|string',
            'aliases.company'          => 'nullable|string',
            'aliases.company_position' => 'nullable|string',
            'aliases.union'            => 'nullable|string',
            'aliases.union_position'   => 'nullable|string',
            'aliases.address'          => 'nullable|string',
            'aliases.postal'           => 'nullable|string',
            'aliases.phone_number'     => 'nullable|string',
            'aliases.mobile_number'    => 'nullable|string',
            'aliases.internal_id'      => 'nullable|string',

            'group_key' => 'required|exists:tenant.groups,group_key',
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
