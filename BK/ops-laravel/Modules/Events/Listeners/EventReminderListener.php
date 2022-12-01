<?php

namespace Modules\Events\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Modules\Cocktail\Emails\DynamicMailable;
use Modules\Events\Events\EventReminderEvent;

class EventReminderListener {
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    
    /**
     * Handle the event.
     *
     * @param EventReminderEvent $event
     * @return void
     */
    public function handle(EventReminderEvent $event) {
        Mail::to($event->to)->send(new DynamicMailable(config('cocktail.view.dynamic'), $event->emailData));
    }
}
