<?php


namespace Modules\KctUser\Repositories\factory;


use Illuminate\Database\Eloquent\Collection;
use Modules\KctAdmin\Entities\GroupSetting;
use Modules\KctUser\Entities\WebhooksLog;
use Modules\KctUser\Repositories\ISettingRepository;
use Modules\KctUser\Traits\Services;

class SettingRepository implements ISettingRepository {

    use Services;

    /**
     * @inheritDoc
     */
    public function getSettingsByKey(array $keys,?int $groupId=1): Collection {
         $settings = GroupSetting::whereIn('setting_key', $keys)->whereGroupId($groupId)->get();
         if(in_array('group_logo', $keys    )) {
             $settings->map(function($setting) {
                 if($setting->setting_key == 'group_logo' && !$setting->setting_value['group_logo']) {
                     $defaultGroup = $this->userServices()->adminService->getDefaultGroup();
                     $defaultGroupLogoSetting = $this->getSettingByKey('group_logo', $defaultGroup->id);
                     $setting->setting_value = $defaultGroupLogoSetting->setting_value;
                 }
                 return $setting;
             });
         }
         return $settings;
    }

    /**
     * @inheritDoc
     */
    public function getSettingByKey(string $key, ?int $groupId=1): ?GroupSetting {
        return GroupSetting::where('setting_key', $key)->whereGroupId($groupId)->first();
    }

    /**
     * @inheritDoc
     */
    public function storeWebhooksLogs(array $data, string $type): WebhooksLog {
        return WebhooksLog::create([
            'webhook_type' => $type,
            'logs'         => $data,
        ]);
    }
}
