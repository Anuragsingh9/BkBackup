<?php

namespace Modules\Messenger\Service;

use Exception;
use Illuminate\Support\Facades\Auth;
use Modules\Messenger\Entities\Channel;
use Modules\Messenger\Entities\Message;
use Modules\Messenger\Entities\MessageMedia;
use Modules\Messenger\Entities\MessageReaction;
use Modules\Messenger\Entities\MessageReply;
use Modules\Messenger\Entities\UserChannelVisit;
use Modules\Messenger\Entities\WorkshopTopic;

class TopicService {
    
    /**
     * @return static|ChannelService
     */
    public static function getInstance() {
        static $instance = null;
        if (null === $instance) {
            $instance = new static(); // by using static if a class inherit this class then by using this method we will get child class object
        }
        return $instance;
    }
    
    /**
     * @param $param
     * @return mixed
     * @throws Exception
     */
    public function create($param) {
        $channelService = ChannelService::getInstance(); // now getting instance inside method so methods which not required service object will save heap memory
        $channelParam = [
            'channel_type' => 1, // 1 is for workshop topic
            'is_private'   => 1, // 0 Public anyone can join , 1 Private invite users can join only
            'owner_id'     => Auth::user()->id, // Created By also
        ];
        $channel = $channelService->createChannel($channelParam); // this method can also be used for workshop or personal
        $param['channel_uuid'] = $channel->uuid;
        
        $topic = WorkshopTopic::create($param);
        if (!$topic)
            throw new Exception();  // to throw the error instead of null so proper message can be shown
        return $topic;
    }
    
    /**
     * @param $param
     * @param $topicId
     * @return mixed
     * @throws Exception
     */
    public function update($param, $topicId) {
        $topic = WorkshopTopic::find($topicId);
        if (!$topic->update($param)) {
            throw new Exception();  // to throw the error instead of null so proper message can be shown
        }
        return $topic;
    }
    
    /**
     * @param $workshopId
     * @return bool
     * @throws Exception
     */
    public function deleteTopicsOfWorkshop($workshopId) {
        $topics = WorkshopTopic::where('workshop_id', $workshopId)->get();
        if ($topics->count()) {
            $channelIds = $topics->pluck('channel_uuid')->toArray();
            $messages = Message::whereIn('channel_uuid', $channelIds)->get();
            if ($messages->count()) {
                $messagesIds = $messages->pluck('id')->toArray();
                $replies = MessageReply::whereIn('message_id', $messagesIds)->get()->pluck('id')->toArray();
                MessageReaction::whereIn('message_id', $messagesIds)->delete();
                MessageMedia::whereIn('attachmentable_id', $messagesIds)
                    ->where('attachmentable_type', Message::class)
                    ->delete();
                MessageMedia::whereIn('attachmentable_id', $replies)
                    ->where('attachmentable_type', MessageReply::class)
                    ->delete();
                Message::whereIn('channel_uuid', $channelIds)->delete();
                MessageReply::whereIn('message_id', $messagesIds)->delete();
            }
            Channel::whereIn('uuid', $channelIds)->delete();
            UserChannelVisit::where('channel_uuid', $channelIds)->delete();
            $topicsDel = WorkshopTopic::where('workshop_id', $workshopId)->delete();
            if (!$topicsDel) {
                throw new Exception();
            }
        }
        return true;
    }
}