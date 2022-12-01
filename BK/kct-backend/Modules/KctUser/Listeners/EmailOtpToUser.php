<?php

namespace Modules\KctUser\Listeners;

use Illuminate\Support\Facades\Mail;
use Modules\KctUser\Emails\DynamicMailable;

class EmailOtpToUser {
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
     * @param object $event
     * @return void
     */
    public function handle($event) {
        Mail::to($event->to)->send(new DynamicMailable(config('cocktail.view.dynamic'), $event->emailData));
    }
}
