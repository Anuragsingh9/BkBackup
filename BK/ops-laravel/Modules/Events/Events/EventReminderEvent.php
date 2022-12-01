<?php

namespace Modules\Events\Events;

use Illuminate\Queue\SerializesModels;

class EventReminderEvent {
    use SerializesModels;
    public $emailData;
    public $to;
    
    /**
     * Create a new event instance.
     *
     * @param $emailData
     * @param $to
     */
    public function __construct($emailData, $to) {
        $this->emailData = $emailData;
        $this->to = $to;
    }
    
    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn() {
        return [];
    }
}
