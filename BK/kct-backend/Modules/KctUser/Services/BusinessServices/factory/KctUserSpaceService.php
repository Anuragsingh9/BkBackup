<?php


namespace Modules\KctUser\Services\BusinessServices\factory;

use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Modules\KctAdmin\Entities\Event;
use Modules\KctAdmin\Entities\EventDummyUser;
use Modules\KctAdmin\Entities\Space;
use Modules\KctUser\Entities\Conversation;
use Modules\KctUser\Entities\ConversationUser;
use Modules\KctUser\Entities\EventSpaceUser;
use Modules\KctUser\Exceptions\CustomValidationException;
use Modules\KctUser\Exceptions\NotExistsException;
use Modules\KctUser\Services\BaseService;
use Modules\KctUser\Services\BusinessServices\IKctUserSpaceService;
use Modules\KctUser\Traits\Repo;
use Modules\KctUser\Traits\Services;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will be managing the user space services
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class KctUserSpaceService
 * @package Modules\KctUser\Services\BusinessServices\factory
 */
class KctUserSpaceService implements IKctUserSpaceService {

    use Services, Repo;

    private $eventSpaceRepo;
    private $conversationRepository;
    private $dummyUserRepository;
    private $convUserRepository;
    private $eventRepo;

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
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will create new conversation if user is not in any conversation else user will be added
     * in the existing conversation.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $userId // with whom current user trying to join
     * @param $spaceUuid
     * @param boolean|null|integer $isDummy
     * @return Conversation
     * @throws CustomValidationException
     * @throws Exception
     */
    public function joinWithConversation($userId, $spaceUuid, $isDummy = null): Conversation {
        $dmyUserId = null;
        $space = $this->userServices()->adminService->findSpaceByUuid($spaceUuid);
        $eventDummyUser = null;
        if ($isDummy) {
            // user is dummy
            $eventDummyUser = $this->validateDummyUserWithEvt($space, $userId);
            $conversation = $eventDummyUser->conversation;
            $dmyUserId = $userId;
            $userId = null;
        } else {
            // user is not dummy so get the current conversation of dummy
            $conversation = $this->getUserConversation($userId, $spaceUuid);
        }
        if (!$conversation) {
            // provided user is part of space(assured by validation)
            // but is now as single, not joined any conversation
            $conversation = $this->createConversation($spaceUuid, $userId);
            if ($dmyUserId && $eventDummyUser) {
                $eventDummyUser->current_conv_uuid = $conversation->uuid;
                $eventDummyUser->update();
            }
        } else {
            // provided user is part of some conversation already
            // so try to join that if limit is not exceed
            $conversation = $this->joinExistingConversation($conversation);
        }
        $conversation->load('users');
        $conversation->load('currentUser');
        $conversation->load(['users.eventUser' => function ($q) use ($space) {
            $q->where('event_uuid', $space->event_uuid);
        }]);

        if ($dmyUserId) {
            $conversation->load('dummyRelation.dummyUsers');
        }
        return $conversation;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check if dummy user belongs to event or not
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $space
     * @param $dmyUserId
     * @return EventDummyUser
     * @throws CustomValidationException
     */
    public function validateDummyUserWithEvt($space, $dmyUserId): ?EventDummyUser {
        $result = $this->userServices()->adminService->findDummyUserForSpace($space->space_uuid, $dmyUserId);
        if (!$result) {
            throw new CustomValidationException('user_not_member', null, 'message');
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getUserConversation($userId, $spaceUuid): ?Conversation {
        return $this->userRepo()->convRepository->getUserConversation($userId, $spaceUuid);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To create the conversation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $userId
     * @param $spaceUuid
     * @return Conversation
     * @throws Exception
     */
    public function createConversation($spaceUuid, $userId = null) {
        $conversation = $this->userRepo()->convRepository->createConversation($spaceUuid);
        //if the conversation already exist then throw the exception
        if (!$conversation)
            throw new Exception();

        // Create the meeting with chime
        $chime = $this->userServices()->videoChatService->createMeeting($conversation->uuid, $spaceUuid);
        if (!$chime->get('Meeting')) {
            throw new Exception();
        }
        $chime = $chime->toArray();
        $meetingId = $chime['Meeting']['MeetingId'];

        $update = $conversation->update([
            'aws_chime_uuid' => $meetingId, // chime meeting id
            'aws_chime_meta' => $chime, // the response of chime meeting to be used as it is in front end aws sdk
        ]);

        if (!$update) {
            throw new Exception();
        }
        // adding user 1 and user 2 which is basically auth user and with whom user starting conversation
        // adding them to aws chime meeting attendee list
        $this->addUserToConversation($conversation, Auth::user()->id);
        if ($userId) {
            $this->addUserToConversation($conversation, $userId);
        }
        return $conversation;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To add user in conversation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $conversation
     * @param $userId
     * @return bool
     * @throws Exception
     */
    public function addUserToConversation($conversation, $userId) {
        $userChime = $this->userServices()->videoChatService
            ->joinAttendee($conversation->aws_chime_uuid, sprintf("%02d", $userId), $conversation->uuid);
        if (!$userChime->get('Attendee'))
            throw new Exception();
        $userChime = $userChime->toArray();

        $user = $this->userRepo()->convUserRepository->createUserConv($conversation->uuid, $userId, $userChime);// todo check
        if (!$user) throw new Exception();
        $this->updateUsersConversationUuidOfSpace([$userId], $conversation->space_uuid, $conversation->uuid);
        return $user;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To update the users current conversation of space
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $userIds
     * @param $spaceUuid
     * @param $conversationUuid
     * @return mixed
     */
    public function updateUsersConversationUuidOfSpace(array $userIds, $spaceUuid, $conversationUuid) {
        return EventSpaceUser::where('space_uuid', $spaceUuid)
            ->whereIn('user_id', $userIds)
            ->update(['current_conversation_uuid' => $conversationUuid]);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To add dummy user in conversation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $spaceUuid
     * @param $dmyUserId
     * @param Conversation $conversation
     * @return Boolean
     */
    public function addDmyUserToConversation($spaceUuid, $dmyUserId, $conversation) {
//        return EventDummyUser::where('space_uuid', $spaceUuid)
//            ->where('dummy_user_id', $dmyUserId)
//            ->update([
//                'current_conv_uuid' => $conversation->uuid,
//            ]);
        return $this->userServices()->adminService->addDummyUserInConv($spaceUuid, $dmyUserId, $conversation);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To join existing conversation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $conversation
     * @return mixed
     * @throws CustomValidationException | Exception
     */
    public function joinExistingConversation($conversation) {
        $user = $this->addUserToConversation($conversation, Auth::user()->id);
        if (!$user) {
            throw new Exception();
        }
        return $conversation;
    }

    /**
     * @inheritDoc
     */
    public function getConversationUserCount($conversation, $includeSpaceHost = true) {
        $users = $conversation->userRelation->count();
        $space = $this->userServices()->adminService->getSpaceWithEvent($conversation->space_uuid);
        if ($space && $space->event) {
            $event = $space->event;
            if ($this->userServices()->kctService->isEventDummy($event)) {
                $users += $conversation->dummyRelation->count();
            }
        }
        if (!$includeSpaceHost) {
            $hosts = $conversation->space->hosts;
            // this contains the ids of hosts which are in conversation
            $hostsInConversation = count(array_intersect(
                $hosts->pluck('id')->toArray(),
                $conversation->users->pluck('id')->toArray()
            ));
            return $users - $hostsInConversation;
        }
        return $users;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will be used for get event with information of space and conversation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @return Builder|Model|object|null
     */
    public function getEventWithSpaceAndConversations($eventUuid) {
        $spaceCondition = function ($q) {
            $q->with('event');
            $q->orderBy('order_id');
        };

        $eventUserCondition = function ($q) use ($eventUuid) {
            $q->where('event_uuid', $eventUuid);
        };
        $dummy = function ($q) use ($eventUuid) {
            $q->with('dummyUsers');
            $q->where('event_uuid', $eventUuid);
        };
        return Event::with([
            'spaces'                                           => $spaceCondition,
            'currentSpace'                                     => $spaceCondition,
            'currentSpace.hosts',
            'currentSpace.currentConversation.users.eventUser' => $eventUserCondition,
            'currentSpace.singleUsers.eventUser'               => $eventUserCondition,
            'currentSpace.conversations.users.eventUser'       => $eventUserCondition,
            'currentSpace.conversations.dummyRelation'         => $dummy,
            'currentSpace.currentConversation.users.userVisibility',
            'currentSpace.currentConversation.dummyRelation.dummyUsers',
        ])->where('event_uuid', $eventUuid)->first();
    }

    /**
     * ----------------------------------------------------------------------------------------------------------------
     * @descripiton this method is for some purpose
     * ----------------------------------------------------------------------------------------------------------------
     *
     * @warn this will return true if provided conversation not found or current user is not participant in that conversation
     *
     * @param $conversationUuid
     * @return bool|Builder|Model|Conversation|object|null
     * @throws Exception
     */
    public function removeUserFromConversation($conversationUuid) {
        $conversation = $this->userRepo()->convRepository
            ->getConversationUserToRemove($conversationUuid);

        if (!$conversation) {
            return true;
        }
        if ($this->getConversationUserCount($conversation) <= 2) {
            // if there are only two users left in conversation and
            // one left remove both from conversation and delete conversation
            $this->deleteConversation($conversation);
            return true;
        } else {
            $this->deleteUsersFromConversation($conversation, [Auth::user()->id]);
            $this->userServices()->kctService->handleHostLeave($conversation);
            return $conversation;
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To delete the conversation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $conversation
     * @throws Exception
     */
    public function deleteConversation($conversation) {
        $this->deleteUsersFromConversation($conversation);
        $this->userServices()->videoChatService->deleteMeeting($conversation->aws_chime_uuid);
        Conversation::where('uuid', $conversation->uuid)->update(['end_at' => Carbon::now()->toDateTimeString()]);
        EventDummyUser::where('current_conv_uuid', $conversation->uuid)->update(['current_conv_uuid' => null]);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To delete user from conversation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $conversation
     * @param null $userIds
     * @return bool
     * @throws Exception
     */
    public function deleteUsersFromConversation($conversation, $userIds = null) {

        if ($userIds) {
            $conversationUsers = $this->userRepo()->convUserRepository->getConversationByUserId($userIds, $conversation->uuid);
        } else {
            $conversationUsers = $this->userRepo()->convUserRepository->getConversationByConvUuid($conversation->uuid);
        }
        $chimeAttendeesIds = $conversationUsers->pluck('chime_attendee_id');
        $this->userServices()->videoChatService->removeAttendees($chimeAttendeesIds);
        $this->userRepo()->convUserRepository->convLeaveUpdate($conversation->uuid, $conversationUsers);
        $this->updateUsersConversationUuidOfSpace($conversationUsers->pluck('user_id')
            ->toArray(), $conversation->space_uuid, null);
        return true;
    }

    /**
     *
     *
     * @param Collection $spaces
     * @param \Modules\Events\Entities\Event $event
     * @param bool $excludeDefaultSpace
     * @return \Illuminate\Support\Collection
     */
    public function filterSpacesWithMaxCapacity($spaces, $event, $excludeDefaultSpace = false) {
        $result = collect([]); // empty result

        $spaces->map(function ($space) use (&$result, $event, $excludeDefaultSpace) {
            if (!$space->max_capacity                                       // if max capacity is null -> allow
                || ($space->max_capacity > $space->spaceUsers->count())     // if max capacity not reached -> allow
                || ($excludeDefaultSpace && $space->space_uuid == $event->defaultSpace->space_uuid) // if need to excludeDefaultSpace check space uuid is default space uuid -> allow
            ) {
                $result->push($space);
            }
            return $space;
        });

        return $result;
    }

    /**
     * @param $userId
     * @param $spaceUuid
     * @param $eventUuid
     * @param $role
     * @return User
     * @throws Exception
     *
     */
    public function addUserToSpace($userId, $spaceUuid, $eventUuid, $role) {
        // Allow space host to change space
//        $isSpaceHost = KctUserValidationService::getInstance()->isUsersAlreadySpaceHost($eventUuid,[$userId]);
        $isSpaceHost = $this->userServices()->validationService->isUsersAlreadySpaceHost($eventUuid, [$userId]);
        if ($isSpaceHost) {
            $space = $this->userServices()->adminService->findSpaceByUuid($spaceUuid);
            $spaceHost = $space->load('hosts');
            $hostId = $spaceHost->hosts[0]['id']; // currently only one space host possible
            if ($hostId == Auth::user()->id) {
//                $this->removeUserFromSpace(Auth::user()->id, $eventUuid);
                $spaceUser = $this->userRepo()->spaceUserRepository->createSpaceUser($userId, $spaceUuid, 1);
                if (!$spaceUser)
                    throw new Exception();
                return $spaceUser;
            } else {
                throw new CustomValidationException('switching_wrong_space', '', 'message');
            }
        }
        $this->removeUserFromSpace($userId, $eventUuid);
        $user = $this->userRepo()->spaceUserRepository->createSpaceUser($userId, $spaceUuid, $role);
        if (!$user)
            throw new Exception();
        return User::find($userId);
    }

    public function getHostSpace($userId, $eventUuid) {
        $hostSpaces = Event::with(['spaces.hosts' => function ($q) use ($userId) {
            $q->where('host_id', $userId);
        }])->where('event_uuid', $eventUuid)->first();
//        $hostSpaces = $this->baseService->adminService->getHostById($userId, $eventUuid);

        foreach ($hostSpaces->spaces as $space) {
            if (isset($space->hosts) && $space->hosts->count()) {
                return $space->space_uuid;
            }
        }

    }

    /**
     * if space uuid provided user will be removed from that space only
     * else if event uuid provided user will be removed from all the spaces for that space;
     *
     * @param $userId
     * @param $eventUuid
     * @param $spaceUuid
     * @return integer
     * @throws Exception
     */
    public function removeUserFromSpace($userId, $eventUuid = null, $spaceUuid = null) {
        if ($spaceUuid) {
            $spaceUuid = [$spaceUuid];
        } else if ($eventUuid) {
            $spaces = Space::where('event_uuid', $eventUuid)->get();
            $spaceUuid = $spaces->pluck('space_uuid');
        }
        $conversations = Conversation::whereIn('space_uuid', $spaceUuid)->get();
        $conversationIds = $conversations->pluck('uuid');
        ConversationUser::whereIn('conversation_uuid', $conversationIds)
            ->where('user_id', Auth::user()->id)
            ->whereNull('leave_at')
            ->update(['leave_at' => Carbon::now()]);
        return EventSpaceUser::where('user_id', $userId)->whereIn('space_uuid', $spaceUuid)->delete();
    }

    /**
     * @inheritDoc
     */
    public function getSpace($spaceUuid) {
        $conversation = function ($q) {
            $q->whereHas('users');
            $q->with('users');
        };

        $space = $this->userServices()->adminService->findSpaceByUuid($spaceUuid);
        $space = $space->load(['conversations' => $conversation, 'singleUsers', 'event']);
        if (!$space) {
            throw new NotExistsException('space');
        }

        $eventUserCondition = function ($q) use ($space) {
            $q->where('event_uuid', $space->event_uuid);
        };

        $space->load([
            'singleUsers.eventUser'         => $eventUserCondition,
            'conversations.users.eventUser' => $eventUserCondition,
        ]);

        return $space;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to get the current conversation for the user in event
     * this will find the current space and the conversation (if any) in provided space for the auth user.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @return \Modules\KctUser\Entities\Event
     */
    public function getCurrentConversation($eventUuid) {
        $eventUser = function ($q) use ($eventUuid) {
            $q->where('event_uuid', $eventUuid);
        };
        return $this->userRepo()->convRepository->getUserCurrentConversation($eventUuid, $eventUser)->first();

    }

    public function markConversationHost(Conversation $conversation) {
        if ($conversation->is_host == 0) {

            $spaceHosts = $conversation->space->hosts;
            $hostsInConversation = count(array_intersect(
                $spaceHosts->pluck('id')->toArray(),
                $conversation->users->pluck('id')->toArray()
            ));
            if ($hostsInConversation > 0) {
            $conversation->is_host = 1;
            $conversation->update();
            }
        }
        return $conversation;
    }

}
