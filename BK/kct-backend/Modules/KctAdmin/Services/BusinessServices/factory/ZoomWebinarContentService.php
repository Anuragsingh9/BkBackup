<?php

namespace Modules\KctAdmin\Services\BusinessServices\factory;

use Carbon\Carbon;
use Exception;
use Modules\KctAdmin\Entities\GroupSetting;
use Modules\KctAdmin\Entities\Moment;
use Modules\KctAdmin\Services\BusinessServices\IZoomService;
use Modules\KctAdmin\Traits\KctHelper;
use Modules\KctAdmin\Traits\ServicesAndRepo;

class ZoomWebinarContentService implements IZoomService {
    use ServicesAndRepo;
    use KctHelper;

    public string $settingKey = 'zoom_webinar_settings';

    public function getOAuthLoginUrl(?string $type = 'custom_zoom_settings'): string {
        return "https://zoom.us/oauth/authorize?response_type=code&client_id="
            . env("ZM_DEFAULT_CLIENT_ID")
            . "&redirect_uri="
            . route('zoomHandler') . "?type=$type";
    }



    /**
     * @throws Exception
     */
    public function getPlanDetails($accountNumber): array {
        $planDetails = $this->getPlan($accountNumber);
        return [
            // as there can be multiple license for webinar so taking first only for now
            'number_of_licenses' => $planDetails['plan_webinar'][0]['hosts'] ?? 0,
            'plan_details'       => $planDetails['plan_webinar'][0] ?? [],
        ];
    }

    public function getSetting(): ?GroupSetting {
        if (!$this->setting) {
            $this->setting = $this->adminRepo()->settingRepository->getSettingByKey($this->settingKey);
        }
        return $this->setting;
    }

    /**
     * @throws Exception
     */
    public function removeUserLicense($user) {
        $allUsers = $this->getAllUsers();
        $data = [
            'feature' => [
                'webinar' => false,
            ]
        ];
        if (isset($allUsers['users'])) {
            foreach ($allUsers['users'] as $zoomUser) {
                if ($zoomUser['email'] == $user->email) {
                    $this->updateUser($zoomUser['id'], ['type' => self::$zoomRole_basic]);
                    $this->updateUserSettings($zoomUser['id'], $data);
                }
            }
        }
    }

    /**
     * @throws Exception
     */
    public function addUserLicense($user) {
        $zUser = $this->firstOrCreateUser($user);

        if (!isset($zUser['id'])) {
            throw new Exception('Invalid User For Zoom');
        }

        $data = [
            'feature' => [
                'webinar' => true,
            ]
        ];

        if (isset($zUser['status']) && $zUser['status'] == 'active') {
            if ($zUser['type'] == 1) { // user is basic try to make user licensed
                $this->updateUser($zUser['id'], ['type' => self::$zoomRole_licensed]);
            }
            $result = $this->updateUserSettings($zUser['id'], $data);
            if ($result['message'] ?? null) {
                throw new Exception($result['message']);
            }
            return true;
        }
        return null;
    }

    /**
     * @throws Exception
     */
    public function getEmbeddedUrl(?Moment $moment): string {
        return $this->getSignature($moment->moment_id);
    }


}
