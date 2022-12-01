<?php


namespace Modules\Events\Service;


use App\AccountSettings;
use App\DummyUsers;
use App\Http\Controllers\CoreController;
use App\Meeting;
use App\Organisation;
use App\Presence;
use App\Services\MeetingService;
use App\Services\Service;
use App\Services\WorkshopService;
use App\Setting;
use App\User;
use App\WorkshopMeta;
use Carbon\Carbon;
use Exception;
use App\Workshop;
use Hyn\Tenancy\Contracts\Hostname;
use Hyn\Tenancy\Environment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Modules\Cocktail\Entities\EventDummyUser;
use Modules\Cocktail\Entities\EventSpace;
use Modules\Cocktail\Events\EventManuallyOpenedEvent;
use Modules\Cocktail\Services\Contracts\EmailFactory;
use Modules\Cocktail\Services\Contracts\ExternalEventFactory;
use Modules\Cocktail\Services\DataService;
use Modules\Cocktail\Services\EventSpaceService;
use Modules\Cocktail\Services\KctEventService;
use Modules\Cocktail\Services\KctService;
use Modules\Cocktail\Services\V2Services\KctCoreService;
use Modules\Events\Entities\Event;
use Modules\Events\Entities\Eventable;
use Modules\Events\Entities\Organiser;
use Modules\Events\Exceptions\CustomException;
use Modules\Events\Exceptions\CustomValidationException;

class EventService extends Service {
    private $accountSetting;
    /**
     * @var WorkshopService|null
     */
    private $workshopService;
    private $eventSettings;
    /**
     * @var ExternalEventFactory
     */
    private $externalEventFactory;
    /**
     * @var ExternalEventFactory
     */
    private $eventFactory;
    
    /**
     * @var EmailFactory
     */
    private $emailFactory;
    
    /**
     * @var Environment
     */
    private $tenancy;
    /**
     * @var Collection
     */
    private $eventsWithSpaces;
    /**
     * @var CoreController
     */
    private $core;
    
