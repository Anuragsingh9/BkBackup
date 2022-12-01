<?php

    namespace Modules\Resilience\Transformers;

    use Illuminate\Http\Request;
    use Illuminate\Http\Resources\Json\Resource;
    use Modules\Resilience\Entities\ConsultationStep;

    class SprintResource extends Resource
    {
        /**
         * Transform the resource into an array.
         *
         * @param Request
         * @return array
         */
        public function toArray($request)
        {
            $consultationStep = collect([]);
            if (isset($this->additional['showStepData']) && $this->additional['showStepData']) {
                $consultationStep = $this->consultationStepByStepType->transform(function (ConsultationStep $consultationStep) {
                    return (new ConsultationStepTransformer($consultationStep))->additional(['showStepData' => FALSE]);
                });
            }
// else {
//                $consultationStep = ConsultationStepTransformer::collection($this->consultationStep);
//            }
            return [
                "id"                 => $this->id,
                "title"              => $this->title,
                "description_1"      => $this->description_1,
                "description_2"      => $this->description_2,
                "description_3"      => $this->description_3,
                "image_non_selected" => $this->image_non_selected,
                "image_selected"     => $this->image_selected,
                "consultation_uuid"    => $this->consultation_uuid,
                "is_accessible"      => $this->is_accessible,
                "updated_at"         => $this->updated_at,
                $this->mergeWhen((isset($this->additional['showStepData']) && $this->additional['showStepData']==true), [
                    "consultation_step" => $consultationStep,
                ]),

            ];
        }

        public function with($request)
        {
            return [
                'status' => TRUE,
            ];
        }
    }
