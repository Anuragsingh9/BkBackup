<?php

namespace Modules\KctUser\Repositories;

interface IUserInvitesRepository {

    /**
     * @param $dataToInsert
     * @return mixed
     */
    public function insert($dataToInsert);

    /**
     * @param $invites
     * @return mixed
     */
    public function getInvitedEmailCount($invites);
}
