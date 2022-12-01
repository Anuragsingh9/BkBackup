<?php

namespace Modules\KctAdmin\Services\BusinessServices;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will contain the email specific functionality0
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IDataService
 *
 * @package Modules\KctAdmin\Services\DataServices
 */
interface IEmailService {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will send the invitation plan emails
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @param $event
     * @return mixed
     */
    public function sendInvitationPlanEmails($request, $event);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will send the invitation email
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @param $data
     * @param $event
     * @return mixed
     */
    public function sendEventInviteEmail($request, $data, $event);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To send email to group related users.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @param $coPilots
     * @return mixed
     */
    public function sendGroupCreationEmail($request, $coPilots);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method use for send mail to pilot and created by pilot when group is modify
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     */
    public function sendGroupModificationEmail($request);
}
