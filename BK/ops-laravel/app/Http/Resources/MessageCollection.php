<?php
    
    namespace App\Http\Resources;
    
    use App\Message;
    use Illuminate\Http\Resources\Json\ResourceCollection;
    
    class MessageCollection extends ResourceCollection
    {
        /**
         * Transform the resource collection into an array.
         *
         * @param \Illuminate\Http\Request $request
         * @return array
         */
        public function toArray($request)
        {
            
            $this->collection->transform(function (Message $message) {
                return (new MessageResource($message))->additional($this->additional);
            });
            return [
                'status' => TRUE,
                'data'   => $this->collection,
            ];
            return parent::toArray($request);
        }
    }
