<?php

namespace Modules\Cocktail\Http\Requests\V2;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Cocktail\Entities\EventSpace;
use Modules\Cocktail\Rules\SpaceExists;
use Modules\Cocktail\Rules\SpaceTypeRule;
use Modules\Cocktail\Rules\V2\SpaceFutureV2Rule;
use Modules\Cocktail\Rules\V2\SpaceTypeUpdateRule;
use Modules\Cocktail\Services\AuthorizationService;

/**
 * @OA\Schema(
 *  title="RequestValidation: UpdateSpaceRequestV2",
 *  description="Validates the request body for updating a space ",
 *  type="object",
 *  required={
 *     "space_uuid",
 *     "space_name",
 *     "space_mood",
 *     "is_vip_space",
 *     "is_duo_space",
 * },
 *  @OA\Property(property="space_uuid",type="uuid",description="UUID of Space",example="123e4567-e89b-12d3-a456-426614174000"),
 *  @OA\Property(property="space_name",type="string",description="Space Name",example="Space Name"),
 *  @OA\Property(property="space_short_name",type="string",description="Space Short Name",example="Space Short Name"),
 *  @OA\Property(property="space_mood",type="string",description="Space Mood",example="Space Mood"),
 *  @OA\Property(property="max_capacity",type="integer",description="Maximum Capacity for space",example="1"),
 *  @OA\Property(property="is_vip_space",type="integer",description="To indicate if space is vip",example="1", enum={"0", "1"}),
 *  @OA\Property(property="is_duo_space",type="integer",description="To indicate if space is duo",example="1", enum={"0", "1"}),
 * )
 *
 * Class CreateSpaceRequestV2
 * @package Modules\Cocktail\Http\Requests\V2
 */
class UpdateSpaceRequestV2 extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {

        $validation = config('cocktail.validations.space');
        $min = config('cocktail.validations.default_min');
        
        return [
            'space_uuid'       => ['required', new SpaceExists, new SpaceFutureV2Rule],
            // treating it as space line 1
            'space_name'       => "required|min:$min|max:{$validation['space_line_1']}",
            // treating as space line 2
            'space_short_name' => "nullable|max:{$validation['space_line_2']}",
            'space_mood'       => "required|min:$min|max:{$validation['mood_max']}",
            'max_capacity'     => "nullable|numeric|max:{$validation['max_capacity_max_v2']}",
            'space_type'       => ['nullable', 'in:0,1,2',new SpaceTypeUpdateRule($this->space_uuid), new SpaceTypeRule($this->space_uuid, 'space_uuid')],
        ];
    }
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return AuthorizationService::getInstance()->isUserEventAdminBySpace($this->space_uuid);
    }
    
    /**
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
    }
    
}
