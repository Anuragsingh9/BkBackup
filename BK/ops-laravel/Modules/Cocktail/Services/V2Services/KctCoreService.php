<?php

namespace Modules\Cocktail\Services\V2Services;

use App\AccountSettings;
use App;
use App\DummyUsers;
use App\User;
use Carbon\Carbon;
use Exception;
use App\Services\Service;
use App\Setting;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Cocktail\Entities\CallLog;
use Modules\Cocktail\Entities\Conversation;
use Modules\Cocktail\Entities\EventSpace;
use Modules\Cocktail\Entities\EventTag;
use Modules\Cocktail\Entities\EventUserInvites;
use Modules\Cocktail\Entities\UserCall;
use Modules\Cocktail\Entities\UserCallConvo;
use Modules\Cocktail\Entities\EventUserTagRelation;
use Modules\Cocktail\Events\ConversationDeleteEvent;
use Modules\Cocktail\Events\EventEndChangedEvent;
use Modules\Cocktail\Entities\EventUser;
use Modules\Cocktail\Events\EventManuallyOpenedEvent;
use Modules\Cocktail\Exceptions\CustomValidationException;
use Modules\Cocktail\Services\AuthorizationService;
use Modules\Cocktail\Services\Contracts\EmailFactory;
use Modules\Cocktail\Services\Contracts\ExternalEventFactory;
use Modules\Cocktail\Services\EventSpaceService;
use Modules\Cocktail\Services\KctEventService;
use Modules\Cocktail\Services\KctService;
use Modules\Events\Entities\Event;
use Modules\Events\Exceptions\CustomException;
use Modules\Events\Service\EventService;
use Modules\Events\Service\ValidationService;
use Modules\SuperAdmin\Entities\UserTag;
use function Aws\load_compiled_json;

class KctCoreService extends Service {
    
