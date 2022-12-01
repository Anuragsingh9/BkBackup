<?php


namespace Modules\KctUser\Repositories\factory;


use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\KctUser\Entities\EventUser;
use Modules\KctUser\Entities\EventUserInvites;
use Modules\KctUser\Entities\Log;
use Modules\KctUser\Entities\LogUserData;
use Modules\KctUser\Entities\OtpCode;
use Modules\KctUser\Repositories\IUserRepository;
use Modules\UserManagement\Entities\EntityUser;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will contain user related Management
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class UserRepository
 * @package Modules\KctUser\Repositories\factory
 */
class UserRepository implements IUserRepository {

    /**
     * @inheritDoc
     */
    public function getUserById($id): ?User {
        return User::find($id);
    }

    public function getUserEvents($id){
        return EventUser::whereHas('event')->where('user_id', $id)->orderBy('created_at', 'DESC')->get();

    }
    /**
     * @inheritDoc
     */
    public function createUser(array $data): ?User {
        $data['fname'] = ucwords($data['fname']);
        $data['lname'] = ucwords($data['lname']);
        return User::create($data);
    }

    /**
     * @inheritDoc
     */
    public function createOtp(?User $user, $type = null) {
        $code = rand(100000, 999999);
        return OtpCode::updateOrCreate([
            'otp_type' => $type ?: OtpCode::$type_Email,
            'user_id'  => $user->id,
        ], [
            'code' => $code,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getOtp($userId, $type = null) {
        return OtpCode::where([
            'otp_type' => $type ?: OtpCode::$type_Email,
            'user_id'  => $userId,
        ])->first();
    }

    /**
     * @inheritDoc
     */
    public function getUserEventInvites($eventUuid) {
        return EventUserInvites::select('email', DB::raw('MAX(id) as target_id'))
            ->where(
                function ($q) use ($eventUuid) {
                    $q->where('event_uuid', $eventUuid);
                    $q->where('invited_by_user_id', Auth::user()->id);
                }
            )
            ->groupBy('email')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function getInvitedEmailCount($invites) {
        return EventUserInvites::whereIn('id', $invites->pluck('target_id'))
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function insertInvite($data) {
        EventUserInvites::insert($data);
    }

    /**
     * @inheritDoc
     */
    public function removeEntity($userId, $entityId) {
        return EntityUser::where('entity_id', $entityId)->where('user_id', $userId)->delete();
    }

    /**
     * @inheritDoc
     */
    public function storeLogs($request) {
        // Prepare data for log table
        $logData = [
            'current_browser' => $request->header('User-Agent'),
            'ip_address'      => $request->ip(),
            'log_type'        => $request->input('log_type')
        ];
        $log = Log::create($logData);

        // Prepare data for log user data
        $userData = [
            'current_selected_device' => $request->input('current_selected_device'),
            'available_devices'       => $request->input('available_devices')
        ];
        $logUserData = [
            'user_id'           => Auth::id(),
            'user_data'         => $userData,
            'event_uuid'        => $request->input('event_uuid'),
            'conversation_uuid' => $request->input('conversation_uuid')
        ];
        // create the log user data
        $log->logUserData()->create($logUserData);
    }

    /**
     * @inheritDoc
     */
    public function getUserByEmail($email){
        return User::whereEmail($email)->first();
    }
}
