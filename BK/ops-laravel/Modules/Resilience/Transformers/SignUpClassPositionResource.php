<?php

    namespace Modules\Resilience\Transformers;

    use Illuminate\Http\Request;
    use Illuminate\Http\Resources\Json\Resource;
    use Modules\Resilience\Entities\ConsultationStep;

    class SignUpClassPositionResource extends Resource
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
                "id"         => $this->id,
                "label"      => $this->positions,
                "sort_order" => $this->sort_order ? $this->sort_order : NULL,
                $this->mergeWhen((isset($this->additional['showAll']) && ($this->additional['showAll'])), [
                    "consultation_sign_up_class_uuid" => $this->consultation_sign_up_class_uuid ? $this->consultation_sign_up_class_uuid : NULL,
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
