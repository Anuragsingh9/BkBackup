<?php

namespace Modules\Cocktail\Http\Controllers\V1\AdminSideControllers;

use App\AccountSettings;
use App\Exceptions\CustomJsonException;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Cocktail\Http\Requests\V1\EventAdminUpdateRequest;
use Modules\Events\Exceptions\CustomException;
use Validator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Cocktail\Exceptions\CustomValidationException;
use Modules\Cocktail\Exceptions\InternalServerException;
use Modules\Cocktail\Http\Requests\V1\EventGraphicsLogoDeleteRequest;
use Modules\Cocktail\Http\Requests\V1\EventKeepContactRequest;
use Modules\Cocktail\Http\Requests\V1\EventRegistrationFormRequest;
use Modules\Cocktail\Http\Requests\V1\EventUserAddRequest;
use Modules\Cocktail\Http\Requests\V1\EventUserRemoveRequest;
use Modules\Cocktail\Http\Requests\V1\EventUserUpdateRequest;
use Modules\Cocktail\Services\Contracts\EmailFactory;
use Modules\Cocktail\Services\KctEventService;
use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Cocktail\Services\DataService;
use Modules\Cocktail\Services\KctService;
use Modules\Cocktail\Transformers\AdminSide\EventGraphicsResource;
use Modules\Cocktail\Transformers\AdminSide\EventRegistrationDetailsResource;
use Modules\Cocktail\Transformers\AdminSide\EventUserResource;
use Modules\Events\Entities\Event;

/**
 * Class KctEventController
 * @package Modules\Cocktail\Http\Controllers
 */
class KctEventController extends Controller {
    
    private $service;
    /**
     * @var EmailFactory
     */
    private $emailFactory;
    
    public function test() {
        return AccountSettings::where('account_id', 1)->first()->setting;
        
    }
    
    public function __construct(EmailFactory $emailFactory) {
        $this->emailFactory = $emailFactory;
        $this->service = KctEventService::getInstance();
    }
    
    
    /*
     * ADMIN PART
     */
    
