<?php

namespace Modules\Cocktail\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DynamicMailable extends Mailable {
    use Queueable, SerializesModels;
    
    /**
     * @var string
     */
    private $viewName;
    
    /**
     * @var array
     */
    private $data;
    
    /**
     * Create a new message instance.
     *
     * @param string $viewName
     * @param array $data
     */
    public function __construct($viewName, $data) {
        $this->viewName = $viewName;
        $this->data = $data;
    }
    
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        return $this->subject($this->data['subject'])
            ->view($this->viewName)
            ->with($this->data['viewData']);
    }
}
