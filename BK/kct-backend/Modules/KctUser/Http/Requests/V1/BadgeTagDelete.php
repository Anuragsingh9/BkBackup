<?php

namespace Modules\KctUser\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @OA\Schema(
 *  title="RequestValidation: BadgeTagDelete",
 *  description="Validates the request body for adding a user in space ",
 *  type="object",
 *  required={
 *     "tag_id"
 * },
 *  @OA\Property(property="tag_id",type="integer",description="ID of Tag",example="1"),
 *  @OA\Property(property="event_uuid",type="string",description="event uuid",example="f6712736-0c17-11ed-af59-b82a72a009b4")
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will be used for validating  delete badge request.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class BadgeTagDelete
 *
 * @package Modules\KctUser\Http\Requests\V1
 */
class BadgeTagDelete extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules() {
        return [
            'tag_id'     => 'required',
            'event_uuid' => 'required|exists:tenant.events,event_uuid',
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Determine if the user is authorized to make this request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will be used for validate the instance
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return Validator
     */
    protected function getValidatorInstance() {
        $validator = parent::getValidatorInstance();
        $validator->sometimes('value', "required|string|max:100|regex:/^[a-zàâçéèêëîïôûùüÿñæœ' .-]*$/i", function () {
            return in_array($this->field, ['fname', 'lname']);
        });
        $validator->sometimes('value', 'required|image', function () {
            return $this->field == 'avatar';
        });
        return $validator;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will be use for custom error message for authorization failed
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
    }
}
