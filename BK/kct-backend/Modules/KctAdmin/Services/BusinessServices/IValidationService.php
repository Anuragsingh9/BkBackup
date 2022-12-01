<?php


namespace Modules\KctAdmin\Services\BusinessServices;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This interface will manage the validation related services
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IValidationService
 * @package Modules\KctAdmin\Services\BusinessServices
 */
interface IValidationService {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @dsecription To validate if the provided hosts can be space host or not
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $hosts
     * @param $space
     */
    public function validateSpaceHostUpdate($hosts, $space);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @dsecription To delete a space
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $spaceUuid
     * @return bool
     */
    public function isSpaceFuture($spaceUuid): bool;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check if the event is currently in provided state or not
     * sending
     * $past = 1 -> method will return true if event is past
     * multiple check allowed
     * $past = 1, and $live = 1 -> denotes event must be either past or live
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @param int $pastCheck
     * @param int $liveCheck
     * @param int $futureCheck
     * @return bool|null
     */
    public function eventTimeCheck($event, int $pastCheck = 0, int $liveCheck = 0, int $futureCheck = 0): ?bool;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @dsecription To get state of the event ex:- is_future,is_past,is_live
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @return array
     */
    public function getEventState($eventUuid): array;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To validate moments
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $moments
     * @return mixed
     */
    public function validateMoments($moments);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To find weather an event is moon space or multi space
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventUuid
     * @return array
     */
    public function isEventMonoType($eventUuid): array;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check if user is a pilot in any of the group
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $userId
     * @return bool
     */
    public function isGroupPilot($userId): bool;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will check if event's current live setting assets are same as demo assets by counting
     * and comparing filename of events live assets with the demo assets according to the given asset type and returns
     * bool value accordingly.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @param $type
     * @return bool
     */
    public function checkIsDefaultAsset($event, $type): bool;

}
