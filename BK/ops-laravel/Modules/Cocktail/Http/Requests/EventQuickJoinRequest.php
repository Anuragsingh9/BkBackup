<?php

namespace Modules\Cocktail\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Cocktail\Rules\EventAndSpaceOpenOrNotStarted;
use Modules\Cocktail\Rules\IsFutureEvent;

class EventQuickJoinRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'event_uuid' => ['required', new EventAndSpaceOpenOrNotStarted],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
