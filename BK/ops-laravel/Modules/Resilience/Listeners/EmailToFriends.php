<?php
    
    namespace Modules\Resilience\Listeners;
    
    use Illuminate\Queue\InteractsWithQueue;
    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Support\Facades\Mail;
    use Modules\Resilience\Emails\SendInviteFriendEmail;
    
    class EmailToFriends
    {
        public $to;
        public $workshop;
        
        /**
         * Create the event listener.
         *
         * @return void
         */
        public function __construct()
        {
        }
        
        /**
         * Handle the event.
         *
         * @param object $event
         * @return void
         */
        public function handle($event)
        {
            try {
                Mail::to($event->to)->send(new SendInviteFriendEmail($event->viewName, $event->viewData));
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        }
    }
