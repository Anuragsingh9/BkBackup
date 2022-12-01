<?php

namespace Modules\KctUser\Events;

use Illuminate\Queue\SerializesModels;

class MainHostAddedEvent
{
    use SerializesModels;
    public $emailData;
    public $to;

    /**
     * MainHostAddedEvent constructor.
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
    public function broadcastOn()
    {
        return [];
    }
}
