<?php


namespace Modules\KctAdmin\Services\DataServices;


use Exception;
use Illuminate\Http\Request;
use Modules\KctAdmin\Entities\Event;
use Modules\KctAdmin\Entities\EventMeta;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will contain preparation of data
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IDataService
 * @package Modules\KctAdmin\Services\DataServices
 */
interface IDataService {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the data for the event creation.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @param $groupId
     * @param string|null $imageUrl
     * @return array
     * @throws Exception
     */
    public function prepareEventCreateData(Request $request, $groupId, ?string $imageUrl = null): array;


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare data for creating moment
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @param $event
     * @return array
     */
    public function prepareMomentData(Request $request): array;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the data for the event dummy users and add them in the default space
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $space
     * @return array
     * @throws Exception
     */
    public function prepareDummyUsers($space): array;


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the param for the default space.
     * This method using the previous version default space param and modifying the parameters according to this new
     * version
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @param Event $event
     * @param $groupId
     * @return array
     * @throws Exception
     */
    public function prepareDefaultSpace($request, Event $event, $groupId): array;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the data for the event update
     * This will check if event is running or not so only possible fields will be updated if event running
     *
     * @warn the manual opening must be validated before calling this method with proper condition
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @param Event $event
     * @param string|null $imageUrl
     * @return array
     */
    public function prepareEventUpdateData(Request $request, Event $event, ?string $imageUrl): array;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for version-4
     * This method is responsible for preparing the event update data. Some fields are not editable when the event is in
     * live status(event is running).
     *
     * @warn The manual opening must be validated before calling this method with proper condition
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @param Event $event
     * @param string|null $imageUrl
     * @return array
     */
    public function prepareV4EventUpdateData(Request $request, Event $event, ?string $imageUrl): array;


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the data for creating space
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return array
     */
    public function spaceCreateParam(Request $request): array;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the parameters for the space update
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @param $space
     * @return array
     */
    public function spaceUpdateParam($request, $space): array;

    /**
     * @param $request
     * @return mixed
     */
    public function prepareGroupCreateData($request): array;

    /**
     * @param array $inputUser
     * @return array
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is responsible for preparing user related data like personal info,company,
     * union and phone.
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function prepareUserCreateData(array $inputUser): array;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method prepares the data needs to be updated according to the request provided.
     * -----------------------------------------------------------------------------------------------------------------
     * @param $request
     * @return mixed
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is responsible for preparing data related to user profile
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function prepareUserUpdateData($request): array;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To prepare data to update user role
     * -----------------------------------------------------------------------------------------------------------------
     * @param $request
     * @return array
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is responsible for preparing data related to user's event role
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function prepareUserRoleUpdate($request): array;

    /**
     * @param $request
     * @return mixed
     */
    public function mainHostForEvent($request);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To prepare the data for event role update
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $userId
     * @param $inputRole
     * @return mixed
     */
    public function prepareMultiUserRoleUpdate($userId, $inputRole);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To filter event's user according to requested key
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @param $users
     * @return array|mixed
     */
    public function filterParticipantsByKey($request, $users);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare data for putting event in draft state
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @param $draft
     * @return array
     */
    public function prepareDraftEventData($event, $draft): array;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare data for updating draft event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @param EventMeta|null $draftStatus
     * @param $event
     * @return array
     */
    public function prepareDraftEventUpdateData($request, ?EventMeta $draftStatus = null, $event): array;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare data for publish event(Used in v4)
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @param $event
     * @return mixed
     */
    public function publishEventData($request, $event);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will copy the given event's live page data(images and video) to another event.
     * @note :-
     *  1. $oldEvent-> This is the event from which data needs to be copied.
     *  2. $targetEvent-> This is the event in which data will be pasted from old event.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $oldEvent
     * @param $targetEvent
     * @return mixed
     */
    public function getEventLivePageData($oldEvent, $targetEvent);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will prepare the for event scenery section
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @param $event
     * @return mixed
     */
    public function prepareDataForEventScenery($request, $event);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will fetch the scenery data for the event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @param false $sendAssetUrl
     * @return mixed
     */
    public function fetchEventSceneryData($eventUuid, bool $sendAssetUrl = false);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare data for updating group
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @param $group
     * @return mixed
     */
    public function prepareGroupUpdateData($request, $group);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Fetch all event's users according to there event's role.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @param $event
     * @return array
     */
    public function prepareDataForInviteEmail($request, $event): array;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To sort user by user fields and order in asc or desc and apply pagination
     * -----------------------------------------------------------------------------------------------------------------
     * @param $userIds
     * @param string|null $orderBy
     * @param string|null $order
     * @param $isPaginated
     * @param $rowPerPage
     * @return mixed
     */
    public function applyPaginationOnUsers($userIds, ?string $orderBy, ?string $order, $isPaginated, $rowPerPage);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To prepare the data for event to be recurring
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @param $event
     * @return array
     */
    public function prepareRecurringEventData($request, $event): array;


    /**
     * @param $request
     * @param $event
     * @return array
     */
    public function prepareRecurringUpdateData($request, $event): array;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is responsible for copying all demo live images in a given event.
     * -----------------------------------------------------------------------------------------------------------------
     * @param $event
     * @return array
     */
    public function copyDemoLiveImages($event): array;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is responsible for copying all demo live video in given event.
     * -----------------------------------------------------------------------------------------------------------------
     * @param $event
     * @return array
     */
    public function copyDemoLiveVideos($event): array;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is responsible for preparing all data which are required for creating an event as per
     * request data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @param $groupId
     * @return array
     */
    public function prepareEventV4Param($request, $groupId): array;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is used for get the event recurrence count
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @param $eventUuid
     * @return mixed
     */
    public function getEventCount($event, $eventUuid);

}
