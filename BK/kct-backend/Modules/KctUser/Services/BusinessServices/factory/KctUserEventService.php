<?php


namespace Modules\KctUser\Services\BusinessServices\factory;


use App\Http\Controllers\WorkshopController;
use App\Presence;
use App\Setting;
use App\User;
use App\WorkshopMeta;
use Carbon\Carbon;
use Exception;
use Hyn\Tenancy\Contracts\Hostname;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\KctAdmin\Entities\Group;
use Modules\KctAdmin\Entities\GroupEvent;
use Modules\KctAdmin\Traits\KctHelper;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\KctUser\Entities\Event;
use Modules\KctUser\Services\BaseService;
use Modules\KctUser\Services\BusinessServices\IEmailService;
use Modules\KctUser\Services\BusinessServices\IKctUserEventService;
use Modules\KctUser\Entities\EventSpaceUser;
use Modules\KctUser\Exceptions\InternalServerException;
use Modules\KctUser\Exceptions\CustomValidationException;
use Modules\KctUser\Services\KctCoreService;
use Modules\Events\Service\ValidationService;
use Modules\KctUser\Traits\Repo;
use Modules\KctUser\Traits\Services;
use UserFactory;

class KctUserEventService implements IKctUserEventService {
    use KctHelper;
    use Services, Repo, ServicesAndRepo;


    /**
     * To mark the current user present if event is open
     *
     * @param $eventUuid
     *
     * @return bool
     */
    public function markUserPresent($eventUuid) {
        if ($event = $this->isEventOpen($eventUuid)) { // todo
            return Presence::where('workshop_id', $event->workshop_id)
                ->where('user_id', Auth::user()->id)
                ->update(['presence_status' => 'P']);
        }
        return false;
    }