    /**
     * @return ExternalEventFactory
     */
    public function getEventFactory() {
        if (!$this->eventFactory) {
            $this->eventFactory = app(ExternalEventFactory::class);
        }
        return $this->eventFactory;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to create an internal event and workshop
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @param $data
     * @return Event
     * @throws CustomException
     * @throws Exception
     */
    public function createInternalEvent($request, $data) {
        // internal event will have the workshop also
        $data['eventData']['workshop_id'] = $this->createWorkshopForEvent($request, $data);
        return $this->createEvent($data);
    }
    
    /**
     * @param Request $request
     * @param $data
     * @return Event
     * @throws CustomException|\Modules\Cocktail\Exceptions\CustomValidationException|Exception
     */
    public function createVirtualEvent($request, $data) {
        // data prepare
        $orgAdmin = $data['orgAdmin'];
        $defaultOrganiserUser = $data['defaultOrganiserUser'];
        
        // virtual event will have the workshop also
        $data['eventData']['workshop_id'] = $this->createWorkshopForEvent($request, $data);
        $data['eventData']['bluejeans_id'] = $this->createBlueJeansEvent($request);
        
        // preparing the data to insert
        $event = $this->createEvent($data);
        $this->createDefaultSpace($data['defaultSpace'], $event);
        KctEventService::getInstance()->addUserToEvent($orgAdmin->id, $event->event_uuid, 1, 1); // adding validator /deputy
        KctEventService::getInstance()->addUserToEvent($defaultOrganiserUser->id, $event->event_uuid, 1, 1); // adding president secretory
        return $event;
    }
    
    /**
     * @param $data
     * @return Event
     * @throws Exception
     */
    public function createExternalEvent($data) {
//        $param = $this->prepareEventParam($request, $imageUrl);
        return $this->createEvent($data);
    }
    
    
    /**
     * @param array $param
     * @return Event
     * @throws Exception
     */
    public function createEvent($param) {
        $data = Event::create($param['eventData']);
        if (!$data)
            throw new Exception();
        
        $param['organiser']['event_id'] = $data->id;
        $eventable = Eventable::create($param['organiser']);
        if (!$eventable)
            throw new Exception();
        return $data;
    }
    
    /**
     * @param $param
     * @param $event
     * @return EventSpace
     * @throws Exception
     */
    public function createDefaultSpace($param, $event) {
        $param['event_uuid'] = $event->event_uuid;
        if (isset($event->event_fields['is_dummy_event']) && $event->event_fields['is_dummy_event']) {
            $space = $this->createSpaceForDummy($param);
        } else {
            $space = EventSpaceService::getInstance()->create($param);
        }
        return $space;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To create the space for the dummy event type
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $param
     * @throws Exception
     */
    public function createSpaceForDummy($param) {
        $param['max_capacity'] = config('cocktail.dummy_event.dummy_space.numberOfUsers');
        $noOfDummySpaces = config('cocktail.dummy_event.dummy_space.dummy_spaces_count');
        $space = [];
        // as there is three spaces
        for ($i = 1; $i <= $noOfDummySpaces; $i++) {
            $param['space_name'] = __("cocktail::message.dummy_space{$i}_line_1");
            $param['space_short_name'] = '';
            $param['space_mood'] = __("cocktail::message.dummy_space{$i}_mood");
            $space[] = EventSpaceService::getInstance()->create($param);
        }
        $this->putDummyUsersInSpace($space);
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @desription To add the dummy users to spaces
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $spaces
     */
    public function putDummyUsersInSpace($spaces) {
        $dummyUsers = DummyUsers::get()->toArray();
        $this->addDummyUserToSpace($spaces[0], 22, $dummyUsers);
        $this->addDummyUserToSpace($spaces[1], 14, $dummyUsers);
        $this->addDummyUserToSpace($spaces[2], 8, $dummyUsers);
        
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To add the provided number of users in space
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $space
     * @param $numberOfUsers
     * @param $dummies
     */
    public function addDummyUserToSpace($space, $numberOfUsers, &$dummies) {
        $c = count($dummies);
        $data = [];
        for ($i = 0; $i < $numberOfUsers && $i < $c; $i++) {
            $data[] = [
                'event_uuid'    => $space->event_uuid,
                'space_uuid'    => $space->space_uuid,
                'dummy_user_id' => $dummies[$i]['id']
            ];
        }
        $remove = min($c, $numberOfUsers);
        $dummies = array_slice($dummies, $remove);
        EventDummyUser::insert($data);
    }
    
    /**
     * To prepare the event default fields which is now adding keep contact graphics setting
     * and storing opening hours of the event for space
     *
     * @param Request $request
     * @return array
     */
    public function prepareEventFields($request) {
        $eventFields = config('events.defaults.keepContact');
        $eventFields['keepContact']['page_customisation']['keepContact_page_logo'] = config('cocktail.default.kct_logo');
        $eventFields = $this->addMainColor($eventFields);
        $eventFields = $this->addGraphicsSetting($eventFields, $request);
        return array_merge(
            $eventFields,
            [
                'opening_hours' => [
                    'after'  => $request->input('opening_hours_after', config('cocktail.default.opening_before')),
                    'before' => $request->input('opening_hours_before'),
                    'during' => $request->input('opening_hours_during', config('cocktail.default.opening_after')),
                ]
            ]
        );
    }
    
    /**
     * To add the graphics for the event,
     * if enabled to reuse previous event graphics fetch and put else use the default one
     *
     * @param array $eventFields
     * @param Request $request
     * @return array
     */
    public function addGraphicsSetting($eventFields, $request) {
        if ($request->has('re_use_graphics') && $request->input('re_use_graphics') == 1) {
            $previousEvent = Event::where('type', config('events.event_type.virtual'))
                ->orderBy('id', 'desc')
                ->first();
            if ($previousEvent && isset($previousEvent->event_fields['keepContact']['graphics_setting'])) {
                $eventFields['keepContact']['graphics_setting'] = $previousEvent->event_fields['keepContact']['graphics_setting'];
            }
        }
        return $eventFields;
    }
    
    /**
     * To get the default main color from account setting and add that to event graphics setting
     *
     * @param $eventFields
     * @return mixed
     */
    public function addMainColor($eventFields) {
        $mainColor = Setting::where('setting_key', 'pdf_graphic')->first();
        if ($mainColor) {
            $color = json_decode($mainColor->setting_value, 1);
            if (isset($color['color1'])) {
                $c1 = [
                    'r' => $color['color1']['r'],
                    'g' => $color['color1']['g'],
                    'b' => $color['color1']['b'],
                ];
                $color1 = function ($alpha) use ($c1) {
                    return array_merge($c1, ['a' => $alpha]);
                };
                $eventFields['keepContact']['graphics_setting']['keepContact_color_1']['color'] = $color1(1);
            }
            if (isset($color['color1'])) {
                $c2 = [
                    'r' => $color['color2']['r'],
                    'g' => $color['color2']['g'],
                    'b' => $color['color2']['b'],
                ];
                $color2 = function ($alpha) use ($c2) {
                    return array_merge($c2, ['a' => $alpha]);
                };
                $eventFields['keepContact']['graphics_setting']['keepContact_color_2']['color'] = $color2(1);
                $eventFields['keepContact']['graphics_setting']['keepContact_background_color_2']['color'] = $color2(0.8);
                $eventFields['keepContact']['graphics_setting']['keepContact_selected_space_color']['color'] = $color2(0.2);
                $eventFields['keepContact']['graphics_setting']['keepContact_unselected_space_color']['color'] = $color2(0.5);
            }
        }
        return $eventFields;
    }
    
    /**
     * @param $request
     * @return array
     */
    public function getBlueJeansSetting($request) {
        if (!$this->isBlueJeansEnabled()) {
            $request = null;
        }
        return DataService::getInstance()->prepareBlueJeansParam($request);
    }
    
    public function isBlueJeansEnabled() {
        $tenancy = app(\Hyn\Tenancy\Environment::class);
        $this->accountSetting = AccountSettings::where('account_id', $tenancy->hostname()->id)->first();
        if ($this->accountSetting && isset($this->accountSetting->setting['event_settings']['bluejeans_enabled'])) {
            $isBluejeansEnabled = $this->accountSetting->setting['event_settings']['bluejeans_enabled'];
            // if bluejeans enabled from account and from event insert values
            return (boolean)$isBluejeansEnabled;
        }
        return false;
    }
    
    
    public function getEventsListWithOrganiser($tense, $orderBy, $order, $itemPerPage = null, $key = null) {
        // the possible sorting fields
        $field = $this->resolveOrderByEventList($orderBy);
        $order = $this->getOrderForEventList($order, $tense);
        // as per past future we need to apply this operator on date time
        $operator = ($tense != 'past') ? '>' : '<';
        
        // preparing the builder which will get the all data
        $data = $this->prepareEventListBuilder($operator, $orderBy);
        
        $data = $this->applyFilterAccordingToUserRole($data);
        
        $data = $this->applyFilterForVirtualEvents($data);
        
        if ($key) { // if key passed that mean no sorting in searching time
            $data = $this->applySearchOrderToEventList($key, $data);
        } else { // apply order as passed
            $data = $this->applyOrderEventList($field, $order, $data);
        }
        
        if ($itemPerPage) {
            return $data->paginate($itemPerPage);
        }
        
        return $data->get();
    }
    
    public function resolveOrderByEventList($orderBy) {
        $possibleOrder = [
            // front side    => actual database field name on which sort possible
            'title'          => 'title',
            'type'           => 'type',
            'end_time'       => 'e_time',
            'start_time'     => 's_time',
            'organiser_name' => 'organiser_name',
            'date'           => 'order_date',
        ];
        return (($orderBy && isset($possibleOrder[$orderBy])) ? $possibleOrder[$orderBy] : $possibleOrder[config('events.defaults.event_list_order')]);
        
    }
    
    /**
     * @param Builder $data
     * @return mixed
     */
    private function applyFilterAccordingToUserRole($data) {
        if (!$this->isAdmin()) {
            $data = $data->whereHas('isCurrentUserMember');
        }
        return $data;
    }
    
    /**
     * @param Builder $data
     * @return mixed
     */
    private function applyFilterForVirtualEvents($data) {
        $actSetting = $this->getAccountSetting();
        $setting = $actSetting->setting;
        if (!isset($setting['event_settings']['keep_contact_enable']) || !$setting['event_settings']['keep_contact_enable']) {
            $data = $data->where('type', '!=', 'virtual');
        }
        return $data;
    }
    
    /**
     *
     * @param $order
     * @param $tense
     * @return string
     */
    private function getOrderForEventList($order, $tense) {
        if ($order) {
            return $order == 'desc' ? 'desc' : 'asc';
        } else { // if order is not given we need to identify order by past future
            return ($tense == 'past') ? 'desc' : 'asc';
        }
    }
    
    /**
     * @param $operator
     * @param $isOrderable // this will make some joins useful for ordering further
     * @return Builder
     */
    private function prepareEventListBuilder($operator, $isOrderable) {
        $data = Event::with(['workshop', 'spaces'])
            ->select('event_info.*')
            ->selectRaw(" TIME_FORMAT(event_info.start_time, '%h:%i %p') as start_time, start_time as s_time ")
            ->selectRaw(" TIME_FORMAT(event_info.end_time, '%h:%i %p') as end_time, end_time as e_time ")
            ->selectRaw("DATE_FORMAT(event_info.date, '%M %d,%Y') as date, date as order_date")
            ->where(function ($q) use ($operator) {
                $q->orWhere('date', $operator, date('Y-m-d'));
                $q->orWhere(function ($q) use ($operator) {
                    $q->where('date', '=', date('Y-m-d'));
                    $q->whereRaw("(CASE WHEN `event_info`.type='virtual' THEN end_time ELSE start_time END) $operator '" . date('H:i:s') . "'");
                });
            });
        if ($isOrderable) {
            $data = $data
                ->selectRaw("(CASE WHEN `event_info`.type='ext' THEN CONCAT(`o`.fname, ' ',`o`.lname) ELSE CONCAT(`u`.fname, ' ',`u`.lname) END) as 'organiser_name'")
                ->join('eventables as ev', 'ev.event_id', '=', 'event_info.id')
                ->leftJoin('users as u', 'u.id', '=', 'ev.eventable_id')
                ->leftJoin('event_organisers as o', 'o.id', '=', 'ev.eventable_id');
        }
        return $data;
    }
    
    /**
     * @param string $field
     * @param string $order
     * @param Builder $data
     * @return Builder
     */
    private function applyOrderEventList($field, $order, $data) {
        if ($field == 'order_date') {
            return $data->orderBy($field, $order)
                ->orderBy('s_time', $order)
                ->orderBy('id');
        } else {
            return $data->orderBy($field, $order)
                ->orderBy('order_date', $order)
                ->orderBy('id');
        }
    }
    
    private function applySearchOrderToEventList($key, $data) {
        $user = function ($q) use ($key) {
            $q->where(DB::raw("LOWER(fname)"), 'like', strtolower("%$key%"));
            $q->orWhere(DB::raw("LOWER(lname)"), 'like', strtolower("%$key%"));
            $q->orWhere(DB::raw("LOWER(email)"), 'like', strtolower("%$key%"));
        };
        $event = function ($query) use ($key) {
            $query->orWhere(DB::raw("LOWER(title)"), 'like', strtolower("%$key%"));
            $query->orWhere(DB::raw("LOWER(date)"), 'like', strtolower("%$key%"));
            $query->orWhere(DB::raw("LOWER(date)"), 'like', strtolower("%$key%"));
        };
        $rawOrder = function ($key, $o) {
            return "WHEN title LIKE '" . addslashes("$key") . "' COLLATE utf8mb4_unicode_ci  THEN $o ";
        };
        return $data->where(function ($query) use ($event, $user) {
            $query->whereHas('users', $user);
            $query->orWhereHas('organisers', $user);
            $query->orWhere($event);
        })->orderBy(DB::raw(
            "CASE {$rawOrder("$key%", 1)} {$rawOrder("%$key%", 2)} {$rawOrder("%$key%", 3)} ELSE 4 END "
        ));
    }
    
    public function isEventWorkshop($workshopId) {
        try {
            $isEventWorkshop = Workshop::where('id', $workshopId)
                ->where('is_qualification_workshop', 3)
                ->withoutGlobalScopes()
                ->count();
            if ($isEventWorkshop)
                return true;
            return response()->json(['status' => false, 'msg' => 'Workshop Doesn\'t belongs to Event'], 422);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'data' => 'Internal Server Error in getting event workshop'], 500);
        }
    }
    
    /**
     * To Prepare the common tags for the event module
     *
     * @param Event $event
     * @param $user
     * @param Hostname $hostname
     * @return array
     */
    public function prepareEmailTags($event, $user, $hostname = null) {
        $event = ValidationService::getInstance()->resolveEvent($event);
        if (!$event) {
            return [];
        }
        $participant = ValidationService::getInstance()->resolveUser($user);
        $eventOrganiser = ($event && $event->type == "ext") ? $event->organisres->first() : $event->users->first();
        $workshop = $event->workshop;
        $meeting = $workshop ? $event->workshop->meetings->first() : null;
        $member = workshopValidatorPresident($workshop);
        if ($event && $event->type == config('events.event_type.int')) {
            return [
                '[[EventName]]'                 => (($event) ? $event->title : ''),
                '[[OrganiserFN]]'               => (($eventOrganiser) ? "$eventOrganiser->fname $eventOrganiser->lname" : ''),
                '[[OrganiserEmail]]'            => (($eventOrganiser) ? $eventOrganiser->email : ''),
                '[[ParticipantLN]]'             => (($participant) ? $participant->lname : ''),
                '[[ParticipantFN]]'             => (($participant) ? $participant->fname : ''),
                '[[WorkshopMeetingAddress]]'    => $meeting ? $meeting->place : '',
                '[[WorkshopMeetingDate]]'       => $meeting ? $meeting->date : '',
                '[[WorkshopMeetingTime]]'       => $meeting ? $meeting->start_time : '',
                '[[WorkshopMeetingName]]'       => $meeting ? $meeting->name : '',
                '[[WorkshopLongName]]'          => $workshop->workshop_name,
                '[[WorkshopShortName]]'         => $workshop->code1,
                '[[WorkshopPresidentFullName]]' => "{$member['p']['fname']} {$member['p']['lname']}",
                '[[PresidentEmail]]'            => $member['p']['email'],
            ];
        } else if ($event && $event->type == config('events.event_type.virtual', 'virtual')) {
            $loginLink = KctCoreService::getInstance()->getRedirectUrl(request(), 'quick-login', ['EVENT_UUID' => $event->event_uuid]);
            $registerLink = KctCoreService::getInstance()->getRedirectUrl(request(), 'event-register', ['EVENT_UUID' => $event->event_uuid]);
            $eventJoinLink = "<a href='$loginLink'>$loginLink</a>";
            $eventRegLink = "<a href='$registerLink'>$registerLink</a>";
            return [
                '[[EventName]]'                 => $event->title,
                '[[OrganiserFN]]'               => (($eventOrganiser) ? "$eventOrganiser->fname $eventOrganiser->lname" : ''),
                '[[OrganiserEmail]]'            => (($eventOrganiser) ? $eventOrganiser->email : ''),
                '[[ParticipantLN]]'             => (($participant) ? $participant->lname : ''),
                '[[ParticipantFN]]'             => (($participant) ? $participant->fname : ''),
                '[[WorkshopMeetingDate]]'       => $meeting ? $meeting->date : '',
                '[[WorkshopMeetingTime]]'       => $meeting ? $meeting->start_time : '',
                '[[WorkshopMeetingName]]'       => $meeting ? $meeting->name : '',
                '[[WorkshopLongName]]'          => $workshop->workshop_name,
                '[[WorkshopShortName]]'         => $workshop->code1,
                '[[WorkshopPresidentFullName]]' => "{$member['p']['fname']} {$member['p']['lname']}",
                '[[PresidentEmail]]'            => $member['p']['email'],
                '[[EventJoinLink]]'             => $eventJoinLink,
                '[[EvenRegistrationLink]]'      => $eventRegLink,
            ];
        }
    }
    
    /**
     * To get the event join/login url
     *
     * @param $event
     * @param $domain
     * @return string
     */
    public function getEventJoinLink($event, $domain) {
        $eventJoinLink = config("cocktail.links.kct_event_join_link");
        $joinLink = env('HOST_TYPE', 'https://') . "$domain." . config("cocktail.default.front_domain");
        return str_replace([':domain', ':event_uuid'], [$joinLink, $event->event_uuid], $eventJoinLink);
    }
    
    /**
     * To get the event registration/login url
     *
     * @param $event
     * @param $domain
     * @return string
     */
    public function getEventRegistrationLink($event, $domain) {
        $eventRegLink = config("cocktail.links.kct_event_reg_link");
        $joinLink = env('HOST_TYPE', 'https://') . "$domain." . config("cocktail.default.front_domain");
        return str_replace([':domain', ':event_uuid'], [$joinLink, $event->event_uuid], $eventRegLink);
    }
    
    public function getSetting($key, $lang = '') {
//        check lang in user setting
        if (!$lang && isset(Auth::user()->setting) && !empty(Auth::user()->setting)) {
            $lang = json_decode(Auth::user()->setting)->lang;
        } else if (!$lang && isset($_SESSION['lang'])) { // check lang in session
            $lang = $_SESSION['lang'];
        }
        $lang = ($lang == 'FR') ? '_FR' : '_EN';
        $key = $key . $lang;
        $data = Setting::where('setting_key', $key)->first();
        return ($data) ? json_decode($data) : $data;
    }
    
    public function getEventWorkshopsWithMemberCount($paginate, $page, $field, $order, $key = null) {
        $builder = $this->eventWorkshopListBuilder();
        $builder = $this->eventWorkshopListSearch($builder, $key);
        $builder = $this->eventWorkshopListRoleFilter($builder);
        $events = $builder->get();
        $orderBy = $this->eventWorkshopListSolveOrder($field);
        $events = $this->eventWorkshopListTransform($events);
        $events = $this->eventWorkshopListSort($events, $orderBy, $order);
        
        if ($paginate) {
            return $this->eventWorkshopListPaginate($events, $paginate, $page);
        }
        return $events;
    }
    
    /**
     * To apply the filter if user is not admin show only that workshop in which current user is admin
     *
     * @param Builder $builder
     * @return Builder
     */
    public function eventWorkshopListRoleFilter($builder) {
        if (!$this->isAdmin()) {
            $builder = $builder->whereHas('workshop', function ($q) {
                $q->whereHas('meta_data', function ($q) {
                    $q->where('user_id', Auth::user()->id);
                    $q->whereIn('role', [1, 2]);
                });
                $q->withoutGlobalScopes();
            });
        }
        return $builder;
    }
    
    /**
     * To send the data in custom paginated form
     *
     * @param Collection $events
     * @param $perPage
     * @param $page
     * @return LengthAwarePaginator
     */
    public function eventWorkshopListPaginate($events, $perPage, $page) {
        return new \Illuminate\Pagination\LengthAwarePaginator(
            array_values($events->forPage($page, $perPage)->all()),
            $events->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath()]
        );
    }
    
    /**
     * @param Collection $events
     * @param $orderBy
     * @param $order
     * @return Collection
     */
    public function eventWorkshopListSort($events, $orderBy, $order) {
        $order = $order && $order == 'asc' ? 'sortBy' : 'sortByDesc';
        $events = $events->$order(function ($r) use ($orderBy) {
            return strtolower($r->$orderBy);
        });
        $result = new Collection();
        foreach ($events as $e) {
            $result->push($e);
        }
        return $result;
    }
    
    
    /**
     * To get the column by which sorting will done from transform columns
     *
     * @param string $field
     * @return string
     */
    public function eventWorkshopListSolveOrder($field) {
        $possibleOrder = [
            'workshop_id'   => 'order_workshop_id',
            'workshop_name' => 'order_name',
            'code1'         => 'order_code1',
            'secretory'     => 'order_secretory',
            'deputy'        => 'order_deputy',
            'member'        => 'order_members',
            'members'       => 'order_members',
        ];
        return $field && isset($possibleOrder[$field]) ? $possibleOrder[$field] : $possibleOrder['workshop_id'];
    }
    
    /**
     * @param Collection $events
     * @return Collection
     */
    public function eventWorkshopListTransform($events) {
        return $events->transform(function ($event) {
            $mCount = [];
            foreach ($event->workshop->meta as $meta) {
                $mCount[$meta->user_id] = 1;
            }
            $event->order_workshop_id = $event->workshop_id;
            $event->order_name = $event->workshop->workshop_name;
            $event->order_code1 = $event->workshop->code1;
            $event->order_secretory = $event->secretory ? "{$event->secretory->user->fname} {$event->secretory->user->lname}" : '';
            $event->order_deputy = $event->deputy ? "{$event->deputy->user->fname} {$event->deputy->user->lname}" : null;
            $event->order_members = count($mCount);
            return $event;
        });
    }
    
    /**
     * To prepare the base builder for getting event workshop list
     *
     * @return Builder|Event
     */
    public function eventWorkshopListBuilder() {
        $types = [config('events.event_type.int'), config('events.event_type.virtual')];
        return Event::with([
            'workshop',
            'workshop.meta',
            'secretory',
            'deputy',
            'workshop.meta_data' => function ($q) {
                $q->where('role', 2);
            },
        ])
            ->select("*")
            ->whereIn('type', $types);
    }
    
    /**
     * @param Builder $builder
     * @param string $key
     * @return Builder
     */
    public function eventWorkshopListSearch($builder, $key) {
        $workshop = function ($q) {
            $q->withoutGlobalScopes();
        };
        if ($key) {
            $builder = $builder->whereHas('workshop', function ($q) use ($key) {
                $q->where('workshop_name', 'like', "%$key%");
                $q->orWhereHas('meta.user', function ($q) use ($key) {
                    $q->where('fname', 'like', "%$key%");
                    $q->orWhere('lname', 'like', "%$key%");
                    $q->orWhere(DB::raw("CONCAT(fname, ' ', lname)"), 'like', "%$key%");
                });
                $q->withoutGlobalScopes();
            });
        } else {
            $builder = $builder->whereHas('workshop', $workshop);
        }
        return $builder;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to get the first org admin in the organisation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return mixed
     * @throws CustomValidationException
     */
    public function getFirstOrgAdmin() {
        $orgAdmin = User::where('role', 'M1')->first();
        if (!$orgAdmin) {
            throw new CustomValidationException("org_admin_not_found");
        }
        return $orgAdmin;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to get the workshop code 1 as its the incremental to previous one workshop
     * which created by same org admin
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $orgAdmin
     * @return string
     */
    public function getIncrementedCode1($orgAdmin) {
        $workshop = Workshop::where('validator_id', $orgAdmin->id)
            ->where('is_qualification_workshop', 3)
            ->orderBy('code1', 'desc')
            ->withoutGlobalScopes()
            ->first(['code1']);
        return sprintf('%03d', (($workshop && isset($workshop->code1)) ? ($workshop->code1 + 1) : 1));
    }
    
    public function prepareEventParam($request, $imageUrl, $defaultOrganiserUser = null) {
        return [
            // event create parameters
            'event'     => [
                'title'              => $request->title,
                'header_text'        => $request->header_text,
                'description'        => $request->description,
                'date'               => $request->date,
                'start_time'         => $request->start_time,
                'end_time'           => $request->end_time,
                'address'            => $request->address,
                'city'               => $request->city,
                'image'              => $imageUrl,
                'type'               => $request->type,
                'created_by_user_id' => Auth::user()->id,
                'territory_value'    => (($request->type != 'ext' && $request->is_territory) ? $request->territor_value : null), // Typo error from front end fixed in backend with territory->territor
            ],
            'organiser' => [
                // eventable parameter -> for organiser of event
                'created_by_user_id' => Auth::user()->id,
                'eventable_id'       => ($request->type != 'ext' ? $defaultOrganiserUser->id : $request->organiser_id),
                'eventable_type'     => ($request->type != 'ext' ? User::class : Organiser::class),
            ],
        ];
    }
    
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To create a workshop for the event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @param $data
     * @return mixed
     * @throws CustomException
     * @throws Exception
     */
    public function createWorkshopForEvent($request, $data) {
        $orgAdmin = $data['orgAdmin'];
        $prefix = $data['prefix'];
        $organisation = $data['organisation'];
        $workshopParams = $data['workshopCreate'];
        
        $this->workshopService = WorkshopService::getInstance();
        
        $workshopId = $this->workshopService->createCommission($workshopParams, '');
        if (!($workshopId && is_int($workshopId))) {
            throw new CustomException([
                'workshop_create_response' => $workshopId,
                'data_send_to_create'      => $workshopParams,
            ], "Workshop not created");
        }
        $meetingId = $this->createMeetingForInternalEventWorkshop(
            $request,
            $orgAdmin,
            $prefix,
            $workshopId,
            $organisation
        );
        $this->createPresenceForSecDep($workshopId, $meetingId, $workshopParams);
        return $workshopId;
    }
    
    public function createPresenceForSecDep($wid, $mid, $param) {
        Presence::insert([[
            'workshop_id'     => $wid,
            'meeting_id'      => $mid,
            'user_id'         => $param['president_id'],
            'register_status' => 'I',
            'presence_status' => 'P',
        ], [
            'workshop_id'     => $wid,
            'meeting_id'      => $mid,
            'user_id'         => $param['validator_id'],
            'register_status' => 'I',
            'presence_status' => 'P',
        ]]);
    }
    
    /**
     * @param Request $request
     * @param $orgAdmin
     * @param $prefix
     * @param $workshopId
     * @param null $organisation
     * @return \Illuminate\Http\JsonResponse|string
     * @throws Exception
     */
    public function createMeetingForInternalEventWorkshop($request, $orgAdmin, $prefix, $workshopId, $organisation = null) {
        $this->meetingService = MeetingService::getInstance();
        if ($request->type == 'virtual') {
            $address = $organisation->address1;
            $title = "{$request->input('title')} - {$request->input('date')}";
            $meetingName = str_start($title, $prefix);
        } else {
            $address = $request->address;
            $city = strtoupper($request->input('city'));
            $meetingName = "$prefix - $city - {$request->input('date')}";
        }
        $meetingParams = [
            'name'        => $meetingName,
            'description' => $request->description,
            'place'       => $address,
            'mail'        => $orgAdmin->email,
            'contact_no'  => $orgAdmin->phone,
            'date'        => $request->date,
            'start_time'  => $request->start_time,
            'end_time'    => $request->end_time,
            'workshop_id' => $workshopId,
            'image'       => '',
            'user_id'     => $orgAdmin->id,
            'status'      => 1,
        ];
        $meetingId = $this->meetingService->createMeeting($meetingParams);
        
        if (!($meetingId && is_int($meetingId))) {
            throw new Exception("meeting not created");
        }
        return $meetingId;
    }
    
    /**
     * @param $request
     * @param $imageUrl
     * @param $event
     * @throws Exception
     */
    public function updateWorkshopAndMeeting($request, $imageUrl, $event) {
        $setting = Setting::where('setting_key', 'event_settings')->first();
        $decode = json_decode($setting->setting_value);
        $prefix = $event->type == 'int' ? $decode->event_org_setting->prefix : $decode->event_virtual_org_setting->prefix;
        $organisation = $this->getOrganisation();
        if ($event->type == 'virtual') {
            $address = $organisation->address1;
            $city = $organisation->city;
        } else {
            $address = $request->address;
            $city = $request->city;
        }
        $meetingData = [
            'name'        => $prefix . ' - ' . strtoupper($city) . ' ' . $request->date,
            'description' => $request->description,
            'place'       => $address,
            'mail'        => Auth::user()['email'],
            'contact_no'  => Auth::user()['phone'],
            'date'        => $request->date,
            'start_time'  => $request->start_time,
            'end_time'    => $request->end_time,
            'user_id'     => Auth::user()['id'],
            'image'       => $imageUrl,
        ];
        $meeting = Meeting::where('workshop_id', $event->workshop_id)->update($meetingData);
        $workshop = Workshop::where('id', $event->workshop_id)->withoutGlobalScopes()
            ->update(['workshop_name' => $prefix . ' - ' . strtoupper($city),]);
    }
    
    /**
     * @param $request
     * @param $imageUrl
     * @param $event
     * @throws Exception
     */
    public function updateInternalEvent($request, $imageUrl, $event) {
        $this->updateWorkshopAndMeeting($request, $imageUrl, $event);
        $eventData = [
            'title'           => $request->title,
            'header_text'     => $request->header_text,
            'description'     => $request->description,
            'date'            => $request->date,
            'start_time'      => $request->start_time,
            'end_time'        => $request->end_time,
            'address'         => $request->address,
            'city'            => $request->city,
            'territory_value' => (($event->type == 'int' && $request->is_territory) ? $request->territor_value : null), // Typo error from front end fixed in backend with territory->territor
            'image'           => $imageUrl,
        ];
        $event->update($eventData);
        $this->sendModificationMail($event);
    }
    
    /**
     * @param $request
     * @param $imageUrl
     * @param $event
     * @throws Exception|\Modules\Cocktail\Exceptions\CustomValidationException
     */
    public function updateVirtualEvent($request, $imageUrl, $event) {
        $eventData = $this->prepareEventUpdateField($request, $event, $imageUrl);
        EventSpaceService::getInstance()
            ->updateEventFollowingSpace($event, $eventData['event_fields']['opening_hours']);
        $eventData['bluejeans_id'] = $this->updateBlueJeansEvent($event, $request);
        $this->updateWorkshopAndMeeting($request, $imageUrl, $event);
        $event->update($eventData);
        $this->sendModificationMail($event);
    }
    
    /**
     * To check and send the manual opening broadcasting to users
     *
     * @param $beforeManualOpening
     * @param $request
     * @param $event
     */
    public function emitManualOpeningEvent($beforeManualOpening, $request, $event) {
        if ($event && $event->type == config("events.event_type.virtual")) {
            $afterManualOpening = $event->manual_opening;
            if ($beforeManualOpening != $afterManualOpening) {
                event(new EventManuallyOpenedEvent([
                    'eventUuid' => $event->event_uuid,
                    'namespace' => KctService::getInstance()->getSubDomain($request)
                ]));
            }
        }
    }
    
    /**
     * @param Request $request
     * @param Event $event
     * @return array
     */
    private function prepareEventUpdateField($request, $event, $imageUrl) {
        $fields = [];
        if ($event->type == config('events.event_type.virtual')) {
            $isEventRunning = ValidationService::getInstance()->isEventOrSpaceRunning($event);
            $eventField = $this->prepareOpeningHourForEvent($event, $request);
            $manualOpening = $this->prepareManualOpeningButton($request);
            if ($isEventRunning) { // event is open so update only possible values
                $fields = [
                    'end_time'       => $request->input('end_time', $event->end_time),
                    'event_fields'   => $eventField,
                    'manual_opening' => $manualOpening,
                ];
            } else {
                $bluejeansSettings = DataService::getInstance()->prepareBlueJeansParam($request);
                $fields = [
                    'title'              => $request->input('title'),
                    'header_text'        => $request->input('header_text'),
                    'description'        => $request->input('description'),
                    'date'               => $request->input('date'),
                    'start_time'         => $request->input('start_time'),
                    'end_time'           => $request->input('end_time'),
                    'address'            => $request->input('address'),
                    'city'               => $request->input('city'),
                    'territory_value'    => (($event->type == 'int' && $request->is_territory) ? $request->territor_value : null), // Typo error from front end fixed in backend with territory->territor
                    'image'              => $imageUrl,
                    'event_fields'       => $eventField,
                    'bluejeans_settings' => $bluejeansSettings,
                    'manual_opening'     => $request->manual_opening,
                ];
            }
        }
        return $fields;
    }
    
    private function prepareOpeningHourForEvent($event, $request) {
        $oldEventField = $event->event_fields;
        $isEventRunning = ValidationService::getInstance()->isEventRunning($event);
        if ($isEventRunning) {
            $oldEventField['opening_hours']['after'] = $request->opening_hours_after;
            $oldEventField['opening_hours']['before'] = $request->opening_hours_before;
        } else {
            $oldEventField['opening_hours'] = [
                'after'  => $request->opening_hours_after,
                'before' => $request->opening_hours_before,
                'during' => $request->opening_hours_during,
            ];
        }
        return $oldEventField;
    }
    
    private function prepareManualOpeningButton($request) {
        return $request->input('manual_opening', 0) ? 1 : 0;
    }
    
    /**
     * @param Request $request
     * @param string $imageUrl
     * @param Event $event
     */
    public function updateExternalEvent($request, $imageUrl, $event) {
        $eventData = [
            'title'       => $request->input('title'),
            'header_text' => $request->input('header_text'),
            'description' => $request->input('description'),
            'date'        => $request->input('date'),
            'start_time'  => $request->input('start_time'),
            'end_time'    => $request->input('end_time'),
            'address'     => $request->input('address'),
            'city'        => $request->input('city'),
            'image'       => $imageUrl,
        ];
        Eventable::where('event_id', $event->id)->update(['eventable_id' => $request->organiser_id]);
        $event->update($eventData);
    }
    
    /**
     * @param Event $event
     * @param $request
     * @return mixed|null
     * @throws \Modules\Cocktail\Exceptions\CustomValidationException
     */
    public function updateBlueJeansEvent($event, $request) {
        if ($event->bluejeans_id) { // if this event already contains a bluejeans event
            $this->externalEventFactory = app(ExternalEventFactory::class);
            $extEvtData = $this->externalEventFactory->prepareUpdateParamFromRequest($request);
            $this->externalEventFactory->update($event->bluejeans_id, $extEvtData);
            return $event->bluejeans_id;
        } else if ($request->input('event_uses_bluejeans_event', 0)) {
            $request->merge([
                'sec' => $event->users()->first()
            ]);
            return $this->createBlueJeansEvent($request);
        }
        return null;
    }
    
    public function sendModificationMail($event) {
        $members = WorkshopMeta::where('workshop_id', $event->workshop_id)
            ->whereIn('role', [0, 3])
            ->get();
        if ($event) {
            if ($event->type == config('events.event_type.int')) {
                foreach ($members as $member) {
                    $data = ($this->prepareEmailTags($event, $member->user_id));
                    $this->getEmailFactory()->sendIntModification($event, $member->user_id, $data);
                }
            } else {
                foreach ($members as $member) {
                    $data = ($this->prepareEmailTags($event, $member->user_id));
                    $this->getEmailFactory()->sendVirtualModification($event, $member->user_id, $data);
                }
            }
        }
    }
    
    private function getEmailFactory() {
        if (!$this->emailFactory) {
            $this->emailFactory = app(EmailFactory::class);
        }
        return $this->emailFactory;
    }
    
    public function deleteInternalEvent($event) {
        $workshop = Workshop::where('id', $event->workshop_id)->withoutGlobalScopes();
        $meeting = Meeting::where('workshop_id', $event->workshop_id);
        $workshopMetas = WorkshopMeta::where('workshop_id', $event->workshop_id);
        $delete = $workshop->delete();
        $delete += $meeting->delete();
        $workshopMetas->delete();
        return $delete;
    }
    
    public function uploadImageGetUrl($image) {
        if ($image) {
            $this->tenancy = app(\Hyn\Tenancy\Environment::class);
            $hostname = $this->tenancy->hostname()['fqdn'];
            $filePath = "$hostname/events";
            $this->core = app(\App\Http\Controllers\CoreController::class);
            return $this->core->getS3Parameter($this->core->fileUploadToS3($filePath, $image, 'public'));
        }
        return '';
    }
    
    /**
     * @return Organisation
     * @throws CustomValidationException
     */
    public function getOrganisation() {
        $this->tenancy = app(\Hyn\Tenancy\Environment::class);
        $hostname = $this->tenancy->hostname();
        $organisation = Organisation::where('account_id', $hostname->id)->first();
        if (!$organisation)
            throw new CustomValidationException('organisation_not_found', null, 'message');
        return $organisation;
    }
    
    /**
     * @param Request $request
     * @return int|null
     * @throws \Modules\Cocktail\Exceptions\CustomValidationException
     */
    public function createBlueJeansEvent($request) {
        // first checking if request have to enable bj
        if ($request->has('event_uses_bluejeans_event') && $request->input('event_uses_bluejeans_event')) {
            // check bj is turned on from super admin or not
            if ($this->isBlueJeansEnabled()) {
                // checking if bj license is available or not
                if ($this->isBlueJeansLicenseAvailable()) {
                    $this->reduceBluejeansLicense();
                    $this->externalEventFactory = app(ExternalEventFactory::class);
                    $extEvtData = $this->externalEventFactory->prepareCreateParamFromRequest($request);
                    return $this->externalEventFactory->create($extEvtData)['id'];
                }
            }
        }
        return null;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to check if bluejeans licenses is available for current account.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return bool
     * @throws \Modules\Cocktail\Exceptions\CustomValidationException
     */
    public function isBlueJeansLicenseAvailable() {
        if ($setting = $this->getEventSettings()) {
            if (isset($setting['event_bluejeans_setting']['number_of_license'])) {
                if ($setting['event_bluejeans_setting']['number_of_license'] > 0) {
                    return true;
                }
            }
            throw new \Modules\Cocktail\Exceptions\CustomValidationException('out_of_license', 'bluejeans', 'message');
        }
        return false;
    }
    
    /**
     * @return integer
     */
    public function reduceBluejeansLicense() {
        $settingValue = $this->getEventSettings();
        $settingValue['event_bluejeans_setting']['number_of_license']--;
        return Setting::where('setting_key', 'event_settings')->update(['setting_value' => json_encode($settingValue)]);
    }
    
    /**
     * @return array|null
     */
    public function getEventSettings() {
        if ($this->eventSettings == null) {
            $setting = Setting::where('setting_key', 'event_settings')->first();
            if ($setting) {
                $this->eventSettings = json_decode($setting->setting_value, JSON_OBJECT_AS_ARRAY);
            }
        }
        return $this->eventSettings;
    }
    
    /**
     * @param $key
     * @param $value
     * @param null $parentKey
     * @return bool
     * @throws Exception
     */
    public function updateOrgAdminSetting($key, $value, $parentKey = null) {
        $setting = Setting::where('setting_key', 'event_settings')->first();
        if ($setting) {
            $decode = json_decode($setting->setting_value, 1);
            if ($parentKey)
                $decode[$parentKey][$key] = $value;
            else
                $decode[$key] = $value;
            $isUpdated = Setting::where('setting_key', 'event_settings')
                ->update(['setting_value' => json_encode($decode)]);
            if (!$isUpdated)
                throw new Exception();
            return true;
        }
        return true;
    }
    
    public function getAccountSetting() {
        if ($this->accountSetting) return $this->accountSetting;
        $this->tenancy = app(\Hyn\Tenancy\Environment::class);
        $this->accountSetting = AccountSettings::where('account_id', $this->tenancy->hostname()['id'])
            ->first(['setting']);
        return $this->accountSetting;
    }
    
    public function isWpOn() {
        $accountSetting = $this->getAccountSetting();
        if (isset($accountSetting->setting['event_settings']['wp_enabled']) && $accountSetting->setting['event_settings']['wp_enabled'] == 1) {
            return true;
        }
        return false;
    }
    
    public function removeBluejeansEvent($event) {
        if ($event->bluejeans_id) {
            $this->externalEventFactory = app(ExternalEventFactory::class);
            $this->externalEventFactory->delete($event->bluejeans_id);
        }
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
    
    public function isEventMember($eventId) {
        $event = Event::find($eventId);
        if ($event) {
            return $this->isWorkshopMember($event->workshop_id, [0]);
        }
        return true;
    }
    
    public function isWorkshopMember($wid, $roles) {
        return (bool)WorkshopMeta::where('workshop_id', $wid)
            ->where('user_id', Auth::user()->id)
            ->whereIn('role', $roles)
            ->first();
    }
    
    /**
     * This will add opening hours to each events start time and end time
     * reduce start time with opening before
     * add end time with opening after
     *
     * @param $events
     * @param $type
     * @return mixed
     */
    public function addOpeningHourToEventTime($events, $type) {
        $valSrv = ValidationService::getInstance();
        if ($type == config('events.event_type.virtual')) {
            return $events->map(function ($event) use ($valSrv) {
                $s = $valSrv->getEventMaxBefore($event);
                $e = $valSrv->getEventMaxAfter($event);
                
                $event->start_time = Carbon::createFromTimestamp($s)->toTimeString();
                $event->end_time = Carbon::createFromTimestamp($e)->toTimeString();
                
                return $event;
            });
        }
        return $events;
    }
    
    /**
     * To get the occupied date of a particular month
     * this method also filter to type of event if given
     * and also can exclude events by id
     *
     * @param Request $request
     * @return Collection
     */
    public function getOccupiedDates($request) {
        $events = $this->occupiedDatesBuilder($request->input('year'), $request->input('month'));
        $events = $this->applyTypeFilterToOccupiedDate($events, $request->input('type', ''));
        $events = $this->excludeEventsToOccupiedDate($events, $request->has('event_id') ? [$request->input('event_id')] : []);
        $events = $events->orderBy('date')
            ->orderBy('start_time')
            ->get();
        $events = $this->addOpeningHourToEventTime($events, $request->input('type'));
        return $this->removePastTimeOfToday($events, $request->input('year'), $request->input('month'));
    }
    
    /**
     * @param Collection $events
     * @param $year
     * @param $month
     * @return Collection
     */
    public function removePastTimeOfToday($events, $year, $month) {
        $currentTime = Carbon::now()->toTimeString();
        $result = $events;
        if ("$month $year" == Carbon::now()->format("m Y")) {
            $result = new Collection();
            $events->map(function ($e) use ($currentTime, $result) {
                if ($e->date == Carbon::now()->toDateString()) {
                    if ($e->end_time < $currentTime) {
                        return false;
                    }
                    if ($e->start_time < Carbon::now()->toTimeString()) {
                        $e->start_time = Carbon::now()->toTimeString();
                    }
                }
                $result->push($e);
                return $e;
            });
        }
        return $result;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description this will prepare the builder to find the occupied dates for the specific month and year
     * this will check for month and year must match with date
     * (date must after tomorrow or if month is current (date= today and end time after now) will also fetched
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $year
     * @param $month
     * @return Builder|Event
     */
    public function occupiedDatesBuilder($year, $month) {
        $endAccepted = Carbon::now()->timestamp - (config('cocktail.validations.space.opening_hour_after_max') * 60);
        return Event::with('spaces')
            // year must match to provided
            ->whereRaw("YEAR(date) = $year")
            // month must match to provided
            ->whereRaw("MONTH(date) = $month")
            // this will add condition
            // date must after today or (only if month is current) date is today and time is after now
            ->where(function ($q) use ($month, $year, $endAccepted) {
                // in this where clause date will be checked
                // either the date must be from next day
                // or if date is today check by time
                $q->orWhere('date', '>', Carbon::now()->format('Y-m-d'));
                // using the carbon format "n Y" not "m Y"
                // because front will send like month number 3 and "m" will check by "03"
                if ("$month $year" == Carbon::now()->format("n Y")) {
                    // if month is current month check if date is today time must be future
                    $q->orWhere(function ($q) use ($endAccepted) {
                        $q->where('date', Carbon::now()->toDateString());
                        $q->where('end_time', '>', $endAccepted); // the end time is already subtracted to allowed opening hour so even event ended we will still compare with space open or not
                    });
                }
            });
    }
    
    /**
     * To apply the filter so only specific type of events occupied date can be fetched
     *
     * @param Builder $builder
     * @param $type
     * @return Builder
     */
    public function applyTypeFilterToOccupiedDate($builder, $type) {
        if ($type) {
            return $builder->where('type', $type);
        }
        return $builder;
    }
    
    /**
     * @param Builder $builder
     * @param array $eventIds
     * @return Builder
     */
    public function excludeEventsToOccupiedDate($builder, $eventIds) {
        if ($eventIds) {
            return $builder->whereNotIn('id', $eventIds);
        }
        return $builder;
    }
    
    /**
     * TO add some additional info required by front team
     *
     * @param $event
     * @return
     */
    public function getEventShowMeta($event, $additional) {
        if ($event->type == config('events.event_type.virtual')) {
            $additional['allow_manual_before'] = config('events.validations.manual_opening_possible') / 60; // sending so front can handle to show manual or not (In minute)
            $additional['timezone'] = Carbon::now()->timezone->getName();
            $additional['is_past'] = !ValidationService::getInstance()->isEventSpaceOpenOrFuture($event);
            $additional['is_during'] = ValidationService::getInstance()->isEventOrSpaceRunning($event);
        }
        return $additional;
    }
    
    /**
     * To update the organiser when we update the event secretory
     *
     * @param $wid
     * @param $uid
     * @param $status
     * @throws CustomException
     */
    public function changeEventOrganiser($wid, $uid, $status) {
        $event = Event::where('workshop_id', $wid)->first();
        if ($event) {
            if ($event->type == config('events.event_type.virtual')) {
                if (!ValidationService::getInstance()->isEventSpaceFuture($event)) {
                    // if event opened don't let the user to change organiser
                    throw new CustomException(null, __('cocktail::message.event_must_future'));
                }
            } else {
                $start = Carbon::createFromFormat(ValidationService::DT_FORMAT, "$event->date $event->start_time");
                if (!Carbon::now()->timestamp >= $start->timestamp) {
                    throw new CustomException(null, __('cocktail::message.event_must_future'));
                }
            }
            if ($status == 1) {
                Eventable::where('event_id', $event->id)->update([
                    'eventable_id' => $uid,
                ]);
            }
        }
        
    }
}
