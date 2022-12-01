<?php

namespace Modules\KctUser\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\KctAdmin\Rules\EventRule;
use Modules\KctUser\Rules\V1\EventAndSpaceOpenOrNotStarted;
use Modules\KctUser\Rules\V1\EventExists;
use Modules\UserManagement\Rules\UserRule;

/**
 * @OA\Schema(
 *  title="RequestValidation: InviteUserRequest",
 *  description="Validates the request body for adding a user in space ",
 *  type="object",
 *  required={ "fname","lname","email","event_uuid" },
 *  @OA\Property(
 *      property="user",type="array",description="UUID of Space",
 *      @OA\Items(
 *          @OA\Property(property="fname",type="string",
 *     description="User fname",example="First"),
 *     @OA\Property(property="lname",type="Last",
 *     description="User lname",example="123e4567-e89b-12d3-a456-426614174000"),
 *     @OA\Property(property="email",type="string",
 *     description="User email",example="xyz@mailinator.com"),
 *      )
 * ),
 *  @OA\Property(property="event_uuid",type="uuid",
 *     description="UUID of Event",example="123e4567-e89b-12d3-a456-426614174000"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will validate request data for inviting users to an event.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class InviteUserRequest
 *
 * @package Modules\KctUser\Http\Requests\V1
 */
class InviteUserRequest extends FormRequest {
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules(): array {
        return [
            'user'         => 'required|array|max:10',
            'user.*'       => 'required|array|max:3',
            'user.*.email' => 'required|email',
            'user.*.fname' => ["required", new UserRule],
            'user.*.lname' => ["required", new UserRule],
            'event_uuid'   => ["required", new EventRule, new EventAndSpaceOpenOrNotStarted],
        ];
    }

    /**
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Determine if the user is authorized to make this request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return bool
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is used for displaying custom message for errors.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function messages(): array {
        $messages = parent::messages();
        $messages['user.0.required'] = ''; // as user field is an array so 0 index always required,so  hide validation message by entering empty string..
        $messages['user.*.fname.required'] = __('validation.required', ['attribute' => 'fname']);
        $messages['user.*.fname.string'] = __('validation.string', ['attribute' => 'fname']);
        $messages['user.*.fname.min'] = __('validation.min.numeric', ['attribute' => 'fname']);
        $messages['user.*.fname.max'] = __('validation.max.numeric', ['attribute' => 'fname']);

        $messages['user.*.lname.required'] = __('validation.required', ['attribute' => 'lname']);
        $messages['user.*.lname.string'] = __('validation.string', ['attribute' => 'lname']);
        $messages['user.*.lname.min'] = __('validation.min.numeric', ['attribute' => 'lname']);
        $messages['user.*.lname.max'] = __('validation.max.numeric', ['attribute' => 'lname']);

        $messages['user.*.email.required'] = __('validation.required', ['attribute' => 'email']);
        $messages['user.*.email.email'] = __('validation.email', ['attribute' => 'email']);
        return $messages;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will be use for custom error message for authorization failed
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator) {
        // removing all error messages which is empty string.
        $errors = $validator->errors()->toArray();
        foreach ($errors as $error => $msg) {
            foreach ($msg as $value) {
                if ($value === '') {
                    continue;
                } else {
                    $messages[] = $value;
                }
            }
        }
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode(',', $messages)
        ], 422));
    }
}
