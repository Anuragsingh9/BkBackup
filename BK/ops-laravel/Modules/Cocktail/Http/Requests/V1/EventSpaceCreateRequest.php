<?php


namespace Modules\Cocktail\Http\Requests\V1;

use App\Services\StockService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Modules\Cocktail\Rules\EventAndSpaceNotStarted;
use Modules\Cocktail\Services\AuthorizationService;
use Modules\Events\Rules\ImageAspectRatioCheck;

class EventSpaceCreateRequest extends FormRequest {
    /**
     * @var array|string|null
     */
    private $authorizationMessage;
    /**
     * @var string
     */
    private $dimension;
    
    
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
            'space_name'                => "required|min:$min|max:{$validation['name_max']}",
            'space_short_name'          => "required|min:$min|max:{$validation['short_name_max']}",
            'space_mood'                => "required|min:$min|max:{$validation['mood_max']}",
            'max_capacity'              => "nullable|numeric|max:{$validation['max_capacity_max']}",
            'space_image'               => 'required',
            'space_image_from'          => 'required|in:1,2', // 1 = System, 2 = Stock
            'space_icon'                => "nullable|image|$this->dimension",/*dimensions:min_width=100,min_height=70*/
            'is_vip_space'              => 'required|in:0,1',
            'opening_hours_before'      => "required|integer|min:0|max:{$validation['opening_hour_before_max']}",
            'opening_hours_during'      => 'required|in:1,0',
            'opening_hours_after'       => "required|integer|min:0|max:{$validation['opening_hour_after_max']}",
            'does_not_follow_main_hour' => 'required|in:0,1',
            'event_uuid'                => ['required', new EventAndSpaceNotStarted],
            'hosts'                     => 'nullable|array|max:' . config('cocktail.validations.hosts_max'),
            'hosts.*'                   => [
                'required',
                Rule::exists('tenant.event_user_data', 'user_id')->where(function ($q) {
                    $q->where('event_uuid', $this->event_uuid);
                }),
            ],
        ];
    }
    
    public function authorize() {
        $isEventAdmin = AuthorizationService::getInstance()->isUserEventAdmin($this->event_uuid);
        if (!$isEventAdmin) {
            $this->authorizationMessage = __('cocktail::message.you_are_not_admin');
            return false;
        }
        if ($this->input('space_image_from', 1) == 2 && !StockService::getInstance()->isStockAvailable()) {
            $this->authorizationMessage = __('cocktail::message.newsletter_not_enable');
            return false;
        }
        return true;
    }
    
    /**
     * @return \Illuminate\Http\JsonResponse|void
     */
    protected function failedAuthorization() {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => $this->authorizationMessage ? $this->authorizationMessage : "Unauthorised",
        ], 403));
    }
    
    
    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'msg'    => implode($validator->errors()->all(), ','),
        ], 422));
    }
    
    protected function getValidatorInstance() {
        $validator = parent::getValidatorInstance();
        $validator->sometimes('space_image', 'required|string', function () {
            return $this->space_image_from == 2; // stock image
        });
    
        $validation = config('cocktail.validations.space');
        $imageRule = [
            "required",
            "image",
            $this->dimension,
            new ImageAspectRatioCheck($validation['image_width_height_ratio'], $validation['image_height_width_ratio'])
        ];
        
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

