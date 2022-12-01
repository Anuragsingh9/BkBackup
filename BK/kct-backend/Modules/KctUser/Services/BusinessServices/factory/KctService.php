<?php


namespace Modules\KctUser\Services\BusinessServices\factory;

use Carbon\Carbon;
use Exception;
use Hyn\Tenancy\Contracts\Hostname;
use Hyn\Tenancy\Environment;
use Hyn\Tenancy\Models\Website;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Modules\KctAdmin\Entities\Moment;
use Modules\KctAdmin\Entities\Space;
use Modules\KctUser\Entities\Conversation;
use Modules\KctUser\Entities\Event;
use Modules\KctUser\Entities\EventSpaceUser;
use Modules\KctUser\Entities\EventUserJoinReport;
use Modules\KctUser\Entities\OrganiserTagUser;
use Modules\KctUser\Entities\UserTag;
use Modules\KctUser\Events\ConversationDeleteEvent;
use Modules\KctUser\Events\ConversationLeaveEvent;
use Modules\KctUser\Exceptions\CustomValidationException;
use Modules\KctUser\Services\BusinessServices\IKctService;
use Modules\KctUser\Traits\KctHelper;
use Modules\KctUser\Traits\Repo;
use Modules\KctUser\Traits\Services;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will manage the user management functionality
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class EventRepository
 * @package Modules\KctAdmin\Repositories\factory
 */
class KctService implements IKctService {

    use Services, Repo, KctHelper;

    private ?Environment $tenant;

