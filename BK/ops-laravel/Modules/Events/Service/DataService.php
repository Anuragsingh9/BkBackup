<?php


namespace Modules\Events\Service;

use App\Services\Service;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Events\Entities\Organiser;
use Modules\Events\Exceptions\CustomValidationException;


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description The responsibility of this class is to prepare the data for the database to insert from given param
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class DataService
 * @package Modules\Events\Service
 */
class DataService extends Service {
    
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to prepare the required data and parameters used for creating event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return array[]
     * @throws CustomValidationException
     * @throws \Modules\Cocktail\Exceptions\CustomValidationException
     */
    public function eventCreateParam($request) {
        
        $data = [
            'imageUrl'             => EventService::getInstance()->uploadImageGetUrl($request->image),
            'prefix'               => OrganiserService::getInstance()->getDefaultPrefix($request->input('type')),
            'defaultOrganiserUser' => OrganiserService::getInstance()->getDefaultOrganiser($request->input('type')),
        ];
        
        switch ($request->input('type')) {
            case 'int':
                $data = $this->internalEventCreateParam($request, $data);
                break;
            case 'ext':
                $data = $this->externalEventCreateParam($request, $data);
                break;
            default:
                $data = $this->virtualEventCreateParam($request, $data);
        }
        
        return $data;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description this will prepare all the data which will be needed to create int type event only
     * there will event table, event organiser data and some extra fields for further needed to create int event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @param $data
     * @return array
     * @throws CustomValidationException|\Modules\Cocktail\Exceptions\CustomValidationException
     */
    public function internalEventCreateParam($request, $data) {
        // event create parameters
        $data['eventData'] = $this->intEventParam($request, $data);
        $data['organiser'] = $this->intEventOrg($data);
        $data['orgAdmin'] = EventService::getInstance()->getFirstOrgAdmin();
        $data['organisation'] = EventService::getInstance()->getOrganisation();
        $data['workshopCreate'] = $this->intVirtualWorkshopParam($request, $data);
        
        return $data;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description this will prepare all the data which will be needed to create virtual type event only
     * there will event table, event organiser data and some extra fields for further needed to create int event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @param $data
     * @return array
     * @throws CustomValidationException|\Modules\Cocktail\Exceptions\CustomValidationException
     */
    public function virtualEventCreateParam($request, $data) {
        $data['organisation'] = EventService::getInstance()->getOrganisation();
        $data['orgAdmin'] = EventService::getInstance()->getFirstOrgAdmin();
        $data['eventData'] = $this->virtualEventParam($request, $data);
        $data['organiser'] = $this->intEventOrg($data);
        $data['workshopCreate'] = $this->intVirtualWorkshopParam($request, $data);
        $data['defaultSpace'] = $this->defaultSpaceParam($request);
        return $data;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the parameters for the external event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @param $data
     * @return mixed
     */
    public function externalEventCreateParam($request, $data) {
        $data['organiser'] = $this->extEventOrg($request);
        // as int/ext doesn't have difference in db fields to store so using same
        // if needed separate for ext type create a new method and call it
        $data['eventData'] = $this->intEventParam($request, $data);
        return $data;
    }
    
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description this will prepare the parameters for the int type event database column fields
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @param $data
     * @return array
     */
    public function intEventParam($request, $data) {
        return [
            'title'              => $request->input('title'),
            'header_text'        => $request->input('header_text'),
            'description'        => $request->input('description'),
            'date'               => $request->input('date'),
            'start_time'         => $request->input('start_time'),
            'end_time'           => $request->input('end_time'),
            'address'            => $request->input('address'),
            'city'               => $request->input('city'),
            'image'              => $data['imageUrl'],
            'type'               => $request->input('type'),
            'created_by_user_id' => Auth::user()->id,
            'territory_value'    => $request->is_territory ? $request->territor_value : null, // Typo error from front end fixed in backend with territory -> territor
        ];
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to prepare the param for virtual event type
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @param $data
     * @return array
     */
    public function virtualEventParam($request, $data) {
        return [
            'title'              => $request->input('title'),
            'header_text'        => $request->input('header_text'),
            'description'        => $request->input('description'),
            'date'               => $request->input('date'),
            'start_time'         => $request->input('start_time'),
            'end_time'           => $request->input('end_time'),
            'address'            => $data['organisation']->address1,
            'city'               => $data['organisation']->city,
            'image'              => $data['imageUrl'],
            'type'               => $request->input('type'),
            'created_by_user_id' => Auth::user()->id,
            'territory_value'    => $request->is_territory ? $request->territor_value : null, // Typo error from front end fixed in backend with territory -> territor
            'event_fields'       => EventService::getInstance()->prepareEventFields($request),
            'bluejeans_settings' => EventService::getInstance()->getBlueJeansSetting($request),
            'manual_opening'     => config('events.defaults.manual_opening'),
        ];
    }
    
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to prepare the param for the int event organiser
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $data
     * @return array
     */
    public function intEventOrg($data) {
        return [
            'created_by_user_id' => Auth::user()->id,
            'eventable_id'       => $data['defaultOrganiserUser'] ? $data['defaultOrganiserUser']->id : null,
            'eventable_type'     => User::class,
        ];
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to prepare the parameters for the workshop of int or virtual type as both have same functionality
     * with workshop
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @param $data
     * @return array
     */
    public function intVirtualWorkshopParam($request, $data) {
        $code1 = EventService::getInstance()->getIncrementedCode1($data['orgAdmin']);
        
        if ($request->type == 'virtual') {
            // for virtual type there is no address so using organisation address for workshop/meetings
            $address = $data['organisation']->address1;
            $title = $request->input('title');
            $workshopName = str_start($title, $data['prefix']);
        } else {
            $address = $request->input('address');
            $city = strtoupper($request->input('city'));
            $workshopName = " {$data['prefix']} - $city";
        }
        
        return [
            'workshop_name'             => $workshopName,
            'workshop_type'             => 1,
            'code1'                     => $code1,
            'code2'                     => null,
            'address'                   => $address,
            'is_private'                => 0,
            'president_id'              => $data['defaultOrganiserUser']->id,
            'validator_id'              => $data['orgAdmin']->id,
            'workshop_desc'             => $request->description,
            'is_qualification_workshop' => 3,
        ];
        
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare param for external event organiser
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @return array
     */
    public function extEventOrg($request) {
        return [
            // eventable parameter -> for organiser of event
            'created_by_user_id' => Auth::user()->id,
            'eventable_id'       => $request->organiser_id,
            'eventable_type'     => Organiser::class,
        ];
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description the default space param is prepared separated  as on next version the parameters have some different
     * values.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @return array
     */
    public function defaultSpaceParam($request) {
        return [
            'space_name'                => __('cocktail::message.space_default_name'),
            'space_short_name'          => config('cocktail.space_default_short_name'),
            'space_mood'                => config('cocktail.space_default_mood'),
            'space_image'               => $request->image,
            'space_image_from'          => config('kct_const.space_image_system'),
            'space_icon'                => null,
            'hosts'                     => null,
            'is_vip_space'              => 0,
            'opening_hours'             => [
                'after'  => $request->opening_hours_after ? $request->opening_hours_after : 0 ,
                'before' => $request->opening_hours_before ? $request->opening_hours_before : 0 ,
                'during' => $request->opening_hours_during ? $request->opening_hours_during : 1 ,
            ],
            // order id to keep space sorted and for default at top for first time
            'order_id'                  => config('kct_const.space_start_order'),
            // as default space is created along with event so setting this space following event opening hours
            'follow_main_opening_hours' => 1,
        ];
    }
    
}
