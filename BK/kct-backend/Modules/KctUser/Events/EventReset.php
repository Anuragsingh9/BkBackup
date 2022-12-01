<?php

namespace Modules\KctUser\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class EventReset implements ShouldBroadcastNow {
    private $dataToSend;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($data) {
        $this->dataToSend = $data;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return PrivateChannel
     */
    public function broadcastOn(): PrivateChannel {
        return new PrivateChannel(config('kctuser.events_name.eventReset'));
    }

    public function broadcastWith() {
        return $this->dataToSend;
    }
}
