<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class UserResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'email' => $this->email,
            'fname' => $this->fname,
            'lname' => $this->lname,
            'phone' => $this->phone,
            'mobile' => $this->mobile,
            'city' => $this->city,
            'country' => $this->country,
        ];
    }
}
