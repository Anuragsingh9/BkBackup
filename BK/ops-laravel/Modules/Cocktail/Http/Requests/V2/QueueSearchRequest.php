<?php

namespace Modules\Cocktail\Http\Requests\V2;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Cocktail\Rules\EventExists;

class QueueSearchRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
            'event_id' => ['required', new EventExists],
            'type'     => 'required|in:1,2,3,4,5',        // 1.Name, 2.Company, 3.union, 4.pro tag, 5.perso tag
            'key'      => 'required',
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
