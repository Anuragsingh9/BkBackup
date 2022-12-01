<?php
    
    namespace App\Http\Resources;
    
    use Illuminate\Http\Resources\Json\Resource;
    
    class MessageResource extends Resource
    {
        /**
         * Transform the resource into an array.
         *
         * @param \Illuminate\Http\Request $request
         * @return array
         */
        public function toArray($request)
        {
            if (!isset($this->id)) {
                return [
                    'status' => FALSE,
                    'msg'    => 'no data found',
                ];
            }
            return [
                "id"    => $this->id,
                "messages_text"    => $this->messages_text,
                "user_id"    => $this->user_id,
                "to_id"    => $this->to_id,
                "created_at"    => !empty($this->created_at)?$this->created_at->format('Y-m-d'):NULL,
                "updated_at"    => !empty($this->updated_at)?$this->updated_at->format('Y-m-d'):NULL,
                "replyCount"    => isset($this->message_replies_count)?$this->message_replies_count:0,
            ];
            return parent::toArray($request);
        }
        
        public function with($request)
        {
            return [
                'status' => TRUE,
            ];
        }
    }
