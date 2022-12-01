<?php

    namespace Modules\Resilience\Transformers;

    use Illuminate\Http\Request;
    use Illuminate\Http\Resources\Json\Resource;
    use Modules\Resilience\Entities\ConsultationStep;

    class SignUpClassResource extends Resource
    {
        protected $CLASS_TYPE = [
            1 => 'union',
            2 => 'company',
        ];

        /**
         * Transform the resource into an array.
         *
         * @param Request
         * @return array
         */
        public function toArray($request)
        {
            return [
                "id"         => $this->uuid,
                "label"      => $this->label,
                "class_type" => $this->class_type ? $this->CLASS_TYPE[$this->class_type] : NULL,
                "positions"  => isset($this->positions) ? $this->positions : NULL,
                $this->mergeWhen((isset($this->additional['showAll']) && ($this->additional['showAll'])), [
                    "sort_order" => $this->sort_order ? $this->sort_order : NULL,
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
