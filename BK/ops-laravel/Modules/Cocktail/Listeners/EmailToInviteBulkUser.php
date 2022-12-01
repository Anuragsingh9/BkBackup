<?php

namespace Modules\Cocktail\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Modules\Cocktail\Emails\DynamicMailable;
use Modules\Cocktail\Events\UserInviteLinkEvent;

class EmailToInviteBulkUser {
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
    public function handle(UserInviteLinkEvent $event) {
        Mail::to($event->to)->send(new DynamicMailable(config('cocktail.view.dynamic'), $event->emailData));
    }
}
