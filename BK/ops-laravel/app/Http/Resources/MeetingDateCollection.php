<?php
    
    namespace App\Http\Resources;
    
    use App\Meeting;
    use Illuminate\Http\Resources\Json\ResourceCollection;
    
    class MeetingDateCollection extends ResourceCollection
    {
        /**
         * Transform the resource collection into an array.
         *
         * @param \Illuminate\Http\Request
         * @return array
         */
        public function toArray($request)
        {
            $this->collection->transform(function (Meeting $meeting) {
                return (new MeetingResource($meeting))->additional($this->additional);
            });
            return [
                'status' => TRUE,
                'data'   => $this->collection,
            ];
        }
    }
