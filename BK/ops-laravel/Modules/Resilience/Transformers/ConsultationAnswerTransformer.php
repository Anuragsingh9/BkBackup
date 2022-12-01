<?php

namespace Modules\Resilience\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;


class ConsultationAnswerTransformer extends Resource {
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
            "answer"                    => $this->isJson($this->answer) ? json_decode($this->answer) : $this->answer,
            "manual_answer"             => $this->manual_answer ? json_decode($this->manual_answer) : null
        ];
    }

    public function isJson($string) {
        return (is_string($string) && (is_object(json_decode($string)) || is_array(json_decode($string)))) ? true : false;
    }

}
