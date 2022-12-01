<?php


namespace Modules\Cocktail\Services\Contracts;


use Illuminate\Http\Request;
use Modules\Cocktail\Exceptions\CustomValidationException;

interface ExternalEventFactory {
    
    const PRESENTER = 'presenter';
    const MODERATOR = 'moderator';
    const ATTENDEE = 'attendee';
    
    /**
     * @param array $parameter
     * @return mixed
     * @throws CustomValidationException
     */
    public function create($parameter);
    
    /**
     * @param Request $request
     * @return mixed
     */
    public function prepareCreateParamFromRequest($request);
    
    /**
     * @param $eventId
     * @param array $parameter
     * @return mixed
     */
    public function update($eventId, $parameter);
    
    /**
     * @param Request $request
     * @return mixed
     */
    public function prepareUpdateParamFromRequest($request);
    
    /**
     * @param string $eventId
     * @return mixed
     */
    public function delete($eventId);
    
    /**
     * @param $eventId
     * @param $data
     * @return boolean
     */
    public function addMember($eventId, $data);
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To remove a user form a conference
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventId
     * @param $data
     * @return mixed
     */
    public function removeMember($eventId, $data);
    
    /**
     * To get the event from the external factory
     *
     * @param $eventId
     * @param bool $validateResponse
     * @return mixed
     */
    public function getEvent($eventId, $validateResponse=false);
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the array which contains options for the conference setting
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return mixed
     */
    public function prepareConferenceOptions($request);
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the join link for the respective role
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $conferenceId
     * @param string $type
     * @return string
     */
    public function getJoinLink($conferenceId, $type);
    
    
    public function prepareEmbeddedLnk($conferenceId);
    
}