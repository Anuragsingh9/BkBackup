<?php

namespace Modules\Crm\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserSocialLinkRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $idRule = 'required';
        if($this->has('type')) {
            if($this->type == 'user') {
                $idRule .= '|exists:tenant.users,id';
            } elseif ($this->type=='contact') {
                $idRule .= '|exists:tenant.newsletter_contacts,id';
            }
        }
        return [
            'id'      => $idRule,
            'type' => 'required|in:user,contact',
            'channel' => 'required|in:linkedin,facebook,twitter,instagram,pinterest',
            'url'     => 'required|url',
            'isMain'  => 'required|in:1,0',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return TRUE;
    }
}
