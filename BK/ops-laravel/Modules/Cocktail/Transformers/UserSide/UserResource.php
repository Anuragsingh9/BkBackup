<?php

namespace Modules\Cocktail\Transformers\UserSide;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Auth;
use Modules\Cocktail\Services\KctService;

class UserResource extends Resource {
    protected static $inSubArray;
    
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        $result = [
            'user_id'     => $this->id,
            'user_fname'  => $this->fname,
            'user_lname'  => $this->lname,
            'user_email'  => $this->email,
            'user_avatar' => $this->avatar ? KctService::getInstance()->getCore()->getS3Parameter($this->avatar): null,
        ];
        if ($this->id == Auth::user()->id) {
            $result['is_self'] = 1;
        }
        return $result;
    }
}
