<?php


namespace Modules\KctUser\Repositories;


use Illuminate\Database\Eloquent\Collection;
use Modules\KctAdmin\Entities\GroupSetting;
use Modules\KctUser\Entities\WebhooksLog;

interface ISettingRepository {
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the multiple settings by keys array
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param array $keys
     * @param int|null $groupId
     * @return Collection
     */
    public function getSettingsByKey(array $keys, ?int $groupId=1): Collection;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the single setting by key
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $key
     * @param int|null $groupId
     * @return GroupSetting|null
     */
    public function getSettingByKey(string $key, ?int $groupId=1): ?GroupSetting;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To store the webhooks log
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param array $data
     * @param string $type
     * @return WebhooksLog
     */
    public function storeWebhooksLogs(array $data, string $type): WebhooksLog;
}
