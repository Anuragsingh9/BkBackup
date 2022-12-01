<?php

namespace Modules\Cocktail\Http\Requests\V1;

use App\Services\StockService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Cocktail\Rules\EventAndSpaceNotStarted;
use Modules\Cocktail\Services\AuthorizationService;

class StockImageUploadRequest extends FormRequest {
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
        $requiredInt = 'required_if:crop,1|integer';
        return [
            'imageId'    => 'required|string',
            'width'      => 'required|integer',
            'search'     => 'required|string',
            'event_uuid' => ['nullable', new EventAndSpaceNotStarted],
            'crop'       => 'required|in:0,1',
            'w'          => $requiredInt,
            'h'          => $requiredInt,
            'x'          => $requiredInt,
            'y'          => $requiredInt,
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
        if (!StockService::getInstance()->isStockAvailable()) {
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
            'msg'    => implode(',', $validator->errors()->all())
        ], 422));
    }
}
