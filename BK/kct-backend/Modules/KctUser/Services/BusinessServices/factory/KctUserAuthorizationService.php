<?php


namespace Modules\KctUser\Services\BusinessServices\factory;


use App\Services\Service;
use EventFactory;
use EventUserFactory;
use Illuminate\Support\Facades\Auth;
use Modules\Events\Service\ValidationService;
use Modules\KctAdmin\Entities\Event;
use Modules\KctUser\Entities\EventSpaceUser;
use Modules\KctAdmin\Entities\EventUser;
use Modules\Events\Service\EventService;
use Modules\KctUser\Entities\EventSpace;
use Modules\KctUser\Exceptions\CustomValidationException;
use Modules\KctUser\Services\BaseService;
use Modules\KctUser\Services\BusinessServices\IAuthorizationService;
use Modules\KctUser\Traits\Services;

class KctUserAuthorizationService implements IAuthorizationService {
    use Services;

    private $eventRepo;
    private $eventUserRepo;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the singleton BaseService Object
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return BaseService
     */
    private ?BaseService $baseService = null;

    public function getBaseService(): BaseService {
        if (!$this->baseService) {
            $this->baseService = app(BaseService::class);
        }
        return $this->baseService;
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
    public function isUserStateAvailable($userId, $eventUuid = null, $spaceUuid = null): bool {
        $event = null;
        if ($eventUuid) {
//            $event = Event::where('event_uuid', $eventUuid)->first();
            $event = $this->baseService->adminService->getEventByUuid($eventUuid);
        } else if ($spaceUuid) {
//            $event = Event::whereHas('spaces', function ($q) use ($spaceUuid) {
//                $q->where('space_uuid', $spaceUuid);
//            })->first();
            $event = $this->baseService->adminService->getEventWhereHasSpace($spaceUuid);
        }
        if (!$event) {
            return true;
        }
//        $eventUser = EventUser::where('event_uuid', $event->event_uuid)->where('user_id', $userId)->first();
        $eventUser = $this->baseService->adminService->getUserByEventUuidAndUserId($event->event_uuid, $userId);
        if ($eventUser) {
            return $eventUser->state == 1;
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
//        $event = $event ? $event : Event::where('event_uuid', $eventUuid)->first();
        $event = $event ? $event : $this->userServices()->adminService->findEvent($eventUuid);
        if ($event) {
//            return (bool)EventUser::where('event_uuid', $eventUuid)->where('user_id', $user->id)->count();
            $event = $this->userServices()->adminService->findEvent($eventUuid);
            $eventUser = function ($q) use ($user) {
                $q->where('user_id', $user->id);
            };
            $event->load(['eventUsers' => $eventUser]);
            return (bool)$event->eventUsers->count();
        }
        return true;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check if the current user is space host or not
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $space
     * @return bool
     */
    public function isUserSpaceHost($space): bool {
        $space = $this->userServices()->validationService->resolveSpace($space);
        if ($space) {
            if (!$space->relationLoaded("hosts")) {
                $space->load("hosts");
            }
            return (bool)$space->hosts->where('id', Auth::user()->id)->count();
        }
        return true;
    }

    /**
     * @inheritDoc
     * @throws CustomValidationException
     */
    public function validateNewUserForSpaceChange($targetSpace): bool {
        $event = $targetSpace->event;
        $userCurrentSpace = $event->currentSpace;
        // keeping flag, to denotes, that need to check for vip or not, as if user is space host on both space then user
        // can change the space even if user is not vip member
        $checkForVip = true;
        if ($this->isUserSpaceHost($userCurrentSpace)) { // checking if user is host of current space
            if ($this->isUserSpaceHost($targetSpace)) {
                // as user is host in both spaces so user can switch even user is not a vip user
                $checkForVip = false;
            } else {
                // user is host of current space but not of target
                throw new CustomValidationException('switching_wrong_space', '', 'message');
            }
        }

        $currentUserRole = $event->eventUserRelation()->where("user_id", Auth::user()->id)->first();

        /* checking
        flag position for vip validation or not
        if space is vip space
            if user have some role in event (if not role that means not joined so user can change/join space)
            if user role is vip (if not vip, throw error)
        */
        if ($checkForVip && $targetSpace->is_vip_space && (!$currentUserRole || !$currentUserRole->is_vip)) {
            throw new CustomValidationException('only_vip_can_enter_in', '', 'message');
        }
        return true;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check if user is host in one of the event space
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @return bool
     */
    public function isUserEventSpacesHost($eventUuid) {
//        $event = $this->userServices()->adminService->findE($eventUuid);
//        dd($event);
        return (bool)Event::whereHas('spaces', function ($q) {
            $q->whereHas('hosts', function ($q) {
                $q->where('host_id', Auth::user()->id);
            });
        })->where('event_uuid', $eventUuid)
            ->first();
//        return (bool)$this->baseService->adminService->findIsUserHostByHostId($eventUuid, Auth::user()->id); // todo
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Validate the user to space change
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $targetSpace
     * @throws CustomValidationException
     */
    public function validateUserForSpaceChange($targetSpace) {
        $event = $targetSpace->event;
        $userCurrentSpace = $event->currentSpace;
        $currentUserRole = $event->eventUserRelation()->where("user_id", Auth::user()->id)->first();
        if (!$userCurrentSpace || !$currentUserRole) {
            throw new CustomValidationException('not_belongs_event', '', 'message');
        }
        //  checking is host trying to change the space
//        $isUserHostOfAnySpace = $this->isUserEventSpacesHost($event->event_uuid);
//        if ($isUserHostOfAnySpace) { // current user is host of any space
//            throw new CustomValidationException('space_host_cannot_switch_space', '', 'message');
//        }

        if (!$currentUserRole->is_vip
            && !$this->isUserSpaceHost($targetSpace)
            && $targetSpace->is_vip_space
        ) {
            throw new CustomValidationException('only_vip_can_enter_in', '', 'message');
        }
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
}
