<?php

namespace Modules\UserManagement\Services\BusinessServices;

interface IEmailService{

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is responsible for sending password reset link on user's email
     * -----------------------------------------------------------------------------------------------------------------
     * @param $request
     * @return mixed
     */
    public function resetPassword($request);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton Sending welcome email to user
     * -----------------------------------------------------------------------------------------------------------------
     * @param $user
     * @param $groupId
     * @param $groupRole
     * @return mixed
     */
    public function sendWelcomeEmail($user,$groupId,$groupRole);
    }
