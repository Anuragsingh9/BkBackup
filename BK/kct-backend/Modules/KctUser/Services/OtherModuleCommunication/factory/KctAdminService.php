<?php


namespace Modules\KctUser\Services\OtherModuleCommunication\factory;


use Exception;
use Illuminate\Support\Facades\Auth;
use Modules\KctAdmin\Entities\Event;
use Modules\KctAdmin\Entities\EventUser;
use Modules\KctAdmin\Entities\Group;
use Modules\KctAdmin\Entities\Setting;
use Modules\KctAdmin\Entities\Space;
use Modules\KctAdmin\Repositories\IEventInfoRepository;
use Modules\KctAdmin\Repositories\IEventUserRepository;
use Modules\KctAdmin\Repositories\IKctSpaceRepository;
use Modules\KctAdmin\Entities\EventTag;
use Modules\KctAdmin\Repositories\IOrganiserTagsRepository;
use Modules\KctAdmin\Repositories\IKctDummyUserRepository;
use Modules\KctAdmin\Repositories\ISettingRepository;
use Modules\KctAdmin\Traits\KctHelper;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\KctUser\Services\OtherModuleCommunication\IKctAdminService;
use Modules\KctUser\Traits\Repo;
use Modules\KctUser\Traits\Services;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will be managing the kct admin services
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface KctAdminService
 * @package Modules\KctUser\Services\OtherModuleCommunication\factory
 */
class KctAdminService implements IKctAdminService {

    use Services, Repo, ServicesAndRepo;
    use KctHelper;


    private ISettingRepository $settingRepo;

    public function __construct() {
        $this->settingRepo = app(ISettingRepository::class);
    }

    /**
     * @inheritDoc
     */
    public function getSetting($key, $groupId): ?array {
        $setting = $this->settingRepo->getSettingByKey($key, $groupId);
        return $setting ? $setting->setting_value : null;
    }

    /**
     * @inheritDoc
     */
    public function getEventById($eventId) {
        return $this->eventRepository->findById($eventId);
    }

    /**
     * @inheritDoc
     */
    public function findEvent($eventUuid) {
        return $this->adminRepo()->eventRepository->findByEventUuid($eventUuid);
    }

    /**
     * @inheritDoc
     */
    public function filterEventsByTime($carbon) {
        //        $event = Event::where('type', 'virtual')
//            ->where(function ($q) use ($carbon) {
//                $q->where('date', '>', $carbon->toDateString());
//                $q->orWhere(function ($q) use ($carbon) {
//                    $q->where('date', '=', $carbon->toDateString());
//                    $q->where('end_time', '>', $carbon->toTimeString());
//                });
//            })->orderBy('date', 'asc')->orderBy('start_time')
//            ->limit(5)->get();
        return $this->eventRepository->filterEventsByTime($carbon);
    }

    /**
     * @inheritDoc
     */
    public function getEventDataForQSS($eventUuid) {
        return $this->eventRepository->getEventDataForQSS($eventUuid);
    }

    /**
     * @inheritDoc
     */
    public function getEventDataBeforeReg($eventUuid) {
        return $this->eventRepository->getEventDataBeforeReg($eventUuid);
    }

    /**
     * @inheritDoc
     */
    public function getEventWhereHasSpace($spaceUuid) {
        //            $event = Event::whereHas('spaces', function ($q) use ($spaceUuid) {
//                $q->where('space_uuid', $spaceUuid);
//            })->first();
        return $this->eventRepository->getEventWhereHasSpace($spaceUuid);
    }

    /**
     * @inheritDoc
     */
    public function findIsUserHostByHostId($eventUuid, $userId) {
        //        return (bool) Event::whereHas('spaces', function($q) {
//            $q->whereHas('hosts', function ($q) {
//                $q->where('host_id', Auth::user()->id);
//            });
//        })->where('event_uuid', $eventUuid)
//            ->first();
        return $this->eventRepository->findIsUserHostByHostId($eventUuid, $userId);
    }

    /**
     * @inheritDoc
     */
    public function findEventByWorkshopId($workshopId) {
        return $this->eventRepository->findEventByWorkshopId($workshopId);
    }

    /**
     * @inheritDoc
     */
    public function getEventWithSpaceAndConversations($spaceCondition, $eventUserCondition, $dummy, $eventUuid) {
        return $this->eventRepository->getEventWithSpaceAndConversations($spaceCondition, $eventUserCondition, $dummy, $eventUuid);
    }

    /**
     * @inheritDoc
     */
    public function findVirtualEvent($eventUuid) {
        return $this->eventRepository->findVirtualEvent($eventUuid);
    }

