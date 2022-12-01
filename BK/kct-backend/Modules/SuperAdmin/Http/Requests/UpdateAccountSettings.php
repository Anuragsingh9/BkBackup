<?php

namespace Modules\SuperAdmin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * -----------------------------------------------------------------------------------------------------------------
 * @description This class will manage the validation of update account setting
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class UpdateAccountSettings
 * @package Modules\SuperAdmin\Http\Requests
 */
class UpdateAccountSettings extends FormRequest
{
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the validation rules that apply to the request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Determine if the user is authorized to make this request.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
