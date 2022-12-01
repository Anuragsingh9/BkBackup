<?php
    
    namespace Modules\Resilience\Transformers;
    
    use Illuminate\Http\Resources\Json\ResourceCollection;
    use Modules\Resilience\Entities\ConsultationSprint;
    
    class SprintCollection extends ResourceCollection
    {
        /**
         * Transform the resource collection into an array.
         *
         * @param \Illuminate\Http\Request
         * @return array
         */
        public function toArray($request)
        {
            $this->collection->transform(function (ConsultationSprint $consultationSprint) {
                return (new SprintResource($consultationSprint))->additional($this->additional);
            });
            return [
                'status' => TRUE,
                'data'   => $this->collection,
            ];
        }
    }
