<?php


namespace Modules\Events\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Events\Events\EventModified;
use Modules\Events\Events\EventReminderEvent;
use Modules\Events\Listeners\EmailEventModifyToUser;
use Modules\Events\Listeners\EventReminderListener;

class EventServiceProvider extends ServiceProvider {
    protected $listen = [
        'Modules\Events\Events\UserRegistered' => [
            'Modules\Events\Listeners\SendMailToUser'
        ],
        EventModified::class                   => [
            EmailEventModifyToUser::class,
        ],
        EventReminderEvent::class=> [
            EventReminderListener::class,
        ]
    ];
}