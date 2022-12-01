<?php


namespace Modules\Cocktail\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Cocktail\Rules\SpaceFuture;
use Modules\Cocktail\Services\AuthorizationService;
use Modules\Events\Rules\ImageAspectRatioCheck;

class EventSpaceUpdateRequest extends FormRequest {
    /**
     * @var string
     */
    private $dimension;
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return AuthorizationService::getInstance()->isUserEventAdminBySpace($this->space_uuid);
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $validation = config('cocktail.validations.space');
        $min = config('cocktail.validations.default_min');
        
        $this->dimension = "dimensions:" .
            "min_width={$validation['image_w']}," .
            "min_height={$validation['image_h']}";
        
        return [
            'space_uuid'                => ['required', new SpaceFuture('end')],
            'space_name'                => "required|min:$min|max:{$validation['name_max']}",
            'space_short_name'          => "required|min:$min|max:{$validation['short_name_max']}",
            'space_mood'                => "required|min:$min|max:{$validation['mood_max']}",
            'max_capacity'              => "nullable|numeric|max:{$validation['max_capacity_max']}",
            'space_image_from'          => 'nullable|in:1,2', // 1 = System, 2 = Stock
            'space_icon'                => "nullable|image|$this->dimension",/*dimensions:min_width=100,min_height=70*/
            'is_vip_space'              => 'required|in:0,1',
            'opening_hours_before'      => "required|integer|min:0|max:{$validation['opening_hour_before_max']}",
            'opening_hours_during'      => 'required|in:1,0',
            'opening_hours_after'       => "required|integer|min:0|max:{$validation['opening_hour_after_max']}",
            'does_not_follow_main_hour' => 'required|in:0,1',
        ];
    }
    
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
    }
    
    protected function getValidatorInstance() {
        $validator = parent::getValidatorInstance();
        
        $validation = config('cocktail.validations.space');
        $imageRule = [
            "required",
            "image",
            $this->dimension,
            new ImageAspectRatioCheck($validation['image_width_height_ratio'], $validation['image_height_width_ratio'])
        ];
        
        $validator->sometimes('space_image', 'required|string', function () {
            return $this->space_image_from == 2; // stock image
        });
        
        $validator->sometimes('space_image', $imageRule, function () {
            return $this->space_image_from == 1; // system image
        });
        return $validator;
    }
    
    public function messages() {
        $validation = config('cocktail.validations.space');
        return [
            'dimensions' => __('events::message.image_validation', [
                'miw' => $validation['image_w'],
                'mih' => $validation['image_h'],
            ]),
        ];
    }
}
