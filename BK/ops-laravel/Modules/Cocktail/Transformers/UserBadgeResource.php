<?php

namespace Modules\Cocktail\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Auth;
use Modules\Cocktail\Services\KctService;
use Modules\Cocktail\Transformers\UserSide\EntityResource;

class UserBadgeResource extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        $result = [
            'user_id'      => $this->id,
            'user_fname'   => $this->fname,
            'user_lname'   => $this->lname,
            'user_email'   => $this->email,
            'user_avatar'  => $this->avatar ? KctService::getInstance()
                ->getCore()
                ->getS3Parameter($this->avatar) : null,
            'unions'       => EntityResource::collection($this->unions),
            'company'      => new EntityResource($this->companies->first()),
            'instance'     => new EntityResource($this->instances->first()),
            'press'        => new EntityResource($this->presses->first()),
            'social_links' => [
                'facebook'  => $this->facebookUrl ? $this->facebookUrl->url : null,
                'twitter'   => $this->twitterUrl ? $this->twitterUrl->url : null,
                'instagram' => $this->instagramUrl ? $this->instagramUrl->url : null,
                'linkedin'  => $this->linkedinUrl ? $this->linkedinUrl->url : null,
            ],
        ];
        
        if ($this->relationLoaded('eventUser')) {
            $result['active_state'] = $this->eventUser->state == 1 ? 1 : 2;
        }
        if ($this->id == Auth::user()->id) {
            $result['is_self'] = 1;
        }
        return $result;
    }
}
