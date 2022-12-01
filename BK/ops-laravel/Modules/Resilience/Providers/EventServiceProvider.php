<?php
    
    
    namespace Modules\Resilience\Providers;
    
    use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
    
    class EventServiceProvider extends ServiceProvider
    {
        protected $listen = [
            'Modules\Resilience\Events\InviteFriends'           => [
                'Modules\Resilience\Listeners\EmailToFriends',
            ],
            'Modules\Resilience\Events\LateParticipants' => [
                'Modules\Resilience\Listeners\EmailToLateParticipants',
            ],
            'Modules\Resilience\Events\ConsultationReminder' => [
                'Modules\Resilience\Listeners\EmailToConsultationParticipants',
            ],
            'Modules\Resilience\Events\OptionMail' => [
                'Modules\Resilience\Listeners\EmailToOptionMail',
            ]
        ];
    }