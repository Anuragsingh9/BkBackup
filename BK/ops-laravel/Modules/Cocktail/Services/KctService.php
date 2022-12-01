<?php

namespace Modules\Cocktail\Services;

use App\AccountSettings;
use App\Entity;
use App\EntityUser;
use App\Http\Controllers\CoreController;
use App\Meeting;
use App\Organisation;
use App\Services\Service;
use App\Services\SettingService;
use App\Setting;
use App\SuperadminSetting;
use App\User;
use Exception;
use Hyn\Tenancy\Contracts\Hostname;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Cocktail\Entities\EventSpace;
use Modules\Cocktail\Entities\EventTag;
use Modules\Cocktail\Entities\EventTagMata;
use Modules\Cocktail\Exceptions\CustomValidationException;
use Modules\Cocktail\Services\V2Services\KctCoreService;
use Modules\Events\Entities\Event;
use DB;
use Modules\Events\Exceptions\CustomException;
use Modules\Events\Service\EventService;
use Modules\Events\Service\ValidationService;

class KctService extends Service {
    
    private $core;
    /**
     * @var \Illuminate\Foundation\Application
     */
    private $tenancy;
    
    /**
     * @return CoreController
     */
    public function getCore() {
        if ($this->core) return $this->core;
        return $this->core = app(\App\Http\Controllers\CoreController::class);
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
     * @return Hostname
     */
    public function getHostname() {
        $tenancyModel = $this->getTenancy();
        return $tenancyModel->hostname();
    }
    
    public function getAccountSetting() {
        $hostname = $this->getHostname();
        $account = AccountSettings::where('account_id', $hostname->id)->first();
        return $account->setting;
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
     * @param $userId
     * @param null $eventUuid
     * @return User
     * @throws CustomValidationException
     */
    public function getUserBadge($userId, $eventUuid = null) {
        $user = User::with([
            'unions',
            'companies',
            'instances',
            'presses',
            'facebookUrl',
            'twitterUrl',
            'instagramUrl',
            'linkedinUrl',
            'eventUsedTags',
            'personalInfo',
        ])->find(Auth::user()->id);
        //get tag
        $tag=$this->getUserTag();
        $user->tag=  $tag;
        if (!$user) {
            throw new CustomValidationException('exists', 'user');
        }
        
        if ($eventUuid) {
            $user->load(['eventUser' => function ($q) use ($eventUuid) {
                $q->where('event_uuid', $eventUuid);
            }]);
        }
    
        return KctCoreService::getInstance()->loadPPTags($user);
    }
    
    public function trans($key, $attribute) {
        return $attribute
            ? __("cocktail::message.$key", ['attribute' => __("cocktail::words.$attribute")])
            : __("cocktail::message.$key");
    }
    
    public function uploadUserProfile($image) {
        return $this->fileUploadToS3(
            config("cocktail.s3.user_avatar"),
            $image,
            'public');
    }
    
    /**
     * @param $param
     * @param $request
     * @return User
     * @throws CustomValidationException|Exception
     */
    public function updateUserProfile($param, $request) {
        if ($request->hasFile('avatar')) {
            $param['avatar'] = $this->uploadUserProfile($request->avatar);
        }
        $update = User::where('id', Auth::user()->id)->update($param);
        if (!$update) {
            throw new Exception();
        }
        return $this->getUserBadge(Auth::user()->id);
    }
    
    public function getUserEventRelation($eventUuid) {
        $activeEvent = KctEventService::getInstance()->getUsersUpcomingEvent(Auth::user());
        return [
            'is_participant' => AuthorizationService::getInstance()->isUserEventMember($eventUuid),
            'active_event'   => $activeEvent ? $activeEvent->event_uuid : null,
        ];
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
    
    public function getDefaultHost($request) {
        $subDomain = $this->getSubDomain($request);
        $subDomain = $subDomain != '' ? "$subDomain." : $subDomain;
        return $subDomain . config("cocktail.default.front_domain");
    }
    
    public function resetPassword($request) {
        if (User::where('identifier', $request->identifier)->count() == 1) {
            User::where('identifier', $request->identifier)
                ->update(['password' => Hash::make($request->password), 'identifier' => null]);
        } else {
            throw new CustomValidationException('invalid_email', null, 'message');
        }
        return true;
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
    
    public function getLexoRank($prev = null, $next = null) {
        // if prev null will assume in very first, if next null we'll assume at the end
        // boundary testing care.
        $prev = $prev == null ? config('kct_const.lexo_rank_min') : $prev;
        $next = $next == null ? config('kct_const.lexo_rank_max') : $next;
        // as between 'a' and 'b' we will need 'an' so for that we need to make string compare like
        // between a0 and b0 so we get an
        $strLen = $this->getGreaterStringLength($prev, $next) + 1;
        // making prev and next to append the a in prev, z in next
        // reason when we need to find between same length and next to each other like
        // b and c so it will like finding between ba and cz ->
        // no to care it will not make like ca cb cc.... cz so order will have prefix b -> bX will be result
        // e.g. 2 -> between baaa and baab  then -> baaam
        $prev = $this->addLexoStrPad($prev, $strLen, true);
        $next = $this->addLexoStrPad($next, $strLen, false);
        return $this->findRankBetween($prev, $next);
    }
    
    /*
     * HELPER METHODS
     */
    
    /**
     * This helper method returns the greatest string list among variable string parameters
     *
     * @param string ...$strings
     * @return int
     */
    public function getGreaterStringLength(...$strings) {
        $count = 0;
        foreach ($strings as $string) {
            $i = strlen($string);
            if ($i > $count) {
                $count = $i;
            }
        }
        return $count;
    }
    
    /**
     * adds the extra digit to string for finding between two consecutive character or lexo
     *
     * @param $string
     * @param $strLen
     * @param $min
     * @return string
     */
    public function addLexoStrPad($string, $strLen, $min) {
        $minMax = ($min ? 'min' : 'max');
        return str_pad($string, $strLen, config("kct_const.lexo_rank_$minMax"));
    }
    
    /**
     * actually finding rank between two equal length strings
     *
     * @param $prev
     * @param $next
     * @return string
     */
    public function findRankBetween($prev, $next) {
        $len = strlen($prev);
        $rank = '';
        for ($i = 0; $i < $len; $i++) {
            if ($prev[$i] == $next[$i]) {
                $rank .= $prev[$i];
            } else {
                $mid = $this->findMiddleChar($prev[$i], $next[$i]);
                $rank .= $mid;
                if ($mid != $prev[$i]) {
                    break;
                }
            }
        }
        return $rank;
    }
    
    public function findMiddleChar($i, $j) {
        return chr((int)((ord($i) + ord($j)) / 2));
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
        return EventService::getInstance()->prepareEmailTags($event, $userId, $hostname);
    }
    
    /**
     * To prepare the registration link
     *
     * @param Request $request
     * @param Event $event
     * @return string
     */
    public function getRegLink($request, $event) {
        $link = config('cocktail.registration_link');
        $replace = [
            ':domain'    => $this->getDefaultHost($request),
            ':eventUuid' => $event->event_uuid,
        ];
        return str_replace(array_keys($replace), array_values($replace), $link);
    }
    
    /**
     * To get the current set language and the available languages possible
     *
     * @param Request $request
     * @return array
     */
    public function getUserLang($request) {
        $lang = config('cocktail.default.lang');
        if ($user = $request->user('api')) {
            if (isset($user->setting)) {
                $json = json_decode($user->setting);
                $lang = isset($json->lang) ? strtolower($json->lang) : $lang;
                session()->put('lang', strtoupper($lang));
            }
        } else {
            $hostname = app(\Hyn\Tenancy\Environment::class)->hostname();
            $setting = AccountSettings::where('account_id', $hostname->id)->first();
            if (session()->has('lang')) {
                $lang = strtolower(session()->get('lang'));
            } else if (!empty($hostname) && isset($setting->setting['lang'])) {
                $lang = strtolower($setting->setting['lang']);
                session()->put('lang', strtoupper($setting->setting['lang']));
            } else {
                session()->put('lang', strtoupper($lang));
            }
            $lang = strtoupper($lang);
        }
        $settingData = Setting::where('setting_key', 'languages_to_show')->first();
        $enabledLanguages = [$lang];
        if ($settingData) {
            $enabledLanguages = json_decode($settingData->setting_value, 1);
        }
        
        return [
            'current'           => strtoupper($lang),
            'enabled_languages' => $enabledLanguages,
        ];
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
            return Entity::where(function ($q) use ($val) {
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
        
        $COMPANY = 2;
        
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
        return Entity::updateOrCreate([
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
     * @param Request $request
     * @return array|null
     */
    public function getUserDetails($request) {
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
        $event = ValidationService::getInstance()->resolveEvent($event);
        if ($event
            // event type must be virtual for version 2
            && $event->type == config('events.event_type.virtual')
            
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
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to find that provided space uuid belongs to which type of events
     * e.g. ini,ext,virtual (bj) -> version 1
     * virtual (non bj) -> version 2
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $spaceUuid
     * @return int
     */
    public function findEventVersionBySpace($spaceUuid) {
        $space = EventSpace::find($spaceUuid);
        if ($space) {
            return $this->findEventVersion($space->event_uuid);
        }
        return 1;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @decsription To prepare the init data to load the application properly with some db values
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @return array
     */
    public function getInitData($request) {
        $account = KctService::getInstance()->getHostname();
        $organisation = $account ? Organisation::where('account_id', $account->id)->first() : null;
        $mainColor = SettingService::getInstance()->getMainColor();
        $lang = $this->getUserLang($request);
        $auth = $this->getUserDetails($request);
        $moduleEnabled = $this->isModuleEnabled();
        $activeEvent = KctEventService::getInstance()->getUserActiveEventUuid($request);
        
        return [
            'organisation_name' => $organisation ? $organisation->name_org : "",
            'main_color'        => $mainColor,
            'lang'              => $lang,
            'auth'              => $auth,
            'kct_enabled'       => checkValSet($moduleEnabled),
            'active_event'      => $activeEvent ? $activeEvent : null,
        ];
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to delete the profile picture and return the user badge response.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return User
     * @throws CustomException
     * @throws CustomValidationException
     */
    public function deleteProfilePic() {
        $user = Auth::user();
        $this->getCore()->fileDeleteBys3($user->avatar);
        $res = User::where('id', Auth::user()->id)->update(['avatar' => null]);
        if (!$res) {
            throw new CustomException(null, "User Profile Delete Failed.");
        }
        return $this->getUserBadge(Auth::user()->id);
    }
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description get user tags
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return User
     * @throws CustomException
     * @throws CustomValidationException
     */
    public function getUserTag() {
        $user = Auth::user();
        $used_tag=[];
        $unused_tag=[];
        $used_tag=EventTag::whereExists(function ($query) use($user) {
            $query->select(DB::raw(1))
                ->from('event_tag_metas')
                ->whereColumn('event_tag_metas.tag_id', 'event_tags.id')
            ->where('event_tag_metas.user_id', $user->id);
        })->where('is_display',1)->orderBy('name','asc')->get(['id','name']);
        $unused_tag=EventTag::whereNotExists(function ($query) use($user) {
            $query->select(DB::raw(1))
                ->from('event_tag_metas')
                ->whereColumn('event_tag_metas.tag_id', 'event_tags.id')
                ->where('event_tag_metas.user_id', $user->id);
        })->where('is_display',1)->orderBy('name','asc')->get(['id','name']);
        return ['used_tag'=>  $used_tag,'unused_tag'=>$unused_tag];
    }
    public function addUserTag($tag_id){

        $user = Auth::user();
        $existTag=EventTagMata::where(['tag_id'=>$tag_id,'user_id'=>$user->id])->first();
        if($existTag){
            throw new CustomException(null, "Tag already added");
        }
        $res=EventTagMata::create(['tag_id'=>$tag_id,'user_id'=>$user->id]);
        if (!$res) {
            throw new CustomException(null, "Failed to Add ");
        }
        return $this->getUserTag();
    }
    public function deleteTagUser($tag_id){

        $user = Auth::user();

        $res=EventTagMata::where(['tag_id'=>$tag_id,'user_id'=>$user->id])->delete();
        if (!$res) {
            throw new CustomException(null, "Failed to delete ");
        }
        return $this->getUserTag();
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
    public function getDecodeSetting($key, $makeResultInArray=0) {
        $setting = Setting::where('setting_key', $key)->first();
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
        $event = Event::with([
            'spaces' => function ($q) {
                $q->orderBy('order_id');
            },
            'spaces.spaceUsers',
            'defaultSpace',
        ])->where('event_uuid', $eventUuid)->where('type', 'virtual')->first();
    
        $event->spaces = EventSpaceService::getInstance()->filterSpacesWithMaxCapacity($event->spaces, $event, true);
        return $event;
    }
}
