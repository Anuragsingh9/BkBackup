<?php

namespace Modules\KctAdmin\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\KctAdmin\Services\BusinessServices\IValidationService;

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
     * @var validationService
     */
    private $validationService;

    public function __construct(IValidationService $validationService) {
        $this->validationService = $validationService;
    }

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
//        return $this->validationService->isOrgOrHigh();
        return true;
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
    }

}