    /**
     * @inheritDoc
     */
    public function addUserToEvent($eventUuid, $userId, $spaceUuid = null, array $roles = []): ?EventUser {
        return $this->adminRepo()->eventRepository->addUserToEvent($eventUuid, $userId, $spaceUuid, $roles);
    }

    /**
     * @inheritDoc
     */
    public function findSpaceByUuid($spaceUuid): ?Space {
        return $this->adminRepo()->kctSpaceRepository->findSpaceByUuid($spaceUuid);
    }

    /**
     * @inheritDoc
     */
    public function getEventDummyUsers($eventUuid) {
        return $this->kctSpaceRepository->getEventDummyUsers($eventUuid);
    }

    /**
     * @inheritDoc
     */
    public function getSpaceWithEvent($spaceUuid) {
        return $this->adminRepo()->kctSpaceRepository->findSpaceByUuid($spaceUuid);
    }

    /**
     * @inheritDoc
     */
    public function loadSpaceWithRelationBySpaceUuid($conversation, $spaceUuid) {
        return $this->kctSpaceRepository->loadSpaceWithRelationBySpaceUuid($conversation, $spaceUuid);
    }

    /**
     * @inheritDoc
     */
    public function getDefaultSpace($eventUuid) {
        return $this->adminRepo()->kctSpaceRepository->getDefaultSpace($eventUuid);
    }

    /**
     * @inheritDoc
     */
    public function getSpaceByUserIdAndSpaceUuid($spaceUuid, $userId) {
//        $space = EventSpace::with(['spaceUsers' => function ($q) {
//            $q->where('user_id', '!=', Auth::user()->id);
//        }])->where('space_uuid', $spaceUuid)->first();
        return $this->kctSpaceRepository->getSpaceByUserIdAndSpaceUuid($spaceUuid, $userId);
    }


    /**
     * @inheritDoc
     */
    public function updateUserByEventUuidAndUserId($eventUuid, $userId) {
        //        return EventUser::where('event_uuid', $eventUuid)->where('user_id', $userId)->update([
//            'is_joined_after_reg' => 0,
//        ]);
        return $this->eventUserRepository->updateUserByEventUuidAndUserId($eventUuid, $userId);
    }


    /**
     * @inheritDoc
     */
    public function getAllEventTag() {
        return $this->adminRepo()->orgTagsRepository->getByGroupId(1);
    }

    /**
     * @inheritDoc
     */
    public function getEventDummyUserForConv($dummyUserId, $eventUuid, $conversationUuid) {
//        EventDummyUser::where('dummy_user_id', $request->dummyUserId)
//                    ->where('event_uuid', $eventUuid)->update(['current_conv_uuid' => $convUuid])
        return $this->kctDummyUserRepository->getEventDummyUserForConv($dummyUserId, $eventUuid, $conversationUuid);
    }

    /**
     * @inheritDoc
     */
    public function getDummyUserDataInsideConv($eventUuid, $dummyUserId) {
        return $this->adminRepo()->eventRepository->findDummyRelation($eventUuid, $dummyUserId);
    }

    /**
     * @inheritDoc
     */
    public function getDummyUserConversation($conversationUuid) {
        //            $dummyUsers = EventDummyUser::where('current_conv_uuid', $conversation->uuid)->get();
        return $this->kctDummyUserRepository->getDummyUserConversation($conversationUuid);
    }

    /**
     * @inheritDoc
     */
    public function getConferenceById($conferenceId) {
//        return KctConference::where('conference_id', $conferenceId)->first();
        return $this->conferenceRepository->getConferenceById($conferenceId);
    }

    /**
     * @inheritDoc
     */
    public function findConferenceId($eventUuid, $type) {
        //        $conference = KctConference::where(function ($q) use ($event, $type) {
//            $q->where('event_uuid', $event->event_uuid);
//            $q->where('conference_time_block', $type);
//            $q->where('is_active', 1);
//        })->first();
        return $this->conferenceRepository->findConferenceId($eventUuid, $type);
    }

    /**
     * @inheritDoc
     */
    public function getConferenceByEventUuid($eventUuid) {
        //        $conference = KctConference::where('event_uuid', $event->event_uuid)->first();
        return $this->eventUserRepository->getConferenceByEventUuid($eventUuid);
    }

    /**
     * @inheritDoc
     */
    public function getEvenUser($eventUuid, $userId) {
        //        $eventUser = EventUser::where('event_uuid', $eventUuid)->where('user_id', Auth::user()->id)->first(); //todo event user
        return $this->eventUserRepository->getEventUser($eventUuid, $userId);
    }

