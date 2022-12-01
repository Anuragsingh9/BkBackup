<?php

namespace Modules\KctUser\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class ConversationLeaveEvent implements ShouldBroadcastNow {

    private $dataToSend;

    /**
     * Create a new event instance.
     *
     * @param $data
     */
    public function __construct($data) {
        $this->dataToSend = $data;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return PrivateChannel
     */
    public function broadcastOn() {
        return new PrivateChannel(config('kctuser.events_name.conversationLeave'));
    }

    public function broadcastWith() {
        return $this->dataToSend;
    }


}
