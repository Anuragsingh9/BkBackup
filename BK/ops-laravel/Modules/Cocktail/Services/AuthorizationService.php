<?php

namespace Modules\Cocktail\Services;

use App\AccountSettings;
use App\Services\Service;
use App\User;
use Hyn\Tenancy\Environment;
use Illuminate\Support\Facades\Auth;
use Modules\Cocktail\Entities\EventSpace;
use Modules\Cocktail\Entities\EventSpaceUser;
use Modules\Cocktail\Entities\EventUser;
use Modules\Events\Entities\Event;
use Modules\Events\Service\EventService;

class AuthorizationService extends Service {
    
    public static function getInstance() {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }
        return $instance;
    }
    
    /**
     * To check that user is super admin or org admin or both not
     *
     * @return bool
     */
    public function isUserAdmin() {
        return EventService::getInstance()->isAdmin();
    }
    
    /**
     * To check that the user belongs to the event or not that is user is participant of that event or not
     * from event users list
     *
     *
     * @param null $eventUuid
     * @param null $event
     * @param null $user
     * @return bool
     */
    public function isUserEventMember($eventUuid = null, $event = null, $user = null) {
        $user = $user ? $user : Auth::user();
        $event = $event ? $event : Event::where('event_uuid', $eventUuid)->first();
        if ($event) {
            return (bool)EventUser::where('event_uuid', $eventUuid)->where('user_id', $user->id)->count();
        }
        return true;
    }
    
    
    /**
     * To check that user is event admin or not
     * for this either user is super admin that ie m0 m1 role
     * or the user must be workshop admin which is checked from workshop meta
     *
     * @param $eventUuid
     * @return bool
     */
    public function isUserEventAdmin($eventUuid) {
        if ($this->isUserAdmin()) {
            return true;
        }
        $event = Event::where('event_uuid', $eventUuid)->where('type', 'virtual')->first();
        if ($event) {
            return (bool)$event->isWorkshopAdmin();
        }
        return true;
    }
    
    /**
     * To check user is event admin by space uuid
     *
     * @param $spaceUuid
     * @return bool
     */
    public function isUserEventAdminBySpace($spaceUuid) {
        $space = EventSpace::where('space_uuid', $spaceUuid)->first();
        if ($space) {
            $event = $space->event_uuid;
            return $this->isUserEventAdmin($event);
        }
        return true;
    }
    
    /**
     * To check that user belongs to space or not
     * if a user is already in space it is ensured that user is also member of event
     * so strictly follow that if we need to add user in space there must be check the user is member of event.
     *
     * @param $spaceUuid
     * @return bool
     */
    public function isUserBelongsToSpace($spaceUuid) {
        if ($this->isUserAdmin()) {
            return true;
        }
        $space = EventSpace::find($spaceUuid);
        if ($space) {
            return $this->isUserEventMember($space->event_uuid) && (bool)EventSpaceUser::where('space_uuid', $spaceUuid)
                    ->where('user_id', Auth::user()->id)
                    ->count();
        }
        return true;
    }
    
    /**
     * To check the user have authority to be a workshop admin
     *
     * @param User $user
     * @return boolean
     */
    public function canBeWorkshopAdmin($user = null) {
        $user = $user ? $user : Auth::user();
        return $this->isUserAdmin() || $user->role_commision;
    }
    
    /**
     * @return bool
     */
    public function isBlueJeansEnabled() {
        $ten = app(Environment::class);
        $this->accountSetting = AccountSettings::where('account_id', $ten->hostname()->id)->first();
        if ($this->accountSetting && isset($this->accountSetting->setting['event_settings']['bluejeans_enabled'])) {
            $isBluejeansEnabled = $this->accountSetting->setting['event_settings']['bluejeans_enabled'];
            // if bluejeans enabled from account and from event insert values
            return (boolean)$isBluejeansEnabled;
        }
        return false;
    }
    
    
    /**
     * To get the status of a user in event is available or DND (do not disturb)
     * true : available
     * false : dnd
     *
     * @param $userId
     * @param null|string $eventUuid
     * @param null|string $spaceUuid
     * @return bool
     */
    public function isUserStateAvailable($userId, $eventUuid = null, $spaceUuid = null) {
        $event = null;
        if ($eventUuid) {
            $event = Event::where('event_uuid', $eventUuid)->first();
        } else if ($spaceUuid) {
            $event = Event::whereHas('spaces', function ($q) use ($spaceUuid) {
                $q->where('space_uuid', $spaceUuid);
            })->first();
        }
        if (!$event) {
            return true;
        }
        $eventUser = EventUser::where('event_uuid', $event->event_uuid)->where('user_id', $userId)->first();
        if ($eventUser) {
            return $eventUser->state == 1;
        }
        return true;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check if user is Org Admin role or higher (e.g. Super Admin)
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return bool
     */
    public function isOrgOrHigh() {
        return Auth::check() && (in_array(Auth::user()->role, ['M1', 'M0']));
    }
    
    public function isUserInConversation($eventUuid) {
        $event = $this->resolveEvent($eventUuid);
        if ($event) {
            return (bool)Event::whereHas('spaces', function ($q) {
                $q->whereHas('spaceUsers', function ($q) {
                    $q->where("user_id", Auth::user()->id);
                    $q->whereNotNull('current_conversation_uuid');
                    $q->whereHas('conversation');
                });
            })->where('event_uuid', $eventUuid)->first();
        }
        return true;
    }
    
    private function resolveEvent($event) {
        if ($event instanceof Event) {
            return $event;
        } else if (is_numeric($event)) {
            $event = Event::find($event);
        } else if (is_string($event)) {
            $event = Event::where('event_uuid', $event)->first();
        }
        return $event;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To add check if the user is with provided role or not in event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @param $role
     * @return false
     */
    public function checkEventRole($event, $role) {
        if($role == 'presenter') {
            return
                EventUser::where( 'event_uuid', $event->event_uuid)
                    ->where( 'user_id',Auth::user()->id)
                    ->where('is_presenter', 1)
                    ->first();
        } else if ($role == 'moderator') {
            return EventUser::where( 'event_uuid', $event->event_uuid)
                ->where( 'user_id',Auth::user()->id)
                ->where('is_moderator', 1)
                ->first();
        }
        return false;
    }
    
    
}