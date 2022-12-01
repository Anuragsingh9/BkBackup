<?php

namespace Modules\KctAdmin\Transformers\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class GrpOrganiserResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        $name = $this->fname . ' ' . $this->lname;
        return [
            'id'             => $this->id,
            'organiser_name' => $name,
        ];
    }
}
