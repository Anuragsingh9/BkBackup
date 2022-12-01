<?php

namespace Modules\KctAdmin\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class GroupUserMultiDeleteRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $validation = config('kctadmin.modelConstants.users.validations');
        return [
            'user'      => "required|array|max:{$validation['bulk_delete_max']}",
            'user.*'    => 'required|array|max:1',
            'user.*.id' => 'required|numeric|exists:tenant.users,id',
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
