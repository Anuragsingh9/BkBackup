<?php

namespace Modules\Cocktail\Http\Requests\V2;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Modules\Cocktail\Rules\EventExists;
use Modules\Cocktail\Rules\SpaceTypeRule;
use Modules\Cocktail\Rules\V2\VirtualEventV2Rule;
use Modules\Cocktail\Services\AuthorizationService;

/**
 * @OA\Schema(
 *  title="RequestValidation: CreateSpaceRequestV2",
 *  description="Validates the request body for creating a space ",
 *  type="object",
 *  required={
 *     "space_name",
 *     "space_mood",
 *     "is_vip_space",
 *     "is_duo_space",
 *     "event_uuid",
 * },
 *  @OA\Property(property="space_name",type="string",description="Space Name",example="Space Name"),
 *  @OA\Property(property="space_short_name",type="string",description="Space Short Name",example="Space Short Name"),
 *  @OA\Property(property="space_mood",type="string",description="Space Mood",example="Space Mood"),
 *  @OA\Property(property="max_capacity",type="integer",description="Maximum Capacity for space",example="1"),
 *  @OA\Property(property="is_vip_space",type="integer",description="To indicate if space is vip",example="1", enum={"0", "1"}),
 *  @OA\Property(property="is_duo_space",type="integer",description="To indicate if space is duo",example="1", enum={"0", "1"}),
 *  @OA\Property(property="event_uuid",type="uuid",description="UUID of Event",example="123e4567-e89b-12d3-a456-426614174000"),
 * )
 *
 * Class CreateSpaceRequestV2
 * @package Modules\Cocktail\Http\Requests\V2
 */
class CreateSpaceRequestV2 extends FormRequest {
    /**
     * @var array|string|null
     */
    private $authorizationMessage;
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $validation = config('cocktail.validations.space');
        $min = config('cocktail.validations.default_min');
        
        return [
            // treating it as space line 1
            'space_name'       => "required|min:$min|max:{$validation['space_line_1']}",
            // treating as space line 2
            'space_short_name' => "nullable|max:{$validation['space_line_2']}",
            'space_mood'       => "required|min:$min|max:{$validation['mood_max']}",
            'max_capacity'     => "nullable|numeric|max:{$validation['max_capacity_max_v2']}",
            'event_uuid'       => ['required', new EventExists, new VirtualEventV2Rule],
            'space_type'       => ['nullable', 'in:0,1,2', new SpaceTypeRule($this->event_uuid, 'event')],
        ];
    }
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        $isEventAdmin = AuthorizationService::getInstance()->isUserEventAdmin($this->event_uuid);
        if (!$isEventAdmin) {
            $this->authorizationMessage = __('cocktail::message.you_are_not_admin');
            return false;
        }
        
        return true;
    }
    
    /**
     * @return JsonResponse|void
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
            'msg'    => implode(',', $validator->errors()->all()),
        ], 422));
    }
}
