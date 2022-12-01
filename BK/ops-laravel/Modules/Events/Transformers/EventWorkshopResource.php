<?php

namespace Modules\Events\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class EventWorkshopResource extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        $memberCount = [];
        if ($this->workshop->meta) {
            foreach ($this->workshop->meta as $meta) {
                $memberCount[$meta->user_id] = isset($memberCount[$meta->user_id]) ? $memberCount[$meta->user_id] + 1 : 1;
            }
        }
        
        return [
            'workshop_id'   => $this->workshop_id,
            'name'          => $this->workshop->workshop_name,
            'code1'         => $this->workshop->code1,
            'code2'         => $this->workshop->code2,
            'secretory'     => $this->secretory ? "{$this->secretory->user->fname} {$this->secretory->user->lname}" : '',
            'deputy'        => $this->deputy ? "{$this->deputy->user->fname} {$this->deputy->user->lname}" : null,
            'members'       => count($memberCount),
            'workshop_meta' => $this->workshop->meta_data,
        ];
    }
}
