<?php

namespace Modules\Cocktail\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Modules\Cocktail\Emails\DynamicMailable;
use Modules\Cocktail\Events\UserForgetPassword;
use Modules\Cocktail\Events\UserInvitedToEvent;

class EmailToInviteUser {
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
     * @param UserInvitedToEvent $event
     * @return void
     */
    public function handle(UserInvitedToEvent $event) {
        Mail::to($event->to)
            ->send(new DynamicMailable(config('cocktail.view.dynamic'), $event->emailData));
    }
}
