<?php

namespace Modules\KctUser\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Modules\KctUser\Emails\DynamicMailable;
use Modules\KctUser\Events\UserJoinLinkEvent;
use Modules\KctUser\Events\UserMagicLinkEvent;

class EmailToJoinLinkUser {
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
     * @param UserJoinLinkEvent $event
     * @return void
     */
    public function handle(UserJoinLinkEvent $event) {
        Mail::to($event->to)
            ->send(new DynamicMailable(config('cocktail.view.dynamic'), $event->emailData));
    }
}
