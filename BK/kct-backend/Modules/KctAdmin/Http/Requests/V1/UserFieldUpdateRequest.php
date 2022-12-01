<?php

namespace Modules\KctAdmin\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *  title="RequestValidation: User profile field update request",
 *  description="Validates the request body for updating user profile",
 *  type="object",
 *  required={"field", "user_id"},
 *  @OA\Property(property="user_id",type="integer",description="ID of User to Update",example="1"),
 *  @OA\Property(property="field",type="string",
 *     description="Field of user profile",example="lang",enum={"lang","password", "avatar"}),
 *  @OA\Property(property="value",type="string",description="Value of user profile field",example="en"),
 *  @OA\Property(property="current_password",type="string",
 *     description="Current password if field is password",example="••••••••"),
 *  @OA\Property(property="password_confirmation",type="string",description="Password confirmation",example="••••••••"),
 *  @OA\Property(property="avatar",type="file",
 *     description="Image to set as avatar, to remove the profile picture send null here or don't send this key"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will validate request data for updating user's profile fields.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 */
class UserFieldUpdateRequest extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules(): array {
        return [
            'user_id' => 'required|numeric|exists:tenant.users,id',
            'field'   => 'required|in:lang,password,avatar',
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method provides validator instance of Validator class.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return Validator
     */
    public function getValidatorInstance(): \Illuminate\Contracts\Validation\Validator {
        $validator = parent::getValidatorInstance();

        $validator = $this->prepareValidatorForLang($validator);
        $validator = $this->prepareValidatorForPassword($validator);
        return $this->prepareValidatorForAvatar($validator);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the validator after putting validations for the lang update
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Validator $validator
     * @return Validator
     */
    private function prepareValidatorForLang(Validator $validator): Validator {
        // adding validation for the language
        $validator->sometimes(
            'value',
            'required|in:' . implode(',', array_keys(config("kctadmin.moduleLanguages"))),
            function () {
                return $this->input('field') == 'lang';
            });
        return $validator;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the validator after putting validations for the password update
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Validator $validator
     * @return Validator
     */
    private function prepareValidatorForPassword(Validator $validator): Validator {
        $validation = config('kctadmin.modelConstants.users.validations');
        // adding validations for the password
        $validator->sometimes(
            'value',
            "required|string|min:{$validation['password_min']}|max:{$validation['password_max']}",
            function () {
                return $this->input('field') == 'password';
            }
        );
        $validator->sometimes('current_password', 'required|string|current_password:api', function () {
            return $this->input('field') == 'password';
        });
        $validator->sometimes('password_confirmation', "required|same:value|min:{$validation['password_min']}|max:{$validation['password_max']}", function () {
            return $this->input('field') == 'password';
        });
        return $validator;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the validator after putting validations for the avatar update
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Validator $validator
     * @return Validator
     */
    private function prepareValidatorForAvatar(Validator $validator): Validator {
        $validator->sometimes('avatar', 'nullable|image', function () {
            return $this->input('field') == 'avatar';
        });
        return $validator;
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
