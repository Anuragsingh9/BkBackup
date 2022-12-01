<?php

namespace Modules\Cocktail\Transformers\AdminSide;

use Illuminate\Http\Resources\Json\Resource;

class UserResource extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        return [
            'user_id'     => $this->id,
            'user_fname'  => $this->fname,
            'user_lname'  => $this->lname,
            'user_email'  => $this->email,
            'user_avatar' => $this->avatar,
        ];
    }
}
