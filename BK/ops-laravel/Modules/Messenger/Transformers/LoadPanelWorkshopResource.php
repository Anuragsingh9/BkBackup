<?php

namespace Modules\Messenger\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Auth;

class LoadPanelWorkshopResource extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        $isAdmin = (in_array(Auth::user()->role, ['M0', 'M1']) || in_array($this->meta_count, [1, 2]));
//        return parent::toArray($request);
        return [
            'id'            => $this->id,
            'workshop_name' => $this->workshop_name,
            'members_count' => $this->meta_data()->groupBy('user_id')->get()->count(),
            'is_admin'      => $isAdmin,
            'topics'        => LoadPanelWorkshopTopicResource::collection($this->imTopics),
        ];
    }
}
