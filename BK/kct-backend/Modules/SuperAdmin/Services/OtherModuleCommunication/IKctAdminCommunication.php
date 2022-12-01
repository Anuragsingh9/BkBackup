<?php


namespace Modules\SuperAdmin\Services\OtherModuleCommunication;


use Modules\KctAdmin\Entities\Group;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will contain the kct admin communication
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IKctAdminCommunication
 * @package Modules\SuperAdmin\Services\OtherModuleCommunication
 */
interface IKctAdminCommunication {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To set the default account settings for the tenant account
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return mixed
     */
    public function createDefaultSettings(): void;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This will return the account related settings for current tenant set
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function getAccountSetting(): array;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the conference related settings data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    public function getConferenceSetting(): array;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update the account settings
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $data
     */
    public function updateAccountSetting($data): void;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update the account settings
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $data
     */
    public function updateConfSetting($data): void;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To create the group
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param array $data
     * @param $groupType
     * @return Group
     */
    public function createGroup(array $data,$groupType): Group;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To fetch the super group or default group
     * -----------------------------------------------------------------------------------------------------------------
     * @param $accountId
     * @return mixed
     */
    public function fetchSuperGroup($accountId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get group event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return mixed
     */
    public function getGroupEvent();

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to create a water fountain event
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return mixed
     */
    public function createWaterFountainEvent();

}
