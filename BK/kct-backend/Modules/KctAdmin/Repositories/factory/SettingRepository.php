<?php


namespace Modules\KctAdmin\Repositories\factory;


use Exception;
use Illuminate\Database\Eloquent\Collection;
use Modules\KctAdmin\Entities\GroupSetting;
use Modules\KctAdmin\Repositories\ISettingRepository;
use Modules\KctAdmin\Traits\KctHelper;
use Modules\KctAdmin\Traits\ServicesAndRepo;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will contain Account Level GroupSetting Management
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class SettingRepository
 * @package Modules\KctAdmin\Repositories\factory
 */
class SettingRepository implements ISettingRepository {
    use ServicesAndRepo;
    use KctHelper;

    /**
     * @inheritDoc
     */
    public function storeDefaultSettings(): void {
        $this->setDefaultAccountSetting();
        $this->setDefaultConfSetting();
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To set the default settings for the account related
     * -----------------------------------------------------------------------------------------------------------------
     */
    private function setDefaultAccountSetting() {
        GroupSetting::create([
            'setting_key'   => 'account_settings',
            'setting_value' => config('superadmin.constants.setting_keys.account_settings'),
        ]);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To set the default settings for the conference related
     * -----------------------------------------------------------------------------------------------------------------
     */
    private function setDefaultConfSetting() {
        $confSetting = config('superadmin.constants.setting_keys.conference_settings');
        // Bluejeans Default Settings
        $confSetting['bluejeans']['app_key'] = env('BJ_DEFAULT_APP_KEY');
        $confSetting['bluejeans']['app_secret'] = env('BJ_DEFAULT_APP_SECRET');
        $confSetting['bluejeans']['app_email'] = env('BJ_DEFAULT_APP_EMAIL');
        // Zoom Default Settings
        $confSetting['zoom']['app_key'] = env('ZM_DEFAULT_APP_KEY');
        $confSetting['zoom']['app_secret'] = env('ZM_DEFAULT_APP_SECRET');
        $confSetting['zoom']['app_email'] = env('ZM_DEFAULT_APP_EMAIL');

        GroupSetting::create([
            'setting_key'   => 'conference_settings',
            'setting_value' => $confSetting,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getAccountSetting(): array {
        $setting = GroupSetting::where('setting_key', 'account_settings')->first();
        if (!$setting) {
            return [];
        }
        return $setting->setting_value;
    }

    /**
     * @inheritDoc
     */
    public function getConferenceSetting(): array {
        $setting = GroupSetting::where('setting_key', 'conference_settings')->first();
        if (!$setting) {
            return [];
        }
        return $setting->setting_value;
    }

    /**
     * @inheritDoc
     */
    public function updateAccountSetting($data): void {
        GroupSetting::where('setting_key', 'account_settings')->update(['setting_value' => $data]);
    }

    /**
     * @inheritDoc
     */
    public function updateConfSetting($data): void {
        GroupSetting::where('setting_key', 'conference_settings')->update(['setting_value' => $data]);
    }

    /**
     * @inheritDoc
     */
    public function getSettingByKey($setting_key, $groupId = 1): ?GroupSetting {
        return GroupSetting::where('setting_key', $setting_key)
            ->where(function ($q) use ($groupId) {
                if ($groupId) {
                    $q->where('group_id', $groupId);
                }
            })->first();
    }

    /**
     * @inheritDoc
     */
    public function getSettingsByKey(int $groupId, array $keys): Collection {
        return GroupSetting::whereIn('setting_key', $keys)->whereGroupId($groupId)->get();
    }

    /**
     * @inheritDoc
     */
    public function setSetting(int $groupId, string $setting_key, array $setting_value, int $followMain = 0) {
        return GroupSetting::updateOrCreate([
            'group_id'    => $groupId,
            'setting_key' => $setting_key,
        ], [
            'setting_value'       => $setting_value,
            'follow_organisation' => $followMain,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function updateFollowOrganisation($value, $groupId) {
        return GroupSetting::whereGroupId($groupId)->update(['follow_organisation' => $value]);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getDefaultGroupSettings() {
        $group = $this->adminRepo()->groupRepository->getDefaultGroup();
        $group->load(['allSettings' => function ($q) {
            $q->whereIn('setting_key', $this->getGraphicKeys());
        }]);
        return $group->allSettings;
    }

    /**
     * @inheritDoc
     */
    public function getFollowOrganisationData($groupId) {
        $grpSettings = GroupSetting::whereGroupId($groupId)->first();
        return $grpSettings->follow_organisation;
    }
}
