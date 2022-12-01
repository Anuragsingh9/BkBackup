<?php


namespace Modules\SuperAdmin\Services\BusinessServices\factory;


use Illuminate\Mail\Message;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Modules\SuperAdmin\Entities\Organisation;
use Modules\SuperAdmin\Entities\SuperAdminUser;
use Modules\SuperAdmin\Services\BusinessServices\IEmailService;
use Modules\SuperAdmin\Traits\ServicesAndRepo;
use Modules\SuperAdmin\Traits\SuHelper as Helper;

/**
 * -----------------------------------------------------------------------------------------------------------------
 * @description This class will contain the email services
 * -----------------------------------------------------------------------------------------------------------------
 *
 * Class EmailService
 * @package Modules\SuperAdmin\Services\BusinessServices\factory
 */
class EmailService implements IEmailService {

    use ServicesAndRepo;
    use Helper;

    /**
     * @inheritDoc
     */
    public function sendSuOtp(string $otp, string $email) {
        $to_name = $email;
        $to_email = $email;
        $data = array_merge([
            'otp' => $otp,
        ], $this->getBasicData());
        Mail::send(
            'superadmin::email-templates.otp',
            $data,
            function ($message) use ($to_name, $to_email) {
                $message->to($to_email, $to_name)->subject('OTP for your HumannConnect Account');
                $message->from(env('MAIL_FROM_ADDRESS'), 'HumannConnect');
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function sendAccountReset(Organisation $organisation) {
        $data = array_merge([
            'organisation' => $organisation,
            'link'         => $this->suServices()->accountService->prepareUrlAccountSignin(
                $organisation->hostname->fqdn
            ),
        ], $this->getBasicData());
        view("superadmin::email-templates.account_reset", $data);
        Mail::send(
            "superadmin::email-templates.account_reset",
            $data,
            function (Message $message) use ($organisation) {
                $message->to($organisation->email, "$organisation->fname $organisation->lname")
                    ->subject(__("superadmin::messages.account_reset_subject"));
            });
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the basic data for the email template
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return array
     */
    private function getBasicData(): array {
        return [
            'headerLogo' => $this->suServices()->fileService
                ->getFileUrl(config('superadmin.constants.filePaths.emailHeaderLogo')),
            'footerLogo' => $this->suServices()->fileService
                ->getFileUrl(config('superadmin.constants.filePaths.emailFooterLogo')),
        ];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method use for send forget password email
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $link
     * @param $otpCode
     * @param $email
     */
    public function forgotPassword($link, $otpCode, $email) {
        $to_name = $email;
        $to_email = $email;
        $hashedOtp = Hash::make($otpCode); // hashed otp code
        $data = [
            'otp'  => $otpCode,
            'link' => "http://$link/v1/superadmin/reset-password/$email/$hashedOtp",
        ];
        Mail::send(
            'superadmin::email-templates.forgot_password',
            $data,
            function ($message) use ($to_name, $to_email) {
                $message->to($to_email, $to_name)->subject(__('superadmin::messages.reset_password'));
                $message->from(env('MAIL_FROM_ADDRESS'), 'Test Mail');
            }
        );
    }

}
