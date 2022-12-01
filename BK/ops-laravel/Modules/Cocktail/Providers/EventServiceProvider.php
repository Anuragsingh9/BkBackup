<?php


namespace Modules\Cocktail\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Cocktail\Events\UserBecomeModeratorEvent;
use Modules\Cocktail\Events\UserInviteLinkEvent;
use Modules\Cocktail\Events\UserJoinLinkEvent;
use Modules\Cocktail\Events\UserMagicLinkEvent;
use Modules\Cocktail\Events\UserForgetPassword;
use Modules\Cocktail\Events\UserInvitedToEvent;
use Modules\Cocktail\Events\UserRegistered;
use Modules\Cocktail\Listeners\EmailModeratorInfo;
use Modules\Cocktail\Listeners\EmailOtpToUser;
use Modules\Cocktail\Listeners\EmailPasswordResetLink;
use Modules\Cocktail\Listeners\EmailToInviteBulkUser;
use Modules\Cocktail\Listeners\EmailToInviteUser;
use Modules\Cocktail\Listeners\EmailToJoinLinkUser;
use Modules\Cocktail\Listeners\EmailToMagicLinkUser;

class EventServiceProvider extends ServiceProvider {
    
    
    protected $listen = [
        UserRegistered::class      => [
            EmailOtpToUser::class
        ],
        UserForgetPassword::class  => [
            EmailPasswordResetLink::class
        ],
        UserInvitedToEvent::class  => [
            EmailToInviteUser::class
        ],
        UserMagicLinkEvent::class  => [
            EmailToMagicLinkUser::class,
        ],
        UserJoinLinkEvent::class   => [
            EmailToJoinLinkUser::class,
        ],
        UserBecomeModeratorEvent::class => [
            EmailModeratorInfo::class,
        ],
        UserInviteLinkEvent::class => [
            EmailToInviteBulkUser::class
        ]
    ];
    
}