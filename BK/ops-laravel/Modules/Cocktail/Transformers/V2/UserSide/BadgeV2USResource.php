<?php

namespace Modules\Cocktail\Transformers\V2\UserSide;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Modules\Cocktail\Services\KctService;
use Modules\Cocktail\Transformers\UserSide\EntityResource;

class BadgeV2USResource extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        // to find the visible columns
        $v = $this->resource->userVisibility;
        
        
        $result = [
            'user_id'           => $this->id,
            'user_fname'        => $this->fname,
            'user_lname'        => $this->lname,
            'user_email'        => $this->email,
            'user_avatar'       => $this->avatar ? KctService::getInstance()
                ->getCore()
                ->getS3Parameter($this->avatar) : null,
            'unions'            => EntityResource::collection($this->unions),
            'company'           => new EntityResource($this->companies->first()),
            'instance'          => new EntityResource($this->instances->first()),
            'press'             => new EntityResource($this->presses->first()),
            'social_links'      => [
                'facebook'  => $this->facebookUrl ? $this->facebookUrl->url : null,
                'twitter'   => $this->twitterUrl ? $this->twitterUrl->url : null,
                'instagram' => $this->instagramUrl ? $this->instagramUrl->url : null,
                'linkedin'  => $this->linkedinUrl ? $this->linkedinUrl->url : null,
            ],
            'visibility'        => [
                // if value is not present in fields that means it never updated so by default show that and send 1
                'user_lname' => isset($v->fields['user_lname']) ? $v->fields['user_lname'] : 1,
                'company'    => isset($v->fields['company']) ? $v->fields['company'] : 1,
                'unions'     => isset($v->fields['unions']) ? $v->fields['unions'] : 1,
                "p_field_1"  => isset($v->fields['p_field_1']) ? $v->fields['p_field_1'] : 1,
                "p_field_2"  => isset($v->fields['p_field_2']) ? $v->fields['p_field_2'] : 1,
                "p_field_3"  => isset($v->fields['p_field_3']) ? $v->fields['p_field_3'] : 1,
            ],
            'personal_info'     => [
                "field_1" => $this->resource->personalInfo ? $this->resource->personalInfo->field_1 : null,
                "field_2" => $this->resource->personalInfo ? $this->resource->personalInfo->field_2 : null,
                "field_3" => $this->resource->personalInfo ? $this->resource->personalInfo->field_3 : null,
            ],
            'personal_tags'     => [],
            'professional_tags' => [],
        ];
        if ($this->is_dummy) {
            $result['is_dummy'] = 1;
            $result['dummy_video_url'] = Storage::disk('kct_video')->url($this->video_url);
        } else {
            if ($this->id != Auth::user()->id) {
                // as current user resource is not self so hide the columns data which are hidden by the respective user
                // after this the data will not send which is hidden by the current resource user
                // either the visible record is not set or value is set to 1 then show that field
                $result['user_lname'] = !isset($v->fields['user_lname']) || $v->fields['user_lname'] ? $result['user_lname'] : '';
                $result['company'] = !isset($v->fields['company']) || $v->fields['company'] ? $result['company'] : '';
                $result['unions'] = !isset($v->fields['unions']) || $v->fields['unions'] ? $result['unions'] : [];
                
                $result['p_field_1'] = !isset($v->fields['p_field_1']) || $v->fields['p_field_1'] ? $result['personal_info']['field_1'] : null;
                $result['p_field_2'] = !isset($v->fields['p_field_2']) || $v->fields['p_field_2'] ? $result['personal_info']['field_2'] : null;
                $result['p_field_3'] = !isset($v->fields['p_field_3']) || $v->fields['p_field_3'] ? $result['personal_info']['field_3'] : null;
            }
            if ($this->relationLoaded('eventUser')) {
                $result['active_state'] = $this->eventUser->state == 1 ? 1 : 2;
            }
            if ($this->id == Auth::user()->id) {
                $result['is_self'] = 1;
            }
            if ($this->resource->personalTags) {
                $result['personal_tags'] = $this->resource->personalTags ? UserTagV2USResource::collection($this->resource->personalTags) : null;
                $result['professional_tags'] = $this->resource->professionalTags ? UserTagV2USResource::collection($this->resource->professionalTags) : null;
            }
        }
        return $result;
    }
}
