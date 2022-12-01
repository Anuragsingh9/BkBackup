<?php

namespace Modules\Cocktail\Services;

use App\Exceptions\CustomJsonException;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\WorkshopController;
use App\Presence;
use App\Services\Service;
use App\User;
use App\WorkshopMeta;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Cocktail\Entities\EventSpace;
use Modules\Cocktail\Entities\EventSpaceUser;
use Modules\Cocktail\Entities\EventUser;
use Modules\Cocktail\Exceptions\CustomValidationException;
use Modules\Cocktail\Exceptions\InternalServerException;
use Modules\Cocktail\Exceptions\NotExistsException;
use Modules\Cocktail\Services\Contracts\EmailFactory;
use Modules\Cocktail\Services\Contracts\ExternalEventFactory;
use Modules\Cocktail\Services\V2Services\KctCoreService;
use Modules\Cocktail\Transformers\UserBadgeResource;
use Modules\Cocktail\Transformers\WPArticlesResource;
use Modules\Events\Entities\Event;
use Exception;
use Modules\Events\Exceptions\CustomException;
use Modules\Events\Service\EventService;
use Modules\Events\Service\ValidationService;
use Modules\Events\Service\WordPressService;
use Modules\Newsletter\Entities\Workshop;
use function GuzzleHttp\Psr7\str;

class KctEventService extends Service {
    
    /**
     * @var ExternalEventFactory
     */
    private $externalEventFactory;
    
    /**
     * @var EmailFactory
     */
    private $emailFactory;
    /**
     * @var MeetingController
     */
    private $meetingController;
    
    /**
     * @return EmailFactory
     */
    public function getEmailFactory() {
        if (!$this->emailFactory) {
            $this->emailFactory = app(EmailFactory::class);
        }
        return $this->emailFactory;
    }
    
    /**
     * @return MeetingController
     */
    public function getMeetingController() {
        if(!$this->meetingController) {
            $this->meetingController = app(MeetingController::class);
        }
        return $this->meetingController;
    }
    
    /**
     * to set the fields in given column like
     * column = [
     *      $oldFieldName = $value,
     *      $oldFieldName = $value,
     *      $oldFieldName = $value,
     *      $fieldName = $value,
     * ]
     * This will keep the old values and if update/add field name already there then it will update its value
     * otherwise it will add extra key to array so previous will be persists and add new column
     *
     * @param null $event
     * @param null $eventUuid
     * @param string $columnName
     * @param array $values
     *
     * @return Event|null
     */
    public function addOrUpdateEventJsonFields($columnName, $values, $eventUuid = null, $event = null) {
        if ($eventUuid) {
            $event = Event::where('event_uuid', $eventUuid)->first();
        }
        if ($event) {
            $oldData = $event->$columnName;
            foreach ($values as $k => $v) {
                $oldData[$k] = $v;
            }
            $event->update([$columnName => $oldData,]);
        }
        return $event;
    }
    
    /**
     * This will check and upload the graphics logo if available from request
     *
     * @param         $param
     * @param Request $request
     * @param Event $event
     *
     * @return mixed
     */
    public function uploadGraphicsLogo($param, $request, $event) {
        if ($request->hasFile('keepContact_page_logo') && $request->keepContact_page_logo) {
            $file = $request->keepContact_page_logo;
            $path = str_replace('event_uuid', $request->event_uuid, config('cocktail.s3.graphic_logo'));
            $upload = KctService::getInstance()->fileUploadToS3(
                $path,
                $file,
                'public');
            if ($upload) {
                $this->deleteGraphicsLogo($event->event_uuid);
            }
        } else {
            $upload = isset($event->event_fields['keepContact']['page_customisation']['keepContact_page_logo'])
                ? $event->event_fields['keepContact']['page_customisation']['keepContact_page_logo']
                : null;
        }
        $param['keepContact']['page_customisation']['keepContact_page_logo'] = $upload;
        return $param;
    }
    
    /**
     * @param $eventUuid
     *
     * @return mixed
     * @throws CustomValidationException
     * @throws NotExistsException
     */
    public function getKeepContactCustomization($eventUuid) {
        $event = Event::where('event_uuid', $eventUuid)->first();
        if (!$event) {
            throw new NotExistsException(__('validation.exists', ['attribute' => 'event']));
        }
        return $event;
    }
    