    public function isEventOpen($eventUuid) {
        $event = Event::where('event_uuid', $eventUuid)->first(); // todo
        if ($event) {
            $start = Carbon::createFromFormat('Y-m-d H:i:s', "$event->date $event->start_time")->timestamp;
            $end = Carbon::createFromFormat('Y-m-d H:i:s', "$event->date $event->end_time")->timestamp;
            $current = Carbon::now()->timestamp;
            if ($start <= $current && $current <= $end) {
                return $event;
            }
            return false;
        } else {
            return null;
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This will return current time position with relative to event and space opening times
     * 1. Event and space both not started              -> is_future = true
     * 2. Event not started but one of space started    -> is_before_space_open = true
     * 3. Event started                                 -> is_during = true
     * 4. Event Ended but space is still open           -> is_after_space_open = true
     * 5. Event And Space both ended                    -> is_past = true
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return array
     */
    public function getEventAndSpaceStatus($event): array {
        $start = Carbon::createFromFormat('Y-m-d H:i:s', "$event->start_time")->timestamp;
        $end = Carbon::createFromFormat('Y-m-d H:i:s', "$event->end_time")->timestamp;
        $current = Carbon::now()->timestamp;
        return [
            'is_future'            => $current < $start,
            'is_before_space_open' => false,
            'is_during'            => $start <= $current && $current < $end,
            'is_after_space_open'  => false,
            'is_past'              => $end <= $current,
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To Prepare the common tags for the event module
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Event $event
     * @param $user
     * @param Hostname $hostname
     * @return array
     */
    public function prepareEventEmailTags($event, $user, $hostname = null) {
//        $event = KctUserValidationService::getInstance()->resolveEvent($event);
        $event = $this->baseService->validationService->resolveEvent($event);
        if (!$event) {
            return [];
        }
//        $participant = KctUserValidationService::getInstance()->resolveUser($user);
        $participant = $this->baseService->validationService->resolveUser($user);
        $eventOrganiser = ($event && $event->type == "ext") ? $event->organisres->first() : $event->users->first();
//        $workshop = $event->workshop;
//        $meeting = $workshop ? $event->workshop->meetings->first() : null;
//        $member = workshopValidatorPresident($workshop); // Todo
        if ($event && $event->type == config('kctuser.event_type.int')) {
            return [
                '[[EventName]]'      => (($event) ? $event->title : ''),
                '[[OrganiserFN]]'    => (($eventOrganiser) ? "$eventOrganiser->fname $eventOrganiser->lname" : ''),
                '[[OrganiserEmail]]' => (($eventOrganiser) ? $eventOrganiser->email : ''),
                '[[ParticipantLN]]'  => (($participant) ? $participant->lname : ''),
                '[[ParticipantFN]]'  => (($participant) ? $participant->fname : ''),
//                '[[WorkshopMeetingAddress]]'    => $meeting ? $meeting->place : '',
//                '[[WorkshopMeetingDate]]'       => $meeting ? $meeting->date : '',
//                '[[WorkshopMeetingTime]]'       => $meeting ? $meeting->start_time : '',
//                '[[WorkshopMeetingName]]'       => $meeting ? $meeting->name : '',
//                '[[WorkshopLongName]]'          => $workshop->workshop_name,
//                '[[WorkshopShortName]]'         => $workshop->code1,
//                '[[WorkshopPresidentFullName]]' => "{$member['p']['fname']} {$member['p']['lname']}",
//                '[[PresidentEmail]]'            => $member['p']['email'],
            ];
        } else if ($event && $event->type == config('kctuser.event_type.virtual', 'virtual')) {
//            $loginLink = KctCoreService::getInstance()->getRedirectUrl(request(), 'quick-login', ['EVENT_UUID' => $event->event_uuid]);
            $loginLink = $this->baseService->kctService->getRedirectUrl(request(), 'quick-login', ['EVENT_UUID' => $event->event_uuid]);
            $registerLink = $this->baseService->kctService->getRedirectUrl(request(), 'event-register', ['EVENT_UUID' => $event->event_uuid]);
            $eventJoinLink = "<a href='$loginLink'>$loginLink</a>";
            $eventRegLink = "<a href='$registerLink'>$registerLink</a>";
            return [
                '[[EventName]]'            => $event->title,
                '[[OrganiserFN]]'          => (($eventOrganiser) ? "$eventOrganiser->fname $eventOrganiser->lname" : ''),
                '[[OrganiserEmail]]'       => (($eventOrganiser) ? $eventOrganiser->email : ''),
                '[[ParticipantLN]]'        => (($participant) ? $participant->lname : ''),
                '[[ParticipantFN]]'        => (($participant) ? $participant->fname : ''),
//                '[[WorkshopMeetingDate]]'       => $meeting ? $meeting->date : '',
//                '[[WorkshopMeetingTime]]'       => $meeting ? $meeting->start_time : '',
//                '[[WorkshopMeetingName]]'       => $meeting ? $meeting->name : '',
//                '[[WorkshopLongName]]'          => $workshop->workshop_name,
//                '[[WorkshopShortName]]'         => $workshop->code1,
//                '[[WorkshopPresidentFullName]]' => "{$member['p']['fname']} {$member['p']['lname']}",
//                '[[PresidentEmail]]'            => $member['p']['email'],
                '[[EventJoinLink]]'        => $eventJoinLink,
                '[[EvenRegistrationLink]]' => $eventRegLink,
            ];
        }
    }

    /**
     * To get the all virtual events list
     * this method supports the
     * search
     * sort
     * pagination
     * we can use any combination like search + sort or search + sort+ pagination
     *
     * @param Request $request
     *
     * @return LengthAwarePaginator|Builder[]|Collection|\Illuminate\Support\Collection
     */
    public function getEventsList(Request $request) {

    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method prepares the order by parameter on which events list gonna sort.
     * @note This method by default returns the sorted events list by event date(event_date)
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @return string
     */
    public function getEventsListOrderBy($request): string {
        $possibleOrder = [
            'event_date'       => 'events.start_time',
            'event_title'      => 'events.title',
//            'organiser_fname'  => 'organiser_name',
            'event_start_time' => 'start_time',
            'event_end_time'   => 'end_time',
        ];
        return $request->has('order_by') && isset($possibleOrder[$request->order_by]) ?
            $possibleOrder[$request->order_by] : $possibleOrder['event_date'];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton If order(asc or desc) from front not passed we need to identify what to use as default order is
     * date,so in future events and past events list there will opposite sorting so this will return order according
     * to tense future/past passed.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     *
     * @return string
     */
    public function getEventsListOrder(Request $request): string {
        if ($request->has('order') && (in_array($request->input("order"), ['desc', 'asc']))) {
            return ($request->order == 'desc' ? 'desc' : 'asc');
        } else {
            // if past we will sort by descending so last event on top
            return ($request->tense == 'past' ? 'desc' : 'asc');
        }
    }

    /**
     * @param $op
     *
     * @return Builder|\Illuminate\Database\Query\Builder|Event
     */


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description this will filter the events list with the search key word
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Builder $builder
     * @param string $key
     *
     * @return mixed
     */
    public function addSearchParamToEventListBuilder(Builder $builder, $key) {
        return $builder
            ->where(function ($q) use ($key) {
                $q->where('title', 'like', "%$key%");
                $q->orWhere('start_time', 'like', "%$key%");
                $q->orWhere('end_time', 'like', "%$key%");
                $q->orWhereHas('organiser.user', function ($q) use ($key) {
                    $q->where(DB::raw("CONCAT(fname, ' ', lname)"), 'like', "%$key%");
                });
            });
    }

    /**
     * @param $builder
     * @param $key
     * @return mixed
     */
    public function addSearchDefaultOrderToEventList($builder, $key) {
        $title = "WHEN events.title LIKE";
        $codec = 'COLLATE utf8mb4_unicode_ci';
        $key = [
            addslashes("$key"),
            addslashes("$key%"),
            addslashes("%$key%"),
            addslashes("%$key"),
        ];
        return $builder->orderBy(DB::raw(
            "CASE " .
            "$title '$key[0]' $codec   THEN 1 " .
            "$title '$key[1]' $codec   THEN 2 " .
            "$title '$key[2]' $codec  THEN 3 " .
            "$title '$key[3]' $codec  THEN 4 " .
            "ELSE 9 " .
            "END"
        ));
    }

    /**
     * @param Request $request
     *
     * @return mixed
     * @throws CustomValidationException
     */
    public function addCurrentUserToEvent($request) {
//        $event = Event::where('event_uuid', $request->event_uuid)->first();
        $event = $this->baseService->adminService->getEventByUuid($request->event_uuid)->first(); // todo event
        if (!$event) {
            throw new CustomValidationException('exists', 'event');
        }
        $request->merge([
            'is_presenter' => 0,
            'is_moderator' => 0,
            'data'         => json_encode([
                "id"    => Auth::user()->id,
                "value" => Auth::user()->fname . ' ' . Auth::user()->lname,
                "text"  => Auth::user()->email,
            ]),
            'email'        => Auth::user()->fname . ' ' . Auth::user()->lname,
            'firstname'    => '',
            'lastname'     => '',
        ]);
        return $this->addUserToEventAndWorkshop($request, $request->input('space_uuid'));
    }

    /**
     * @param Request $request
     * @param null $spaceUuid
     *
     * @return mixed
     * @throws Exception|InternalServerException
     */
    public function addUserToEventAndWorkshop($request, $spaceUuid = null) {
        if ($request->has('workshop_id')) {
//            $event = Event::where('workshop_id', $request->input('workshop_id'))->first();
            $event = $this->baseService->adminService->findEventByWorkshopId($request->input('workshop_id')); // todo
        } else {
//            $event = Event::where('event_uuid', $request->input('event_uuid'))->first();
            $event = $this->baseService->adminService->findVirtualEvent($request->input('event_uuid'));
            $request->merge(['workshop_id' => $event->workshop_id]);
        }
        $request->merge(['event_uuid' => $event->event_uuid]);
//        $userId = $this->addUserToWorkshop($request);
        $userId = Auth::user()->id;
        if ($event->type == 'virtual') {
            // in virtual user should show as registered as present
            $this->updateUserPresenceToRegister($userId, $request->workshop_id);
            $isP = config('kctuser.default.is_presenter');
            $isM = config('kctuser.default.is_moderator');
            $eventUser = $this->addUserToEvent($userId, $event->event_uuid, $isP, $isM, $spaceUuid);
            if ($isM) {
//                $user = User::find($userId);
                $user = $this->baseService->userManagementService->findUserById($userId);
//                $this->getEmailFactory()->sendModeratorInfo($event, $user);
                $this->baseService->emailService->sendModeratorInfo($event, $user);
            }
            $eventUser->load('event');
            $eventUuid = $event->event_uuid;
            $eventUser->load(['isHost' => function ($q) use ($eventUuid) {
                $hasSpace = function ($q) use ($eventUuid) {
                    $q->where('event_uuid', $eventUuid);
                };
                $q->with(['space' => $hasSpace]);
                $q->whereHas('space', $hasSpace);
            }]);
            $eventUser->userId = $userId;
            return $eventUser;
        }
        $res = new \stdClass();
        $res->event = $event;
        $res->userId = $userId;
        return $res;
    }

    /**
     * To update the user presence to registered
     *
     * @param $userId
     * @param $wid
     */
    public function updateUserPresenceToRegister($userId, $wid) {
        Presence::updateOrCreate([
            'workshop_id' => $wid,
            'user_id'     => $userId
        ], [
            'presence_status' => 'R'
        ]);
    }

    /**
     * @param      $userId
     * @param      $eventUuid
     * @param int $isP is presenter
     * @param int $isM is moderator
     * @param null $spaceUuid
     *
     * @return EventUser
     * @throws Exception
     */
    public function addUserToEvent($userId, $eventUuid, $isP = 0, $isM = 0, $spaceUuid = null) {
//        $eventUser = EventUser::updateOrCreate([
//            'user_id'    => $userId,
//            'event_uuid' => $eventUuid,
//        ], [
//            'user_id'             => $userId,
//            'is_presenter'        => $isP,
//            'is_moderator'        => $isM,
//            'event_uuid'          => $eventUuid,
//            'state'               => 1,
//            'is_joined_after_reg' => 0,
//        ]);
        $eventUser = $this->baseService->adminService->updateOrCreateEventUser($userId, $eventUuid, $isP, $isM);

        if ($spaceUuid) {
//            KctUserSpaceService::getInstance()
//                ->addUserToSpace($userId, $spaceUuid, $eventUuid, EventSpaceUser::$ROLE_MEMBER);
            $this->baseService->spaceService->addUserToSpace($userId, $spaceUuid, $eventUuid, EventSpaceUser::$ROLE_MEMBER);
        } else {
            $this->addUserToDefaultSpace($eventUuid, $userId);
        }
        return $eventUser;
    }

    /**
     * @param $eventUuid
     * @param $userId
     *
     * @return bool
     * @throws Exception
     */
    public function addUserToDefaultSpace($eventUuid, $userId) {
        $space = $this->getEventDefaultSpace($eventUuid);
        if (!$space)
            throw new Exception('No space found to add this user');
//        KctUserSpaceService::getInstance()
//            ->addUserToSpace($userId, $space->space_uuid, $eventUuid, EventSpaceUser::$ROLE_MEMBER);
        $this->baseService->spaceService->addUserToSpace($userId, $space->space_uuid, $eventUuid, EventSpaceUser::$ROLE_MEMBER);
        return true;
    }

    public function getEventDefaultSpace($eventUuid) {
//        return EventSpace::where('event_uuid', $eventUuid)->orderBy('created_at', 'asc')->first();
        return $this->baseService->adminService->findSpaceByEventUuid($eventUuid)->orderBy('created_at', 'asc')->first();
    }

    /**
     * To add the user to the workshop by already created method
     *
     * @param $request
     *
     * @return mixed
     * @throws InternalServerException
     */
    public function addUserToWorkshop($request) {
        $request->merge(['email_send' => 0]);
        $userId = app(WorkshopController::class)->addMember($request);
        if (!is_int($userId) || (is_object($userId) && $userId instanceof JsonResponse)) {
            throw new InternalServerException('cannot_add_user_to_workshop');
        }
        if ($request->data != null) {// register existing user
            $data = json_decode($request->data);
            if (isset($data->id)) {
                $userId = $data->id;
            }
        }

        return $userId;
    }

    /**
     * To update the user's dnd status for provided event uuid
     *
     * @param        $state
     * @param string $eventUuid
     *
     * @return int
     */
    public function updateUserDnd($state, $eventUuid) {
        $authService = KctUserAuthorizationService::getInstance();
        if ($authService->isUserEventMember($eventUuid)) {
//            EventUser::where('event_uuid', $eventUuid)
//                ->where('user_id', Auth::user()->id)
//                ->update(['state' => $state]);
            $this->eventUserRepo->getUserByEventUuidAndUserId($eventUuid, Auth::user()->id)->update(['state' => $state]); // todo event user
        }
        return (int)$state;
    }

    public function getDefaultHost($request) {
        $subDomain = $this->getSubDomain($request);
        $subDomain = $subDomain != '' ? "$subDomain." : $subDomain;
        return $subDomain . config("kctuser.default.front_domain");
    }

    public function eventsForPilotsAndOwners($builder) {
        return $builder->whereHas('group', function ($q){
            $q->whereHas('groupUser', function ($q){
                $q->where('user_id', Auth::id())->whereIn('role', [2, 3]);
            });
        });
    }

    public function getSubDomain($request) {
        $subDomain = explode('.', $request->getHost());
        if (count($subDomain) > 1) {
            $subDomain = $subDomain[0];
        } else {
            $subDomain = '';
        }
        return $subDomain;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the user active event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return array|null
     */
    public function getUserActiveEventUuid(Request $request): ?array {
        if ($request->user('api')) {
            $event = $this->userRepo()->eventRepository->findUserActiveEventUuid();
            if ($event) {
                return [
                    'uuid'     => $event->event_uuid,
                    'end_time' => $this->getCarbonByDateTime($event->end_time),
                ];
            }
        }
        return null;
    }

    public function isAdmin() {
        $setting = Setting::where('setting_key', 'event_settings')->first();
        $org1 = $org2 = null;
        if ($setting) {
            $decode = json_decode($setting->setting_value);
            $org1 = isset($decode->event_org_setting->default_organiser) ? $decode->event_org_setting->default_organiser : null;
            $org2 = isset($decode->event_virtual_org_setting->default_organiser) ? $decode->event_virtual_org_setting->default_organiser : null;
        }
        if (!(Auth::user()->role == 'M1' || Auth::user()->role == 'M0' || in_array(Auth::user()->id, [$org1, $org2]))) {
            return false;
        }
        return true;
    }

    /**
     * @warn this will return true even event not found so handle if event not found
     *
     * @param $eventId
     * @param string $column
     * @return bool
     */
    public function isEventAdmin($eventId, $column = 'id') {
        $event = Event::where($column, $eventId)->first();
        if ($event) {
            return $this->isWorkshopMember($event->workshop_id, [1, 2]);
        }
        return true;
    }

    public function isWorkshopMember($wid, $roles) {
        return (bool)WorkshopMeta::where('workshop_id', $wid)
            ->where('user_id', Auth::user()->id)
            ->whereIn('role', $roles)
            ->first();
    }


    public function resolveUser($user) {
        // TODO: Implement resolveUser() method.
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To fetch all the event group wise.
     * -----------------------------------------------------------------------------------------------------------------
     * @param $request
     * @param $groupId
     * @param $op
     * @return Builder
     */
    public function getGroupEvents($request, $groupId, $op): Builder {
        $order = $this->getEventsListOrder($request);
        $eventUsers = function ($q) {
            $q->where('users.id', Auth::user()->id);
        };
        $notDraftEvents = function ($q) {
            $q->where('event_status', 2);
        };
        $relations = [
            'eventUsers' => $eventUsers,
            'isHostOfAnySpace',
            'selfUserBanStatus',
            'organiser',
        ];

        if ($op == '>') {
            // future events, fetching the links as well
            $relations[] = 'moderatorMoments';
            $relations[] = 'speakerMoments';
        }
        $grpEventBuilder = Group::with(['events' => function ($q) use ($order, $notDraftEvents) {
            $q->whereDoesntHave('draft', $notDraftEvents)
                ->where('end_time', '>', Carbon::now()->toDateTimeString());
            $q->orderBy('start_time', $order);
        }, 'events.eventUsers'                   => $eventUsers,
            'events.isHostOfAnySpace',
            'events.selfUserBanStatus',
            'events.organiser',
            'events.moments',
            'events.draft',
            'events.eventUserRelation'
        ]);
        if ($op == '<') { // < means past event and in past we need to show only those events in which user is participated
            $grpEventBuilder = $grpEventBuilder->whereHas('eventUsers', $eventUsers);
        }
        if ($groupId) {
            $grpEventBuilder->whereIn('id', $groupId);
        }
        return $grpEventBuilder;
    }

    /**
     * @inheritDoc
     */
    public function isUserInGroup() {
        $group = $this->adminServices()->groupService->getUserCurrentGroup(Auth::id());
        $user = $this->adminRepo()->groupUserRepository->isUserPartOfGroup($group->id);
        if($user){
            return $user;
        }
        else{
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function isMultiGroupEnabled(): bool{
        $accountSetting = $this->adminRepo()->groupRepository->getAccountSettings();
        if($accountSetting['setting_value']['allow_multi_group'] == 1){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function getUserGroups(){
        if($this->isMultiGroupEnabled()){
            $isUserSuperPilotOrOwner = $this->adminRepo()->groupUserRepository->isUserSuperPilotOrOwner();
            if($isUserSuperPilotOrOwner){
                $groups = $this->adminRepo()->groupRepository->fetchAllGroups();
            }else{
                $groups = $this->adminRepo()->groupRepository->getAllGroups();
            }
        }
        else{
            $defaultGroup = $this->adminRepo()->groupRepository->getDefaultGroup();
            $user = $this->adminRepo()->groupUserRepository->isUserPartOfGroup($defaultGroup->id);
            if($user){
                $groups = $defaultGroup;
            }
            else{
                $groups = null;
            }
        }
        return $groups;
    }

    /**
     * @inheritDoc
     */
    public function isUserMemberOfGroup($groupId, $userId){
        return $this->adminRepo()->groupUserRepository->isUserMemberOfGroup($groupId, $userId);
    }

    /**
     * @inheritDoc
     */
    public function hasUserRegisteredEvent($eventUuid,$userId): bool {
        $event = $this->userRepo()->eventRepository->findParticipant($eventUuid,$userId);
        if ($event && $event->is_joined_after_reg){
            return true;
        }
        return false;
    }
}
