<?php

namespace Modules\Resilience\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;


class CollectAnswerTransformer extends Resource {
    /**
     * Transform the resource into an array.
     * @param Request
     * @return array
     */
    public function toArray($request) {
        return [
            "id"                        => $this->id,
            "user_id"                   => $this->user_id,
            "user_workshop_id"          => $this->user_workshop_id,
            "consultation_question_id"  => $this->consultation_question_id,
            "answer"                    => json_decode($this->answer),
            "is_manual"                 => $this->is_manual,
        ];
    }

}
