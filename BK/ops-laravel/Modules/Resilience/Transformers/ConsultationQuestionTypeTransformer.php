<?php

namespace Modules\Resilience\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;

class ConsultationQuestionTypeTransformer extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request) {
        return [
            "id"                    => $this->id,
            "question_type"         => $this->question_type,
            "is_enable"             => $this->is_enable,
            "show_add_allow_button" => $this->show_add_allow_button,
            "format"                => json_decode($this->format),
        ];
    }

}
