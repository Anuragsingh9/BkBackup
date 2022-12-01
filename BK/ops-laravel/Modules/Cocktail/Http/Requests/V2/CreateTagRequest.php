<?php

namespace Modules\Cocktail\Http\Requests\V2;

use Illuminate\Foundation\Http\FormRequest;

class CreateTagRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'tag_name' => 'required|string',
            'tag_type' => 'required|in:1,2',
        ];
    }
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }
}