    /**
     * @var ExternalEventFactory
     */
    private $conferenceService;
    
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
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To set the default logo value for graphics setting
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $path
     * @throws CustomValidationException
     */
    public function setDefaultLogoUrl($path) {
        $setting = Setting::where('setting_key', 'event_settings')->first();
        if ($setting) {
            // event setting already present
            
            // deleting the previous logo if not universal logo
            $this->deleteKctDefaultLogo($setting);
            
            // setting new logo to database setting table
            $this->setKCTSettingValue('kct_graphics_logo', $path, $setting);
        }
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to delete the current default logo set,
     * if default logo is OPS KCT default logo, i.e. common for all account it will be ignored
     * @note this will only delete the logo if it has uploaded, it will not delete the logo if its common logo
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param null $setting
     * @throws CustomValidationException
     */
    public function deleteKctDefaultLogo($setting = null) {
        if (!$setting) {
            $setting = Setting::where('setting_key', 'event_settings')->first();
        }
        if (!$setting) {
            throw new CustomValidationException('event_setting_not_found', null, 'message');
        }
        $data = json_decode($setting->setting_value, 1);
        $logoPath = isset($data['event_kct_setting']['kct_graphics_logo']) ? $data['event_kct_setting']['kct_graphics_logo'] : '';
        
        // to check if logo exists and logo path is not other than logo path ,
        // as there can be chance to have org logo or ops logo so no need to delete that
        if ($logoPath && !$this->isLogoDefault($logoPath)) {
            // logo is present and not equals to default logo for ops
            KctService::getInstance()->getCore()->fileDeleteBys3($logoPath);
        }
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check if the current KCT Logo set is default logo or not
     * here default logo means the logo is not set by the org admin and its either Organisation logo set or OPS
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $logoPath
     * @return bool
     */
    public function isLogoDefault($logoPath) {
        $path = config('cocktail.s3.default_graphics_logo');
        return $logoPath && !strpos($logoPath, $path);
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to set the default color value to new color value
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $key
     * @param $value
     * @param null $setting
     * @return mixed
     */
    public function setKCTSettingValue($key, $value, $setting = null) {
        if (!$setting) {
            $setting = Setting::where('setting_key', 'event_settings')->first();
        }
        if ($setting) {
            $data = json_decode($setting->setting_value, 1);
            $data['event_kct_setting'][$key] = $value;
            Setting::updateOrCreate(
            // searching
                [
                    'setting_key' => 'event_settings'
                ],
                // data to insert
                [
                    'setting_key'   => 'event_settings',
                    'setting_value' => json_encode($data),
                ]
            );
            $setting = Setting::where('setting_key', 'event_settings')->first();
            return json_decode($setting->setting_value)->event_kct_setting->$key;
        }
        return null;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To set the default logo for the kct if no logo is found
     * -----------------------------------------------------------------------------------------------------------------
     * @throws CustomValidationException
     */
    public function setDefaultLogo() {
        $path = $this->getDefaultLogoForKct();
        $this->setDefaultLogoUrl($path);
        return KctService::getInstance()->getCore()->getS3Parameter($path);
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This will prepare the default logo for the kct
     * this will first check for organisation default logo
     * if its not present then it will send OPS default logo
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return string|null
     */
    public function getDefaultLogoForKct() {
        $setting = getSettingData('graphic_config', 1);
        if (isset($setting->header_logo) && $setting->header_logo) {
            $path = $setting->header_logo;
        } else {
            $path = config('cocktail.default.ops_logo');
        }
        return $path;
    }
    
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To create a new event for kct type
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @param $data
     * @return Event
     * @throws CustomException
     * @throws Exception
     */
    public function createEvent($request, $data) {
        
        $orgAdmin = $data['orgAdmin'];
        $defaultOrganiserUser = $data['defaultOrganiserUser'];
        $data['eventData']['workshop_id'] = EventService::getInstance()->createWorkshopForEvent($request, $data);
        $event = EventService::getInstance()->createEvent($data);
        EventService::getInstance()->createDefaultSpace($data['defaultSpace'], $event);
        
        KctEventService::getInstance()->addUserToEvent($orgAdmin->id, $event->event_uuid, 1, 1); // adding validator /deputy
        KctEventService::getInstance()->addUserToEvent($defaultOrganiserUser->id, $event->event_uuid, 1, 1); // adding president secretory
        
        $this->sendRegistrationEmail($event, $orgAdmin->id, $request);
        if ($orgAdmin->id != $defaultOrganiserUser->id) {
            $this->sendRegistrationEmail($event, $defaultOrganiserUser->id, $request);
        }
        return $event;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To send the registration email to event member
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @param $userId
     * @param $request
     */
    public function sendRegistrationEmail($event, $userId, $request) {
        // Sending Email to User for Email Registration
        $tags = KctService::getInstance()->prepareEmailTags($event, $userId);
        $root = $request->input('link', KctService::getInstance()->getDefaultHost($request));
        
        if ($event->type == config('events.event_type.virtual')) {
            $data = ['tags' => $tags, 'root' => $root];
            app()->make(EmailFactory::class)->sendVirtualRegistration($event, $userId, $data);
        } else {
            app()->make(EmailFactory::class)->sendIntRegistration($event, $userId, $tags);
        }
    }
    
    public function attachConferenceWithEvent($event, $conference) {
        $event->update([
            'bluejeans_id' => isset($conference['id']) ? $conference['id'] : null,
        ]);
    }
    
    /**
     * @param Request $request
     * @param array $data
     * @param Event $event
     * @throws Exception
     */
    public function updateEvent($request, $data, $event) {
        $imageUrl = $event->image;
        EventService::getInstance()->updateWorkshopAndMeeting($request, $imageUrl, $event);
        $event->update($data['eventData']);
        EventService::getInstance()->sendModificationMail($event);
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the init data for the version 2 to load the application
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param array $previousData
     * @return array
     */
    public function getInitData($previousData = []) {
        $previousData = $this->getGraphicsData($previousData);
        return $previousData;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to get the graphics data from the setting
     * which is default for kct v2 from org setting.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $data
     * @return mixed
     */
    public function getGraphicsData($data) {
        $setting = Setting::where('setting_key', 'event_settings')->first();
        $decode = json_decode($setting->setting_value)->event_kct_setting;
        $settingData = $this->getCustomGraphicsSetting();
        $data['graphics_data'] = $this->prepareCustomizationResource($settingData);
        
        if (isset($data['graphics_data']['customized_colors'])) {
            $data['graphics_data'] = $this->putDefaultColorIfDisable($data['graphics_data']);
        }
        
        $data['graphics_data']['kct_graphics_logo'] = isset($decode->kct_graphics_logo) ? KctService::getInstance()->getCore()->getS3Parameter($decode->kct_graphics_logo) : null;
        $data['graphics_data']['kct_graphics_color1'] = isset($data['graphics_data']['event_color_1']) ? $data['graphics_data']['event_color_1'] : (isset($settingData->event_color_1) ? $settingData->event_color_1 : []);
        $data['graphics_data']['kct_graphics_color2'] = isset($data['graphics_data']['event_color_2']) ? $data['graphics_data']['event_color_2'] : (isset($settingData->event_color_2) ? $settingData->event_color_2 : []);
        
        
        return $data;
    }
    
    public function putDefaultColorIfDisable($graphicsData) {
        if ($graphicsData['customized_colors'] == 0) {
            $graphicsData['event_color_1'] = (object)config('cocktail.default.kct_event_default_colors.event_color_1');
            $graphicsData['event_color_2'] = (object)config('cocktail.default.kct_event_default_colors.event_color_2');
            $graphicsData['event_color_3'] = (object)config('cocktail.default.kct_event_default_colors.event_color_3');
        }
        return $graphicsData;
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
        $event = ValidationService::getInstance()->resolveEvent($event);
        return [
            'event_version' => KctService::getInstance()->findEventVersion($event)
        ];
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to get the data for the front user side
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return mixed|Event
     */
    public function getEventDataForUser($event) {
        return ValidationService::getInstance()->resolveEvent($event);
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To sort the conversations array as requirements
     * Currently sorting on the basis of conversation users count ascending.
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
     * -----------------------------------------------------------------------------------------------------------------
     * @description To emit the redis event when the event time is update so front users can find if the event end
     * time has changed
     * This will first check for the event is running or not then it will send the event to redis
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $beforeEndTime
     * @param Request $request
     * @param Event $event
     */
    public function emitEventEndChangeEvent($beforeEndTime, $request, $event) {
        $isEventRunning = ValidationService::getInstance()->isEventOrSpaceRunning($event);
        if ($isEventRunning) {
            // event is running so send the event
            if ($beforeEndTime != $event->end_time) {
                // the event end time (before updating) is not same as new event end time so emit the event
                event(new EventEndChangedEvent([
                    'eventUuid' => $event->event_uuid,
                    'namespace' => KctService::getInstance()->getSubDomain($request)
                ]));
            }
        }
        
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To get the dummy users for the event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return Event
     */
    public function getDummyUsers($event) {
        if (isset($event->event_fields["is_dummy_event"]) && $event->event_fields["is_dummy_event"]) {
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
     * @param $space
     * @return mixed
     */
    public function getDummyUsersForSpace($space) {
        $dummyUsers = DummyUsers::whereHas('eventDummyUser', function ($q) use ($space) {
            $q->where('space_uuid', $space->space_uuid);
            // current conversation uuid must be null as these records will be fetched along with the conversation
            $q->whereNull('current_conv_uuid');
        })->get();
        $dummyUsers = $this->mapDummyUserEqToUsers($dummyUsers);
        $space->singleUsers = $space->singleUsers->merge($dummyUsers);
        $space = $this->mapDummyUsersToSpaceConv($space);
        return $space;
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
     * @description this will map dummy users to provided conversation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $conversation
     * @return mixed
     */
    public function mapDummyUsersToConv($conversation) {
        $dummyUsersInCurrentConv = $conversation->dummyRelation->pluck('dummyUsers');
        $dummyUsersConvertedInUsers = $this->mapDummyUserEqToUsers($dummyUsersInCurrentConv);
        $conversation->users = $conversation->users->merge($dummyUsersConvertedInUsers);
        return $conversation;
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
            $u->companies = collect([
                (object)[
                    'id'         => null,
                    'entity_id'  => null,
                    'long_name'  => $u->company,
                    'short_name' => $u->company,
                    'pivot'      => (object)[
                        'entity_label' => $u->company_position,
                    ]
                ]
            ]);
            $u->instances = collect([]);
            $u->presses = collect([]);
            $u->is_dummy = 1;
        });
        return $dummyUsers;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To modify the request according to the dummy event conversation environment if any
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @return mixed
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
    public function validateRealUsersInConversation($conversation, $request) {
        if (!$this->isEventDummy($this->getEventByConversation($conversation))) {
            return;
        }
        $dummyCount = $this->countRealUsers($conversation);
        if ($dummyCount['realUserCount'] == 0) {
            // no real user present in conversation so delete it and tell all users that conversation deleted
            $space = EventSpace::find($conversation->space_uuid);
            EventSpaceService::getInstance()->deleteConversation($conversation);
            
            event(new ConversationDeleteEvent([
                'conversationId' => $conversation->uuid,
                'dummyUsersId'   => $dummyCount['dummyIds'],
                'namespace'      => KctService::getInstance()->getSubDomain($request),
                'spaceId'        => $space ? $space->space_uuid : null,
                'eventId'        => $space ? $space->event_uuid : null,
            ]));
        }
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To find if the event is dummy or not
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return bool
     */
    public function isEventDummy($event) {
        $event = ValidationService::getInstance()->resolveEvent($event);
        return isset($event->event_fields["is_dummy_event"]) && $event->event_fields["is_dummy_event"];
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
     * @description To get the dummy users of a event spaces
     * This will return each space with respective dummy users
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @return Collection
     */
    public function getEventDummyUsers($eventUuid) {
        return EventSpace::with('dummyRelations.dummyUsers')->where('event_uuid', $eventUuid)->get();
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To find which conference is set currently
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function getCurrentConference() {
        $eventSetting = $this->getEventSetting();
        if ($eventSetting) {
            return
                (
                    // the value must present
                    isset($eventSetting['event_current_conference'])
                    // check if the value set is valid or not
                    && in_array(
                        $eventSetting['event_current_conference'],
                        array_values(config('kct_const.conference_type'))
                    )
                )
                    // if value is proper return that else return null;
                    ? $eventSetting['event_current_conference'] : null;
        }
        return config('kct_const.conference_type.zoom');
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
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This will take event as input and return the conference type of event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Event $event
     * @return string|null
     */
    public function findEventConferenceType($event) {
        $event = ValidationService::getInstance()->resolveEvent($event);
        return isset($event->event_fields["conference_type"]) ? $event->event_fields["conference_type"] : null;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the additional data for the event get
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Event $event
     * @param Request $request
     * @return array
     */
    public function getEventGetAdditional($event, $request) {
        $add1 = EventService::getInstance()->getEventShowMeta($event, []);
        $add2 = $this->getEventJoinLink($event, $request);
        return array_merge($add1, $add2);
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the event join link for the role wise
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Event $event
     * @param Request $request
     * @return array
     */
    public function getEventJoinLink($event, $request) {
        if ($this->findEventConferenceType($event) != null) {
            $result = [
                'attendee_url' => KctEventService::getInstance()->getAttendeeJoinUrl($event, $request),
            ];
            $this->addJoinUrl($event, $result, 'presenter');
            $this->addJoinUrl($event, $result, 'moderator');
            return $result;
        }
        return [];
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @param $result
     * @param $type
     */
    public function addJoinUrl($event, &$result, $type) {
        $conferenceService = app()->make(ExternalEventFactory::class);
        if ($type == 'presenter' && AuthorizationService::getInstance()->checkEventRole($event, $type)) {
            $result['presenter_url'] = $conferenceService->getJoinLink($event->bluejeans_id, $type);
        } else if ($type == 'moderator' && AuthorizationService::getInstance()->checkEventRole($event, $type)) {
            $result['moderator_url'] = $conferenceService->getJoinLink($event->bluejeans_id, $type);
        }
    }
    
    public function updateUserRole($eventUuid, $userId, $field, $space = null, $presence = null) {
        if (in_array($field, [1, 2])) { // presenter or moderator
            $update = $this->getRoleValueToUpdate($eventUuid, $field, $userId);
            $this->updateUserEventRole($eventUuid, $userId, $update);
            $event = Event::where('event_uuid', $eventUuid)->first();
            if ($event && $this->findEventConferenceType($event)) {
                // to update the conference if event follows
                $user = User::find($userId);
                if ($update) {
                    $this->updateConferenceRole(
                        $field,
                        $update['value'],
                        $user,
                        $event->bluejeans_id
                    );
                }
            }
        } else {
            KctEventService::getInstance()->eventUserUpdateRole($eventUuid, $userId, $field, $space, $presence);
        }
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the role value to set for updating the role of a conference
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @param $field
     * @param $userId
     * @return array|null
     */
    public function getRoleValueToUpdate($eventUuid, $field, $userId) {
        $eventUser = EventUser::where('event_uuid', $eventUuid)
            ->where('user_id', $userId)->first();
        if ($field == 1) { // presenter
            $column = 'is_presenter';
        } else if ($field == 2) { // moderator
            $column = 'is_moderator';
        } else {
            return null;
        }
        return [
            'column' => $column,
            'value'  => $eventUser->$column ? 0 : 1,
        ];
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This will update the value for the user inside the event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @param $userId
     * @param $data
     * @return int|null
     */
    public function updateUserEventRole($eventUuid, $userId, $data) {
        if (isset($data['column']) && isset($data['value'])) {
            return EventUser::where('user_id', $userId)
                ->where('event_uuid', $eventUuid)
                ->update([$data['column'] => $data['value']]);
        }
        return null;
    }
    
    /**
     * @param int $field
     * @param int $value
     * @param User $user
     * @param string $conferenceId
     */
    public function updateConferenceRole($field, $value, $user, $conferenceId) {
        $data = [
            'email'     => $user->email,
            'presenter' => $field == 1 ? 1 : 0, // 1 indicate the field to update
            'moderator' => $field == 2 ? 1 : 0, // 1 indicate the field to update
            'user'      => $user
        ];
        if ($value == 1) {
            // add role
            $this->getConferenceFactory()->addMember($conferenceId, $data);
        } else {
            // remove role
            $this->getConferenceFactory()->removeMember($conferenceId, $data);
        }
    }
    
    public function getEmbeddedUrl($event) {
        if (ValidationService::getInstance()->isEventRunning($event)
            && $this->findEventConferenceType($event)) {
            return $this->getConferenceFactory()->prepareEmbeddedLnk($event->bluejeans_id);
        }
        return null;
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
        $settingAfterDecode = KctService::getInstance()->getDecodeSetting(config('cocktail.setting_keys.event_custom_graphics'));
        
        if (!$settingAfterDecode) {
            $this->setDefaultCustomGraphics();
            $settingAfterDecode = KctService::getInstance()->getDecodeSetting(config('cocktail.setting_keys.event_custom_graphics'));
        }
        // todo verify each key if new introduced
        
        return $settingAfterDecode;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To set the default values to the custom graphics setting key
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function setDefaultCustomGraphics() {
        $defaults = config('cocktail.default.custom_graphics');
        
        $defaultValues = [];
        // this will add all the keys present
        foreach ($defaults as $keysArray) {
            $defaultValues = array_merge($defaultValues, $keysArray);
        }
        
        $accountSettings = getSettingData('graphic_config', 1);
        
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
    
    public function validateCustomizationKeys($setting) {
        $defaults = config('cocktail.default.custom_graphics');
        $keys = [];
        foreach ($defaults as $k => $v) {
            $keys = array_merge($keys, array_keys($defaults[$k]));
        }
        $existing = array_keys((array)$setting);
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update the single value of the custom graphics
     * first get the setting
     * if setting not found set the default values
     *  fetch the setting again
     * decode previous value
     * update the previous value according to color or checkbox
     * store
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $field
     * @param $value
     */
    public function updateCustomGraphics($field, $value) {
        $setting = Setting::where(['setting_key' => config('cocktail.setting_keys.event_custom_graphics')])
            ->first();
        if (!$setting) {
            $this->setDefaultCustomGraphics();
            $setting = Setting::where(['setting_key' => config('cocktail.setting_keys.event_custom_graphics')])
                ->first();
        }
        $prev = json_decode($setting->setting_value, JSON_OBJECT_AS_ARRAY);
        if ($prev) {
            $fieldType = $this->findFieldType($field);
            if ($fieldType == 'color') {
                // this will ensure only rgba is save in case if value contains extra keys
                $value = json_decode($value, JSON_OBJECT_AS_ARRAY);
                $update = [
                    'r' => $value['r'],
                    'g' => $value['g'],
                    'b' => $value['b'],
                    'a' => $value['a'],
                ];
            } else if ($fieldType == 'checkbox') {
                $update = (int)$value;
            } else {
                $update = $value;
            }
            $prev[$field] = $update;
            Setting::where(['setting_key' => config('cocktail.setting_keys.event_custom_graphics')])
                ->update([
                    'setting_value' => json_encode($prev)
                ]);
        }
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
        $config = config('cocktail.default.custom_graphics');
        if (in_array($field, array_keys($config['colors']))) {
            return 'color';
        } else if (in_array($field, array_keys($config['checkboxes']))) {
            return 'checkbox';
        } else if (in_array($field, array_keys($config['urls']))) {
            return 'urls';
        }
        return null;
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
        $allTags = EventTag::all();
        $allPPTags = UserTag::where('status', 1)->get();
        if (isset($event->currentSpace->singleUsers) && $event->currentSpace->singleUsers) {
            $event->currentSpace->singleUsers = $this->loadTagForUserCollection($event->currentSpace->singleUsers, $allTags, $allPPTags);
        }
        if (isset($event->currentSpace->currentConversation) && $event->currentSpace->currentConversation) {
            $event->currentSpace->currentConversation = $this->loadTagForConversationModel($event->currentSpace->currentConversation, $allTags, $allPPTags);
        }
        if (isset($event->currentSpace->conversations) && $event->currentSpace->conversations) {
            $event->currentSpace->conversations = $this->loadTagsForConversationCollection($event->currentSpace->conversations, $allTags, $allPPTags);
        }
        return $event;
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
     * @description To load the tags for the specific conversation
     *
     * Get the users collection of conversation
     * apply tags to user collection, attach it back to conversation users
     * return the modified conversation;
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Conversation $conversation
     * @param Collection $allTags
     * @param null $allPPTags
     * @return Conversation
     */
    public function loadTagForConversationModel($conversation, $allTags, $allPPTags) {
        $conversation->users = $this->loadTagForUserCollection($conversation->users, $allTags, $allPPTags);
        return $conversation;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To load the tags for the user collection
     * map each user with tags
     * return the collection back
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Collection $userCollection
     * @param $allTags
     * @param null $allPPTags
     * @return Collection
     */
    public function loadTagForUserCollection($userCollection, $allTags, $allPPTags) {
        $userCollection->map(function ($user) use ($allTags, $allPPTags) {
            return $this->addTagsToUserModel($user, $allTags, $allPPTags);
        });
        return $userCollection;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To add the tags for the single user model object
     * first fetch the user used tags
     * now from all tags fetch those tags which are not present in used tags
     * attach all the used and unused tags like already implemented array
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param User $user
     * @param Collection $allTags
     * @param null $allPPTags
     * @return User
     */
    public function addTagsToUserModel($user, $allTags,$allPPTags) {
        $usedTagIds = $user->eventUsedTags ? $user->eventUsedTags->pluck('id') : [];
        $unUsedTags = $allTags->whereNotIn('id', $usedTagIds);
        $user->tag = [
            'used_tag'   => $user->eventUsedTags,
            'unused_tag' => $unUsedTags,
        ];
        return DataMapService::getInstance()->loadPPTagsForUser($user, $allPPTags);
    }
    
    public function getUserInvites($id) {
        return EventUserInvites::where('invited_by_user_id', $id)->get();
    }
    
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To send the event data for the quick sign in signup process
     * @note in case of user is not member in current space, default space will send
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @return Event
     */
    public function getEventForQss($eventUuid) {
        $event = Event::with(['currentSpace', 'spaces' => function ($q) {
            $q->with('spaceUsers');
            $q->orderBy('order_id');
            $q->where('is_duo_space', 0);
        }])->where('event_uuid', $eventUuid)->first();
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
     * @description to get the redirect url on the basis of type
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @param $type
     * @param array $replace
     * @return string
     */
    public function getRedirectUrl($request, $type, $replace = []) {
        $baseUrl = KctService::getInstance()->getDefaultHost($request);
        switch ($type) {
            case 'email_verify':
                $path = str_replace(array_keys($replace), array_values($replace), config('cocktail.front_path.email_verify'));
                return env('HOST_TYPE', 'https://') . "$baseUrl/$path";
            case 'quick-login':
                $path = str_replace(array_keys($replace), array_values($replace), config('cocktail.front_path.quick_login'));
                return env('HOST_TYPE', 'https://') . "$baseUrl/$path";
            case 'event-list':
                $path = config('cocktail.front_path.event_list');
                return env('HOST_TYPE', 'https://') . "$baseUrl/$path";
            case 'event-register':
                $path = str_replace(array_keys($replace), array_values($replace), config('cocktail.front_path.event_register'));
                return env('HOST_TYPE', 'https://') . "$baseUrl/$path";
            case 'quick_user_info':
                $path = str_replace(array_keys($replace), array_values($replace), config('cocktail.front_path.quick_user_info'));
                return env('HOST_TYPE', 'https://') . "$baseUrl/$path";
            default:
                return env('HOST_TYPE', 'https://') . "$baseUrl";
        }
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
        $eventUser = EventUser::where('event_uuid', $eventUuid)->where('user_id', Auth::user()->id)->first();
        return !$eventUser->is_joined_after_reg;
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
        return EventUser::where('event_uuid', $eventUuid)->where('user_id', $userId)->update([
            'is_joined_after_reg' => 0,
        ]);
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To separate the email with existing in database for invite purpose
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $data
     * @return array|array[]
     */
    public function separateExistingEmailForInvite($data) {
        $emails = array_pluck($data, 'email');
        $existingUsers = User::whereIn('email', $emails)->get();
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
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the invitees by the current user for the event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @param array $emails
     * @return Collection
     */
    public function getUserEventInvites($eventUuid) {
        $invites = EventUserInvites::select('*', DB::raw('MAX(id) as target_id'))
            ->where(
                function ($q) use ($eventUuid) {
                    $q->where('event_uuid', $eventUuid);
                    $q->where('invited_by_user_id', Auth::user()->id);
                }
            )
            ->groupBy('email')
            ->orderBy('created_at', 'desc')
            ->get();
        return EventUserInvites::select('*', DB::raw('count(email) as invited_times'))
            ->whereIn('id', $invites->pluck('target_id'))
            ->groupBy('email')
            ->get();
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the response for the graphics customization
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $setting
     * @return array
     */
    public function prepareCustomizationResource($setting) {
        
        $defaults = config('cocktail.default.custom_graphics');
        
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
     * @description To update the language for the user
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $lang
     * @param null $user
     * @return User|null;
     */
    public function updateUserLanguage($lang, $user = null) {
        $user = $user ? $user : Auth::user();
        if ($lang) {
            $data = $user->setting ? json_decode($user->setting, JSON_OBJECT_AS_ARRAY) : [];
            $data['lang'] = strtoupper($lang);
            $user->setting = json_encode($data);
            $user->update();
            App::setLocale(strtolower($lang));
        }
        return $user;
    }

    /**
     * Create rejected call log for a user
     * @param $data
     * @return CallLog object
     */
    public function createRejectedLog($data){
        $rejectedCallUser = $this->createRejectedCall($data);
        $rejectedCallId = $this->getRejectedCallId($rejectedCallUser);
        $this->createUserCallConversation($data,$rejectedCallId);
        $convoId = $this->getUserCallConversationId($data,$rejectedCallId);
        return $this->createUserCallLog($rejectedCallId,$convoId);

    }

    /**
     * create rejected call in UserCall
     * @param $data
     * @return $data['from_id']
     */
    public function createRejectedCall($data){
        $convUser = $data['from_id'];
        $userCall = UserCall::updateOrCreate([
            'from_id'    => $data['from_id'],
            'to_id'      => $data['to_id'],
            'status'     => $data['status'],
            'event_uuid' => $data['event_uuid'],
        ]);
        return $convUser;
    }

    /**
     * get rejected call id
     * @param $userId
     * @return int(UserCall id)
     */
    public function getRejectedCallId($userId){
        $userCall = UserCall::where(function ($q) use ($userId) {
            $q->where('from_id', $userId);
            $q->where('to_id', Auth::user()->id);
            $q->where('status', config('cocktail.que_log_status.rejected'));
        })->first();

        return [$userCall->id];
    }

    /**
     * create answered call log for a user
     * @param $data
     * @return CallLog object
     */
    public function createAnsweredLog($data){
        $answeredCallUser = $this->createAnsweredCall($data);
        $answeredCallId = $this->getAnsweredCallId($answeredCallUser);
        $this->createUserCallConversation($data,$answeredCallId);
        $convoId = $this->getUserCallConversationId($data,$answeredCallId);
        return $this->createUserCallLog($answeredCallId,$convoId);

    }

    /**
     * create answered call in UserCall
     * @param $data
     * @return $data['from_id']
     */
    public function createAnsweredCall($data){
        $convUser = $data['from_id'];
        $userCall = UserCall::updateOrCreate([
            'from_id'    => $data['from_id'],
            'to_id'      => $data['to_id'],
            'status'     => $data['status'],
            'event_uuid' => $data['event_uuid'],
        ]);
        return $convUser;
    }

    /**
     * get answered call id
     * @param $userId
     * @return int(UserCall id)
     */
    public function getAnsweredCallId($userId){
        $userCall = UserCall::where(function ($q) use ($userId) {
            $q->where('from_id', $userId);
            $q->where('to_id', Auth::user()->id);
            $q->where('status', config('cocktail.que_log_status.answered'));
        })->first();

        return [$userCall->id];
    }

    /**
     * create missed call log for a user
     * @param $data
     * @return CallLog object
     */
    public function createMissedLog($data){
        $missedCallUser = $this->createMissedCall($data);
        $missedCallId = $this->getMissedCallId($missedCallUser);
        $this->createUserCallConversation($data,$missedCallId);
        $convoId = $this->getUserCallConversationId($data,$missedCallId);
        return $this->createUserCallLog($missedCallId,$convoId);

    }

    /**
     * create missed call in UserCall
     * @param $data
     * @return $data['from_id'] if single user or array of id if multiple users
     */
    public function createMissedCall($data){
        if (isset($data['conversation_uuid']) && $data['conversation_uuid'] != null){
            $conversation = Conversation::with('users')->where('uuid',$data['conversation_uuid'])->first();
            $convUsers = $conversation->users->pluck('id');
            foreach ($convUsers as $userId) {
                $userCall[] = [
                    'from_id'    => $userId,
                    'to_id'      => $data['to_id'],
                    'status'     => $data['status'],
                    'event_uuid' => $data['event_uuid'],
                ];
            }
            UserCall::updateOrCreate($userCall);
            return $convUsers;
        }else{
            $convUsers = $data['from_id'];
            $userCall = UserCall::updateOrCreate([
                'from_id'    => $data['from_id'],
                'to_id'      => $data['to_id'],
                'status'     => $data['status'],
                'event_uuid' => $data['event_uuid'],
            ]);
            return [$convUsers];
        }
    }

    /**
     * get missed call id
     * @param $userId
     * @return array(UserCall id)
     */
    public function getMissedCallId($userId){
        foreach ($userId as $id) {
            $userCall[] = UserCall::where(function ($q) use ($id) {
                $q->where('from_id', $id);
                $q->where('to_id', Auth::user()->id);
                $q->where('status', config('cocktail.que_log_status.missed'));
            })->first();

        }
        foreach ($userCall as $call){
            $callId[] = $call->id;
        }
        return $callId;
    }

    /**
     * create log in UserCallConvo
     * @param $data
     * @param $missedCallId
     */
    public function createUserCallConversation($data,$missedCallId){
        foreach ($missedCallId as $id) {
            $callConvo[] = [
                'user_call_id'      => $id,
                'conversation_uuid' => $data['conversation_uuid'],
                'space_uuid'        => $data['space_uuid'],
                'event_uuid'        => $data['event_uuid'],
            ];
        }
        UserCallConvo::updateOrCreate($callConvo);

    }

    /**
     * get id from UserCallConvo
     * @param $data
     * @param $missedCallId
     * @return array(UserCallConvo id)
     */
    public function getUserCallConversationId($data,$missedCallId){
        foreach ($missedCallId as $id) {
            $conversation[] = UserCallConvo::where(function ($q) use ($id,$data) {
                $q->where('user_call_id', $id);
                $q->where('conversation_uuid', $data['conversation_uuid']);
            })->first();
        }
        foreach ($conversation as $convo){
            $convoId[] = $convo->id;
        }
        return $convoId;
    }

    /**
     * create logs in CallLog
     * @param $convoId
     * @param $missedCallId
     * return CallLog object
     */
    public function createUserCallLog($missedCallId,$convoId){
        for ($i = 0; $i < count($convoId); $i++) {
            $createLog[] = [
                'user_call_id' => $missedCallId[$i],
                'user_conv_id' => $convoId[$i],
            ];
        }
        $log = CallLog::create($createLog);

//        if (!$log){
//            throw new Exception();
//        }
        return $log;
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
        $tagsRelation = EventUserTagRelation::where('user_id', Auth::user()->id)->pluck('tag_id');
        return UserTag::whereIn('id', $tagsRelation)->where('tag_type', $tagType)->get();
    }

    public function loadPPTags($user) {
        $user->professionalTags = $this->getUserTags(1);
        $user->personalTags = $this->getUserTags(2);
        return $user;
    }

    public function getIMissedId($request){
        return  UserCall::where(function ($q) use ($request){
            $q->where('to_id',Auth::user()->id);
            $q->where('status',config('cocktail.que_log_status.missed'));
            $q->where('event_uuid',$request->event_uuid);
        })->get()->pluck('from_id')->toArray();
    }

    public function getTheyMissedId($request){
        return  UserCall::where(function ($q) use ($request){
            $q->where('from_id',Auth::user()->id);
            $q->where('status',config('cocktail.que_log_status.missed'));
            $q->where('event_uuid',$request->event_uuid);
        })->get()->pluck('to_id')->toArray();
    }

    /**
     * helper builder for searching user in queue
     * @param $eventUuid
     * @return Builder $builder
     */
    public function searchQueueBuilder($eventUuid) {
        $builder = User::with([
            'userVisibility', 'tagsRelationForPP',
            'eventUser' => function($q) use ($eventUuid) {
                $q->where('event_uuid', $eventUuid);
            },
        ])
        ->where('id', '!=', Auth::user()->id)
        ->whereHas('eventUser')
        ->whereHas('userVisibility');
        return $builder;
    }

    /**
     * get user tags related to all user in collection
     * @param $data
     * @return tags loaded data $data
     */
    public function getPPTags($data) {

        $allPPTags = UserTag::where('status', 1)->get();
        $data = DataMapService::getInstance()->loadPPTagsForUserCollection($data, $allPPTags);

        return $data;
    }

    /**
     * search user by name
     * @param $data
     * @param $eventUuid
     * @param $val
     * @param users data
     */
    public function searchByName($eventUuid, $val, $data) {
        
        $data = $data->where(function($q) use ($val) {
            $q->where('fname', 'like', '%' . $val . '%');
            $q->orWhere('lname', 'like', '%' . $val . '%');
            $q->orWhere(DB::raw("CONCAT('fname', ' ', 'lname')"), 'like', '%' . $val . '%');
        })
        ->get();

        $data = $this->getPPTags($data);
        return $data;
    }

    /**
     * search user by company/union name
     * @param $data
     * @param $eventUuid
     * @param $val
     * @param $filter
     * @param users data
     */
    public function searchByCompanyUnion($eventUuid, $val, $filter, $data) {
        $check = function($q) use ($val) {
            $q->where('long_name', 'like', '%' . $val . '%');
            $q->orWhere('short_name', 'like', '%' . $val . '%');
            $q->orWhere(DB::raw("CONCAT('long_name', ' ', 'short_name')"), 'like', '%' . $val . '%');
        };
        
        $data = $data->with([$filter => $check])
        ->whereHas($filter, $check)
        ->get();

        $data = $this->getPPTags($data);
        return $data;
    }

    /**
     * search user by user tags
     * @param $data
     * @param $eventUuid
     * @param $val
     * @param $filter
     * @param users data
     */
    public function searchByUserTags($eventUuid, $val, $filter, $data) {

        $tags = UserTag::where(function($q) use ($filter, $val) {
            $q->where('status', '1');
            $q->where('tag_type', $filter);
            $q->where('tag_EN', 'like', '%' . $val . '%');
            $q->orWhere('tag_FR', 'like', '%' . $val . '%');
        })->get()->pluck('id')->toArray();
        
        $tagsId = function($q) use ($tags) {
            $q->whereIn('id', $tags);
        };
        $data = $data->with(['tagsRelationForPP' => $tagsId])->whereHas('tagsRelationForPP', $tagsId)
        ->get();

        $data = $this->getPPTags($data);
        return $data;
    }
}
