<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class UserAccessTokenResource extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request) {
        return [
            'status' => true,
            'data'   => [
                'id'           => $this->id,
                'fname'        => $this->fname,
                'lname'        => $this->lname,
                'email'        => $this->email,
                'validated'    => (boolean)$this->on_off,
                'access_token' => $this->createToken('check')->accessToken,
            ]
        ];
    }
}
