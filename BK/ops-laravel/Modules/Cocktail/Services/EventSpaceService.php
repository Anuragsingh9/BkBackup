<?php

namespace Modules\Cocktail\Services;

use App\Services\Service;
use App\Services\StockService;
use App\User;
use Aws\Chime\Exception\ChimeException;
use Carbon\Carbon;
use Exception;
use Auth;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Modules\Cocktail\Entities\Conversation;
use Modules\Cocktail\Entities\ConversationUser;
use Modules\Cocktail\Entities\EventDummyUser;
use Modules\Cocktail\Entities\EventSpace;
use Modules\Cocktail\Entities\EventSpaceUser;
use Modules\Cocktail\Exceptions\CustomValidationException;
use Modules\Cocktail\Exceptions\NotExistsException;
use Modules\Cocktail\Services\V2Services\KctCoreService;
use Modules\Cocktail\Transformers\OpeningHourResource;
use Modules\Events\Entities\Event;
use Modules\Events\Service\ValidationService;

class EventSpaceService extends Service {
    
    
    /**
     * @param $param
     * @return EventSpace
     * @throws Exception
     */
    public function create($param) {
        $param = $this->uploadImageAndIcon($param); // this will add param of media to param array if exists in request
        $space = EventSpace::create($param);
        if (isset($param['hosts']) && $param['hosts']) {
            $hosts = [];
            foreach ($param['hosts'] as $host) {
                $hosts[] = [
                    'user_id'    => $host,
                    'space_uuid' => $space->space_uuid,
                    'role'       => 1, // host,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ];
            }
            $users = EventSpaceUser::insert($hosts);
        }
        $space->load('hosts'); // to load after creation
        if (!$space)
            throw new Exception();  // to throw the error instead of null so proper message can be shown// to throw exception so that proper msg can be shown
        return $space;
    }
    
    /**
     * @param $param
     * @param $space_uuid
     * @return mixed
     * @throws Exception
     */
    public function update($param, $space_uuid) {
        $space = EventSpace::find($space_uuid);
        $param['event_uuid'] = $space->event_uuid;
        $param = $this->uploadImageAndIcon($param);
        $updated = $space->update($param);
        if (!$updated)
            throw new Exception();  // to throw the error instead of null so proper message can be shown// to throw exception so that proper msg can be shown
        return EventSpace::find($space_uuid);
    }
    
    /**
     * @param $key
     * @param $eventUuid
     * @return mixed
     * @throws CustomValidationException
     */
    public function searchUserForHost($key, $eventUuid) {
        $event = Event::with(['eventUsers' => function ($q) use ($key) {
            $q->where('fname', 'like', "%$key%");
            $q->orWhere('lname', 'like', "%$key%");
            $q->orWhere('email', 'like', "%$key%");
            $q->orWhere(DB::raw("CONCAT(fname, ' ', lname)"), 'like', "%$key%");
        }])->where('event_uuid', $eventUuid)->first();
        if ($event)
            return $event->eventUsers;
        else throw new CustomValidationException('exists', 'event');
    }
    
    public function getSpace($spaceUuid) {
        $conversation = function ($q) {
            $q->whereHas('users');
            $q->with('users');
        };
        
        $space = EventSpace::with([
            'conversations' => $conversation,
            'singleUsers',
            'event',
        ])->where('space_uuid', $spaceUuid)
            ->first();
        
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
     * @param $userId
     * @param $spaceUuid
     * @param $eventUuid
     * @param $role
     * @return User
     * @throws Exception
     *
     */
    public function addUserToSpace($userId, $spaceUuid, $eventUuid, $role) {
        $this->removeUserFromSpace($userId, $eventUuid);
        $user = EventSpaceUser::create([
            'user_id'    => $userId,
            'space_uuid' => $spaceUuid,
            'role'       => $role,
        ]);
        if (!$user)
            throw new Exception();
        return User::find($userId);
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
            $spaces = EventSpace::where('event_uuid', $eventUuid)->get();
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
     * @param $userId // with whom current user trying to join
     * @param $spaceUuid
     * @param boolean $isDummy
     * @return Conversation
     * @throws CustomValidationException
     * @throws Exception
     */
    public function joinWithConversation($userId, $spaceUuid, $isDummy = false) {
        $dmyUserId = null;
        if ($isDummy) {
            // user is dummy
            $space = EventSpace::find($spaceUuid);
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
            if ($dmyUserId) {
                $this->addDmyUserToConversation($spaceUuid, $dmyUserId, $conversation);
            }
        } else {
            // provided user is part of some conversation already
            // so try to join that if limit is not exceed
            $conversation = $this->joinExistingConversation($conversation);
        }
        $conversation->load('users');
        $conversation->load('currentUser');
        
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
    public function validateDummyUserWithEvt($space, $dmyUserId) {
        $result = EventDummyUser::where('space_uuid', $space->space_uuid)
            ->with('conversation')
            ->where('dummy_user_id', $dmyUserId)
            ->first();
        if (!$result) {
            throw new CustomValidationException('user_not_member', null, 'message');
        }
        return $result;
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
        return EventDummyUser::where('space_uuid', $spaceUuid)
            ->where('dummy_user_id', $dmyUserId)
            ->update([
                'current_conv_uuid' => $conversation->uuid,
            ]);
    }
    
    /**
     * @param $userId
     * @param $spaceUuid
     * @return Conversation
     * @throws Exception
     */
    public function createConversation($spaceUuid, $userId = null) {
        // creating meeting on chime
        $conversation = Conversation::create([
            'space_uuid' => $spaceUuid,
        ]);
        
        if (!$conversation)
            throw new Exception();
        
        $chime = AwsChimeService::getInstance()->createMeeting($conversation->uuid, $spaceUuid);
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
     * @param $conversation
     * @param $userId
     * @return bool
     * @throws Exception
     */
    public function addUserToConversation($conversation, $userId) {
        $userChime = AwsChimeService::getInstance()
            ->joinAttendee($conversation->aws_chime_uuid, sprintf("%02d", $userId), $conversation->uuid);
        if (!$userChime->get('Attendee'))
            throw new Exception();
        $userChime = $userChime->toArray();
        $user = ConversationUser::create([
            'conversation_uuid' => $conversation->uuid,
            'user_id'           => $userId,
            'chime_attendee'    => $userChime,
        ]);
        if (!$user) throw new Exception();
        $this->updateUsersConversationUuidOfSpace([$userId], $conversation->space_uuid, $conversation->uuid);
        return $user;
    }
    
    /**
     * @param $conversation
     * @return mixed
     * @throws CustomValidationException | Exception
     */
    public function joinExistingConversation($conversation) {
        $conversationUsersCount = $this->getConversationUserCount($conversation);
        if ($conversationUsersCount >= config('cocktail.conversation_max_member')) {
            throw new CustomValidationException(
                'conversation_member_limit',
                config('cocktail.conversation_max_member'),
                'message');
        }
        $user = $this->addUserToConversation($conversation, Auth::user()->id);
        if (!$user) {
            throw new Exception();
        }
        return $conversation;
    }
    
    /**
     * @param array $userIds
     * @param $spaceUuid
     * @param $conversationUuid
     * @return EventSpaceUser
     * @throws Exception
     *
     * To update the users current conversation of space
     */
    public function updateUsersConversationUuidOfSpace($userIds, $spaceUuid, $conversationUuid) {
        return EventSpaceUser::where('space_uuid', $spaceUuid)
            ->whereIn('user_id', $userIds)
            ->update(['current_conversation_uuid' => $conversationUuid]);
    }
    
    /**
     * this method is for some purpose
     *
     * @warn this will return true if provided conversation not found or current user is not participant in that conversation
     *
     * @param $conversationUuid
     * @return bool|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|Conversation|object|null
     * @throws Exception
     */
    public function removeUserFromConversation($conversationUuid) {
        $conversation = Conversation::with(['userRelation', 'dummyRelation.dummyUsers'])->whereHas('userRelation', function ($q) {
            $q->where('user_id', Auth::user()->id);
        })->where('uuid', $conversationUuid)->first();
        if (!$conversation) {
            return true;
        }
        if ($this->getConversationUserCount($conversation) <= 2) { // if there are only two users left in conversation and
            // one left remove both from conversation and delete conversation
            $this->deleteConversation($conversation);
            return true;
        } else {
            $this->deleteUsersFromConversation($conversation, [Auth::user()->id]);
            return $conversation;
        }
        
    }
    
    public function getConversationUserCount($conversation) {
        $users = $conversation->userRelation->count();
        $space = EventSpace::with('event')->find($conversation->space_uuid);
        if ($space && $space->event) {
            $event = $space->event;
            if (isset($event->event_fields["is_dummy_event"]) && $event->event_fields["is_dummy_event"]) {
                $users +=  $conversation->dummyRelation->count();
            }
        }
        return $users;
    }
    
    /**
     * @param $conversation
     * @throws Exception
     */
    public function deleteConversation($conversation) {
        $this->deleteUsersFromConversation($conversation);
        AwsChimeService::getInstance()->deleteMeeting($conversation->aws_chime_uuid);
        Conversation::where('uuid', $conversation->uuid)->update(['end_at' => Carbon::now()->toDateTimeString()]);
        EventDummyUser::where('current_conv_uuid', $conversation->uuid)->update(['current_conv_uuid' => null]);
    }
    
    /**
     * @param $conversation
     * @param null $userIds
     * @return bool
     * @throws Exception
     */
    public function deleteUsersFromConversation($conversation, $userIds = null) {
        
        if ($userIds) {
            $conversationUsers = ConversationUser::whereIn('user_id', $userIds)
                ->where('conversation_uuid', $conversation->uuid)
                ->get();
        } else {
            $conversationUsers = ConversationUser::where('conversation_uuid', $conversation->uuid)->get();
        }
        $chimeAttendeesIds = $conversationUsers->pluck('chime_attendee_id');
        AwsChimeService::getInstance()->removeAttendees($chimeAttendeesIds);
        ConversationUser::where('conversation_uuid', $conversation->uuid)
            ->whereIn('user_id', $conversationUsers->pluck('user_id')->toArray())
            ->update(['leave_at' => Carbon::now()]);
        $this->updateUsersConversationUuidOfSpace($conversationUsers->pluck('user_id')
            ->toArray(), $conversation->space_uuid, null);
        return true;
    }
    
    /**
     * @param string $spaceUuid
     * @return bool
     * @throws Exception
     */
    public function removeCurrentUserFromConversation($spaceUuid) {
        $conversation = $this->getUserConversation(Auth::user()->id, $spaceUuid);
        if ($conversation) {
            $delete = ConversationUser::where('conversation_uuid', $conversation)
                ->where('user_id', Auth::user()->id)
                ->delete();
            if (!$delete) {
                throw new Exception('Can not remove user from existing conversation');
            }
        }
        return true;
    }
    
    public function resortSpace($spaceUuid, $offset = null) {
        $selectedSpace = EventSpace::find($spaceUuid);
        $currentOffset = $this->getCurrentOffsetOfSpace($selectedSpace);
        $maxOffset = $this->getMaxOffsetOfSpaces($selectedSpace->event_uuid);
        $aboveOffset = $this->getAboveOffsetOfSpace($offset, $currentOffset, $maxOffset);
        $belowOffset = $this->getBelowOffsetOfSpace($offset, $currentOffset, $maxOffset);
        $prev = $this->getSpaceByOffset($aboveOffset, $selectedSpace->event_uuid);
        $next = $this->getSpaceByOffset($belowOffset, $selectedSpace->event_uuid);
        if ($currentOffset == $offset || ($belowOffset >= $maxOffset && $currentOffset == $maxOffset)) {
            return $selectedSpace->order_id;
        }
        $aboveRank = $prev ? $prev->order_id : null;
        $belowRank = $next ? $next->order_id : null;
        $rank = KctService::getInstance()->getLexoRank($aboveRank, $belowRank);
        EventSpace::where('space_uuid', $spaceUuid)->update(['order_id' => $rank]);
        return $rank;
    }
    
    /*
     * HELPER METHODS
     * helper methods are which have parameters specifically used for particular functionality
     */
    
    public function getAboveOffsetOfSpace($offset, $current, $total) {
        if ($offset >= $total) {
            return $total - 1;
        }
        return $offset + ($offset < $current ? -1 : 0);
    }
    
    public function getBelowOffsetOfSpace($offset, $current, $total) {
        if ($offset >= $total) {
            return $total;
        }
        return $offset + ($offset < $current ? 0 : 1);
    }
    
    public function getMaxOffsetOfSpaces($eventUuid) {
        return EventSpace::where('event_uuid', $eventUuid)->count();
    }
    
    public function getCurrentOffsetOfSpace($space) {
        return EventSpace::where('order_id', '<', $space->order_id)->where('event_uuid', $space->event_uuid)->count();
    }
    
    public function getSpaceByOffset($offset, $eventUuid) {
        return $offset >= 0 ? EventSpace::where('event_uuid', $eventUuid)
            ->orderBy('order_id')
            ->offset($offset)
            ->first() : null;
    }
    
    /**
     * @param string $spaceUuid
     * @return Conversation
     */
    public function getUserConversation($userId, $spaceUuid) {
        return Conversation::whereHas("userRelation", function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->where('space_uuid', $spaceUuid)->first();
    }
    
    public function uploadImageFromSystem($param) {
        if ($param['space_image']) {
            $param['space_image_url'] = KctService::getInstance()->fileUploadToS3(
                str_replace('event_uuid', $param['event_uuid'], config('cocktail.s3.space_image')),
                $param['space_image'],
                'public');
        }
        return $param;
    }
    
    /**
     * This method will either upload the image from system or will store the stock image url to as image url
     * and this will also update space icon
     *
     * @param $param
     * @return mixed
     */
    public function uploadImageAndIcon($param) {
        if (isset($param['space_image_from']) && $param['space_image_from'] == config('kct_const.space_image_system')) {
            $param = $this->uploadImageFromSystem($param);
        } else if (isset($param['space_image_from']) && $param['space_image_from'] == config('kct_const.space_image_stock')) {
            $param['space_image_url'] = $param['space_image'];
        }
        if (isset($param['space_icon']) && $param['space_icon']) {
            $param['space_icon_url'] = KctService::getInstance()->fileUploadToS3(
                str_replace('event_uuid', $param['event_uuid'], config('cocktail.s3.space_icon')),
                $param['space_icon'],
                'public');
        }
        return $param;
    }
    
    /**
     * To perform the stock upload task from stock service class
     *
     * @param $request
     * @return array
     * @throws \App\Exceptions\CustomValidationException
     */
    public function uploadStockImage($request) {
        if ($request->has('event_uuid')) {
            $path = str_replace('event_uuid', $request->event_uuid, config('cocktail.s3.space_image'));
        } else {
            $path = KctService::getInstance()->getHostname()->fqdn . config('cocktail.s3.space_image');
        }
        $p = StockService::getInstance()->uploadImage($request, $path, 'public');
        return [
            'url'  => KctService::getInstance()->getCore()->getS3Parameter($p),
            'path' => $p,
        ];
    }
    
    /*
     * HELPER METHOD
     */
    
    public function getLastSpaceOrderId($eventUuid) {
        $space = EventSpace::where('event_uuid', $eventUuid)->orderBy('created_at', 'desc')->first();
        return $space ? $space->order_id : config('kct_const.space_start_order'); // if no space found stat from begin.
    }
    
    public function deleteSpace($spaceUuid) {
        $space = EventSpace::find($spaceUuid);
        // fetching default space
        $defaultSpace = KctEventService::getInstance()->getEventDefaultSpace($space->event_uuid);
        if ($spaceUuid == $defaultSpace->space_uuid) { // default space can not be deleted as there should be at least one space
            throw new CustomValidationException('cannot_delete_default_space', '', 'message');
        }
        $this->shiftUserToAnotherSpace($space, $defaultSpace); // so users registered in space shift to default one
        return $space->delete();
    }
    
    public function shiftUserToAnotherSpace($srcSpace, $desSpace) {
        $srcUsers = EventSpaceUser::where('space_uuid', $srcSpace->space_uuid)->get()->pluck('user_id')->toArray();
        $desUsers = EventSpaceUser::where('space_uuid', $desSpace->space_uuid)->get()->pluck('user_id')->toArray();
        $usersToShift = array_values(array_diff($srcUsers, $desUsers));
        $userShift = 0;
        if (count($usersToShift)) {
            $userShift = EventSpaceUser::where('space_uuid', $srcSpace->space_uuid)
                ->whereIn('user_id', $usersToShift)
                ->update([
                    'space_uuid' => $desSpace->space_uuid,
                    'role'       => 1,
                ]);
        }
        EventSpaceUser::where('space_uuid', $srcSpace->space_uuid)->delete();
        return $userShift;
    }
    
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
            'currentSpace.currentConversation.users.eventUser' => $eventUserCondition,
            'currentSpace.singleUsers.eventUser'               => $eventUserCondition,
            'currentSpace.conversations.users.eventUser'       => $eventUserCondition,
            'currentSpace.conversations.dummyRelation'         => $dummy,
            'currentSpace.currentConversation.users.userVisibility',
            'currentSpace.currentConversation.dummyRelation.dummyUsers',
        ])->where('event_uuid', $eventUuid)->first();
    }
    
    /**
     * To update the opening hour of the spaces which follow event opening hour
     *
     * @param $event
     * @param $openingHour
     */
    public function updateEventFollowingSpace($event, $openingHour) {
        EventSpace::where("event_uuid", $event->event_uuid)
            ->where('follow_main_opening_hours', 1)
            ->update([
                'opening_hours' => json_encode($openingHour),
            ]);
    }
    
    /**
     * To verify if current conversation is present then
     * check that conversation is valid from aws chime and meeting id exists
     *
     * @param $eventUuid
     * @throws Exception
     */
    public function verifyCurrentConversation($eventUuid) {
        $event = Event::with('currentSpace.currentConversation')->where('event_uuid', $eventUuid)->first();
        if ($event
            && $event->currentSpace
            && $event->currentSpace->currentConversation
            && $event->currentSpace->currentConversation->aws_chime_uuid
            && ValidationService::getInstance()->isSpaceOpen($event->currentSpace)
        ) {
            // current user is in space and that space is open now and also the current user is in conversation
            // validate the conversation from chime sdk
            try {
                $meeting = AwsChimeService::getInstance()
                    ->getMeeting($event->currentSpace->currentConversation->aws_chime_uuid);
            } catch (ChimeException $e) {
                $this->handleMeetingExpire($event, $e);
            }
        }
    }
    
    /**
     * @param $event
     * @param ChimeException $message
     * @throws Exception
     */
    public function handleMeetingExpire($event, $message) {
        if ($event->currentSpace && $event->currentSpace->currentConversation
            && $message->getAwsErrorCode() == config('kct_const.aws_meeting_expired_code')) {
            $this->recreateConversation($event->currentSpace->currentConversation);
        }
    }
    
    /**
     * To create conversation and shift all the previous users of that conversation to new conversation
     *
     * @param $conversation
     * @throws Exception
     */
    public function recreateConversation($conversation) {
        if ($previous = $conversation->uuid) {
            $previousUsers = EventSpaceUser::where('current_conversation_uuid', $previous)->pluck('user_id');
            $newConversation = $this->createConversation($conversation->space_uuid);
            EventSpaceUser::where('current_conversation_uuid', $previous)->update([
                'current_conversation_uuid' => $newConversation->uuid,
            ]);
            foreach ($previousUsers as $uid) {
                if ($uid != Auth::user()->id) {
                    $this->addUserToConversation($newConversation, $uid);
                }
            }
            Conversation::where('uuid', $previous)->delete();
            ConversationUser::where('conversation_uuid', $previous)->delete();
        }
    }
    
    /**
     * To check users current space is open or not by event uuid
     *
     * @warn will also return false if user is not in any space
     * @param $eventUuid
     * @return bool
     */
    public function isCurrentSpaceOpen($eventUuid) {
        $currentSpace = EventSpace::where('event_uuid', $eventUuid)
            ->whereHas('spaceUsers', function ($q) {
                $q->where('user_id', Auth::user()->id);
            })->first();
        if ($currentSpace) {
            return ValidationService::getInstance()->isSpaceOpen($currentSpace);
        }
        return false;
    }
    
    
    /**
     *
     *
     * @param Collection $spaces
     * @param Event $event
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
     * -----------------------------------------------------------------------------------------------------------------
     * @description to get the all spaces of the event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @return Collection
     */
    public function getSpacesList($eventUuid) {
        return $spaces = EventSpace::with('hosts', 'event', 'event.defaultSpace')
            ->where('event_uuid', $eventUuid)
            ->orderBy('order_id')
            ->get();
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to get the additional data for the space list which helps for the further processing
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @return array
     * @throws CustomValidationException
     */
    public function getSpaceListAdditional($eventUuid) {
        $event = Event::where('event_uuid', $eventUuid)->first();
        if (!$event) {
            throw new CustomValidationException('exists', 'event');  // to throw the error instead of null so proper message can be shown
        }
        $startTime = Carbon::createFromFormat("Y-m-d H:i:s", $event->date . ' ' . $event->start_time);
        return [
            'event_id'         => $event->id,
            'workshop_id'      => $event->workshop_id,
            'event_start_time' => $event->start_time,
            'event_date'       => $event->date,
            'is_past'          => Carbon::now()->timestamp > $startTime->timestamp,
            'event_end_time'   => $event->end_time,
            'event_openings'   => isset($event->event_fields['opening_hours']) ? new OpeningHourResource($event->event_fields['opening_hours']) : [],
            'event_status'     => KctEventService::getInstance()->getEventStatus($event),
        ];
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to get the current conversation for the user in event
     * this will find the current space and the conversation (if any) in provided space for the auth user.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @return Event
     */
    public function getCurrentConversation($eventUuid) {
        $eventUser = function ($q) use ($eventUuid) {
            $q->where('event_uuid', $eventUuid);
        };
        return Event::with([
            'currentSpace',
            'currentSpace.currentConversation.users.eventUser' => $eventUser,
        ])->where('event_uuid', $eventUuid)->first();
    }
}