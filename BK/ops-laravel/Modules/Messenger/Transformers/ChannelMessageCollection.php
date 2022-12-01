<?php

namespace Modules\Messenger\Transformers;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Nwidart\Modules\Collection;

class ChannelMessageCollection extends ResourceCollection {
    public $channel;
    
    public function __construct($resource, $channel = NULL) {
        parent::__construct($resource);
        $this->channel = $channel;
    }
    
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        return ['data' => [
            'messages' => $this->collection->transform(function ($message) {
                return new ChannelMessageResource($message);
            })
        ]
        ];
    }
    
    public function with($request) {
        
        if ($this->channel) {
            $channelTypes = ['', 'topic', 'channel', 'personal_conversation'];
            $result = [
                'data' => [
                    'channel_details' => [
                        'channel_name' => $this->channel->channel_name,
                        'channel_type' => (isset($channelTypes[$this->channel->channel_type]) ? $channelTypes[$this->channel->channel_type] : NULL),
                        'member'       => $this->channel->memberCount,
                        'is_admin'     => $this->channel->isAdmin,
                        'files_count'  => $this->channel->files_counts,
                    ],
                ]
            ];
            if ($this->channel->channel_type == 1) { // workshop chat
                $result['data']['channel_details']['workshop_id'] = $this->channel->topic ? $this->channel->topic->workshop_id : NULL;
                $result['data']['channel_details']['channel_name'] = $this->channel->topic ? $this->channel->topic->topic_name : NULL;
                $result['data']['channel_details']['topic_id'] = $this->channel->topic ? $this->channel->topic->id : NULL;
                $result['data']['channel_details']['files_count'] += 0; // todo get workshop docs here
            } else if ($this->channel->channel_type == 3) { // personal chat so include user id
                $result['data']['channel_details']['user_id'] = $this->channel->secondUserOfPersonalChat ? $this->channel->secondUserOfPersonalChat->user1_id : NULL;
                $result['data']['channel_details']['channel_name'] = $this->channel->secondUserOfPersonalChat->user1 ? $this->channel->secondUserOfPersonalChat->user1->fname . ' ' . $this->channel->secondUserOfPersonalChat->user1->lname : '';
            }
            return $result;
        }
        
        return parent::with($request);
    }
}
