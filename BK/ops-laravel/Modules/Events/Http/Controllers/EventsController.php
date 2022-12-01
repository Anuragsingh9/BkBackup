<?php

namespace Modules\Events\Http\Controllers;

use App\Entity;
use App\EntityUser;
use App\Exports\EventMemberExport;
use App\Http\Controllers\CoreController;
use App\Http\Resources\MeetingResource;
use App\Meeting;
use App\Services\MeetingService;
use App\Services\WorkshopService;
use App\User;
use App\WorkshopMeta;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Excel;
use Illuminate\Validation\Rule;
use Modules\Cocktail\Entities\EventSpace;
use Modules\Cocktail\Entities\EventUser;
use Modules\Cocktail\Exceptions\CustomValidationException;
use Modules\Cocktail\Services\KctEventService;
use Modules\Cocktail\Services\V2Services\DataV2Service;
use Modules\Events\Entities\Event;
use Modules\Events\Entities\Eventable;
use Modules\Events\Exceptions\CustomException;
use Modules\Events\Http\Requests\CreateEventRequest;
use Modules\Events\Http\Requests\UpdateEventRequest;
use Modules\Events\Http\Requests\UpdateOrgAdminRequest;
use Modules\Events\Service\DataService;
use Modules\Events\Service\EventService;
use Modules\Events\Service\OrganiserService;
use Modules\Events\Service\ValidationService;
use Modules\Events\Service\WordPressService;
use Modules\Events\Transformers\EventCollection;
use Modules\Events\Transformers\EventOccupiedDateResource;
use Modules\Events\Transformers\EventResource;
use Modules\Events\Transformers\EventResourceForMember;
use Modules\Events\Transformers\EventWorkshopResource;
use Validator;
use Exception;
use Illuminate\Support\Facades\DB;
use Auth;

class EventsController extends Controller {
    private $tenancy;
    private $workshopService;
    private $meetingService;
    private $eventService;
    /**
     * @var CoreController
     */
    private $core;
    private $workshop, $WP_URL, $WP_USER_PASS;
    /**
     * @var ValidationService|null
     */
    private $validationService;
    
    public function __construct() {
        $this->core = app(\App\Http\Controllers\CoreController::class);
        $this->tenancy = app(\Hyn\Tenancy\Environment::class);
        $this->workshopService = WorkshopService::getInstance();
        $this->meetingService = MeetingService::getInstance();
        $this->eventService = EventService::getInstance();
        $this->validationService = ValidationService::getInstance();
        $this->workshop = app(\App\Http\Controllers\WorkshopController::class);
    
    }

//    const WP_URL = 'https://projectdevzone.com/planbim2022/wp-json/wp/v2/';
//    const WP_USER_PASS = 'planbim:8MUJ yIRN I4cW gaZg XTV2 rZXK';
    
    public function uploadImageGetUrl($image) {
        if ($image) {
            $hostname = $this->tenancy->hostname()['fqdn'];
            $filePath = "$hostname/events";
            return $this->core->getS3Parameter($this->core->fileUploadToS3($filePath, $image, 'public'));
        }
        return '';
    }
    
