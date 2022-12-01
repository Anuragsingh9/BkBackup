<?php

namespace Modules\Messenger\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class ChannelUserResource extends Resource {
    
    public function __construct($resource) {
        parent::__construct($resource);
        $this->core = app(\App\Http\Controllers\CoreController::class);
    }
    
    
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        return [
            'user_id'    => $this->id,
            'first_name' => $this->fname,
            'last_name'  => $this->lname,
            'avatar'     => $this->avatar ? $this->core->getS3Parameter($this->avatar, 2) : NULL,
            'email'      => $this->email,
        ];
    }
}
