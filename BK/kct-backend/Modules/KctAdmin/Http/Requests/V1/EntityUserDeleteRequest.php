<?php

namespace Modules\KctAdmin\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will validate request data for deleting entity from user's profile.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class EntityUserDeleteRequest
 *
 * @package Modules\KctAdmin\Http\Requests\V1
 */
class EntityUserDeleteRequest extends FormRequest {
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules(): array {
        return [
            'user_id'   => 'required|exists:tenant.users,id',
            'entity_id' => 'required|exists:tenant.entities,id',
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
