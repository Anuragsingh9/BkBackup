<?php

namespace Modules\KctAdmin\Services\BusinessServices;

use Modules\KctAdmin\Entities\Moment;

interface IZoomService {
    public function setEnvironment($e);
    public function setEnvironmentByKey($key);
    public function getTokenFromCode($code, $type, $groupKey = 'default');
    public function getUserByToken(string $token);
    public function storeToken($token, string $type, string $zoomAccId);
    public function syncUser($hosts): ?array;
    public function getPlan($accountId = null);
    public function getWebinarHosts(): array;
    public function getMeetingHosts(): array;
    public function getOAuthLoginUrl(?string $type = 'custom_zoom_settings'): string;
    public function fetchHost($technicalSetting, $settingKey);
    public function toggleSettings(?string $currentKey);
    public function createWebinar($param): array;
    public function createMeeting($param): array;
    public function getEmbeddedUrl(?Moment $moment): string;
    public function getZoomSettings();
    public function getMeeting(?string $meetingId): ?array;
}
