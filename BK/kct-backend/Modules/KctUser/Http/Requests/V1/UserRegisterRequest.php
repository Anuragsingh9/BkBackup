<?php

namespace Modules\KctUser\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\KctAdmin\Rules\DraftEventRule;
use Modules\KctUser\Rules\EventOpenOrFutureRule;
use Modules\KctUser\Rules\EventRegisterRule;
use Modules\KctUser\Rules\FnameRule;
use Modules\KctUser\Rules\LnameRule;

/**
 * @OA\Schema(
 *  title="RequestValidation: User Register Request Validation",
 *  description="Validates the request body for registering a user account ",
 *  type="object",
 *  required={"email", "fname", "lname", "event_uuid", "password"},
 *  @OA\Property(property="email",type="string",description="Unique email of user",example="email@email.com"),
 *  @OA\Property(property="fname",type="string",description="First Name",example="First Name"),
 *  @OA\Property(property="lname",type="string",description="Last Name",example="Last Name"),
 *  @OA\Property(property="event_uuid", type="uuid",
 *     description="Event UUID for which user wants to register after creating an account",
example="123e4567-e89b-12d3-a456-426614174000"),
 *  @OA\Property(property="password", type="string",
 *     description="Password for user account to login into account",example="••••••••"),
 *  @OA\Property(property="password_confirmation", type="string",description="Password Confirmation",example="••••••••"),
 *  @OA\Property(property="lang", type="string",description="Language to set after register",example="en"),
 * )
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @descripiton This class will validate request data for registering an user in HE(attendee) side.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class UserRegisterRequest
 *
 * @package Modules\KctUser\Http\Requests\V1
 */
class UserRegisterRequest extends FormRequest {

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
    public function rules(): array {
        $userVal = config('kctuser.validations.user');
        $langs = implode(',', config('kctuser.moduleLanguages'));
        return [
            'email'      => 'required|email|unique:tenant.users,email',
            'fname'      => ['required', new FnameRule,],
            'lname'      => ['required', new LnameRule,],
            'event_uuid' => ['required', new EventOpenOrFutureRule, new EventRegisterRule],
            'password'   => [
                'required', 'confirmed', 'string', "min:{$userVal['password_min']}", "max:{$userVal['password_max']}",
            ],
            'lang'       => "nullable|in:$langs",
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
