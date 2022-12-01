<?php

namespace Modules\Events\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Mail;
use Modules\Events\Emails\SendUserRegisterEmail;
use Modules\Events\Service\EventService;

class SendMailToUser {
    public $to;
    public $workshop;
    private $eventService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {
        $this->eventService = EventService::getInstance();
    }

    /**
     * Handle the event.
     * todo prepare the data of email to send and send to user
     * @param object $event
     * @return bool
     */
    public function handle($event) {
        try {
            Mail::to($event->to)->send(new SendUserRegisterEmail($event->viewName, $event->viewData));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
