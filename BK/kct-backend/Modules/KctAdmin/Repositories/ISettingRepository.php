<?php


namespace Modules\KctAdmin\Repositories;


use Illuminate\Database\Eloquent\Collection;
use Modules\KctAdmin\Entities\GroupSetting;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will contain Account Level GroupSetting Management
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface ISettingRepository
 * @package Modules\KctAdmin\Repositories
 */
interface ISettingRepository {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To store the organisation basic details.
     * -----------------------------------------------------------------------------------------------------------------
     */
    public function storeDefaultSettings(): void;

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
     * @description To get account settings data  by setting key for a specific group
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $setting_key
     * @param int|null $groupId
     * @return GroupSetting|null
     */
    public function getSettingByKey(string $setting_key, int $groupId = 1): ?GroupSetting;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the multiple settings by keys array
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $groupId
     * @param array $keys
     * @return Collection
     */
    public function getSettingsByKey(int $groupId, array $keys): Collection;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To set the setting value for the specific setting key of specific group
     *
     * @note this will search setting key for specific group and update the following fields
     *      setting_value
     *      follow_main_organization
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param int $groupId
     * @param string $setting_key
     * @param array $setting_value
     * @param int $followMain
     * @return mixed
     */
    public function setSetting(int $groupId, string $setting_key, array $setting_value, int $followMain = 0);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update the follow_organisation column value in group setting table
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $value
     * @param $groupId
     * @return mixed
     */
    public function updateFollowOrganisation($value, $groupId);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Returns all the group settings of default group(id=1)
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return mixed
     */
    public function getDefaultGroupSettings();

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Return the value of follow organisation column value
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $groupId
     * @return mixed
     */
    public function getFollowOrganisationData($groupId);
}