    /**
     * @inheritDoc
     */
    public function getExistingUserTag($userId) {
        //        $used_tag=EventTag::whereExists(function ($query) use($user) {
//            $query->select(DB::raw(1))
//                ->from('event_tag_metas')
//                ->whereColumn('event_tag_metas.tag_id', 'event_tags.id')
//                ->where('event_tag_metas.user_id', $user->id);
//        })->where('is_display',1)->orderBy('name','asc')->get(['id','name']);
        return $this->orgTagsRepository->getExistingUserTag($userId);
    }

    /**
     * @inheritDoc
     */
    public function getNotExistingUserTag($userId) {
//        $unused_tag=EventTag::whereNotExists(function ($query) use($user) {
//            $query->select(DB::raw(1))
//                ->from('event_tag_metas')
//                ->whereColumn('event_tag_metas.tag_id', 'event_tags.id')
//                ->where('event_tag_metas.user_id', $user->id);
//        })->where('is_display',1)->orderBy('name','asc')->get(['id','name']);
        return $this->orgTagsRepository->getNotExistingUserTag($userId);

    }

    /**
     * @inheritDoc
     */
    public function prepareBuilderForEventList($eventUsers, $op) {
//        $builder = Event::with([ // todo
//            'eventUsers' => $eventUsers,
//            'users',
//            'isHostOfAnySpace',
//            'selfUserBanStatus',
//        ])
//            ->where(function ($q) use ($op) {
//                $q->orWhere('date', $op, date('Y-m-d'));
//                $q->orWhere(function ($q) use ($op) {
//                    $q->where('date', '=', date('Y-m-d'));
//                    $q->where('end_time', $op, date('H:i:s'));
//                });
//            })
//            ->where('type', 'virtual')
//            ->join('eventables as ev', 'ev.event_id', '=', 'event_info.id')
//            ->leftJoin('users as u', 'u.id', '=', 'ev.eventable_id')
//            ->select('event_info.*', 'u.id as user_id', 'u.fname', 'u.lname', DB::raw("CONCAT(fname, ' ' , lname) as organiser_name"));
        return $this->eventRepository->prepareBuilderForEventList($eventUsers, $op);
    }

    /**
     * @inheritDoc
     */
    public function updateUserDnd($eventUuid, $userId) {
        //            EventUser::where('event_uuid', $eventUuid)
//                ->where('user_id', Auth::user()->id)
//                ->update(['state' => $state]);
        return $this->eventUserRepository->updateUserDnd($eventUuid, $userId);
    }

    /**
     * @inheritDoc
     */
    public function findUserActiveEventUuid($user) {
        //            $events = Event::with('spaces')
//                ->whereHas('eventUsers', function ($q) use ($user) {
//                    $q->where('users.id', $user->id);
//                })
//                ->where('date', Carbon::now()->toDateString())
//                ->where('type', config('events.event_type.virtual'))
//                ->orderBy('id', 'desc')
//                ->get();
        return $this->eventRepository->findUserActiveEventUuid($user);
    }

    /**
     * @inheritDoc
     */
    public function validateDummyUserWithEvt($space, $dmyUserId) {
        return $this->adminRepo()->eventRepository->findDummyRelationBySpaceUuid($space->space_uuid, $dmyUserId);
    }

    /**
     * @inheritDoc
     */
    public function findDummyUserForSpace($spaceUuid, $dmyUserId) {
        return $this->adminRepo()->eventRepository->findDummyRelationBySpaceUuid($spaceUuid, $dmyUserId);
    }

    /**
     * @inheritDoc
     */
    public function findUserPassedJoinEvent($eventUuid, $userId) {
        //        return EventUser::where('event_uuid', $eventUuid)->where('user_id', Auth::user()->id)->where('is_joined_after_reg', 1)->count();
        return $this->eventUserRepository->findUserPassedJoinEvent($eventUuid, $userId);
    }

    /**
     * @inheritDoc
     */
    public function getAllEventSpace($eventUuid) {
        //            $spaces = EventSpace::where('event_uuid', $eventUuid)->get();
        return $this->kctSpaceRepository->getAllEventSpace($eventUuid);
    }

    /**
     * @inheritDoc
     */
    public function findHostByUserId($event, $usersId, $column) {
        //        return Event::whereHas('spaces.hosts', function ($q) use ($usersId) {
//            $q->whereIn('host_id', $usersId);
//        })
//            ->where($column, $event)
//            ->first();
        return $this->eventRepository->findHostByUserId($event, $usersId, $column);
    }