    /**
     * Display a listing of the resource.
     * @return EventCollection
     */
    public function index() {
        return new EventCollection(Event::paginate(2));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param CreateEventRequest $request
     * @return JsonResponse|EventResource
     */
    public function store(CreateEventRequest $request) {
        $wpService = WordPressService::getInstance();
        $param = [];
        try {
            DB::connection('tenant')->beginTransaction();

//            $defaultOrganiserUser = $organiserService->getDefaultOrganiser($request->type);
//            $imageUrl = $this->uploadImageGetUrl($request->image);
            
            // to get image url of s3 if image is uploaded
            $param = DataService::getInstance()->eventCreateParam($request);
            
            $param['eventData']['wp_post_id'] = $wpService->createEvent($request, $param);
            
            // to prepare the necessary data for event create task
            
            if ($request->input('type') === 'int') {
                $event = $this->eventService->createInternalEvent($request, $param);
            } else if ($request->input('type') == 'virtual') {
                $event = $this->eventService->createVirtualEvent($request, $param);
            } else { // external currently
                $event = $this->eventService->createExternalEvent($param);
            }
            
            if ($event->wp_post_id) {
                $wpService->updateEvent($request, $event, $param['imageUrl']);
            }
            
            DB::connection('tenant')->commit();
            return new EventResource($event);
        } catch (CustomValidationException $e) {
            DB::connection('tenant')->rollback();
            $result = $e->render();
        } catch (CustomException $e) {
            DB::connection('tenant')->rollback();
            $result = $e->render();
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            $result = response()->json(['status' => false, 'msg' => 'Internal server error', 'error' => $e->getTrace()], 500);
        }
        // if code executes here means some exception has thrown so try to delete wp event if created
        try {
            $wpPostId = isset($param['wpPostId']) ? $param['wpPostId'] : null;
            $wpService->rollback($request->input('type'), $wpPostId);
        } catch (Exception $e) {
            $result = response()->json(['status' => false, 'msg' => 'Internal server error', 'w' => $wpPostId, 'error' => $e->getTrace()], 500);
        }
        return $result;
    }
    
    
    /**
     * Show the specified resource.
     *
     *
     * @param Request $request
     * @param $event_id
     * @return EventResource|EventResourceForMember|JsonResponse
     */
    public function show(Request $request, $event_id) {
        $event = Event::find($event_id);
        if (!$event) {
            return response()->json(['status' => false, 'msg' => __("events::message.invalid_event")], 422);
        }
        
        if ($this->eventService->isAdmin() || $this->eventService->isEventAdmin($event_id)) {
            $additional = KctEventService::getInstance()->getEventJoinLink($event, $request);
            $additional = $this->eventService->getEventShowMeta($event, $additional);
            $result = (new EventResource($event))->additional(['data' => $additional]);
        } else if ($this->eventService->isEventMember($event_id)) { // as we don't need to show all fields to member
            $externalJoinLinks = KctEventService::getInstance()->getEventJoinLink($event, $request);
            $result = (new EventResourceForMember($event))->additional(['status' => true, 'data' => $externalJoinLinks]);
        } else {
            $result = response()->json(['status' => true, 'msg' => __('events::message.not_belongs_event')], 403);
        }
        return $result;
    }
    
    /**
     * Update the specified resource in storage.
     * @param UpdateEventRequest $request
     * @param int $event_id
     * @return EventResource | JsonResponse
     */
    public function update(UpdateEventRequest $request, $event_id) {
        // Fetch data from database
        
        $wpService = WordPressService::getInstance();
        try {
            DB::connection('tenant')->beginTransaction();
            $event = Event::find($event_id);
            if ($event->type == config('events.event_type.virtual')) {
                if (!$this->validationService->isEventSpaceOpenOrFuture($event)) {
                    return response()->json(['status' => false, 'msg' => __('cocktail::message.event_must_future')], 422);
                }
            } else if (!$this->validationService->isEventFuture($event)) {
                return response()->json(['status' => false, 'msg' => __('cocktail::message.event_must_future')], 422);
            }
            $beforeManualOpening = $event->manual_opening;
            $imageUrl = ($request->has('image') && $request->image != '') ? $this->uploadImageGetUrl($request->image) : $event->image;
            /* validations : checking entry exists for further update */
            if ($event->type == 'int') {
                $this->eventService->updateInternalEvent($request, $imageUrl, $event);
            } else if ($event->type == 'virtual') {
                $this->eventService->updateVirtualEvent($request, $imageUrl, $event);
            } else if ($event->type == 'ext') {
                $this->eventService->updateExternalEvent($request, $imageUrl, $event);
            }
            /* Done */
            if ($event->wp_post_id) {
                $wpPostId = $wpService->updateEvent($request, $event, $imageUrl);
            }
            
            DB::connection('tenant')->commit();
            $this->eventService->emitManualOpeningEvent($beforeManualOpening, $request, $event);
            return (new EventResource($event))->additional(['msg' => 'Record Updated Successfully']);
        } catch (CustomValidationException $e) {
            return $e->render();
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            if (!empty($event->type)) {
                if (!empty($wpPostId) && is_int($wpPostId)) {
                    $wpService->rollback($request->type, $wpPostId);
                }
            }
            return response()->json(['status' => false, 'msg' => 'Internal Server Error ' . $e->getMessage(), 'error' => $e->getTrace()], 500);
        }
    }
    
    /**
     * Remove the specified resource from storage.
     * @param $event_id
     * @return JsonResponse
     */
    public function destroy($event_id) {
        try {
            DB::connection('tenant')->beginTransaction();
            
            $wpService = WordPressService::getInstance();
            $validation = ValidationService::getInstance();
            $event = Event::find($event_id);
            
            /*
             * Validations
             */
            if (!$event) {
                return response()->json(['status' => false, 'msg' => "Record Not Found"], 422);
            } else {
                if ($event->type == config('events.event_type.virtual') && !$validation->isEventSpaceFuture($event)) { // checking virtual event
                    return response()->json(['status' => false, 'msg' => __("events::message.cannot_delete_during")], 422);
                } else { // checking int and ext events for past or not
                    $start = Carbon::createFromFormat($validation::DT_FORMAT, "$event->date $event->start_time")->timestamp;
                    $current = Carbon::now()->timestamp;
                    if($start <= $current) { // either event started or ended
                        return response()->json(['status' => false, 'msg' => __("events::message.cannot_delete_during")], 422);
                    }
                }
            }
            /*
             * Validation end
             */
            $wpId = $event->wp_post_id;
            Eventable::where('event_id', $event_id)->delete();
            $event->delete();
            if ($event->type == 'int') {
                WorkshopService::getInstance()->deleteWorkshop($event->workshop_id);
            } else if ($event->type == 'virtual') {
                WorkshopService::getInstance()->deleteWorkshop($event->workshop_id);
                EventSpace::where('event_uuid', $event->event_uuid)->delete();
                EventUser::where("event_uuid", $event->event_uuid)->delete();
                $this->eventService->removeBluejeansEvent($event);
            }
            if ($wpId) {
                $wpService->deleteEvent($event);
            }
            DB::connection('tenant')->commit();
            return response()->json(['status' => true, 'msg' => 'Record Deleted Successfully']);
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }
    
    /*
     * Fetch DATA
     */
    public function getEventsListWithOrganiser(Request $request, $tense, $itemPerPage = null) {
        if ($itemPerPage && (!is_numeric($itemPerPage) || $itemPerPage < 1)) {
            return response()->json(['status' => false, 'msg' => 'Invalid Page Items'], 422);
        }
        $request->transform = 0;
        
        return EventResource::collection($this->eventService->getEventsListWithOrganiser($tense, $request->field, $request->order, $itemPerPage))
            ->additional(['status' => true,]);
        
    }
    
    /**
     *
     *
     * @param Request $request
     * @param null $itemPerPage
     * @return AnonymousResourceCollection
     */
    public function getEventWorkshops(Request $request, $itemPerPage = null) {
        $data = $this->eventService->getEventWorkshopsWithMemberCount(
            $itemPerPage,
            $request->input('page', 1),
            $request->input('field'),
            $request->input('order')
        );
        return EventWorkshopResource::collection($data)->additional(['status' => true]);
    }
    
    public function getEventWorkshopMeeting($workshopId) {
        $check = $this->workshopService->isWorkshopExists($workshopId);
        
        $event = Event::where('workshop_id', $workshopId)->first();
        if (!$event) {
            return response()->json([
                'status' => false,
                'msg'    => __('validation.exists', ['attribute' => __('cocktail::words.event')]),
            ]);
        }
        
        if ($check !== true)
            return $check;
        return (new MeetingResource(Meeting::with(['workshop' => function ($q) {
            $q->withoutGlobalScopes();
        }])->where('workshop_id', $workshopId)->first()))->additional([
            'status' => true,
            'data'   => ['event_type' => $event->type],
        ]);
    }
    
    /*
    ENF OF FETCH DATA
    */
    /*
     * FUNCTIONALITY METHOD
     */
    public function exportMemberList($workshopId) {
        $event = Event::where('workshop_id', $workshopId)->first();
        if (!$event) {
            return response()->json(['status' => false, 'msg' => 'Invalid Workshop'], 422);
        }
        $collection = WorkshopMeta::selectRaw('DISTINCT user_id')
            ->where('workshop_id', $workshopId)
            ->with(['user', 'user.unions', 'user.companies'])
            ->whereHas('user')
            ->get();
        if ($collection->count()) {
            $result = [];
            $collection->map(function ($row) use (&$result) {
                $result[] = [
                    stripcslashes($row->user->fname),
                    stripcslashes($row->user->lname),
                    $row->user->email,
                    $row->user->unions->first() ? $row->user->unions->first()->long_name : '',
                    $row->user->unions->first() ? $row->user->unions->first()->pivot->entity_label : '',
                    $row->user->companies->first() ? stripcslashes($row->user->companies->first()->long_name) : '',
                    $row->user->companies->first() ? stripcslashes($row->user->companies->first()->pivot->entity_label) : '',
                ];
                if ($row->user->unions->count() > 1) {
                    $unions = $row->user->unions->count();
                    for ($i = 1; $i < $unions; $i++) {
                        $result[] = [
                            '',
                            '',
                            '',
                            $row->user->unions->get($i)->long_name,
                            $row->user->unions->get($i)->pivot->entity_label,
                            '',
                            '',
                        ];
                    }
                }
                
            });
            if (isset(json_decode(Auth::user()->setting)->lang) && json_decode(Auth::user()->setting)->lang == 'FR')
                $headerRow = ['Prénom', 'Nom', 'Email', 'Unions', 'Fonction dans le syndicat', 'Structure', 'Fonction dans la Société'];
            else
                $headerRow = ['First Name', 'Last Name', 'Email', 'Unions', 'Position In The Union', 'Company', 'Position In The Company'];
            $export = new EventMemberExport(collect($result), $headerRow);
            return Excel::download($export, "$event->title $event->date.xlsx");
        }
        return response()->json(['status' => true, 'data' => null], 200);
    }
    
    
    /*
     * END OF FUNCTIONALITY METHOD
     */
    /*
     * ORG ADMIN SECTION
     */
    public function changeDefaultOrganiser($key, $value) {
        try {
            $validations = [
                'prefix'    => 'required|string|max:100',
                'organiser' => ['required', Rule::exists('tenant.users', 'id')]
            ];
            $column = [
                'prefix'    => 'prefix',
                'organiser' => 'default_organiser'
            ];
            $validator = Validator::make(
                ['key'   => $key,
                 'value' => $value],
                ['key'   => 'required|in:prefix,organiser',
                 'value' => isset($validations[$key]) ? $validations[$key] : 'required',
                ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            $this->eventService->updateOrgAdminSetting($column[$key], $value, 'event_org_setting');
            return response()->json(['status' => true, 'msg' => 'Successfully Updated'], 200);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => 'Internal server error' . $e->getMessage()], 500);
        }
    }
    
    /**
     * To update the org admin settings
     *
     * @param UpdateOrgAdminRequest $request
     * @return JsonResponse
     */
    public function updateOrgAdminSetting(UpdateOrgAdminRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $parent = null;
            $key = $request->key;
            if (in_array($request->key, ['virtual_do', 'virtual_prefix'])) {
                $parent = 'event_virtual_org_setting';
                $key = $request->key == 'virtual_do' ? 'default_organiser' : 'prefix';
            }
            $this->eventService->updateOrgAdminSetting($key, $request->value, $parent);
            DB::connection('tenant')->commit();
            return response()->json(['status' => true, 'msg' => 'Successfully Updated'], 200);
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => 'Internal server error: ' . $e->getMessage(), 'error' => $e->getTrace()], 500);
        }
    }
    
    /*
    * END OF ORG ADMIN SECTION
    */
    /*
     * SEARCH API
     */
    public function searchOrgAdminList($key) {
        try {
            $users = [];
            if (strlen($key) >= 3) {
                $key = ltrim($key);
                $key = rtrim($key);
                $users = User::selectRaw("id, CONCAT(fname, ' ', lname) as name, email")
                    ->where('role', 'M1')
                    ->where(function ($query) use ($key) {
                        $query->orWhere('fname', 'like', '%' . $key . '%')
                            ->orWhere('lname', 'like', '%' . $key . '%')
                            ->orWhereRaw("CONCAT(fname,' ',lname) like '%$key%'")
                            ->orWhere('email', 'like', '%' . $key . '%');
                    })
                    ->get();
            }
            return response()->json(['status' => true, 'data' => $users], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'data' => 'Internal server error in getting org admin list' . $e->getMessage()], 500);
        }
    }
    
    public function searchSecretoryList($key) {
        try {
            $users = null;
            if (strlen($key) >= 3) {
                $key = ltrim($key);
                $key = rtrim($key);
                $users = User::selectRaw("id, CONCAT(fname, ' ', lname, ' (', email, ')') as name, email, role_commision")
                    ->where(function ($q) {
                        $q->orWhere('role_commision', '1');
                        $q->orWhere('role', 'M1');
                        $q->orWhere('role', 'M0');
                    })
                    ->where(function ($query) use ($key) {
                        $query->orWhere('fname', 'like', '%' . $key . '%')
                            ->orWhere('lname', 'like', '%' . $key . '%')
                            ->orWhereRaw("CONCAT(fname,' ',lname) like '%$key%'")
                            ->orWhere('email', 'like', '%' . $key . '%');
                    })
                    ->get();
            }
            return response()->json(['status' => true, 'data' => $users], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'data' => 'Internal server error in getting org admin list' . $e->getMessage()], 500);
        }
    }
    
    public function searchEvent($tense, $key, $paginate = null) {
        try {
            $events = [];
            if (strlen($key) >= 3) {
                $key = trim($key);
                return EventResource::collection($this->eventService
                    ->getEventsListWithOrganiser($tense, null, null, null, $key))->additional(['status' => true,]);
            }
            return EventResource::collection($events->get())->additional(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'data' => 'Internal server error in getting event list' . $e->getMessage()], 500);
        }
    }
    
    public function searchEventWorkshop(Request $request, $key) {
//        try {
        $data = $this->eventService->getEventWorkshopsWithMemberCount(
            null,
            $request->input('page', 1),
            $request->input('field'),
            $request->input('order'),
            $key
        );
        return EventWorkshopResource::collection($data)->additional(['status' => true]);

//        } catch (\Exception $e) {
//            return response()->json(['status' => false, 'data' => 'Internal server error in getting event list' . $e->getMessage()], 500);
//        }
    }
    
    /*
    To register external the member from wordpress to our event
    */
    public function addMember(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'email'        => 'required|unique:tenant.users,email',
                'firstname'    => 'required|regex:/^[0-9a-zA-Zu00E0-u00FC&àâäèéêëîïôœùûüÿççÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ _\'\ "-]*$/m',
                'lastname'     => 'required|regex:/^[0-9a-zA-Zu00E0-u00FC&àâäèéêëîïôœùûüÿççÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ _\'\ "-]*$/m',
                'post_id'      => 'required|exists:tenant.event_info,wp_post_id',
                'company_id'   => 'required',
                'company_name' => 'sometimes|required',
                'position'     => 'sometimes|required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()
                    ->all())]);
            }
            //get workshopId based on Post Id
            $event = Event::where('wp_post_id', $request->post_id)->first(['id', 'workshop_id', 'wp_post_id']);
            $request->merge(['email_send' => 0, 'workshop_id' => $event->workshop->id, 'firstname' => stripcslashes($request->firstname), 'lastname' => stripcslashes($request->lastname)]);
            
            DB::connection('tenant')->beginTransaction();
            $reponse = $this->workshop->addMember($request);
            $reponse = json_decode($reponse->getContent());
            if (isset($reponse->status) && $reponse->status) {
                //checking company is created or not
                if ($request->company_id < 0) {
                    $company = Entity::create([
                        'long_name'      => stripcslashes($request->company_name),
                        'entity_type_id' => 2,
                        //'created_by'     => Auth::user()->id,
                    ]);
                }
                $user = User::where('email', $request->email)->first(['id']);
                $eventUser = EntityUser::create([
                    'user_id'      => $user->id,
                    'entity_id'    => (($request->company_id < 0) ? $company->id : $request->company_id),
                    'entity_label' => isset($request->postion) ? stripcslashes($request->postion) : stripcslashes($request->position),
                ]);
                DB::connection('tenant')->commit();
                return response()->json(['status' => true, 'data' => __('message.memberCreated')], 200);
            } else {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => false, 'data' => 'Something Wrong Happens'], 400);
            }
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'data' => 'Internal server error in adding event Workshop Member ' . $e->getMessage()], 500);
        }
        
    }
    
    public function getCompanies($val) {
        $entity = Entity::where('long_name', 'LIKE', $val . '%')
            ->where('entity_type_id', 2)
            ->get(['id', 'long_name']);
        return response()->json(['status' => true, 'data' => $entity], 200);
    }
    
    public function occupiedDates(Request $request) {
        $validator = Validator::make($request->all(), [
            'month'    => 'required|integer|max:12',
            'year'     => 'required|integer|min:' . Carbon::now()->year,
            'type'     => 'nullable|in:int,ext,virtual',
            'event_id' => 'nullable|exists:tenant.event_info,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()
                ->all())], 422);
        }
        $events = $this->eventService->getOccupiedDates($request);
        return EventOccupiedDateResource::collection($events)->additional(['status' => true]);
    }
}
/*


select `id`, `event_id`
from `events_members`
where exists
    (
        select * from `event_info`
        where `events_members`.`event_id` = `event_info`.`id`
                and (`date` < ? or (`date` = ? and `start_time` < ?))
                and `event_info`.`deleted_at` is null
    )
    and `member_id` = ?


 */
