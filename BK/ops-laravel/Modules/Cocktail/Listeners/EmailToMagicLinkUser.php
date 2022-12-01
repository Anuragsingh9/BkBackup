<?php

namespace Modules\Cocktail\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Modules\Cocktail\Emails\DynamicMailable;
use Modules\Cocktail\Events\UserMagicLinkEvent;
use Modules\Cocktail\Events\UserInvitedToEvent;

class EmailToMagicLinkUser {
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
     * @param UserMagicLinkEvent $event
     * @return void
     */
    public function handle(UserMagicLinkEvent $event) {
        Mail::to($event->to)
            ->send(new DynamicMailable(config('cocktail.view.dynamic'), $event->emailData));
    }
}
