<?php

namespace Modules\Events\Events;

use Illuminate\Queue\SerializesModels;

class UserRegistered {
    use SerializesModels;
    public $to;
    public $viewName;
    public $viewData;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($viewName, $data, $to) {
        $this->to = $to;
        $this->viewName = $viewName;
        $this->viewData = $data;
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
