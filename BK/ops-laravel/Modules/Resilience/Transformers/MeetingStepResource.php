<?php
    
    namespace Modules\Resilience\Transformers;
    
    use App\Meeting;
    use Illuminate\Http\Request;
    use Illuminate\Http\Resources\Json\Resource;
    
    class MeetingStepResource extends Resource
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
                "meeting" => isset($this->meeting_id) ? $this->meeting : NULL,
                "id"      => $this->id,
            ];
        }
        
        public function with($request)
        {
            return [
                'status' => isset($this->id) ? TRUE : FALSE,
            ];
        }
    }
