<?php

namespace Modules\KctUser\Events;

use Illuminate\Queue\SerializesModels;

class EmailToTeamMemberEvent
{
    use SerializesModels;
    public $emailData;
    public $to;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($emailData,$to)
    {
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
