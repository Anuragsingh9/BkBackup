<?php

namespace Modules\Resilience\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendInviteFriendEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $viewName;
    public $viewData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($viewName, $viewData) {
        //
        $this->viewName = $viewName;
        $this->viewData = $viewData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->viewData['mail']['subject'])->view($this->viewName)->with($this->viewData);
    }
}
