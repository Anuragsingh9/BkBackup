<?php

namespace App\Listeners;

use App\Events\MeetingEvent;
use App\Mail\MeetingEmail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Modules\Events\Emails\SendUserRegisterEmail;

class MeetingEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @param MeetingEvent $event
     * @return string
     */
    public function handle(MeetingEvent $event)
    {
        try {
            Mail::to($event->to)->send(new MeetingEmail($event->viewName, $event->viewData));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