    /**
     * To update the event setting, specially for keepContact setting of particular event
     *
     * @param EventKeepContactRequest $request
     * @return EventGraphicsResource|JsonResponse
     */
    public function updateKeepContactCustomization(EventKeepContactRequest $request) {
        $dataService = DataService::getInstance();
        try {
            DB::connection('tenant')->beginTransaction();
            $event = Event::where('event_uuid', $request->input('event_uuid'))->first();
            $param = $dataService->prepareKeepContactCustomization($request);
            $param = $this->service->uploadGraphicsLogo($param, $request, $event);
            // this will add keepContact in json data to eventFields column of event
            // and providing event uuid so event will be also fetched from there
            $event = $this->service->addOrUpdateEventJsonFields('event_fields', $param, null, $event);
            DB::connection('tenant')->commit();
            return (new EventGraphicsResource($event))->additional(['status' => true]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => 'Internal Server Error',], 500);
        }
    }
    
    public function updateRegistrationFormDetail(EventRegistrationFormRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $param = [
                'registration_details' => [
                    'display' => $request->display ? 1 : 0,
                    'title'   => $request->title,
                    'points'  => $request->points,
                ]
            ];
            $event = $this->service->addOrUpdateEventJsonFields('event_fields', $param, $request->event_uuid);
            DB::connection('tenant')->commit();
            return (new EventRegistrationDetailsResource($event))->additional(['status' => true]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'error' => $e->getTrace()], 500);
        }
    }
    
    /**
     * @description to add the user to event either by event uuid or workshop id
     *
     * @param EventUserAddRequest $request
     *
     * @return JsonResponse|EventUserResource
     */
    public function eventUserAdd(EventUserAddRequest $request) {
        
        try {
            DB::connection('tenant')->beginTransaction();
    
            // to check for duplicate entry
            $this->service->checkUserForDuplicateAdd($request);
            
            // to check if the user already exists or not
            $user = User::where('email', strtolower($request->email))->first();
    
            // adding user to event and the workshop attached to that event
            // this method will either create new user or add existing user to event depends on request
            $res = $this->service->addUserToEventAndWorkshop($request);
            
            // as user is not present and created so send the register email
            if(!$user) {
                $this->service->sendWelcomeMail($request);
            }
    
            // updating user status to Email Verified as user has been added from OPS Admin Side
            $this->service->verifyUserEmail($res->userId);
    
            // Sending Email to User for Email Registration
    
            $tags = KctService::getInstance()->prepareEmailTags($res->event, $res->userId);
            $root = $request->input('link', KctService::getInstance()->getDefaultHost($request));
    
            if ($res->event->type == config('events.event_type.virtual')) {
                $result = (new EventUserResource($res))->additional(['status' => true]);
                $data = ['tags' => $tags, 'root' => $root];
                $this->emailFactory->sendVirtualRegistration($res->event, $res->userId, $data);
            } else {
                $this->emailFactory->sendIntRegistration($res->event, $res->userId, $tags);
                $result = response()->json(['status' => 1]);
            }
            DB::connection('tenant')->commit();
            return $result;
        } catch (CustomValidationException $e) {
            DB::connection('tenant')->commit();
            return $e->render();
        } catch (InternalServerException $e) {
            DB::connection('tenant')->commit();
            return $e->render();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'error' => $e->getTrace()], 500);
        }
    }
    
    /**
     * @description to remove the user from event
     *
     * @param EventUserRemoveRequest $request
     *
     * @return JsonResponse
     */
    public function eventUserRemove(EventUserRemoveRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $this->service->eventUserRemove($request->event_uuid, $request->user_id);
            DB::connection('tenant')->commit();
            return response()->json(['status' => true, 'data' => ['user_id' => $request->user_id]], 200);
        } catch (InternalServerException $e) {
            return $e->render();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => 'Internal Server Error', 'error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * To update the role of user inside event
     * this can update the following fields of user inside event
     * 1. toggle presenter, 2. toggle moderator, 3 - host, 4 - presence
     *
     * @param EventUserUpdateRequest $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    
    public function eventUserUpdateRole(EventUserUpdateRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $this->service->eventUserUpdateRole($request->event_uuid, $request->user_id, $request->field, $request->space_uuid, $request->presence);
            $result = $this->service->getEventUsers($request->event_uuid, null, $request);
            $result = $result ? $result : new Collection();
            $additional = $this->service->getEventUserMeta($request->event_uuid);
            DB::connection('tenant')->commit();
            return EventUserResource::collection($result)->additional([
                'status' => true,
                'meta'   => $additional,
            ]);
        } catch (CustomValidationException $e) {
            DB::connection('tenant')->rollback();
            return $e->render();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => 'Internal Server Error', 'error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * @description To update the user role to admin level of workshop like sec/dep if applicable
     *
     * @param EventAdminUpdateRequest $request
     *
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function updateAdminRole(EventAdminUpdateRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            
            $event = $this->service->getEventByWorkshop($request->input('event_uuid'), $request->input('workshop_id'));
            
            $this->service->checkUserForWorkshopAdmin($request->input('user_id'), $event->workshop_id, $request->input('role'));
            $this->service->updateEventAdminRole($event, $request);
            
            $result = $this->service->getEventUsers($event->event_uuid, null, $request);
            $additional = $this->service->getEventUserMeta($event->event_uuid);
            
            DB::connection('tenant')->commit();
            return EventUserResource::collection($result)->additional([
                'status' => true,
                'meta'   => $additional
            ]);
        } catch (CustomValidationException $e) {
            DB::connection('tenant')->rollback();
            $result = $e->render();
        } catch (InternalServerException $e) {
            DB::connection('tenant')->rollback();
            $result = $e->render();
        } catch (CustomException $e) {
            DB::connection('tenant')->rollback();
            $result = $e->render();
        } catch(CustomJsonException $e) {
            DB::connection('tenant')->rollback();
            $result = $e->render();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            $result = response()->json(['status' => false, 'msg' => 'Internal Server Error', 'error' => $e->getMessage()], 500);
        }
        return $result;
    }
    
    public function eventUsers(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'page'          => 'nullable|numeric',
                'order_by'      => 'nullable|string',
                'order'         => 'nullable|in:asc,desc',
                'item_per_page' => 'nullable|numeric',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 422);
            }
            $result = $this->service->getEventUsers($request->event_uuid, null, $request);
            $result = $result ? $result : new Collection();
            $additional = $this->service->getEventUserMeta($request->event_uuid);
            return EventUserResource::collection($result)->additional([
                'status' => true,
                'meta'   => $additional,
            ]);
        } catch (CustomValidationException $e) {
            return $e->render();
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => 'Internal Server Error ', 'error' => $e->getMessage()], 500);
        }
    }
    
    public function eventUsersSearch(Request $request) {
        try {
            $result = $this->service->getEventUsersSearch($request->event_uuid, $request->key, $request->item_per_page);
            $result = $result ? $result : new Collection();
            return EventUserResource::collection($result)->additional(['status' => true]);
        } catch (CustomValidationException $e) {
            return $e->render();
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => 'Internal Server Error ', 'error' => $e->getMessage()], 500);
        }
    }
    
    public function getEventGraphicsCustomization($event_uuid) {
        try {
            $event = Event::where('event_uuid', $event_uuid)->first();
            if (!$event)
                throw new CustomValidationException('exists', 'event');
            return (new EventGraphicsResource($event))->additional(['status' => true]);
        } catch (CustomValidationException $e) {
            return $e->render();
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => 'Internal Server Error ', 'error' => $e->getMessage()], 500);
        }
    }
    
    public function getEventRegistrationDetails($event_uuid) {
        try {
            $event = Event::where('event_uuid', $event_uuid)->first();
            if (!$event) {
                throw new CustomValidationException('exists', 'event');
            }
            $startTime = Carbon::createFromFormat("Y-m-d H:i:s", $event->date . ' ' . $event->start_time);
            return (new EventRegistrationDetailsResource($event))
                ->additional([
                    'status' => true,
                    'meta'   => [
                        'event_id'         => $event->id,
                        'workshop_id'      => $event->workshop_id,
                        'event_start_time' => $event->start_time,
                        'event_date'       => $event->date,
                        'is_past'          => Carbon::now()->timestamp > $startTime->timestamp,
                        'event_end_time'   => $event->end_time,
                    ],
                ]);
        } catch (CustomValidationException $e) {
            return $e->render();
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => 'Internal Server Error ', 'error' => $e->getMessage()], 500);
        }
    }
    
    public function deleteGraphicsLogo(EventGraphicsLogoDeleteRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $result = $this->service->deleteGraphicsLogo($request->input('event_uuid'));
            if ($result) {
                $param = $result->event_fields;
                $param['keepContact']['page_customisation']['keepContact_page_logo'] = null;
                $this->service->addOrUpdateEventJsonFields('event_fields', $param, null, $result);
            }
            $event = Event::where('event_uuid', $request->input('event_uuid'))->first();
            DB::connection('tenant')->commit();
            return (new EventGraphicsResource($event))->additional(['status' => true]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => 'Internal Server Error ', 'error' => $e->getMessage()], 500);
        }
    }
}
