<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MeetingEmail extends Mailable {
    use Queueable, SerializesModels;

    public $viewName;
    public $viewData;

    /**
     * Create a new message instance.
     * @param $viewName
     * @param $viewData
     */
    public function __construct($viewName, $viewData) {
        $this->viewName = $viewName;
        $this->viewData = $viewData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        $this->subject($this->viewData['mail']['subject'])
            ->view($this->viewName)
            ->with($this->viewData);
    }
}
