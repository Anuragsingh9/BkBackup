<?php


namespace Modules\KctUser\Repositories\factory;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\KctUser\Entities\EventUserInvites;
use Modules\KctUser\Repositories\IUserInvitesRepository;

class UserInvitesRepository implements IUserInvitesRepository {

    public function insert($dataToInsert) {
        EventUserInvites::insert($dataToInsert);
    }

    public function getInvitedEmailCount($invites) {
        return EventUserInvites::select('*', DB::raw('count(email) as invited_times'))
            ->whereIn('id', $invites->pluck('target_id'))
            ->groupBy('email')
            ->get();
    }

}
