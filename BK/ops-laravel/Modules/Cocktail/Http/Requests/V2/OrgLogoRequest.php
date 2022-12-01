<?php

namespace Modules\Cocktail\Http\Requests\V2;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Modules\Cocktail\Services\AuthorizationService;

/**
 * @OA\Schema(
 *      title="KCT-V2-RequestValidation: OrgLogoRequest",
 *      description="To validate data before adding default kct logo",
 *      type="object",
 *      required={"logo"},
 *      @OA\Property(
 *          property="logo",
 *          type="file",
 *          description="Default logo image file",
 *      ),
 * ),
 *
 */
class OrgLogoRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'logo' => 'required|image',
        ];
    }
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return AuthorizationService::getInstance()->isOrgOrHigh();
    }
    
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
    }
}
