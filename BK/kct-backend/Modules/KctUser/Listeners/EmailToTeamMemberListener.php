<?php

namespace Modules\KctUser\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Modules\KctUser\Emails\DynamicMailable;
use Modules\KctUser\Events\EmailToTeamMemberEvent;

class EmailToTeamMemberListener
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
     * @param  object  $event
     * @return void
     */
    public function handle(EmailToTeamMemberEvent $event)
    {
        Mail::to($event->to)
            ->send(new DynamicMailable(config('cocktail.view.dynamic'), $event->emailData));
    }
}