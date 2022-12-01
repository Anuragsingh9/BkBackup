<?php

namespace Modules\KctAdmin\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Modules\KctAdmin\Rules\GroupRule;

/**
 * @OA\Schema(
 *  title="RequestValidation: Virtual Event Request Validation",
 *  description="Validates the request body for creating a virtual event ",
 *  type="object",
 *  required={"title", "date", "start_time", "end_time"},
 *  @OA\Property(property="group_key",type="string",description="Group key",example="1"),
 *  @OA\Property(property="labels",type="array",description="array of labels",@OA\Items(
 *      @OA\Property(property="name",type="string",
 *     description="Name of label, currently availble names are {space_host,business_team,expert,vip,moderator,
speaker,participants,}",example="space_host"
 *     ),
 *      @OA\Property(property="locales",type="array",description="array of labels",@OA\Items(
 *          @OA\Property(property="value",type="string",
 *           description="Value of label for current locale",example="Space Host"
 *      ),
 *          @OA\Property(property="locale",type="string",
 *     description="Locale name in small letters { en, fr }",example="en"),
 *      )),
 *  )),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will validate request data for updating labels value and their locale
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class UpdateLabelRequest
 *
 * @package Modules\KctAdmin\Http\Requests\V1
 */
class UpdateLabelRequest extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules(): array {
        return [
            'group_key'                  => ['required', new GroupRule],
            'labels'                    => 'required|array',
            'labels.*.name'             => 'required|exists:tenant.labels,name',
            'labels.*.locales'          => 'required|array',
            'labels.*.locales.*.value'  => 'required|string',
            'labels.*.locales.*.locale' => 'required|in:' . implode(',', array_keys(config("kctadmin.moduleLanguages"))),
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