    public function __construct(Environment $tenant) {
        $this->tenant = $tenant;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To find the event version by the event model object
     * e.g. ini,ext,virtual (bj) -> version 1
     * virtual (non bj) -> version 2
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Event $event
     * @return int
     */
    public function findEventVersion($event) {
        $event = KctUserValidationService::getInstance()->resolveEvent($event);
        if ($event
            // event type must be virtual for version 2
            && $event->type == config('events.event_type.virtual') // todo

            // CHANGES: As from new requirements all the virutal event either with conference or without will follow new
            // interface so commented the below conditions

            // there should be bj data stored to indicate it doesn't follow bj
            // this is necessary to check as in int/ext we are not storing this data
//            && isset($event->bluejeans_settings['event_uses_bluejeans_event'])
            // the event must not follow bj
//            && $event->bluejeans_settings['event_uses_bluejeans_event'] == 0
        ) {
            return 2;
        }
        return 1;
    }

    function getS3Parameter($file_path, $type = NULL, $file_name = NULL) {
        /*
              $file_path = full file url with folder name
              `   $type =>  1: get file download url, 2: get file view url
              https://s3.ap-south-1.amazonaws.com/ops.sharabh.org/
             */

        $url = '';
        $config['Bucket'] = env('AWS_BUCKET');
        $config['Key'] = $file_path;

        $s3 = Storage::disk('s3');
        if ($s3->exists($file_path)) {
            if ($type == 1) {
                if ($file_name != NULL) {
                    $config['ResponseContentDisposition'] = 'attachment;filename="' . $file_name . '"';
                } else {
                    $config['ResponseContentDisposition'] = 'attachment';
                }
                $command = $s3->getDriver()->getAdapter()->getClient()->getCommand('GetObject', $config);
                $requestData = $s3->getDriver()->getAdapter()->getClient()->createPresignedRequest($command, '+5 minutes');

                $url = $requestData->getUri();
                return (string)$url;
            } else {
                return Storage::disk('s3')->url($file_path);
            }
        }
        return NULL;
    }

    /**
     * To prepare the available email tags
     *
     * @param $event
     * @param $userId
     * @return array
     */
    public function prepareEmailTags($event, $userId) {
        $hostname = $this->getHostname();
//        return KctUserEventService::getInstance()->prepareEmailTags($event, $userId, $hostname);
        return $this->baseService->eventService->prepareEventEmailTags($event, $userId, $hostname);
    }

    /**
     * @return Hostname
     */
    public function getHostname() {
        $tenancyModel = $this->getTenancy();
        return $tenancyModel->hostname();
    }

    /**
     * @return \Illuminate\Foundation\Application|mixed
     */
    public function getTenancy() {
        if (!$this->tenancy)
            $this->tenancy = app(\Hyn\Tenancy\Environment::class);
        return $this->tenancy;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the any setting by setting key and return the decoded setting
     * if will return null in case of empty setting value or setting not found
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $key
     * @param int $makeResultInArray // to indicate the result of decoded will be in object or in assoc array
     * @return mixed|null
     */
    public function getDecodeSetting($key, $makeResultInArray = 0) {
        $setting = Setting::where('setting_key', $key)->first(); // todo
        if ($setting && $setting->setting_value) {
            return json_decode($setting->setting_value, $makeResultInArray);
        }
        return null;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To send the event with spaces before the registration for showing the details of event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @return Event
     */
    public function getEventBeforeRegistration($eventUuid) {
//        $event = Event::with([
//            'spaces' => function ($q) {
//                $q->orderBy('order_id');
//            },
//            'spaces.spaceUsers',
//            'defaultSpace',
//        ])->where('event_uuid', $eventUuid)->where('type', 'virtual')->first();
        $event = $this->eventRepo->getEventDataBeforeReg($eventUuid)->first(); // todo event

        $event->spaces = KctUserSpaceService::getInstance()->filterSpacesWithMaxCapacity($event->spaces, $event, true);
        return $event;
    }

    /**
     * @inheritDoc
     */
    public function resetPassword($request) {
//        if (User::where('identifier', $request->identifier)->count() == 1) {
        if ($this->userServices()->userManagementService->checkUserByIdentifier($request->identifier) == 1) { //todo
//            User::where('identifier', $request->identifier)
//                ->update(['password' => Hash::make($request->password), 'identifier' => null]);
            $this->baseService->userManagementService->updatePassword($request->identifier, $request->password);
        } else {
        }
        return true;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method used for add the user tag
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @return array[]
     * @throws Exception
     */
    public function addUserTag($request) {
        $user = Auth::user();
        $existTag = OrganiserTagUser::where(['tag_id' => $request->tag_id, 'user_id' => $user->id])->first();
        if ($existTag) {
            throw new Exception("Tag already added");
        }
        $res = OrganiserTagUser::create(['tag_id' => $request->tag_id, 'user_id' => $user->id]);
        return $this->userServices()->userService->getUserTag($user, $request->event_uuid);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description get user tags
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return User
     * @throws CustomException
     * @throws \Modules\Cocktail\Exceptions\CustomValidationException
     */
    public function getUserTag() {
        $user = Auth::user();
        $used_tag = [];
        $unused_tag = [];
//        $used_tag=EventTag::whereExists(function ($query) use($user) {
//            $query->select(DB::raw(1))
//                ->from('event_tag_metas')
//                ->whereColumn('event_tag_metas.tag_id', 'event_tags.id')
//                ->where('event_tag_metas.user_id', $user->id);
//        })->where('is_display',1)->orderBy('name','asc')->get(['id','name']);
        $used_tag = $this->eventTagRepo->getExistingUserTag($user->id); // todo
//        $unused_tag=EventTag::whereNotExists(function ($query) use($user) {
//            $query->select(DB::raw(1))
//                ->from('event_tag_metas')
//                ->whereColumn('event_tag_metas.tag_id', 'event_tags.id')
//                ->where('event_tag_metas.user_id', $user->id);
//        })->where('is_display',1)->orderBy('name','asc')->get(['id','name']);
        $unused_tag = $this->eventTagRepo->getNotExistingUserTag($user->id); // todo

        return ['used_tag' => $used_tag, 'unused_tag' => $unused_tag];
    }

    /**
     * ----------------------------------------------------------------------------------------------------------------
     * @descripiton To delete the user tag
     * ----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @return array[]
     */
    public function deleteTagUser($request): array {
        $user = Auth::user();
        $res = OrganiserTagUser::where(['tag_id' => $request->tag_id, 'user_id' => $user->id])->delete();
        return $this->userServices()->userService->getUserTag($user, $request->event_uuid);
    }

    /**
     * This method can perform 3 task conditionally
     * 1. Update Existing Entity Relation with user
     * 2. Create new entity relation with user
     * 3. Create new entity and add that user to entity
     *
     * @param Request $request
     * @return EntityUser
     * @throws Exception
     */
    public function updateUserEntity($request) {

        $type = $request->input('entity_type');
        $newEntityId = $request->input('entity_id');
        $entityName = $request->input('entity_name');
        $position = $request->input('position');
        $entityOldId = $request->input('entity_old_id');

        $COMPANY = 1;

        if (!$newEntityId && $entityName) { // id not present  and name present, create a entity
            $newEntityId = $this->createNewEntity($entityName, $type)->id;
        }

        if ($type == $COMPANY) {
            $memberType = null;
            $this->deletePreviousEntityRelationship($COMPANY, Auth::user()->id);
            $entityOldId = $newEntityId; // in company there will be no old id as deleted previous relation so.
        } else { // currently union
            $memberType = config('cocktail.default.union_member_type');
            if ($entityOldId && $entityOldId != $newEntityId) { // if old id not equals means changing union else means updating position so in position update don't delete previous relation
                // deleting relation so if user already have relation with newEntityId ,
                // after updating there will be only updated relation with newEntityId
                $this->deletePreviousEntityRelationship(null, Auth::user()->id, [$newEntityId]);
            }
        }
        return $this->updateCreateEntityRelation($entityOldId, $newEntityId, $position, $memberType);
    }

    private function createNewEntity($name, $type) {
        return
            Entity::updateOrCreate([
                'long_name'      => $name,
                'entity_type_id' => $type,
                //'created_by'     => Auth::user()->id,
            ], [
                'long_name'      => $name,
                'short_name'     => $name,
                'entity_type_id' => $type,
                'created_by'     => Auth::user()->id,
            ]);
    }

    /**
     * Currently return value is not using anywhere
     * developer can change return value and will be not reflected
     *
     * this method can remove user from
     * either all entities of specific type if type given
     * or from a specific entity ids collection|array (type independent)
     *
     * @param integer $entityType
     * @param integer $userId
     * @param array $entityId
     * @return integer
     * @throws Exception
     */
    public function deletePreviousEntityRelationship($entityType, $userId, $entityId = []) {
        if ($entityType) {
            return EntityUser::with(['entity' => function ($q) use ($entityType) {
                $q->select("*");
                $q->where('entity_type_id', $entityType);
            }])->where('user_id', $userId)->whereHas('entity', function ($q) use ($entityType) {
                $q->select("*");
                $q->where('entity_type_id', $entityType);
            })->delete();
        } else if ($entityId) {
            return EntityUser::where('user_id', $userId)->whereIn('entity_id', $entityId)->delete();
        }
    }

    /**
     * To update the previous entity relation
     *
     * @param $oldEntityId
     * @param $newEntityId
     * @param $position
     * @param null $memberType
     * @return EntityUser
     */
    private function updateCreateEntityRelation($oldEntityId, $newEntityId, $position, $memberType = null) {
        return EntityUser::updateOrCreate([
            'entity_id' => $oldEntityId,
            'user_id'   => Auth::user()->id
        ], [
            'entity_id'       => $newEntityId,
            'entity_label'    => $position,
            'created_by'      => Auth::user()->id,
            'membership_type' => $memberType,
        ]);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to delete the profile picture and return the user badge response.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return User
     * @throws CustomException
     * @throws \Modules\KctUser\Exceptions\CustomValidationException
     */
    public function deleteProfilePic() {
        $user = Auth::user();
        $this->getCore()->fileDeleteBys3($user->avatar);
        $res = User::where('id', Auth::user()->id)->updatedeleteProfilePic(['avatar' => null]); //todo
        if (!$res) {
            throw new CustomException(null, "User Profile Delete Failed.");
        }
        return $this->getUserBadge(Auth::user()->id);
    }

    /**
     * @return CoreController
     */
    public function getCore() {
        if ($this->core) return $this->core;
        return $this->core = app(\App\Http\Controllers\CoreController::class);
    }

    /**
     * To search the entity in which user is not added
     *
     * @param $val
     * @param $type
     * @return Collection
     */
    public function searchEntity($val, $type) {
        if (strlen($val) >= 3) {
            return Entity::where(function ($q) use ($val) { // todo Entity
                $q->orWhere('long_name', 'LIKE', "%$val%");
                $q->orWhere('short_name', 'LIKE', "%$val%");
                $q->orWhere(DB::raw("CONCAT(`long_name`, ' ', `short_name`)"), 'LIKE', "%$val%");
                $q->orWhere('entity_description', 'LIKE', "%$val%");
            })
                ->where('entity_type_id', $type)
                ->whereDoesntHave('entityUser', function ($q) {
                    $q->where('user_id', Auth::user()->id);
                })->get();
        }
        return null;
    }

    public function trans($key, $attribute) {
        return $attribute
            ? __("kctuser::message.$key", ['attribute' => __("kctuser::words.$attribute")])
            : __("kctuser::message.$key");
    }

    /**
     * @param $param
     * @param $request
     * @return User
     * @throws CustomValidationException
     */
    public function updateUserProfile($param, $request) {
        if ($request->hasFile('avatar')) {
            $param['avatar'] = $this->uploadUserProfile($request->avatar);
        }
//        $update = User::where('id', Auth::user()->id)->update($param);
        $update = $this->userRepo->getUserById(Auth::user()->id)->update($param); //todo
        if (!$update) {
            throw new Exception();
        }
        return $this->getUserBadge(Auth::user()->id);
    }

    public function uploadUserProfile($image) {
        return $this->fileUploadToS3(
            config("kctuser.s3.user_avatar"),
            $image,
            'public');
    }

    public function fileUploadToS3($filePath, $file, $visibility) {
        $domain = KctService::getInstance()->getHostname()->fqdn;
        return $this->getCore()->fileUploadToS3(
            "$domain/$filePath",
            $file,
            $visibility
        );
    }

    /**
     *
     * ---------------------------------------------------------------------------------------------------------------------
     * @description To get the current set language and the available languages possible
     * ---------------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return array
     */
    public function getUserLang($request) {
        $lang = $this->getCurrentLang();
        return [
            'current'           => strtoupper($lang),
            'enabled_languages' => array_keys(config('kctuser.moduleLanguages')),
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the current possible lang
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return string
     */
    public function getCurrentLang() {
        $lang = null;
        if (request()->user('api')) {
            // get language from auth
            $lang = $this->getLangFromAuth();
        } else if ($hostname = app(\Hyn\Tenancy\Environment::class)->hostname()) {
            // get language from hostname
            $lang = $this->getLangFromHostname($hostname);
        }

        if (!$lang) {
            $lang = config('cocktail.default.lang');
        }
        return strtoupper($lang);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the language from current user setting
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return string|null
     */
    public function getLangFromAuth() {
        $user = request()->user('api');
        if (isset($user->setting)) {
            return $user->setting['lang'] ?? '';
        }
        return null;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the language from current hostname setting
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return string|null
     */
    public function getLangFromHostname($hostname) {
        $setting = AccountSettings::where('account_id', $hostname->id)->first();
        return !empty($hostname) && isset($setting->setting['lang']) ? $setting->setting['lang'] : '';
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for the getting user details(fname and lname).
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @return array|null
     */
    public function getUserDetails($request): ?array {
        if ($request->user('api')) {
            return [
                'fname' => $request->user('api')->fname,
                'lname' => $request->user('api')->lname,
            ];
        }
        return null;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @decription to find that if KCT Module is enabled or not
     * This will first check if event module is enabled or not then it will check kct module is enabled or not
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return bool
     */
    public function isModuleEnabled() {
        $setting = $this->getAccountSetting();
        return isset($setting['event_enabled'])
            && $setting['event_enabled']
            // in case module never enabled we may not have key in setting so checking by isset
            && isset($setting['event_settings']['keep_contact_enable'])
            && $setting['event_settings']['keep_contact_enable'];
    }

    public function getAccountSetting() {
        $hostname = $this->getHostname();
        $account = AccountSettings::where('account_id', $hostname->id)->first();
        return $account->setting;
    }

    /**
     * @inheritDoc
     */
    public function modifyConvReqForDummy($request) {
        if ($request->has('dummy_user_id') && $request->input('dummy_user_id')) {
            $request->merge([
                'user_id' => $request->input('dummy_user_id'),
            ]);
        }
        return $request;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To Check if event is dummy type the conversation should not have only dummy users left
     * if so then remove the conversation and tell node to inform all users that dummy users left the conversation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $conversation
     * @param $request
     * @throws Exception
     */
    public function validateRealUsersInConversation($conversation, $request=null) {
        if (!$this->isEventDummy($this->getEventByConversation($conversation))) {
            return;
        }
        $dummyCount = $this->countRealUsers($conversation);
        if ($dummyCount['realUserCount'] == 0) {
            // no real user present in conversation so delete it and tell all users that conversation deleted
            $space = $conversation->space;
            $this->userServices()->spaceService->deleteConversation($conversation);


            event(new ConversationDeleteEvent([
                'conversationId' => $conversation->uuid,
                'dummyUsersId'   => $dummyCount['dummyIds'],
                'namespace'      => $request ? $this->userServices()->kctService->getSubDomain($request) : '',
                'spaceId'        => $space ? $space->space_uuid : null,
                'eventId'        => $space ? $space->event_uuid : null,
            ]));
            return true;
        }
    }

    /**
     * @inheritDoc
     */
    public function isEventDummy($event): int {
        $event = $this->userServices()->validationService->resolveEvent($event);
        return (int)$event->event_settings["is_dummy_event"] ?? 0;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the event by conversation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Conversation $conversation
     * @return Event|null
     */
    public function getEventByConversation($conversation) {
        $space = $conversation ? $conversation->space : null;
        if ($space) {
            $space->load('event');
            return $space->event;
        }
        return null;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To count the real users.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $conversation
     * @return array
     */
    public function countRealUsers($conversation) {
        $count = 0;
        $dummyIds = [];

        foreach ($conversation->users as $u) {
            if ($u->is_dummy != 1) {
                $count++;
            } else {
                $dummyIds[] = $u->id;
            }
        }

        return [
            'realUserCount' => $count,
            'dummyIds'      => $dummyIds,
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To change the conversation type. This is done in following steps-
     * 1. Get the conversation
     * 2. Load the conversation data
     * 3. Update the conversation type, if updated return Conversation data.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @return Conversation|null
     * @throws CustomValidationException
     */
    public function changeConversationType($request): ?Conversation {
        $conversation = $this->userRepo()->convRepository->getConversation($request->conversation_uuid);
        if (!$conversation) {
            return null;
        }
        $space = $conversation->space;
        if ($space) {
            $eventUuid = $space->event_uuid;
            $conversation->load(['users',
                'users.eventUser' => function ($q) use ($eventUuid) {
                    $q->where("event_uuid", $eventUuid);
                }]);
        }
        $isHost = $this->userServices()->validationService->isUserSpaceHost($space, Auth::user()->id);
        if ($isHost) {
            // if space host breaking isolation then set private by null
            $conversation->private_by = $request->is_private ? Auth::user()->id : null;
        } else {
            // current user is not host check if user is trying to break isolation
            if ($conversation->private_by && in_array($conversation->private_by, $space->hosts->pluck('id')->toArray())) {
                throw new CustomValidationException('only_host_change_iso', null, 'message');
            }
        }
        $conversation->is_private = $request->is_private;
        $conversation->update();
        return $conversation;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the dummy users of a event spaces
     * This will return each space with respective dummy users
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @return Collection
     */
    public function getEventDummyUsers($eventUuid) {
//        return EventSpace::with('dummyRelations.dummyUsers')->where('event_uuid', $eventUuid)->get();
        return $this->userRepo()->eventRepository->getEventDummyUsers($eventUuid)->get();// todo
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to get the data for the front user side
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return mixed|\Modules\Events\Entities\Event
     */
    public function getEventDataForUser($event) {
        return KctUserValidationService::getInstance()->resolveEvent($event);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @decription the meta data for the api's so front team can identify the current event belongs to which version
     * so they can load the component accordingly
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return array
     */
    public function metaForEventVersion($event) {
        $event = KctUserValidationService::getInstance()->resolveEvent($event);
        return [
            'event_version' => KctService::getInstance()->findEventVersion($event)
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the embedded url for event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return string|null
     */
    public function getEmbeddedUrl($event): ?string {
        if ($event && $moment = $this->getEventCurrentMoment($event)) {
            return $this->userServices()->adminService->getMomentEmbeddedUrl($moment);
        }
        return null;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the event current moment
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return Moment|null
     */
    public function getEventCurrentMoment($event): ?Moment {
        $current = Carbon::now();
        return $event->moments()
//            ->where('start_time', '<=', $current->toDateTimeString())
//            ->where('end_time', '>', $current->toDateTimeString())
            ->whereIn('moment_type', [2, 3, 4])
            ->first();
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This will take event as input and return the conference type of event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param \Modules\Events\Entities\Event $event
     * @return string|null
     */
    public function findEventConferenceType($event) {
        $event = \Modules\Events\Service\ValidationService::getInstance()->resolveEvent($event);
        $conference = KctConference::where('event_uuid', $event->event_uuid)->first(); // todo

        if (isset($conference)) {
            if ($conference->conference_type == 2)
                return 'zoom';
            else
                return 'bj';
        }
        return null;
    }

    public function findEventConferenceTimeType($event) {
        $start = Carbon::parse("{$event->date} {$event->start_time}")->timestamp;
        $end = Carbon::parse("{$event->date} {$event->end_time}")->timestamp;

        $openingHours = $event->event_fields['opening_hours'];
        $before = $openingHours['before'] * 60;
        $after = $openingHours['after'] * 60;
        $current = Carbon::now()->timestamp;
        if (($start - $before) <= $current && $current < $start) {
            return ['time_block' => 1];
        } else if ($start <= $current && $current < $end) {
            return ['time_block' => 2];
        } else if (($end) <= $current && $current < ($end + $after)) {
            return ['time_block' => 3];
        }
        return null;
    }

    public function findEventConferenceId($event, $type = null) {
        $conference = KctConference::where(function ($q) use ($event, $type) { // todo
            $q->where('event_uuid', $event->event_uuid);
            $q->where('conference_time_block', $type);
            $q->where('is_active', 1);
        })->first();
        return $conference ? $conference->conference_id : null;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the conference factory service as singleton.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return ExternalEventFactory
     */
    public function getConferenceFactory() {
        if (!$this->conferenceService) {
            $this->conferenceService = app()->make(ExternalEventFactory::class);
        }
        return $this->conferenceService;
    }

    public function getBannedUser($user_id, $ban_id) {
//        $getUser = UserBan::where('user_id', $user_id)->where('ban_type', 'event')->where('banable_id', $ban_id)->first();
        $getUser = $this->banUserRepo->getBanUserByIdAndBanableId($user_id, $ban_id)->first(); //todo
        if ($getUser) {
            return $getUser;
        }
        return false;
    }

    /**
     * @param $request
     * @return bool
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function storeBanUser($request) {
        $data = $this->getDataFactory()->prepareBanUserDetails($request->user_id, $request->severity, $request->ban_reason);
        $event = KctUserValidationService::getInstance()->resolveEvent($request->event_uuid);
        $event->banUser()->create($data);
        if ($event) {
            return true;
        }
        return false;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare data for storing ban user
     * -----------------------------------------------------------------------------------------------------------------
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getDataFactory() {
        $this->dataService = app()->make(IDataService::class);
        return $this->dataService;
    }

    /**
     *
     * @inheritDoc
     */
    public function getRedirectUrl($request, $type, array $replace = []): string {
        $baseUrl = $this->getDefaultHost($request);
        switch ($type) {
            case 'email_verify':
                $path = str_replace(array_keys($replace), array_values($replace), config('kctuser.front_path.email_verify'));
                return env('HOST_TYPE', 'https://') . "$baseUrl/$path";
            case 'quick-login':
                $path = str_replace(array_keys($replace), array_values($replace), config('kctuser.front_path.quick_login'));
                return env('HOST_TYPE', 'https://') . "$baseUrl/$path";
            case 'event-list':
                $path = config('cocktail.front_path.event_list');
                return env('HOST_TYPE', 'https://') . "$baseUrl/$path";
            case 'event-register':
                $path = str_replace(array_keys($replace), array_values($replace), config('kctuser.front_path.event_register'));
                return env('HOST_TYPE', 'https://') . "$baseUrl/$path";
            case 'quick_user_info':
                $path = str_replace(array_keys($replace), array_values($replace), config('kctuser.front_path.quick_user_info'));
                return env('HOST_TYPE', 'https://') . "$baseUrl/$path";
            default:
                return env('HOST_TYPE', 'https://') . "$baseUrl";
        }
    }

    /**
     * @param $request
     * @return string
     */
    public function getDefaultHost($request) {
        $subDomain = $this->getSubDomain($request);
        $subDomain = $subDomain != '' ? "$subDomain." : $subDomain;
        return $subDomain . env("APP_FRONT_HOST");
    }

    /**
     * @param $request
     * @return mixed|string
     */
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
     * @description To get the setting of custom graphics
     * @note in case of setting not found it will add a new setting with default values
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function getCustomGraphicsSetting() {
        $settingAfterDecode = KctService::getInstance()->getDecodeSetting(config('kctuser.setting_keys.event_custom_graphics'));

        if (!$settingAfterDecode) {
            $this->setDefaultCustomGraphics();
            $settingAfterDecode = KctService::getInstance()->getDecodeSetting(config('kctuser.setting_keys.event_custom_graphics'));
        }
        // todo verify each key if new introduced

//        $settingAfterDecode = $this->validateCustomizationKeys($settingAfterDecode);
        return $settingAfterDecode;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To set the default values to the custom graphics setting key
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function setDefaultCustomGraphics() {
        $defaults = config('kctuser.default.custom_graphics');

        $defaultValues = [];
        // this will add all the keys present
        foreach ($defaults as $keysArray) {
            $defaultValues = array_merge($defaultValues, $keysArray);
        }

        $accountSettings = getSettingData('graphic_config', 1); // todo

        // modifying some values as these are dynamic values to get and set from current setting at time of updating
        $defaultValues['text_color'] = $accountSettings->color2;
        $defaultValues['event_color_1'] = $accountSettings->color1;
        $defaultValues['event_color_2'] = $accountSettings->color2;
        $defaultValues['event_color_3'] = $accountSettings->color2;
        $defaultValues['join_text_color'] = $accountSettings->color2;

        Setting::updateOrCreate(
            ['setting_key' => config('cocktail.setting_keys.event_custom_graphics')],
            ['setting_value' => json_encode($defaultValues)]
        );

    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the response for the graphics customization
     * here all the keys of the graphics will be prepared which are used to customize the graphics settings in front end
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $setting
     * @return array
     */
    public function prepareCustomizationResource($setting) {

        $defaults = config('kctuser.default.custom_graphics');

        // this will check the value is present in setting or not in case not it will return the default value
        $customIsset = function ($value, $valueIfNotFound) use ($setting) {
            return isset($setting->$value) ? $setting->$value : $valueIfNotFound;
        };

        $data = [];

        foreach ($defaults as $keysArray) {
            // now in keys array there will be specific type of keys like, color, checkboxes keys etc.
            foreach ($keysArray as $key => $defaultValue) {
                $data[$key] = $customIsset($key, $defaultValue);
            }
        }

        return $data;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To send the event data for the quick sign in signup process
     * @note in case of user is not member in current space, default space will send
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @return \Modules\Events\Entities\Event
     */
    public function getEventForQss($eventUuid) {
//        $event = Event::with(['currentSpace', 'spaces' => function ($q) {
//            $q->with('spaceUsers');
//            $q->orderBy('order_id');
//            $q->where('is_duo_space', 0);
//        }, 'eventUserRelation'                         => function ($q) {
//            $q->where('user_id', Auth::user()->id);
//        }])->where('event_uuid', $eventUuid)->first();
        $event = $this->eventRepo->getEventDataForQSS($eventUuid)->first(); // todo event
        if (!$event->currentSpace) {
            // user is not member of event so no current space
            // as requirement send default space as current if not member
            $event->currentSpace = $event->spaces->first();
        }
        $event->spaces = DataMapService::getInstance()->removeFullSpaces($event->spaces);
        return $event;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the invitees by the current user for the event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @return Collection
     */
    public function getUserEventInvites($eventUuid): Collection {
        $invites = $this->userRepo()->userRepository->getUserEventInvites($eventUuid);
        return $this->userRepo()->userRepository->getInvitedEmailCount($invites);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To separate the email with existing in database for invite purpose
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $data
     * @return array|array[]
     */
    public function separateExistingEmailForInvite($data): array {
        $emails = Arr::pluck($data, 'email');
        $existingUsers = $this->userServices()->userManagementService->getUsersByEmail($emails);
        $newData = [
            'existingUsers' => [],
            'newUsers'      => [],
        ];
        foreach ($data as $user) {
            if ($u = $existingUsers->where('email', $user['email'])->first()) {
                // as user already exists so just putting user object as it is
                $newData['existingUsers'][] = $u;
            } else {
                $newData['newUsers'][] = $user;
            }
        }
        return $newData;
    }

    /**
     * @param $lang
     * @param null $user
     * @return User|null;
     */
    public function updateUserLanguage($lang, $user = null) {
        $user = $user ? $user : Auth::user();
        if ($lang) {
            $data = $user->setting ?: ['lang' => null];
            $data['lang'] = strtoupper($lang);
            $user->setting = $data;
            $user->update();
            App::setLocale(strtolower($lang));
        }
        return $user;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To mark the provided user as not filled join event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @param $userId
     * @return bool
     */
    public function markUserAsFirstLogin($eventUuid, $userId) {
//        return EventUser::where('event_uuid', $eventUuid)->where('user_id', $userId)->update([
//            'is_joined_after_reg' => 0,
//        ]);
        return $this->baseService->adminService->getUserByEventUuidAndUserId($eventUuid, $userId); //todo event user
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To find if the user is logging in first time after the registration to that event or not.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @return bool
     */
    public function isFirstLoginToEventAfterRegistration($eventUuid) {
//        $eventUser = EventUser::where('event_uuid', $eventUuid)->where('user_id', Auth::user()->id)->first();
        $eventUser = $this->baseService->adminService->getUserByEventUuidAndUserId($eventUuid, Auth::user()->id); //todo event user
        return !$eventUser->is_joined_after_reg;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get the dummy users for the event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return \Modules\Events\Entities\Event
     */
    public function getDummyUsers($event) {
        if ($event->event_settings["is_dummy_event"] ?? false) {
            $event->currentSpace = $this->getDummyUsersForSpace($event->currentSpace);
            $event = $this->mapDummyUsersToEvent($event);
        }
        return $event;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get the dummy users for the specific space
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Space|null $space
     * @return mixed
     */
    public function getDummyUsersForSpace(?Space $space) {
//        $dummyUsers = DummyUsers::whereHas('eventDummyUser', function ($q) use ($space) {
//            $q->where('space_uuid', $space->space_uuid);
//            // current conversation uuid must be null as these records will be fetched along with the conversation
//            $q->whereNull('current_conv_uuid');
//        })->get();
        $dummyUsers = $space->dummyRelations()->whereNull('current_conv_uuid')->get()->pluck('dummyUser');
        $dummyUsers = $this->mapDummyUserEqToUsers($dummyUsers);
        $space->singleUsers = $space->singleUsers->merge($dummyUsers);
        $space = $this->mapDummyUsersToSpaceConv($space);
        return $space;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This will map dummy users to each conversation of space
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $space
     * @return mixed
     */
    public function mapDummyUsersToSpaceConv($space) {
        $space->conversations->map(function ($conversation) {
            return $this->mapDummyUsersToConv($conversation);
        });
        return $space;
    }

    /**
     * @inheritDoc
     */
    public function mapDummyUsersToConv($conversation): Conversation {
        $dummyUsersInCurrentConv = $conversation->dummyRelation->pluck('dummyUsers');
        $dummyUsersConvertedInUsers = $this->mapDummyUserEqToUsers($dummyUsersInCurrentConv);
        $conversation->users = $conversation->users->merge($dummyUsersConvertedInUsers);
        return $conversation;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This will map the dummy users column which wil be equivalent with users response
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $dummyUsers
     * @return mixed
     */
    public function mapDummyUserEqToUsers($dummyUsers) {
        $dummyUsers->map(function ($u) {
            $u->email = null;
            $u->password = null;
            $u->unions = collect([
                (object)[
                    'id'         => null,
                    'entity_id'  => null,
                    'long_name'  => $u->union,
                    'short_name' => $u->union,
                    'pivot'      => (object)[
                        'entity_label' => $u->union_position,
                    ]
                ]
            ]);
            $u->company = (object)[
                'id'         => null,
                'entity_id'  => null,
                'long_name'  => $u->company,
                'short_name' => $u->company,
                'pivot'      => (object)[
                    'entity_label' => $u->company_position,
                ]
            ];

            $u->instances = collect([]);
            $u->presses = collect([]);
            $u->is_dummy = 1;
        });
        return $dummyUsers;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To map the dummy users to current conversation and to each conversation of current space
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return mixed
     */
    public function mapDummyUsersToEvent($event) {
        $event = $this->mapDummyUsersInCurrentConv($event);
        $event->currentSpace = $this->mapDummyUsersToSpaceConv($event->currentSpace);
        return $event;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Map dummy users in current conversation of event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return mixed
     */
    public function mapDummyUsersInCurrentConv($event) {
        if (isset($event->currentSpace->currentConversation)
            && $event->currentSpace->currentConversation
            && $event->currentSpace->currentConversation->dummyRelation->count()) {

            $event->currentSpace->currentConversation = $this->mapDummyUsersToConv($event->currentSpace->currentConversation);
        }
        return $event;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To add the tags for the space response
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return mixed
     */
    public function loadUsersTagForSpaceResponse($event) {
        //prepare all user tag
        $allTags = $this->userRepo()->userTagsRepository->allTags();
        //prepare all tags of status is 1
        $allPPTags = $this->userServices()->superAdminService->getAllTags(1);
        if (isset($event->currentSpace->singleUsers) && $event->currentSpace->singleUsers) {
            //if user is single in current space then load the tags for the user collection
            $event->currentSpace->singleUsers = $this->loadTagForUserCollection(
                $event->currentSpace->singleUsers,
                $allTags,
                $allPPTags
            );
        }
        if (isset($event->currentSpace->currentConversation) && $event->currentSpace->currentConversation) {
            //if user in current conversation then load the tags for the specific conversation
            $event->currentSpace->currentConversation = $this->loadTagForConversationModel(
                $event->currentSpace->currentConversation,
                $allTags,
                $allPPTags
            );
        }
        if (isset($event->currentSpace->conversations) && $event->currentSpace->conversations) {
            //if user in conversation then apply the tags for the conversations collection
            $event->currentSpace->conversations = $this->loadTagsForConversationCollection(
                $event->currentSpace->conversations,
                $allTags,
                $allPPTags
            );
        }
        return $event;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To load the tags for the user collection,map each user with tags and return the collection back.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Collection $userCollection
     * @param $allTags
     * @param null $allPPTags
     * @return Collection
     */
    public function loadTagForUserCollection($userCollection, $allTags, $allPPTags): Collection {
        $userCollection->map(function ($user) use ($allTags, $allPPTags) {
            return $this->addTagsToUserModel($user, $allTags, $allPPTags);
        });
        return $userCollection;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To add the tags for the single user model object
     * 1.First fetch the user used tags
     * 2.Now from all tags fetch those tags which are not present in used tags
     * 3.Attach all the used and unused tags like already implemented array
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param User $user
     * @param Collection $allTags
     * @param null $allPPTags
     * @return User
     */
    public function addTagsToUserModel($user, $allTags, $allPPTags) {
        $usedTagIds = $user->eventUsedTags ? $user->eventUsedTags->pluck('id') : [];
        $unUsedTags = $allTags->whereNotIn('id', $usedTagIds);
        if ($user->id != Auth::user()->id) {
            $unUsedTags = [];
        }
        $user->tag = [
            'used_tag'   => $user->eventUsedTags,
            'unused_tag' => $unUsedTags,
        ];
        return $this->userServices()->dataMapServices->loadPPTagsForUser($user, $allPPTags);
    }

    /**
     * @inheritDoc
     */
    public function loadTagForConversationModel($conversation, $allTags, $allPPTags) {
        $conversation->users = $this->loadTagForUserCollection($conversation->users, $allTags, $allPPTags);
        return $conversation;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To apply the tags for the conversations collection
     *
     * For each conversation this will add tags to all conversation users.
     * return the conversations collection back
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Collection $conversations
     * @param Collection $allTags
     * @param $allPPTags
     * @return Collection
     */
    public function loadTagsForConversationCollection($conversations, $allTags, $allPPTags) {
        $conversations->map(function ($conversation) use ($allTags, $allPPTags) {
            return $this->loadTagForConversationModel($conversation, $allTags, $allPPTags);
        });
        return $conversations;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To sort the conversations array as requirements
     * Currently, sorting on the basis of conversation users count ascending.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return mixed
     */
    public function sortConversations($event) {
        if (isset($event->currentSpace->conversations)) {
            $conversations = $event->currentSpace->conversations->sortBy(function ($conv) {
                return $conv->users->count();
            });
            $event->currentSpace->conversations = $conversations;
        }
        return $event;
    }

    /**
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function bindInterfaces($app) {
        $currentConference = \Modules\KctUser\Services\BusinessServices\factory\KctCoreService::getInstance()->getCurrentConference();
        if ($currentConference == 'zoom') {
            $app->bind(ExternalEventFactory::class, ZoomEventFactory::class);
        } else {
            $app->bind(ExternalEventFactory::class, BluejeansEventFactory::class);
        }
    }

    public function getEventHeaders($event): array {
        if ($event->event_settings['is_self_header'] ?? 0) {
            $header1 = $event->header_line_1;
            $header2 = $event->header_line_2;
            $source = 'event';
        } else {
            $group = $event->group;

            $setting = $group->setting()->where('setting_key', 'group_has_own_customization')->first();
            if (!$setting || !$setting->setting_value['group_has_own_customization']) {
                $group = $this->userServices()->adminService->getDefaultGroup();
            }

            $group->load(['allSettings' => function ($q) {
                $q->whereIn('setting_key', ['header_line_1', 'header_line_2', 'header_footer_customized']);
            }]);

            $setting = $group->allSettings->where('setting_key', 'header_footer_customized')->first();

            if ($setting->setting_value['header_footer_customized']) {
                $header1 = $group->allSettings->where('setting_key', 'header_line_1')->first();
                $header1 = $header1 ? $header1->setting_value['header_line_1'] ?? null : null;

                $header2 = $group->allSettings->where('setting_key', 'header_line_2')->first();
                $header2 = $header2 ? $header2->setting_value['header_line_2'] ?? null : null;
            } else {
                $header1 = null;
                $header2 = null;
            }
            $source = 'setting';
        }
        return [
            'h1'     => $header1,
            'h2'     => $header2,
            'source' => $source,
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the namespace from the current hostname set
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return mixed|string|null
     */
    public function getNamespaceFromHost($hostname) {
        if ($hostname) {
            $subDomain = explode('.', $hostname->fqdn);
            if (count($subDomain) > 1) {
                $subDomain = $subDomain[0];
            } else {
                $subDomain = '';
            }
            return $subDomain;
        }
        return null;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the space opening hours
     * If the space follows main (event) opening hours it will return that
     * @warn null will return if space or event doesn't have opening hours
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param EventSpace $space
     */
    public function getSpaceOpeningHours($space) {
        if (!$space) return null;
        if ($space->follow_main_opening_hours) {
            return $this->getEventOpeningHours($space->event);
        }
        return $space->opening_hours;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the event opening hours
     * @warn null will return if event doesn't have opening hours
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param \Modules\Events\Entities\Event $event
     * @return mixed|null
     */
    public function getEventOpeningHours($event) {
        if (!$event) {
            return null;
        }
        if ($event->event_fields && isset($event->event_fields['opening_hours'])) {
            return $event->event_fields['opening_hours'];
        }
        return null;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To find which conference is set currently
     * This will check in event settings if event setting present then it will check for conference else null return
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function getCurrentConference() {
        $eventSetting = $this->getEventSetting();
        if ($eventSetting && (
                // the value must present
                isset($eventSetting['event_current_conference'])
                // check if the value set is valid or not
                && in_array(
                    $eventSetting['event_current_conference'],
                    array_values(config('kct_const.conference_type'))
                )
            )) {
            // if value is proper return that else return null;
            return $eventSetting['event_current_conference'];
        }
        return null;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the event settings
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return mixed|null
     */
    public function getEventSetting() {
        $setting = Setting::where('setting_key', 'event_settings')->first();
        if ($setting && $setting->setting_value) {
            $decode = json_decode($setting->setting_value, JSON_OBJECT_AS_ARRAY);
            if ($decode) {
                return $decode;
            }
        }
        return null;
    }

    public function getAllCustomizationKeys() {
        $config = config('kctuser.default.custom_graphics');
        $keys = [];
        foreach ($config as $keyType => $keyCollection) {
            $keys = array_merge($keys, array_keys($keyCollection));
        }
        return $keys;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To find the type of the field, either color or checkbox to be validated
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $field
     * @return string|null
     */
    public function findFieldType($field) {
        $config = config('kctuser.default.custom_graphics');
        if (in_array($field, array_keys($config['colors']))) {
            return 'color';
        } else if (in_array($field, array_keys($config['checkboxes']))) {
            return 'checkbox';
        } else if (in_array($field, array_keys($config['urls']))) {
            return 'urls';
        } else if (in_array($field, array_keys($config['label']))) {
            return 'label';
        } else if (in_array($field, array_keys($config['number']))) {
            return 'number';
        }
        return null;
    }

    public function loadPPTags($user) {
        $user->professionalTags = $this->getUserTags(1);
        $user->personalTags = $this->getUserTags(2);
        return $user;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the user tags by type
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $tagType
     * @return mixed
     */
    public function getUserTags($tagType) {
        $tagsRelation = UserTag::where('user_id', Auth::user()->id)->pluck('tag_id');
        return \Modules\SuperAdmin\Entities\UserTag::whereIn('id', $tagsRelation)->where('tag_type', $tagType)->get();
//        return $this->userTagRepo->getTagsByTagIdAndTagType($tagsRelation, $tagType)->get();
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle the changes when host leave
     * This will mark conversation private by as null so if host is made conversation private others can break it
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $conversation
     * @param null $userId
     */
    public function handleHostLeave($conversation, $userId = null) {
        $userId = $userId ?: (Auth::check() ? Auth::user()->id: null);
        if(!$userId) return;
        $space = $conversation->space;
        $isHost = $this->userServices()->validationService->isUserSpaceHost($space, $userId);
        if ($isHost && $conversation->private_by == $userId) {
            $conversation->private_by = null;
            $conversation->save();
        }
    }

    /**
     * @inheritDoc
     */
    public function getDataByEvent($event, $key) {
        return $event->load([
            'group' => function ($q) use ($key) {
                $q->with([
                    'labelSetting.label',
                    'settings' => function ($q) use ($key) {
                        $q->whereIn('setting_key', $key);
                    }
                ]);
            }
        ]);
    }


    /**
     * @inheritDoc
     */
    public function findAndSetHostnameByMeetingId($meetingId): array {
        $websites = Website::all();
        $moment = null;
        $hostname = null;
        foreach ($websites as $website) {
            foreach ($website->hostnames as $account) {
                $this->tenant->tenant($website);
                $this->tenant->hostname($account);
                if ($moment = $this->userRepo()->eventRepository->findMomentByMomentId($meetingId)) {
                    // once moment is found return the iteration for each account, and store the moment in moment var
                    $hostname = $account;
                    break;
                }
            }
            if ($moment) {
                break;
            }
        }
        return ['moment' => $moment, 'hostname' => $hostname];
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function prepareGraphicsData(int $groupId = 1): array {
        $graphicsData = [];
        $keys = $this->getGraphicKeys();

        // getting all the group settings for design
        $settings = $this->userRepo()->settingRepository->getSettingsByKey(['account_settings', ...$keys,], $groupId);
        // getting settings from main database
        $generalSettings = $this->userServices()->superAdminService->getGeneralSettings();

        // checking if any setting is or created new but not present in db
        if ($settings->count() - 1 != count($this->getGraphicKeys())) {
            // one or more setting is missing from db so syncing the missing settings with default values
            $this->userServices()->adminService->syncGroupSettings($groupId);
            $settings = $this->userRepo()->settingRepository->getSettingsByKey(['account_settings', ...$keys,], $groupId);
        }


        // function variable to get the value according to design setting type
        $findValue = function ($key) use ($settings) {
            $section = $this->findSettingSection($key);
            if ($section == 'colors') {
                return $this->getColorFromSetting($settings, $key);
            } else if ($section == 'checkboxes') {
                return $this->getCheckFromSetting($settings, $key);
            } else {
                $data = $settings->where('setting_key', $key)->first();
                if ($data && isset($data->setting_value[$key])) {
                    $data = $data->setting_value[$key];
                    if ($section == 'images') {
                        return $this->userServices()->fileService->getFileUrl($data);
                    }
                    return $data;
                }
            }
            return null;
        };
        // as in previous application some keys were used as other name
        // so here the previous keys will be used and value will be fetched from new source
        foreach (config('kctadmin.hct_oit_graphic_aliases') as $key => $alias) {
            $graphicsData[$key] = $findValue($alias);
        }
        // some keys are not aliased because they introduced newly so adding them as it is.
        $newKeys = array_diff(
            $keys,
            array_values(config('kctadmin.hct_oit_graphic_aliases'))
        );

        foreach ($newKeys as $newKey) {
            $graphicsData[$newKey] = $findValue($newKey);
        }
        if (App::getLocale() == "en") {
            $videoUrl = $generalSettings->setting_value['public_video_en'] ?? null;
        } else {
            $videoUrl = $generalSettings->setting_value['public_video_fr'] ?? null;
        }
        return [
            // Merging new keys with alias key
            'graphics_data' => array_merge($graphicsData, [
                'bottom_bg_is_colored'    => 0,
                "video_explainer_enabled" => $generalSettings->setting_value['video_explainer_enabled'] ?? 0,
                "display_on_reg"          => $generalSettings->setting_value['display_on_reg'] ?? 0,
                "display_on_live"         => $generalSettings->setting_value['display_on_live'] ?? 0,
                "video_url"               => $videoUrl,
            ]),
        ];
    }

    public function eventCheckAccessCode($event, $accessCode): bool {
        return $event && isset($event->event_settings['manual_access_code']) && $accessCode && $accessCode == $event->event_settings['manual_access_code'];
    }

    public function getEventMaxConvCount($event): int {
        return (int)$event->event_settings['event_conv_limit'] ?? 4;
    }
}
