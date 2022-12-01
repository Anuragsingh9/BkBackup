<?php

namespace Modules\KctAdmin\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *  title="RequestValidation: Import Users Step 2",
 *  description="Validates the request body for actual importing users ",
 *  type="object",
 *  required={"file"},
 *  @OA\Property(property="file",type="file",description="Import File"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class is used to validate request for import step1 functionality.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class EventTagRequest
 *
 * @package Modules\KctAdmin\Http\Requests
 */
class ImportUserStep1Request extends FormRequest {
    /**
     * ---------------------------------------------------------------------------------------------------------------------
     * @descripiton Get the validation rules that apply to the request.
     * ---------------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules(): array {
        return [
            'file' => 'required|file|max:1024|mimes:xlsx,xls,csv',
        ];

    }

    /**
     * ---------------------------------------------------------------------------------------------------------------------
     * @descripiton Determine if the user is authorized to make this request.
     * ---------------------------------------------------------------------------------------------------------------------
     *
     * @return bool
     */
    public function authorize(): bool {
        return true;
    }
}
