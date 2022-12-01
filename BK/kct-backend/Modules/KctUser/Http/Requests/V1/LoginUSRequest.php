<?php

namespace Modules\KctUser\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\KctAdmin\Rules\EventRule;
use Modules\KctUser\Rules\EventRegisterRule;
use Modules\KctUser\Rules\V1\EventExists;

/**
 * @OA\Schema(
 *  title="RequestValidation: LoginUSRequest",
 *  description="Validates the request body for adding a user in space ",
 *  type="object",
 *  required={
 *     "email",
 *     "password"
 * },
 *  @OA\Property(property="email",type="string",description="ID of user",example="xyz@mailinator.com"),
 *  @OA\Property(property="password",type="string",description="Password",example="*********"),
 *  @OA\Property(property="event_uuid",type="string",
 *     description="UUID of Event",example="123e4567-e89b-12d3-a456-426614174000"),
 *  @OA\Property(property="lang",type="string",description="Language to set",example="en")
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will validate request data for user login in HE(attendee) side.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class LoginUSRequest
 *
 * @package Modules\KctUser\Http\Requests\V1
 */
class LoginUSRequest extends FormRequest {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is responsible for preparing data for validation before execution reach to rule method.
     * -----------------------------------------------------------------------------------------------------------------
     *
     */
    protected function prepareForValidation() {
        if (is_string($this->input('lang'))) {
            $this->merge([
                'lang' => strtolower($this->input('lang')),
            ]);
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules() {
        return [
            'email'      => 'required|email',
            'password'   => 'required|string',
            'event_uuid' => ['nullable', new EventRule,new EventRegisterRule],
            'lang'       => 'nullable|in:' . implode(',', config('kctuser.moduleLanguages')),
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Determine if the user is authorized to make this request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method handles the failed validation and returns the error.
     * -----------------------------------------------------------------------------------------------------------------
     *
     */
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
    }
}
