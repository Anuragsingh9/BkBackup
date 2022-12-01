<?php

namespace Modules\KctAdmin\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\KctAdmin\Rules\JsonRGBARule;
use Modules\KctAdmin\Rules\LabelCustomizationRule;
use Modules\KctAdmin\Services\BusinessServices\ICoreService;

/**
 * @OA\Schema(
 *  title="RequestValidation: Event Graphics Customisation Request Validation",
 *  description="Validates the request body for updating event graphics customisation ",
 *  type="object",
 *  required={"field"},
 *  @OA\Property(property="field",type="string",description="Field of graphics customisation",example="tag_color"),
 * )
 *
 * Class GraphicsCustomizationRequest
 *
 * @package Modules\KctAdmin\Http\Requests
 */
class GraphicsCustomizationRequest extends FormRequest {
    /**
     * @var kctService
     */
    private ICoreService $kctService;

    public function __construct(ICoreService $kctService) {
        $this->kctService = $kctService;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $fields = $this->kctService->getAllCustomizationKeys();
        $rules = [
            'field' => 'required|in:' . implode(',', $fields),
        ];
        return $rules;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    protected function getValidatorInstance() {
        $validator = parent::getValidatorInstance();
        $fieldType = $this->kctService->findFieldType($this->input('field'));
        $validator->sometimes('value', ['required', 'in:0,1'], function ($q) use ($fieldType) {
            return $fieldType == 'checkbox';
        });

        $validator->sometimes('value', [new JsonRGBARule], function () use ($fieldType) {
            return $fieldType == 'color';
        });

        $validator->sometimes('value', 'required|url', function () use ($fieldType) {
            return $fieldType == 'urls';
        });


        $validator->sometimes('value', ['required', new LabelCustomizationRule], function () use ($fieldType) {
            return $fieldType == 'label';
        });

        $validator->sometimes('value', 'required|int', function () use ($fieldType) {
            return $fieldType == 'number';
        });

        return $validator;
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
    }

}
