<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MeetingEvent {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $to;
    public $viewName;
    public $viewData;

    /**
     * MeetingEvent constructor.
     * @param $viewName
     * @param $data
     * @param $to
     */
    public function __construct($viewName, $data, $to) {
        $this->to = $to;
        $this->viewName = $viewName;
        $this->viewData = $data;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn() {
        return new PrivateChannel('channel-name');
    }
}
