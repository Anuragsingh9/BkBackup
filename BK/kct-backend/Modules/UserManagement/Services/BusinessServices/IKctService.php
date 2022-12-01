<?php

namespace Modules\UserManagement\Services\BusinessServices;

interface IKctService {
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the oit side pages url according to type parameter
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param string $type // oit page name
     * @param array $data
     * @return string|null
     */
    public function prepareUrl(string $type, array $data = []): ?string;

    /**
     * @param $request
     * @return mixed
     */
    public function getSubDomainName($request);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To check if user needs to set his password on first login.
     * @info if user is super admin or account created by user then allow him to move to dashboard page without
     * setting password on set password page by updating login count in db
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $email
     * @return mixed
     */
    public function skipSetPasswordForAuth($email);

    }
