<?php

namespace Modules\KctAdmin\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddMultipleOrganiserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'user.*'    => "required|max:1",
            'user.*.id' => ["required", "exists:tenant.users,id,deleted_at,NULL"],
            'group_id'  => ["required", "exists:tenant.groups,id,deleted_at,NULL"],
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
