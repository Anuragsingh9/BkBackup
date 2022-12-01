<?php

namespace Modules\KctAdmin\Services\BusinessServices\factory;

use Carbon\Carbon;
use Exception;
use Modules\KctAdmin\Entities\GroupSetting;
use Modules\KctAdmin\Entities\Moment;
use Modules\KctAdmin\Services\BusinessServices\IZoomService;
use Modules\KctAdmin\Traits\KctHelper;

class ZoomMeetingContentService extends IZoomService {
    use KctHelper;

    public string $settingKey = 'zoom_meeting_settings';





    /**
     * @throws Exception
     */
    public function removeUserLicense($user) {
        $allUsers = $this->getAllUsers();
        if (isset($allUsers['users'])) {
            foreach ($allUsers['users'] as $zoomUser) {
                if ($zoomUser['email'] == $user->email) {
                    $this->updateUser($zoomUser['id'], ['type' => self::$zoomRole_basic]);
                }
            }
        }
    }

    /**
     * @throws Exception
     */
    public function addUserLicense($user): ?bool {
        $zUser = $this->firstOrCreateUser($user);
        if (!isset($zUser['id'])) {
            throw new Exception('Invalid User For Zoom');
        }
        if (isset($zUser['status']) && $zUser['status'] == 'active') {
            $result = $this->updateUser($zUser['id'], ['type' => self::$zoomRole_licensed]);
            if ($result['message'] ?? null) {
                throw new Exception($result['message']);
            }
            return true;
        }
        return null;
    }

    public function getEmbeddedUrl(?Moment $moment): string {
        return $this->getSignature($moment->moment_id);
    }
    /**
     * @throws Exception
     */
    public function getPlanDetails($accountNumber): array {
        $planDetails = $this->getPlan($accountNumber);
        return [
            // meeting number of license = meeting license - webinar license
            'number_of_licenses' => ($planDetails['plan_base']['hosts'] ?? 0) - ($planDetails['plan_webinar'][0]['hosts'] ?? 0),
            'plan_details'       => $planDetails['plan_base'] ?? [],
        ];
    }

    /**
     * @throws Exception
     */

    /**
     * @throws Exception
     */

}