    /**
     * @param null $eventUuid
     *
     * @return string
     */
    public function getEventEmbeddedUrl($eventUuid = null) {
        $event = Event::where('event_uuid', $eventUuid)->first();
        $url = '';
        if ($this->isEventUsageBluejeans($event) && AuthorizationService::getInstance()->isBlueJeansEnabled()) {
            $start = Carbon::createFromFormat("Y-m-d H:i:s", "$event->date $event->start_time")->timestamp - config('cocktail.embedded_url_show_before');
            $end = Carbon::createFromFormat("Y-m-d H:i:s", "$event->date $event->end_time")->timestamp;
            $current = Carbon::now()->timestamp;
            $today = Carbon::now()->toDateString();
            if (($start <= $current && $current < $end) || ($event->date < $today)) { // either event started or about to start within 15 minute
                $res = $this->getExternalEventFactory()->getEvent($event->bluejeans_id);
                if (isset($res['attendeeUrl'])) {
                    $attendee = $res['attendeeUrl'];
                    $code = explode('/', $attendee);
                    if (count($code) == 6) {
                        return str_replace('[[SHARING_ID]]', end($code), config('cocktail.embedded_url_template'));
                    }
                }
            }
        }
        return $url;
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
            $event = Event::where('workshop_id', $request->input('workshop_id'))->first();
        } else {
            $event = Event::where('event_uuid', $request->input('event_uuid'))->first();
            $request->merge(['workshop_id' => $event->workshop_id]);
        }
        $request->merge(['event_uuid' => $event->event_uuid]);
        $userId = $this->addUserToWorkshop($request);
        if ($event->type == 'virtual') {
            // in virtual user should show as registered as present
            $this->updateUserPresenceToRegister($userId, $request->workshop_id);
            $isP = config('cocktail.default.is_presenter');
            $isM = config('cocktail.default.is_moderator');
            $eventUser = $this->addUserToEvent($userId, $event->event_uuid, $isP, $isM, $spaceUuid);
            if ($isM) {
                $user = User::find($userId);
                $this->getEmailFactory()->sendModeratorInfo($event, $user);
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
     *
     * @param $userId
     * @param $event
     * @param $isP
     * @param $isM
     *
     * @deprecated previously adding user to bluejeans event also but deprecated as bluejeans sends email
     *
     */
    public function addUserToExternalAgent($userId, $event, $isP, $isM) {
        if ($bluejeansEvent = $event->bluejeans_id && $isM) { // do not add members to event
            $user = User::find($userId);
            $data = [
                'email'     => $user->email,
                'presenter' => $isP,
                'moderator' => $isM,
            ];
            // if is moderator send email that account is needed
            $this->getEmailFactory()->sendModeratorInfo($event, $user);
            $this->getExternalEventFactory();
            $this->externalEventFactory->addMember($bluejeansEvent, $data);
        }
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
        $eventUser = EventUser::updateOrCreate([
            'user_id'    => $userId,
            'event_uuid' => $eventUuid,
        ], [
            'user_id'             => $userId,
            'is_presenter'        => $isP,
            'is_moderator'        => $isM,
            'event_uuid'          => $eventUuid,
            'state'               => 1,
            'is_joined_after_reg' => 0,
        ]);
        if ($spaceUuid) {
            EventSpaceService::getInstance()
                ->addUserToSpace($userId, $spaceUuid, $eventUuid, EventSpaceUser::$ROLE_MEMBER);
        } else {
            $this->addUserToDefaultSpace($eventUuid, $userId);
        }
        return $eventUser;
    }
    
    public function verifyUserEmail($userId) {
        User::where('id', $userId)->update(['on_off' => 1]);
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
        EventSpaceService::getInstance()
            ->addUserToSpace($userId, $space->space_uuid, $eventUuid, EventSpaceUser::$ROLE_MEMBER);
        return true;
    }
    
    public function getEventDefaultSpace($eventUuid) {
        return EventSpace::where('event_uuid', $eventUuid)->orderBy('created_at', 'asc')->first();
    }
    
    /**
     * @param $eventUuid
     * @param $userId
     *
     * @return bool
     * @throws InternalServerException
     */
    public function eventUserRemove($eventUuid, $userId) {
        $event = Event::where('event_uuid', $eventUuid)->first();
        // removing user from workshop
        if ($event->workshop_id) {
            WorkshopMeta::where('workshop_id', $event->workshop_id)->where('user_id', $userId)->delete();
        }
        // removing user from event
        $eventUser = EventUser::where([['user_id', $userId], ['event_uuid', $eventUuid]])->delete();
        // removing user from all spaces as host can be member of more than one space
        $eventSpaces = EventSpace::where('event_uuid', $eventUuid)->select('space_uuid')->get();
        EventSpaceUser::whereIn('space_uuid', $eventSpaces->pluck('space_uuid')->toArray())
            ->where('user_id', $userId)
            ->delete();
        // if user not removed throw exception
        if (!$eventUser) {
            throw new InternalServerException('event_user_remove');
        }
        return true;
    }
    
    
    /**
     * @param      $eventUuid
     * @param      $userId
     * @param      $field
     * @param null $space
     * @param null $presence
     *
     * @return Collection|EventUser|bool
     */
    public function eventUserUpdateRole($eventUuid, $userId, $field, $space = null, $presence = null) {
        if ($field == 3) { // host updating
            EventSpaceUser::updateOrCreate(
                ['user_id' => $userId, 'space_uuid' => $space,],
                ['space_uuid' => $space, 'user_id' => $userId, 'role' => DB::raw("IF(role=1, 0, 1)"),]
            );
        } else if ($field == 4) {
            $event = Event::where('event_uuid', $eventUuid)->first();
            Presence::where('workshop_id', $event->workshop_id)
                ->where('user_id', $userId)
                ->update(['presence_status' => $presence]);
        } else {
            $column = ['', 'is_presenter', 'is_moderator']; // 1 index presenter, 2 index moderator
            EventUser::where('user_id', $userId)
                ->where('event_uuid', $eventUuid)
                ->update([$column[$field] => DB::raw("IF($column[$field]=1, 0, 1)")]);
            $event = Event::where('event_uuid', $eventUuid)->first();
            $eventUser = EventUser::where('user_id', $userId)
                ->where('event_uuid', $eventUuid)->first();
            if ($field == 2 && $eventUser && $eventUser->is_moderator) {
                $user = User::find($userId);
                $this->getEmailFactory()->sendModeratorInfo($event, $user);
            }
        }
        return true;
    }
    
    /**
     * @param string $eventUuid
     * @param null $userId if provided means we are getting single user data for that event
     * @param Request $request to get the orderBy
     *
     * @return LengthAwarePaginator|Collection|EventUser
     * @throws CustomValidationException
     */
    public function getEventUsers($eventUuid, $userId = null, $request = null) {
        $event = Event::where('event_uuid', $eventUuid)->first();
        if (!$event) throw new CustomValidationException('exists', 'event');
        
        // what we need to show the R (registered presence status)
        // before event R = present and after event r = absent
        $attendeeR = ValidationService::getInstance()->isEventEnded($event) ? 'ANE' : 'P';
        // function which applies to extract only selected event workshop's meta data
        $isWorkshopMember = function ($q) use ($event) {
            $q->where('workshop_id', $event->workshop_id);
        };
        $isHost = function ($q) use ($eventUuid) {
            $hasSpace = function ($q) use ($eventUuid) {
                $q->where('event_uuid', $eventUuid);
            };
            $q->with(['space' => $hasSpace]);
            $q->whereHas('space', $hasSpace);
        };
        
        $builder = EventUser::with([
            'user',
            'isSecretory'    => $isWorkshopMember,
            'isDeputy'       => $isWorkshopMember,
            'presenceStatus' => $isWorkshopMember,
            'isHost'         => $isHost,
        ])
            ->whereHas('user')
            ->where('event_uuid', $eventUuid);
        
        $attendeeMap = function ($row) use ($attendeeR) {
            if ($row->presenceStatus && $row->presenceStatus->presence_status && $row->presenceStatus->presence_status == 'R') {
                $row->presenceStatus->presence_status = $attendeeR;
            }
            return $row;
        };
        
        if ($userId) { // if user id provided we need only single users data for the selected event
            return $builder->where('user_id', $userId)->first();
        }
        if ($request) {
            $data = $builder->get()->map($attendeeMap);
            return $this->returnEventUserByOrder($request, $data);
        }
        return $builder->get()->map($attendeeMap);
    }
    
    /**
     * @param         $request
     * @param Builder $queryBuilder
     *
     * @return mixed
     */
    public function returnEventUserByOrder($request, $eventUsers) {
        $orderBy = $request->input('order_by');
        $order = $request->input('order', 'asc');
        
        $sortBy = $order == 'asc' ? 'sortBy' : 'sortByDesc';
        
        $filterAdmin = function ($row, $next) {
            return strtolower("$next|{$row->user->fname} {$row->user->lname}");
        };
        
        if ($orderBy == 'unions') {
            $data = $eventUsers->$sortBy(function ($row) use ($filterAdmin) {
                $union = $row->user->unions->first();
                return $filterAdmin($row, $union ? $union->long_name : '');
            });
        } else if ($orderBy == 'company') {
            $data = $eventUsers->$sortBy(function ($row) use ($filterAdmin) {
                $company = $row->user->companies->first();
                return $filterAdmin($row, $company ? $company->long_name : '');
            });
        } else if ($orderBy == 'is_presenter') {
            $data = $eventUsers->$sortBy(function ($row) use ($filterAdmin) {
                return $filterAdmin($row, "$row->is_presenter");
            });
        } else if ($orderBy == 'is_moderator') {
            $data = $eventUsers->$sortBy(function ($row) use ($filterAdmin) {
                return $filterAdmin($row, "$row->is_moderator");
            });
        } else if ($orderBy == 'presence') {
            $data = $eventUsers->$sortBy(function ($row) use ($filterAdmin) {
                return $filterAdmin($row, $row->presenceStatus ? $row->presenceStatus->presence_status : '');
            });
        } else if ($orderBy == 'user_name') {
            $data = $eventUsers->$sortBy(function ($row) use ($filterAdmin) {
                return $filterAdmin($row, '');
            });
        } else if ($orderBy == 'is_host') {
            $data = $eventUsers->$sortBy(function ($row) use ($filterAdmin) {
                return $filterAdmin($row, $row->isHost->count() ? 0 : 1);
            });
        } else {
            $value = $order == "asc" ? "0" : "1";
            $iValue = $order == 'asc' ? "1" : "0";
            $data = $eventUsers->$sortBy(function ($row) use ($value, $iValue) {
                $sec = $row->isSecretory->count() ? $value : $iValue;
                $dep = $row->isDeputy->count() ? $value : $iValue;
                return "{$sec}{$dep}" . strtolower("|{$row->user->fname} {$row->user->lname}");
            });
        }
        $result = new Collection();
        foreach ($data as $row) {
            $result->push($row);
        }
        $perPage = $request->input('item_per_page', config('cocktail.pagination.event_participants'));
        $page = $request->input('page', 1);
        return new \Illuminate\Pagination\LengthAwarePaginator(
            array_values($result->forPage($page, $perPage)->all()),
            $result->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath()]
        );
    }
    
    /**
     * To get the event with present presence records
     *
     * @param $eventUuid
     *
     * @return Event
     */
    public function getPresencesOfEvent($eventUuid) {
        return Event::with(['presences' => function ($q) {
            $q->where("presence_status", "P");
        }, 'spaces'])->where('event_uuid', $eventUuid)->first();
    }
    
    /**
     * To send the additional meta data for some functionality
     *
     * @param $eventUuid
     *
     * @return array
     */
    public function getEventUserMeta($eventUuid) {
        $additional = $this->getPresencesOfEvent($eventUuid);
        $startTime = Carbon::createFromFormat("Y-m-d H:i:s", "$additional->date $additional->start_time");
        $result = [
            'presence_count'   => $additional->presences->count(), // total number of present
            'workshop_id'      => $additional->workshop_id,
            'event_start_time' => $additional->start_time,
            'event_date'       => $additional->date,
            'event_end_time'   => $additional->end_time,
            'is_past'          => Carbon::now()->timestamp > $startTime->timestamp,
            'event_status'     => $this->getEventStatus($additional),
        ];
        if ($additional && $additional->type == config('events.event_type.virtual')) {
            $result['conference_type'] = KctCoreService::getInstance()->findEventConferenceType($additional);
        }
        return $result;
    }
    
    /**
     * @param  $eventUuid
     * @param  $key
     * @param  $item_per_page
     *
     * @return LengthAwarePaginator|Collection|EventUser
     * @throws CustomValidationException
     */
    public function getEventUsersSearch($eventUuid, $key, $item_per_page) {
        if (strlen(trim($key)) >= 3) {
            
            $event = Event::where('event_uuid', $eventUuid)->first();
            if (!$event) throw new CustomValidationException('exists', 'event');
            
            $isWorkshopMember = function ($q) use ($event) {
                $q->where('workshop_id', $event->workshop_id);
            };
            $searchUser = function ($q) use ($key) {
                $q->where(function ($q) use ($key) {
                    $q->orWhere('fname', 'like', "%$key%");
                    $q->orWhere('lname', 'like', "%$key%");
                    $q->orWhere('email', 'like', "%$key%");
                    $q->orWhere(DB::raw("CONCAT(fname, ' ', lname)"), 'like', "%$key%");
                });
            };
            
            $isHost = function ($q) use ($eventUuid) {
                $hasSpace = function ($q) use ($eventUuid) {
                    $q->where('event_uuid', $eventUuid);
                };
                $q->with(['space' => $hasSpace]);
                $q->whereHas('space', $hasSpace);
            };
            
            $builder = EventUser::with([
                'user'        => $searchUser,
                'isSecretory' => $isWorkshopMember,
                'isDeputy'    => $isWorkshopMember,
                'isHost'      => $isHost,
            ])
                ->whereHas('user', $searchUser)
                ->withCount(['isHost' => function ($q) use ($eventUuid) {
                    $hasSpace = function ($q) use ($eventUuid) {
                        $q->where('event_uuid', $eventUuid);
                    };
                    $q->with(['space' => $hasSpace]);
                    $q->whereHas('space', $hasSpace);
                }])->where('event_uuid', $eventUuid);
            
            return $builder->paginate($item_per_page ? $item_per_page : config('cocktail.pagination.event_participants'));
        } else return null;
    }
    
    /**
     * This method prepared the order by parameter on which events list gonna sort
     * this method by default return to sort the events list by the date
     *
     * @param $request
     *
     * @return mixed|string
     */
    public function getEventsListOrderBy($request) {
        $possibleOrder = [
            'event_date'       => 'event_info.date',
            'event_title'      => 'event_info.title',
            'organiser_fname'  => 'organiser_name',
            'event_start_time' => 'start_time',
            'event_end_time'   => 'end_time',
        ];
        return $request->has('order_by') && isset($possibleOrder[$request->order_by]) ?
            $possibleOrder[$request->order_by] : $possibleOrder['event_date'];
    }
    
    /**
     * if asc desc from front not passed we need to identify what to use
     * as default order is date , so in future events list and in past events list
     * there will opposite sorting so this will return order according to tense future/past passed
     *
     * @param Request $request
     *
     * @return string
     */
    public function getEventsListOrder($request) {
        if ($request->has('order') && (in_array($request->input("order"), ['desc', 'asc']))) { // checking in request present or not
            return ($request->order == 'desc' ? 'desc' : 'asc');
        } else {
            return ($request->tense == 'past' ? 'desc' : 'asc'); // if past we will sort by descending so last event on top
        }
    }
    
    /**
     * @param $op
     *
     * @return Builder|\Illuminate\Database\Query\Builder|Event
     */
    public function prepareEventListBuilder($op) {
        $eventUsers = function ($q) {
            $q->where('users.id', Auth::user()->id);
        };
        
        $builder = Event::with(['eventUsers' => $eventUsers, 'users', 'isHostOfAnySpace'])
            ->where(function ($q) use ($op) {
                $q->orWhere('date', $op, date('Y-m-d'));
                $q->orWhere(function ($q) use ($op) {
                    $q->where('date', '=', date('Y-m-d'));
                    $q->where('end_time', $op, date('H:i:s'));
                });
            })
            ->where('type', 'virtual')
            ->join('eventables as ev', 'ev.event_id', '=', 'event_info.id')
            ->leftJoin('users as u', 'u.id', '=', 'ev.eventable_id')
            ->select('event_info.*', 'u.id as user_id', 'u.fname', 'u.lname', DB::raw("CONCAT(fname, ' ' , lname) as organiser_name"));
        if ($op == '<') { // < means past event and in past we need to show only those events in which user is participated
            $builder = $builder->whereHas('eventUsers', $eventUsers);
        }
        return $builder;
    }
    
    /**
     * this will filter the events list with the search key word
     *
     * @param Builder $builder
     * @param string $key
     *
     * @return mixed
     */
    public function addSearchParamToEventListBuilder(Builder $builder, $key) {
        return $builder
            ->where(function ($q) use ($key) {
                $q->where('event_info.title', 'like', "%$key%");
                $q->orWhere('event_info.date', 'like', "%$key%");
                $q->orWhere(DB::raw("CONCAT(u.fname, ' ', u.lname)"), 'like', "%$key%");
            });
    }
    
    public function addSearchDefaultOrderToEventList($builder, $key) {
        $title = "WHEN event_info.title LIKE";
        $organiser = "WHEN organiser_name LIKE";
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
            "$organiser '$key[0]' $codec  THEN 5 " .
            "$organiser '$key[1]' $codec  THEN 6 " .
            "$organiser '$key[2]' $codec  THEN 7 " .
            "$organiser '$key[3]' $codec  THEN 8 " .
            "ELSE 9 " .
            "END"
        ));
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
        $orderBy = $this->getEventsListOrderBy($request);
        $order = $this->getEventsListOrder($request);
        $op = $request->has('tense') && $request->input('tense') == 'past' ? '<' : '>';
        $builder = $this->prepareEventListBuilder($op);
        if ($request->has('key')) {
            $builder = $this->addSearchParamToEventListBuilder($builder, $request->input('key'));
        }
        if ($request->has('key') && !$request->has('order_by')) {
            $builder = $this->addSearchDefaultOrderToEventList($builder, $request->input('key'));
        } else {
            $builder = $builder->orderBy($orderBy, $order);
            if ($orderBy != 'start_time') {
                $builder = $builder->orderBy('start_time', $order);
            }
        }
        if ($request->has('item_per_page')) {
            return $builder->paginate($request->input('item_per_page'));
        } else {
            return $builder->get();
        }
    }
    
    /**
     * @param Request $request
     *
     * @return mixed
     * @throws CustomValidationException
     */
    public function addCurrentUserToEvent($request) {
        $event = Event::where('event_uuid', $request->event_uuid)->first();
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
    
    public function getUsersUpcomingEvent($user) {
        return Event::whereHas('eventUsers', function ($q) {
            $q->where('users.id', Auth::user()->id);
        })->where('date', Carbon::now()->toDateString())->orderBy('date')->orderBy('start_time')->first();
    }
    
    /**
     * To delete the previous uploaded logo to keep s3 clean
     *
     * @param $eventUuid
     *
     * @return bool
     */
    public function deleteGraphicsLogo($eventUuid) {
        $event = Event::where('event_uuid', $eventUuid)->first();
        if (isset($event->event_fields['keepContact']['page_customisation']['keepContact_page_logo'])) {
            $domain = KctService::getInstance()->getHostname()->fqdn;
            $logo = $event->event_fields['keepContact']['page_customisation']['keepContact_page_logo'];
            if ($logo && $logo != config('cocktail.default.kct_logo')) {
                // the logo should be not equals to default we don't need to delete the default logo for default grapihcs
                $result = KctService::getInstance()->getCore()->fileDeleteBys3($logo);
                if ($result) {
                    return $event;
                }
            }
        }
        return false;
    }
    
    public function graphicsFilter() {
        return function ($row) {
            return [
                'color'        => [
                    'r' => isset($row['color']['r']) ? $row['color']['r'] : '',
                    'g' => isset($row['color']['g']) ? $row['color']['g'] : '',
                    'b' => isset($row['color']['b']) ? $row['color']['b'] : '',
                    'a' => 1,
                ],
                'transparency' => [
                    'r' => isset($row['color']['r']) ? $row['color']['r'] : '',
                    'g' => isset($row['color']['g']) ? $row['color']['g'] : '',
                    'b' => isset($row['color']['b']) ? $row['color']['b'] : '',
                    'a' => isset($row['color']['a']) ? $row['color']['a'] : 1,
                ],
            ];
        };
    }
    
    /**
     * @param Event $event
     * @param       $request
     *
     * @return array
     */
    public function getEventJoinLink($event, $request) {
        $result = [];
        
        if ($event->type == config('events.event_type.virtual')) {
            if ($this->isEventUsageBluejeans($event) && !$this->isEventAndSpacesEnded($event)) {
                $res = $this->getExternalEventFactory()->getEvent($event->bluejeans_id);
                $result = $this->addRoleWiseJoinLink($event, $res);
            }
            $result['attendee_url'] = $this->getAttendeeJoinUrl($event, $request);
        }
        return $result;
    }
    
    private function addRoleWiseJoinLink($event, $res) {
        $event->load(['eventUsers' => function ($q) {
            $q->where('user_id', Auth::user()->id);
        }]);
        $result = [];
        $isAdmin = EventService::getInstance()->isAdmin();
        if ($isAdmin || $eventUser = $event->eventUsers->first()) {
            $isEventAdmin = EventService::getInstance()->isEventAdmin($event->id);
            if (isset($res['moderatorUrl']) && ($isAdmin || $isEventAdmin || $eventUser->pivot->is_moderator)) {
                $result['moderator_url'] = $res['moderatorUrl'];
            }
            if (isset($res['panelistUrl']) && ($isAdmin || $isEventAdmin || $eventUser->pivot->is_presenter)) {
                $result['presenter_url'] = $res['panelistUrl'];
            }
        }
        return $result;
    }
    
    /**
     * To check the provided event has finished or not
     *
     * @param $event
     *
     * @return bool
     */
    public function isEventAndSpacesEnded($event) {
        $end = ValidationService::getInstance()->getEventMaxAfter($event);
        $current = Carbon::now()->timestamp;
        return $current >= $end;
    }
    
    /**
     * @param Event $event
     * @param Request $request
     *
     * @return string
     */
    public function getAttendeeJoinUrl($event, $request) {
        $root = $request->input('link', KctService::getInstance()->getDefaultHost($request));
        return "https://$root/j/$event->event_uuid";
    }
    
    /**
     * @param Event $event
     *
     * @return bool
     */
    public function isEventUsageBluejeans($event) {
        return
            $event->type == config('events.event_type.virtual')
            && $event->bluejeans_id
            && isset($event->bluejeans_settings['event_uses_bluejeans_event'])
            && $event->bluejeans_settings['event_uses_bluejeans_event'];
    }
    
    /**
     * @return ExternalEventFactory
     */
    public function getExternalEventFactory() {
        if (!$this->externalEventFactory) {
            $this->externalEventFactory = app(ExternalEventFactory::class);
        }
        return $this->externalEventFactory;
    }
    
    /**
     * To attach some extra data to event graphics
     *
     * @param Event $event
     *
     * @return array
     * @throws CustomValidationException
     */
    public function getGraphicsAdditional($event) {
        $embeddedUrl = $this->getEventEmbeddedUrl($event->event_uuid);
        $wpArticles = WordPressService::getInstance()->getWpArticles($event);
        $userBadge = KctService::getInstance()->getUserBadge(Auth::user()->id, $event->event_uuid);
        return [
            'articles'     => $wpArticles ? WPArticlesResource::collection($wpArticles) : [],
            'auth'         => $userBadge ? (new UserBadgeResource($userBadge)) : null,
            'embedded_url' => $embeddedUrl,
            'time_zone'    => Carbon::now()->timezone->getName(),
            'event_status' => $this->getEventAndSpaceStatus($event),
        ];
    }
    
    /**
     * To mark the current user present if event is open
     *
     * @param $eventUuid
     *
     * @return bool
     */
    public function markUserPresent($eventUuid) {
        if ($event = $this->isEventOpen($eventUuid)) {
            return Presence::where('workshop_id', $event->workshop_id)
                ->where('user_id', Auth::user()->id)
                ->update(['presence_status' => 'P']);
        }
        return false;
    }
    
    public function isEventOpen($eventUuid) {
        $event = Event::where('event_uuid', $eventUuid)->first();
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
     * This will check user is workshop member or not and can become admin or not
     *
     * @param $userId
     * @param $wid
     * @param $role
     *
     * @throws CustomValidationException
     */
    public function checkUserForWorkshopAdmin($userId, $wid, $role) {
        $meta = WorkshopMeta::where('workshop_id', $wid)->where('user_id', $userId)->first();
        if (!$meta) {
            throw new CustomValidationException('user_not_member', '', 'message');
        }
        $user = User::find($userId);
        if (($role == 1 || $role == 2)
            && !($user->role_commision || in_array($user->role, ['M1', 'M0']))) {
            throw new CustomValidationException('cannot_become_admin', '', 'message');
        }
    }
    
    /**
     * To update the role of user in workshop
     *
     * @param Event $event
     * @param Request $request
     *
     * @return EventUser
     * @throws CustomJsonException
     * @throws CustomValidationException
     */
    public function updateEventAdminRole($event, Request $request) {
        $request->merge([
            'status'  => $request->input('role'),
            'id'      => $request->input('user_id'),
            'wid'     => $event->workshop_id,
            'is_last' => $request->input('is_last', 0),
        ]);
        
        $controllerRes = app(WorkshopController::class)->updateMemberStatus($request);
        if ($controllerRes instanceof JsonResponse) {
            $statusCode = isset($controllerRes->getData()->status) ? $controllerRes->getData()->status : true;
            if ($controllerRes->getStatusCode() != 200 || $statusCode == false) {
                throw new CustomJsonException($controllerRes);
            }
        }
        return $this->getEventUsers($event->event_uuid);
    }
    
    /**
     * this will make remove user from dep and
     * if user is sec already return
     * if not make user as member
     *
     * @param $wid
     * @param $userId
     * @param $role
     *
     * @return bool|mixed
     */
    private function removeFromDeputy($wid, $userId, $role) {
        WorkshopMeta::where('workshop_id', $wid)
            ->where('user_id', $userId)
            ->where('role', 2) // remove as member
            ->delete();
        
        $isSec = WorkshopMeta::where('workshop_id', $wid)
            ->where('user_id', $userId)
            ->where('role', 1)
            ->first();
        if (!$isSec) {
            return $this->updateWorkshopUser($wid, $userId, $role, 2);
        }
        return true;
    }
    
    /**
     * this will remove previous sec and if prev sec is dep also leave that otherwise make prev sec as member
     * then the new user will be allocated as sec
     *
     * @param $wid
     * @param $userId
     * @param $role
     *
     * @return mixed
     * @throws InternalServerException
     */
    public function makeUserWorkshopSecretory($wid, $userId, $role) {
        $previousSecretory = $this->getPreviousSecretory($wid);
        $this->removePreviousSecretory($wid, $previousSecretory);
        return $this->updateWorkshopUser($wid, $userId, $role);
    }
    
    /**
     * @param $wid
     *
     * @return mixed
     */
    public function getPreviousSecretory($wid) {
        return WorkshopMeta::where('workshop_id', $wid)->where('role', 1)->first();
    }
    
    /**
     * this will remove previous user as sec
     * now if prev sec is dep also then return
     * if not make prev sec as member
     *
     * @param $wid
     * @param $previousSecretory
     *
     * @return bool
     * @throws InternalServerException
     */
    public function removePreviousSecretory($wid, $previousSecretory) {
        if ($previousSecretory) {
            WorkshopMeta::where('workshop_id', $wid)
                ->where('user_id', $previousSecretory->user_id)
                ->where('role', 1) // remove as member
                ->delete();
            $isDeputy = WorkshopMeta::where('workshop_id', $wid)
                ->where('user_id', $previousSecretory->user_id)
                ->where('role', 2)
                ->first();
            if (!$isDeputy) { // the previous secretory is not also dep so make it now member
                $makeSecAsMem = $this->updateWorkshopUser($wid, $previousSecretory->user_id, 0);
                if (!$makeSecAsMem) {
                    throw new InternalServerException();
                }
            }
        }
        return true;
    }
    
    /**
     * @param      $wid
     * @param      $userId
     * @param      $role
     * @param null $prev
     *
     * @return mixed
     *
     */
    private function updateWorkshopUser($wid, $userId, $role, $prev = null) {
        $prev = $prev ? $prev : $role;
        $find = [
            'workshop_id' => $wid,
            'role'        => $prev,
            'user_id'     => $userId
        ];
        $update = [
            'role' => $role,
        ];
        return WorkshopMeta::updateOrCreate($find, $update);
    }
    
    
    /**
     * To get the event by workshop id or by event uuid
     *
     * @param $eventUuid
     * @param $wid
     *
     * @return mixed
     */
    public function getEventByWorkshop($eventUuid, $wid) {
        if ($eventUuid) {
            $event = Event::where('event_uuid', $eventUuid)->first();
        } else {
            $event = Event::where('workshop_id', $wid)->first();
        }
        return $event;
    }
    
    /**
     * @param $wid
     * @param $userId
     *
     * @return bool
     * @throws InternalServerException
     */
    public function removeWorkshopMember($wid, $userId) {
        $event = Event::where('workshop_id', $wid)->first();
        if ($event && $userId) {
            $this->eventUserRemove($event->event_uuid, $userId);
        }
        return false;
    }
    
    /**
     * @param Request $request
     *
     * @return |null
     */
    public function getUserActiveEventUuid($request) {
        if ($user = $request->user('api')) {
            $events = Event::with('spaces')
                ->whereHas('eventUsers', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                })
                ->where('date', Carbon::now()->toDateString())
                ->where('type', config('events.event_type.virtual'))
                ->orderBy('id', 'desc')
                ->get();
            foreach ($events as $event) {
                if (ValidationService::getInstance()->isEventOrSpaceRunning($event)) {
                    return [
                        'uuid'     => $event->event_uuid,
                        'end_time' => $event->end_time,
                    ];
                }
            }
        }
        return null;
    }
    
    /**
     * To get the event state time wise
     *
     * @warn it the is during will also be true if any space is open and either event started or not
     *
     * @param $event
     *
     * @return array
     */
    public function getEventStatus($event) {
        $validation = ValidationService::getInstance();
        return [
            'is_future' => $validation->isEventSpaceFuture($event),
            'is_during' => $validation->isEventOrSpaceRunning($event),
            'is_past'   => $validation->isEventEnded($event),
        ];
    }
    
    /**
     * This will return current time position with relative to event and space opening times
     * 1. Event and space both not started              -> is_future = true
     * 2. Event not started but one of space started    -> is_before_space_open = true
     * 3. Event started                                 -> is_during = true
     * 4. Event Ended but space is still open           -> is_after_space_open = true
     * 5. Event And Space both ended                    -> is_past = true
     *
     * @param Event $event
     *
     * @return array
     */
    public function getEventAndSpaceStatus($event) {
        if (!$event->relationLoaded('spaces')) {
            $event->load('spaces');
        }
        
        $validation = ValidationService::getInstance();
        return [
            'is_future'            => $validation->isEventSpaceFuture($event),
            'is_before_space_open' => $validation->isEventSpaceOpenBefore($event),
            'is_during'            => $validation->isEventRunning($event),
            'is_after_space_open'  => $validation->isEventSpaceOpenAfter($event),
            'is_past'              => $validation->isEventEnded($event),
        ];
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
        $authService = AuthorizationService::getInstance();
        if ($authService->isUserEventMember($eventUuid)) {
            EventUser::where('event_uuid', $eventUuid)
                ->where('user_id', Auth::user()->id)
                ->update(['state' => $state]);
        }
        return (int)$state;
    }
    
    /**
     * To check for the user is added for the first time only
     *
     * @note this will only check if user exists already or not, And will return true if no proper data found(user or event)
     *
     * @param $request
     *
     * @return bool
     * @throws CustomValidationException
     */
    public function checkUserForDuplicateAdd($request) {
        
        $exists = function ($field) use ($request) {
            return $request->has($field) && !empty($request->input($field));
        };
        $user = null;
        // getting the user from the input
        if ($exists('data') && ($decode = json_decode($request->input('data'), 1)) && isset($decode['id'])) {
            $user = User::find($decode['id']);
        }
        if (!$user && $exists('email')) {
            $user = User::where('email', $request->input('email'))->first();
        }
        if (!$user) {
            return true;
        }
        
        // getting event and checking in workshop meta if user exists for that event workshop or not
        if ($exists('event_uuid') || $exists('workshop_id')) {
            
            if ($event = Event::where('event_uuid', $request->input('event_uuid'))->orWhere('workshop_id', $request->input('workshop_id'))->first()) {
                if (WorkshopMeta::where('workshop_id', $event->workshop_id)->where('user_id', $user->id)->first()) {
                    throw new CustomValidationException('already_event_member', null, 'message');
                }
            }
        }
        return true;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To send the mail to new user added in event and in ops platform
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     */
    public function sendWelcomeMail($request) {
        // getting user after create
        $user = User::where('email', strtolower($request->email))->first();
        if ($user) {
            $route = route(
                'redirect-meeting-view', [
                    'userid' => base64_encode($user->id),
                    'type'   => 'm',
                    'url'    => str_rot13('dashboard'),
                ]
            );
            $dataMail = $this->getMeetingController()->getUserMailData('user_email_setting');
            $subject = utf8_encode($dataMail['subject']);
            
            $mailData['mail'] = [
                'subject' => $subject,
                'email' => $user->email,
                'password' => Hash::make(strtolower($request->email)),
                'url' => $route
            ];
            KctService::getInstance()->getCore()->SendEmail($mailData, 'new_user');
        }
    }
}
