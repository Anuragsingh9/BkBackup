<?php

namespace Modules\Cocktail\Http\Requests\V2;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Cocktail\Rules\ColorRGBA;
use Modules\Cocktail\Services\AuthorizationService;

/**
 * @OA\Schema(
 *      title="KCT-V2-RequestValidation: OrgColorRequest",
 *      description="To validate data before adding default kct logo",
 *      type="object",
 *      required={"field", "value"},
 *      @OA\Property(
 *          property="field",
 *          type="string",
 *          enum={"color1", "color2"},
 *          description="Default Color Value for Event Graphics ",
 *          example="color1",
 *      ),
 *      @OA\Property(
 *          property="value",
 *          type="object",
 *          description="Color RGBA Object",
 *          @OA\Property(
 *              property="transparency",
 *              type="object",
 *              description="Color RGBA Object",
 *              ref="#/components/schemas/DocColorObject",
 *          ),
 *      ),
 * ),
 *
 */
class OrgColorRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $rgbaColorValidation = ['required', 'json', new ColorRGBA];
        return [
            'field' => 'required|in:color1,color2',
            'value' => $rgbaColorValidation,
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
