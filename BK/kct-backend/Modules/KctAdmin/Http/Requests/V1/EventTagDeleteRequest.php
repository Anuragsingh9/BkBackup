<?php

namespace Modules\KctAdmin\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *  title="RequestValidation: Event Tag Delete",
 *  description="Validates the request body for deleting event tag ",
 *  type="object",
 *  required={"tag_id"},
 *
 *  @OA\Property(property="tag_id", type="integer", format="text", example="1"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class validate the delete event tag Fr request
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class EventTagDeleteRequest
 * @package Modules\KctAdmin\Http\Requests\V1
 */
class EventTagDeleteRequest extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules(): array {
        return [
            'tag_id' => 'required|exists:tenant.organiser_tags,id',
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Determine if the user is authorized to make this request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return bool
     */
    public function authorize(): bool {
        return true;
    }
}
