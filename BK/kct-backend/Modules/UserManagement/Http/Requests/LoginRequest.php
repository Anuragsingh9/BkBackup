<?php

namespace Modules\UserManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *  title="RequestValidation: Virtual Event Request Validation",
 *  description="Validates the request body for creating a virtual event ",
 *  type="object",
 *  required={"title", "date", "start_time", "end_time"},
 *  @OA\Property(property="email",type="string",description="email of user",example="example@example.com"),
 *  @OA\Property(property="password",type="password",description="Password for the user account",example="********"),
 *  @OA\Property(property="lang",type="string",description="Language to keep after login",example="en"),
 * )
 *
 * Class LoginRequest
 * @package Modules\UserManagement\Http\Requests
 */
class LoginRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array {
        return [
            'email'    => 'required|email',
            'password' => 'required|string',
            'lang'     => 'nullable|in:' . implode(',', array_keys(config('usermanagement.moduleLanguages'))),
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool {
        return true;
    }
}
