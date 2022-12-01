<?php

namespace Modules\Resilience\Listeners;

use Modules\Resilience\Events\ConsultationReminder;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Modules\Resilience\Emails\SendInviteFriendEmail;

class EmailToConsultationParticipants
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
     * Handle the event.
     *
     * @param ConsultationReminder $event
     * @return void
     */
    public function handle(ConsultationReminder $event)
    {
        try {
            Mail::to($event->to)->send(new SendInviteFriendEmail($event->viewName, $event->viewData));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
