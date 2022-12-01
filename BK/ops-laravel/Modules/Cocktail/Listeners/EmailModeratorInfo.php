<?php

namespace Modules\Cocktail\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Modules\Cocktail\Emails\DynamicMailable;
use Modules\Cocktail\Events\UserBecomeModeratorEvent;

class EmailModeratorInfo {
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
     * @param UserBecomeModeratorEvent $event
     * @return void
     */
    public function handle(UserBecomeModeratorEvent $event) {
        Mail::to($event->to)->send(new DynamicMailable(config('cocktail.view.dynamic'), $event->emailData));
        
    }
}
