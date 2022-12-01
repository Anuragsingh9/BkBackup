<?php

    namespace Modules\Resilience\Transformers;

    use Illuminate\Http\Request;
    use Illuminate\Http\Resources\Json\Resource;


    class ConsultationSprintTransformer extends Resource
    {
        /**
         * Transform the resource into an array.
         *
         * @param Request
         * @return array
         */
        public function toArray($request)
        {

            return [
                "id"                 => $this->id,
                "title"              => $this->title,
                "description_1"      => $this->description_1,
                "description_2"      => $this->description_2,
                "description_3"      => $this->description_3,
                "image_non_selected" => $this->image_non_selected,
                "image_selected"     => $this->image_selected,
                "consultation_id"    => $this->consultation_id,
                "is_accessible"      => $this->is_accessible,
                "updated_at"         => $this->updated_at,
                $this->mergeWhen((isset($this->additional['showStep']) && ($this->additional['showStep'])), [
                    "consultation_step" => ConsultationStepTransformer::collection($this->consultationStep),
                ]),
            ];
        }

    }
