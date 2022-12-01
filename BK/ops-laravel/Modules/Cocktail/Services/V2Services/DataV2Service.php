<?php

namespace Modules\Cocktail\Services\V2Services;

use App\Services\Service;
use App\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Cocktail\Entities\Conversation;
use Modules\Cocktail\Exceptions\CustomValidationException;
use Modules\Cocktail\Services\Contracts\ExternalEventFactory;
use Modules\Cocktail\Services\EventSpaceService;
use Modules\Cocktail\Services\KctService;
use Modules\Events\Entities\Event;
use Modules\Events\Service\DataService;
use Modules\Events\Service\EventService;
use Modules\Events\Service\OrganiserService;
use Modules\Events\Service\ValidationService;

class DataV2Service extends Service {
    
    /**
     * @var ExternalEventFactory
     */
    private $conferenceFactory;
    
    public function __construct() {
        $this->conferenceFactory = app()->make(ExternalEventFactory::class);
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This will add some required data to the request
     * as the request is targeted to create KCT Virtual event and some fields are required for db
     * so request is confirmed to have fields value for other column which are always same for kct events
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @return mixed
     */
    public function addFieldForEventCreate($request) {
        $request->merge([
            'type'                       => 'virtual', // as the type will always gonna remain virtual for this api
            'event_uses_bluejeans_event' => 0, // as this type of event doesn't follow the external event (e.g. BJE),
//            'image'                      => '',
            'opening_hours_after'        => 0,
            'opening_hours_before'       => 0,
            'opening_hours_during'       => 1,
        ]);
        return $request;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to prepare the data for the event creation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return array
     * @throws CustomValidationException
     * @throws \Modules\Events\Exceptions\CustomValidationException
     */
    public function eventCreateData($request) {
        $data['imageUrl'] = EventService::getInstance()->uploadImageGetUrl($request->image);
        $data['prefix'] = OrganiserService::getInstance()->getDefaultPrefix($request->input('type'));
        $data['defaultOrganiserUser'] = OrganiserService::getInstance()->getDefaultOrganiser($request->input('type'));
        $data['organisation'] = EventService::getInstance()->getOrganisation();
        $data['orgAdmin'] = EventService::getInstance()->getFirstOrgAdmin();
        $data['organiser'] = DataService::getInstance()->intEventOrg($data);
        $data['eventData'] = $this->prepareEventTableData($request, $data);
        $data['defaultSpace'] = $this->prepareDefaultSpace($request);
        $data['workshopCreate'] = DataService::getInstance()->intVirtualWorkshopParam($request, $data);
        return $data;
    }
    
    /**
     * @param Request $request
     * @param Event $event
     * @return mixed
     */
    public function eventUpdateData($request, $event) {
        $data['eventData'] = $this->prepareEventUpdateData($request, $event);
        return $data;
    }
    
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to prepare the event table data fields
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @param $data
     * @return array
     * @throws CustomValidationException
     */
    public function prepareEventTableData($request, $data) {
        $eventData = [
            'title'              => $request->input('title'),
            'header_text'        => $request->input('header_text'),
            'header_line_1'      => $request->input('header_line_one'),
            'header_line_2'      => $request->input('header_line_two'),
            'description'        => $request->input('description'),
            'date'               => $request->input('date'),
            'start_time'         => $request->input('start_time'),
            'type'               => 'virtual',
            'end_time'           => $request->input('end_time'),
            'address'            => $data['organisation']->address1,
            'city'               => $data['organisation']->city,
            'image'              => $data['imageUrl'],
            'created_by_user_id' => Auth::user()->id,
            'territory_value'    => null,
            'manual_opening'     => config('events.defaults.manual_opening'),
            'event_fields'       => [
                'opening_hours'   => config('cocktail.default.v2_opening_hour'),
                'is_dummy_event'  => $request->input('is_dummy_event', 0),
                'conference_type' => null,
            ],
            "bluejeans_settings" => [
                'event_uses_bluejeans_event' => 0,
            ],
        ];
        return $this->addConferenceData($eventData, $request);
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To add the conference data in event fields if event follow conference and currently conference is on
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param array $eventData
     * @param Request $request
     * @return array
     * @throws CustomValidationException
     */
    public function addConferenceData($eventData, $request) {
        if ($request->input('follow_conference')) {
            $currentConference = KctCoreService::getInstance()->getCurrentConference();
            if ($currentConference) {
                $eventData['event_fields'] = array_merge($eventData['event_fields'],
                    $this->conferenceFactory->prepareConferenceOptions($request)
                );
            } else {
                // here event wants to use conference but its disabled from super admin so throw validation
                throw new CustomValidationException('conference_not_enabled', null, 'message');
            }
        }
        return $eventData;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the data for the event update
     * this will check if event is running or not so only possible fields will be updated if event running
     *
     * @warn the manual opening must be validated before calling this method with proper condition
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @param Event $event
     * @return array
     */
    public function prepareEventUpdateData($request, $event) {
        $isEventRunning = ValidationService::getInstance()->isEventOrSpaceRunning($event);
        if (isset($request->image)){
            $imageUrl = EventService::getInstance()->uploadImageGetUrl($request->image);
        }else{
            $imageUrl = $event->image;
        }
        if ($isEventRunning) { // event is open so update only possible values
            $fields = [
                'end_time'       => $request->input('end_time', $event->end_time),
                'manual_opening' => $request->input('manual_opening', config('events.defaults.manual_opening')),
            ];
        } else {
            $fields = [
                'title'          => $request->input('title'),
                'header_text'    => $request->input('header_text'),
                'header_line_1'  => $request->input('header_line_one'),
                'header_line_2'  => $request->input('header_line_two'),
                'description'    => $request->input('description'),
                'date'           => $request->input('date'),
                'start_time'     => $request->input('start_time'),
                'end_time'       => $request->input('end_time'),
                'manual_opening' => $request->input('manual_opening', config('events.defaults.manual_opening')),
                'event_fields'   => $this->getFieldUpdateParam($event, $request),
                'image'          => $imageUrl,
            ];
        }
        return $fields;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the param for the updating event conference options fields
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Event $event
     * @param $request
     * @return array
     */
    public function getFieldUpdateParam($event, $request) {
        $oldEventFields = $event->event_fields;
        $conferenceType = KctCoreService::getInstance()->findEventConferenceType($event);
        if ($conferenceType && isset($oldEventFields['conference_settings'])) {
            $oldEventFields['conference_settings'] = $this->conferenceFactory->prepareConferenceOptions($request)['conference_settings'];
        }
        return $oldEventFields;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the data for creating space
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return array
     */
    public function spaceCreateParam($request) {
        $lastSpaceOrderId = EventSpaceService::getInstance()->getLastSpaceOrderId($request->input('event_uuid'));
        $rank = KctService::getInstance()->getLexoRank($lastSpaceOrderId);
        return [
            'space_name'                => $request->space_name,
            'space_short_name'          => $request->space_short_name,
            'space_mood'                => $request->space_mood,
            'max_capacity'              => $request->max_capacity,
            'is_vip_space'              => $request->space_type == config('cocktail.default.space_type_vip') ? 1 : 0,
            'is_duo_space'              => $request->space_type == config('cocktail.default.space_type_duo') ? 1 : 0,
            'event_uuid'                => $request->event_uuid,
            'follow_main_opening_hours' => 1,
            'order_id'                  => $rank,
            // putting 0 as there is no role of space opening hour,
            // but previous validations check for opening hour and for safe from undefined putting 0
            'opening_hours'             => [
                'after'  => 0,
                'before' => 0,
                'during' => 1,
            ],
        ];
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the parameters for the space update
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @return array
     */
    public function spaceUpdateParam($request) {
        return [
            'space_name'       => $request->space_name,
            'space_short_name' => $request->space_short_name,
            'space_mood'       => $request->space_mood,
            'max_capacity'     => $request->max_capacity,
            'is_vip_space'     => $request->space_type == config('cocktail.default.space_type_vip') ? 1 : 0,
            'is_duo_space'     => $request->space_type == config('cocktail.default.space_type_duo') ? 1 : 0,
        ];
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the param for the default space
     * This method using the previous version default space param and modifying the parameters according to this new
     * version
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @return array
     */
    public function prepareDefaultSpace($request) {
        $data = DataService::getInstance()->defaultSpaceParam($request);
        $data['space_name'] = __('cocktail::message.space_default_name');
        $data['space_short_name'] = __('cocktail::message.space_default_short_name');
        $data['opening_hours'] = config("cocktail.default.v2_opening_hour");
        $data['max_capacity'] = config('cocktail.default.v2_space_max_capacity');
        return $data;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the invite users data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @return array
     */
    public function prepareInviteUsers($request) {
        $users = $request->input('user');
        $data = [];
        foreach ($users as $user) {
            if ($this->checkEmailAlreadyPresent($data, $user['email'])) {
                continue;
            }
            $data[] = $this->prepareUserForInvite($user, 0, $request->input('event_uuid'));
        }
        return $data;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check if the email has already included or not
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $data
     * @param $email
     * @return bool
     */
    private function checkEmailAlreadyPresent($data, $email) {
        foreach ($data as $user) {
            if ($user['email'] == $email) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the data for user invite
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $user
     * @param int $object
     * @param $eventUuid
     * @return array
     */
    public function prepareUserForInvite($user, $object = 0, $eventUuid) {
        $data = [
            'invited_by_user_id' => Auth::user()->id,
            'event_uuid'         => $eventUuid,
            'created_at'         => Carbon::now()->toDateTimeString(),
            'updated_at'         => Carbon::now()->toDateTimeString(),
        ];
        if ($object) {
            $data['first_name'] = $user->fname;
            $data['last_name'] = $user->lname;
            $data['email'] = $user->email;
        } else {
            $data['first_name'] = $user['fname'];
            $data['last_name'] = $user['lname'];
            $data['email'] = $user['email'];
        }
        return $data;
    }

    public function prepareParamForQueueLog($request){
        $conversationUiid = isset($request->conversation_uuid) ? $request->conversation_uuid : null;
        if ($conversationUiid){
            $conversation = $this->getSpaceByConversation($conversationUiid);
        }
        return  [
            'status'=> $request->input('status'),
            'from_id'=> $request->input('from_id'),
            'to_id'=> Auth::user()->id,
            'event_uuid'=> $request->input('event_uuid'),
            'conversation_uuid'=> $conversationUiid,
            'space_uuid'=> isset($conversation) ? $conversation->space_uuid : null,
        ];
    }

    public function getSpaceByConversation($conversationUuid){
        // returning conversation as this will be used to show the conversation type etc
        return Conversation::where('uuid',$conversationUuid)->first();
    }
}