    /**
     * @inheritDoc
     */
    public function countDuoSpace($eventUuid, $exclude) {
        //        return (bool)EventSpace::where('event_uuid', $eventUuid) // todo event space
//            ->where('is_duo_space', 1)
//            ->where('space_uuid', '!=', $exclude)
//            ->count();
        return $this->kctSpaceRepository->countDuoSpace($eventUuid, $exclude);
    }

    /**
     * @inheritDoc
     */
    public function updateOrCreateEventUser($userId, $eventUuid, $isP, $isM) {
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
        return $this->eventUserRepository->updateOrCreateEventUser($userId, $eventUuid, $isP, $isM);
    }

    /**
     * @inheritDoc
     */
    public function getUserByEventUuidAndUserId($eventUuid, $userId) {
        //        $eventUser = EventUser::where('event_uuid', $eventUuid)->where('user_id', Auth::user()->id)->first();
        return;
    }

    /**
     * @inheritDoc
     */
    public function getHostById($userId, $eventUuid) {
//        Event::with(['spaces.hosts' => function ($q) use($userId){
//            $q->where('host_id',$userId);
//        }])->where('event_uuid',$eventUuid)->first();
        return $this->getHostById($userId, $eventUuid);
    }

    /**
     * @inheritDoc
     */
    public function countPassedJoinEventUser($eventUuid, $userId) {
        //        return EventUser::where('event_uuid', $eventUuid)->where('user_id', Auth::user()->id)->where('is_joined_after_reg', 1)->count();
        return $this->eventUserRepository->countPassedJoinEventUser($eventUuid, $userId);
    }

    /**
     * @inheritDoc
     */
    public function syncGroupSettings(int $groupId): int {
        return $this->adminServices()->groupService->syncGroupSettings($groupId);
    }

    /**
     * @inheritDoc
     */
    public function getEventImage(): ?string {
        $setting = $this->adminRepo()->settingRepository->getSettingByKey('event_image');
        $url = $setting->setting_value['event_image'] ?? null;
        $tenant = true;
        if ($url === config('kctadmin.constants.event_default_image_path')) {
            $tenant = false;
        }
        return $url ? $this->adminServices()->fileService->getFileUrl($url, $tenant) : null;
    }

    /**
     * @inheritDoc
     */
    public function getMomentEmbeddedUrl($moment): ?string {
        return $this->adminServices()->kctService->getMomentEmbeddedUrl($moment);
    }

    /**
     * @inheritDoc
     */
    public function getEventBroadcastingLinks(Event $event): array {
        return $this->adminServices()->coreService->prepareBroadcastingLinks($event);
    }

    /**
     * @inheritDoc
     */
    public function getLabels(int $groupId) {
        return $this->adminRepo()->labelRepository->getAll($groupId);
    }

    /**
     * @inheritDoc
     */
    public function isUserPilotOrOwner(): bool {
        return $this->adminRepo()->groupUserRepository->isUserPilotOrOwner();

    }

    /**
     * @inheritDoc
     */
    public function getUserCurrentGroupId() {
        $currentGroup = $this->adminServices()->groupService->getUserCurrentGroup(Auth::id());
        return $currentGroup->id;
    }

    /**
     * @inheritDoc
     */
    public function getCurrentUserGroupIds() {
        $groups = $this->adminServices()->groupService->getCurrentUserGroups(Auth::id());
        return $groups->pluck('group_id');
    }

    /**
     * @inheritDoc
     */
    public function getGroupIdByGroupKey($groupKey) {
        return $this->adminRepo()->groupRepository->getGroupByGroupKey($groupKey);
    }

    /**
     * @inheritDoc
     */
    public function getGroups() {
        return $this->adminRepo()->groupUserRepository->getGroups();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getDefaultGroup(): Group {
        return $this->adminRepo()->groupRepository->getDefaultGroup();
    }

    public function getAllGroup() {
        return $this->adminRepo()->groupRepository->fetchAllGroups();
    }

    /**
     * @param $groupIds
     * @return mixed
     */
    public function getGroupByIds($groupIds) {
        return $this->adminRepo()->groupRepository->getGroupByIds($groupIds);
    }

    /**
     * @inheritDoc
     */
    public function findEventGroup($eventUuid) {
        return $this->adminRepo()->eventRepository->findEventGroup($eventUuid);
    }

    public function fetchEventSceneryData($eventUuid, $sendAssetUrl) {
        return $this->adminServices()->dataFactory->fetchEventSceneryData($eventUuid, $sendAssetUrl);
    }
}
