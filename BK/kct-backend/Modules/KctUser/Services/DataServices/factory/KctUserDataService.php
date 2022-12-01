<?php


namespace Modules\KctUser\Services\DataServices\factory;


use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\KctUser\Services\DataServices\IDataService;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will be managing the user service related management
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class IDataService
 * @package Modules\KctUser\Services\DataServices\factory
 */
class KctUserDataService implements IDataService {

    /**
     * @inheritDoc
     */
    public function prepareBanUserDetails($userId, $severity, $banReason) {
        $banDetails = [
            'user_id'    => $userId,
            'severity'   => isset($severity) ? $severity : 1,
            'ban_reason' => $banReason,
            'ban_type'   => 'event',
            'banned_by'  => Auth::user()->id,
        ];
        return $banDetails;
    }

    /**
     * @inheritDoc
     */
    public function prepareInviteUsers($request): array {
        $users = $request->input('user');
        $data = [];
        foreach ($users as $user) {
            if ($this->checkEmailAlreadyPresent($data, $user['email'])) {
                continue;
            }
            $data[] = $this->prepareUserForInvite($user, 0, $request->input('event_uuid'));
        }
        return $data;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To check if the email has already included or not
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $data
     * @param $email
     * @return bool
     */
    private function checkEmailAlreadyPresent($data, $email): bool {
        foreach ($data as $user) {
            if ($user['email'] == $email) {
                return true;
            }
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function prepareUserForInvite($user, $object = 0, $eventUuid): array {
        $data = [
            'invited_by_user_id' => Auth::user()->id,
            'event_uuid'         => $eventUuid,
            'created_at'         => Carbon::now()->toDateTimeString(),
            'updated_at'         => Carbon::now()->toDateTimeString(),
        ];
        if ($object) {
            $data['first_name'] = $user->fname;
            $data['last_name'] = $user->lname;
            $data['email'] = $user->email;
        } else {
            $data['first_name'] = $user['fname'];
            $data['last_name'] = $user['lname'];
            $data['email'] = $user['email'];
        }
        return $data;
    }
}
