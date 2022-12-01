<?php

namespace Modules\KctUser\Services\BusinessServices;

use Modules\KctUser\Entities\Event;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will manage the user mangement
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IKctService
 * @package Modules\KctUser\Services\BusinessServices
 */
interface IKctService {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update the language for the user
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $lang
     * @return mixed
     */
    public function updateUserLanguage($lang);

    /**
     * @param $eventUuid
     * @param $userId
     * @return mixed
     */
    public function markUserAsFirstLogin($eventUuid, $userId);

    /**
     * @param $eventUuid
     * @return mixed
     */
    public function isFirstLoginToEventAfterRegistration($eventUuid);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the redirect url on the basis of type
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @param $type
     * @param array $replace
     * @return string
     */
    public function getRedirectUrl($request, $type, array $replace = []): string;

    /**
     * @param $event
     * @param $userId
     * @return mixed
     */
    public function prepareEmailTags($event, $userId);

    /**
     * @param $request
     * @return mixed
     */
    public function getDefaultHost($request);

    /**
     * @param $request
     * @return mixed
     */
    public function resetPassword($request);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To modify the request according to the dummy event conversation environment if any
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @return mixed
     */
    public function modifyConvReqForDummy($request);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This will map dummy users to provided conversation
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $conversation
     * @return mixed
     */
    public function mapDummyUsersToConv($conversation);

    /**
     *-----------------------------------------------------------------------------------------------------------------
     * @description To load the tags for a specific conversation.This is done in following steps-
     * 1.Get the users collection of conversation.
     * 2.Apply tags to user collection, attach it back to conversation users
     * 3.Return the modified conversation;
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $conversation
     * @param $allTags
     * @param $allPPTags
     * @return mixed
     */
    public function loadTagForConversationModel($conversation, $allTags, $allPPTags);

    /**
     * @param $event
     * @return array
     */
    public function getEventHeaders($event): array;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To find the event is dummy or not
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return int
     */
    public function isEventDummy($event): int;

    /**
     * ----------------------------------------------------------------------------------------------------------------
     * @descripiton find and set the host name by meeting id
     * ----------------------------------------------------------------------------------------------------------------
     *
     * @param $meetingId
     * @return mixed
     */
    public function findAndSetHostnameByMeetingId($meetingId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Get the data by event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @param $key
     * @return mixed
     */
    public function getDataByEvent($event, $key);


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to get the graphics data by the group id
     * @warn if no group id is provided default group graphics will be return
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $groupId
     * @return mixed
     */
    public function prepareGraphicsData(int $groupId = 1);



    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check if event access code is correct or not
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @param $accessCode
     * @return bool
     */
    public function eventCheckAccessCode($event, $accessCode): bool;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to get the event max conversation users
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return int
     */
    public function getEventMaxConvCount($event): int;
}
