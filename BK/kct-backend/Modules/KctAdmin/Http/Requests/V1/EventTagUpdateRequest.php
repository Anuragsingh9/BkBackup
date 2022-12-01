<?php

namespace Modules\KctAdmin\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *  title="RequestValidation: Event Tag Request Validation",
 *  description="Validates the request body for event tag ",
 *  type="object",
 *  required={"tag_id","tag_name"},
 *  @OA\Property(property="tag_id",type="integer",description="ID Event Tag",example="4"),
 *  @OA\Property(property="tag_name",type="string",description="Name of Event Tag",example="Name of Event Tag"),
 *  @OA\Property(property="is_display",type="int",description="To Display the Event Tag",example="1",enum={"0", "1"}),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class validate the update event tag Fr request
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class EventTagUpdateRequest
 * @package Modules\KctAdmin\Http\Requests\V1
 */
class EventTagUpdateRequest extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules(): array {
        return [
            'tag_id'     => 'required|exists:tenant.organiser_tags,id',
            'tag_name'   => ['required', Rule::unique('tenant.organiser_tags', 'name')->where(function ($q) {
                $q->where('id', '!=', $this->input('tag_id'));
            })],
            'is_display' => 'required|in:0,1'
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
