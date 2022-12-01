<?php


namespace Modules\KctUser\Services\BusinessServices\factory;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\KctAdmin\Entities\Event;
use Modules\KctAdmin\Entities\Space;
use Modules\KctUser\Entities\Conversation;
use Modules\KctUser\Exceptions\CustomValidationException;
use Modules\KctUser\Services\BusinessServices\IKctUserValidationService;
use Modules\KctUser\Traits\Repo;
use Modules\KctUser\Traits\Services;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will be manage the user validation services
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class KctUserValidationService
 * @package Modules\KctUser\Services\BusinessServices\factory
 */
class KctUserValidationService implements IKctUserValidationService {
    use Services, Repo;

    const DT_FORMAT = 'Y-m-d H:i:s';

    /**
     * @inheritDoc
     * @throws CustomValidationException
     */
    public function canUserJoinConversation($userId, $spaceUuid, $isDummy): bool {
        $conversation = $this->userServices()->spaceService->getUserConversation($userId, $spaceUuid);
        // if other user is not in conversation then send true;
        // as because the other user is not in conversation so skip the validation
        if ($isDummy) {
            $space = $this->userServices()->adminService->findSpaceByUuid($spaceUuid);
            $eventDummyUser = $this->userServices()->adminService->validateDummyUserWithEvt($space, $userId);
            if (!$eventDummyUser) {
                throw new CustomValidationException('user_not_member', null, 'message');
            }
            $conversation = $eventDummyUser->conversation;
        }
        if (!$conversation) {
            return true;
        }
        $conversation->load(['space', 'space.hosts']);

        $this->validateUserForPrivateConversation($conversation);
        $this->validateConversationSeat($conversation);
        return true;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check if conversation is private
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $conversation
     * @throws CustomValidationException
     */
    public function validateUserForPrivateConversation($conversation) {
        if ($conversation->is_private) {
            if (!$this->isUserSpaceHost($conversation->space, Auth::user()->id)) {
                throw new CustomValidationException('conversation_is_private', null, 'message');
            }
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check if user is current space host or not
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $space
     * @param $userId
     * @return false
     */
    public function isUserSpaceHost($space, $userId) {
        $space = $this->resolveSpace($space);
        if ($space) {
            $spaceHosts = $space->hosts()->where('host_id', $userId)->get();
            return $spaceHosts->count();
        }
        return false;
    }

    /**
     * @param $space
     * @return mixed|EventSpace|null
     */
    public function resolveSpace($space) {
        if ($space instanceof Space) {
            return $space;
        } else if (is_string($space)) {
            return $this->userServices()->adminService->getSpaceWithEvent($space);
        }
        return null;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check if there is enough seat in the conversation
     * @warn it will update the conversation seat config value so further bypass can be also checked with correct count
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Conversation $conversation
     * @throws CustomValidationException
     */
    public function validateConversationSeat($conversation) {
        $maxCount = $this->userServices()->kctService->getEventMaxConvCount($conversation->space->event);

        $usersCount = $this->userServices()->spaceService->getConversationUserCount($conversation);
        $hosts = $conversation->space->hosts;
        // this contains the ids of hosts which are in conversation
        $hostsInConversation = count(array_intersect(
            $hosts->pluck('id')->toArray(),
            $conversation->users->pluck('id')->toArray()
        ));

        if ($hostsInConversation) {
            // as (hosts are in conversation) or (host is joining conversation)
            // then increase the max users limit with count of hosts in conversation
            $maxCount = $maxCount + $hostsInConversation;
        }
        $spaceHosts = $conversation->space->hosts()->where('host_id', Auth::user()->id)->get();
        if ($spaceHosts->count()) {
            // if host is joining the conversation just increase the limit by 1
            $maxCount = $maxCount + 1;
        }
        // now checking the conversation max limit with current
        if ($usersCount >= $maxCount) {
            throw new CustomValidationException(
                'conversation_member_limit',
                $maxCount,
                'message'
            );
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description this will try to resolve the user variable and return the User object
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param User|string $user
     * @return User
     */
    public function resolveUser($user) {
        if ($user instanceof User) {
            return $user;
        } else if (is_numeric($user)) {
            return $this->baseService->userManagementService->findUserById($user);
        }
        return $user;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check if provided user ids are already a space host or not in a given event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @param array $usersId
     * @param string $column
     * @return mixed
     */
    public function isUsersAlreadySpaceHost($event, $usersId, $column = 'event_uuid') {
        return Event::whereHas('spaces.hosts', function ($q) use ($usersId) {
            $q->whereIn('host_id', $usersId);
        })->where($column, $event)->first();
    }

    /**
     * @inheritDoc
     * @throws CustomValidationException
     */
    public function isSpaceHaveSeat($eventUuid, $spaceUuid, bool $allowDefault = false, bool $allowException = false): bool {
        if ($allowDefault) {
            $defaultSpace = $this->userServices()->adminService->getDefaultSpace($eventUuid);
            if ($defaultSpace && $spaceUuid == $defaultSpace->space_uuid) {
                return true;
            }
        }

        $space = $this->userServices()->adminService->findSpaceByUuid($spaceUuid);

        if ($space) {
            $space->load(['spaceUsers' => function ($q) {
                $q->where('user_id', '!=', Auth::user()->id);
            }]);
        }

        if ($space && $space->max_capacity && $space->spaceUsers->count() >= $space->max_capacity) {
            // if space not found other validation handling
            if ($allowException) {
                throw new CustomValidationException('space_full', '', 'message');
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isEventSpaceOpenOrFuture($event): bool {
        $event = is_string($event) ? $this->userRepo()->eventRepository->findByEventUuid($event) : $event;
        if ($event) {
            $end = $this->getEventMaxAfter($event);
            $current = Carbon::now()->timestamp;
            return ($current < $end);
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function resolveEvent($event) {
        if ($event instanceof Event) {
            return $event;
        } else if (is_string($event)) {
            $event = $this->userRepo()->eventRepository->findByEventUuid($event);
        }
        return $event;
    }

    /**
     * ----------------------------------------------------------------------------------------------------------------
     * @descripiton To get the maximum after time of the event
     * ----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return int
     */
    public function getEventMaxAfter($event) {
        $carbon = Carbon::createFromFormat(self::DT_FORMAT, "$event->end_time")->timestamp;
        return $carbon;
    }

    /**
     * returns the first opening hour of event
     * either event opening hour or any space which is opening first will be calculated first
     *
     * @param Event $event
     * @param string $openingType
     * @return int // return seconds
     */
    public function getMaxOpeningSeconds($event, $openingType) {
        if (!$event->relationLoaded('spaces')) {
            $event->load('spaces');
        }
        $max = isset($event->event_fields['opening_hours'][$openingType])
            ? $event->event_fields['opening_hours'][$openingType]
            : 0;

        foreach ($event->spaces as $space) {
            if ($space->opening_hours[$openingType] > $max) {
                $max = $space->opening_hours[$openingType];
            }
        }
        return $max * 60;
    }

    /**
     * |---------*space_open*-------------*event_start*---------------*event_end*--------------*space_end*------------------|
     *          |---------current time must be here------------------------------------------------------|
     *
     * This will check the event
     * Event or any its space is opened
     * will return false if event is past (ended) or not yet started (future)
     *
     * @param $event
     * @return bool
     */
    public function isEventOrSpaceRunning($event) {
        $event = $this->resolveEvent($event);
        if ($event) {

            if ($this->isManuallyOpen($event)) {
                return true;
            }

            $end = $this->getEventMaxAfter($event);
            $start = $this->getEventMaxBefore($event);
            $current = Carbon::now()->timestamp;
            return ($start <= $current && $current < $end);
        }
        return false;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check if event is manually opened within appropriate timing
     * -----------------------------------------------------------------------------------------------------------------
     * @warn it will return true if event not found
     *
     * @param $event
     * @return bool
     */
    public function isManuallyOpen($event) {
        $event = $this->resolveEvent($event);

        if (!$event) {
            return true;
        }

        if (isset($this->openEvent["$event->id"])) {
            return (bool)$this->manuallyOpenedEvent["$event->id"];
        }

        $this->manuallyOpenedEvent["$event->id"] = 0;
        $result = false;

        if ($event->manual_opening) {
            if ($this->isEventRunning($event)) {
                $this->manuallyOpenedEvent["$event->id"] = 1;
                $result = true;
            } else {
                $start = Carbon::createFromFormat('Y-m-d H:i:s', "$event->date $event->start_time");

                $timeBetweenEventStartAndCurrent = $start->timestamp - Carbon::now()->timestamp;

                if ($timeBetweenEventStartAndCurrent <= config('events.validations.manual_opening_possible') && $timeBetweenEventStartAndCurrent >= 0) {
                    $this->manuallyOpenedEvent["$event->id"] = 1;
                    $result = true;
                }
            }
        }
        return $result;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description |-------*space_open*-----*event_start*--------------------*event_end*------*space_end*-----------|
     *                                      |----current time must be here----|
     *
     * This will check the event
     * either started
     * or yet to start
     * or space is still opened
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @warn handle the event exists this method will return null if event not found
     * @param Event|string|int $event
     * @return bool
     */
    public function isEventRunning($event) {
        $event = $this->resolveEvent($event);
        if ($event) {

            $start = Carbon::createFromFormat(self::DT_FORMAT, "{$event->date} {$event->start_time}")->timestamp;
            $end = Carbon::createFromFormat(self::DT_FORMAT, "{$event->date} {$event->end_time}")->timestamp;

            $openingHours = $event->event_fields;
            $before = $openingHours['opening_hours']['before'] * 60;
            $after = $openingHours['opening_hours']['after'] * 60;

            $current = Carbon::now()->timestamp;
            return (($start - $before) <= $current && $current < ($end + $after));
        }
        return null;
    }

    /**
     *  To get the maximum before time of the event
     *  when the first space will start
     *
     * @param $event
     * @return int
     */
    public function getEventMaxBefore($event) {
        $startTime = $this->getMaxOpeningSeconds($event, 'before');
        $carbon = Carbon::createFromFormat(self::DT_FORMAT, "$event->date $event->start_time")->timestamp;
        return $carbon - $startTime;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check event should have opening hours or not.
     * -----------------------------------------------------------------------------------------------------------------
     * @param $conference
     * @param $mainHost
     * @return bool
     */
    public function haveOpeningHours($conference, $mainHost) {
        if (isset($conference, $mainHost) && $conference == 1 && count($mainHost) > 0) {
            return true;
        }
        return false;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To find if space is future or not
     * @warn if space not found it will return @true;
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $space
     * @return boolean
     */
    public function isSpaceFuture($space) {
        $space = $this->resolveSpace($space);
        if ($space) {
            $space->load("event");
            return $this->isEventFuture($space->event);
        }
        return true;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To find if event is future or not
     * @warn if event not found it will return @true;
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return boolean
     */
    public function isEventFuture($event) {
        $event = KctUserValidationService::getInstance()->resolveEvent($event);
        if ($event) {
            $start = Carbon::createFromFormat(KctUserValidationService::DT_FORMAT, "{$event->date} {$event->start_time}")->timestamp;
            $current = Carbon::now()->timestamp;
            return ($current < $start);
        }
        return true;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To find if there is already a duo space created in event or not
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @param null $exclude
     * @return bool
     */
    public function isDuoCreated($eventUuid, $exclude = null) {
        return (bool)EventSpace::where('event_uuid', $eventUuid)
            ->where('is_duo_space', 1)
            ->where('space_uuid', '!=', $exclude)
            ->count();
    }

    /**
     * @warn if space not found it will return true so handle space not found
     * @param string|\Modules\Cocktail\Entities\EventSpace $space
     * @return bool
     */
    public function isSpaceOpen($space) {
        $space = $this->resolveSpace($space);
        if ($space) {
            $start = $this->getSpaceStart($space);
            $end = $this->getSpaceEnd($space);
            $during = $this->getSpaceDuring($space);


            $current = Carbon::now()->timestamp;
            if ($current < $start || $end <= $current) { // either space not started or ended
                return false;
            }

            $eventStart = Carbon::createFromFormat(self::DT_FORMAT, "{$space->event->date} {$space->event->start_time}")->timestamp;
            $eventEnd = Carbon::createFromFormat(self::DT_FORMAT, "{$space->event->date} {$space->event->end_time}")->timestamp;

            if (($eventStart <= $current && $current < $eventEnd) && !$during) { // current time is in event and during not allowed
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * To get the start time in time format of space by opening hour
     *
     * @param EventSpace $space
     * @return int
     */
    private function getSpaceStart($space) {
        $openingBefore = isset($space->opening_hours['before']) ? $space->opening_hours['before'] : 0;
        $openingBefore *= 60; // converting to seconds
        $start = Carbon::createFromFormat(self::DT_FORMAT, "{$space->event->date} {$space->event->start_time}")->timestamp;
        return $start - $openingBefore; // reducing opening before from event start time
    }

    /**
     * To get the start time in time format of space by opening hour
     *
     * @param EventSpace $space
     * @return int
     */
    private function getSpaceEnd($space) {
        $openingBefore = isset($space->opening_hours['after']) ? $space->opening_hours['after'] : 0;
        $openingBefore *= 60; // converting to seconds
        $end = Carbon::createFromFormat(self::DT_FORMAT, "{$space->event->date} {$space->event->end_time}")->timestamp;
        return $end + $openingBefore; // reducing opening before from event start time
    }

    private function getSpaceDuring($space) {
        return isset($space->opening_hours['during']) ? $space->opening_hours['during'] : 0;
    }

    /**
     * @inheritDoc
     */
    public function getEventCreateByUserId($eventUuid) {
        $event = $this->userServices()->adminService->findEvent($eventUuid);
        return $event->created_by_user_id;
    }

}
