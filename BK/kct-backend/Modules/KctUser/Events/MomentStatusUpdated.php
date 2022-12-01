<?php

namespace Modules\KctUser\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description REDIS Event Emitter
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class MomentStatusUpdated
 * @package Modules\Cocktail\Events
 */
class MomentStatusUpdated implements ShouldBroadcastNow {
    use SerializesModels;

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
        return new PrivateChannel(config('kctuser.events_name.momentStatusUpdated'));
    }

    public function broadcastWith() {
        return $this->dataToSend;
    }
}
