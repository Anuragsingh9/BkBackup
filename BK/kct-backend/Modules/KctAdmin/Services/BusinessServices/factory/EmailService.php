<?php

namespace Modules\KctAdmin\Services\BusinessServices\factory;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Modules\KctAdmin\Jobs\GroupCreationForCopilots;
use Modules\KctAdmin\Jobs\InviteEventUsers;
use Modules\KctAdmin\Services\BusinessServices\IEmailService;
use Modules\KctAdmin\Traits\KctHelper;
use Modules\KctAdmin\Traits\ServicesAndRepo;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will contain the email related functionality
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class EmailService
 * @package Modules\KctAdmin\Services\BusinessServices\factory
 */
class EmailService implements IEmailService {

    use ServicesAndRepo, KctHelper;

    /**
     * @inheritDoc
     */
    public function sendInvitationPlanEmails($request, $event) {

        if (isset($event->draft)) {
            if ($request->event_status == 1 && ($event->draft->event_status != 1)) { // if event is published
                $this->sendEventPublished($event);
            }
            if ($request->is_reg_open == 1 && ($event->draft->is_reg_open != 1)) { // if event is published and registration for the event is opened.
                $this->sendEventRegOpened($event);
            }
            // if registration time updated
            if ($request->reg_time_updated) {
                $this->sendEventRegUpdated($event);
            }
        } else {
            if ($request->event_status == 1) {
                $this->sendEventPublished($event);
            }
            if ($request->is_reg_open == 1) {
                $this->sendEventRegOpened($event);
            }
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will send the event published mail
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     */
    public function sendEventPublished($event) {
        $user = Auth::user();
        $data = [
            'description' => __('kctadmin::messages.event_published_desc', ['event' => $event->title])
        ];
        Mail::send('kctadmin::email_templates.event_published', $data, function ($message) use ($user, $event) {
            $message->to($user->email, $user->email)->subject(__('kctadmin::messages.event_published',
                ['event' => $event->title]));
            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        });
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will send the event registration opening mail
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     */
    public function sendEventRegOpened($event) {
        $user = Auth::user();
        $data = [
            'description' => __('kctadmin::messages.event_reg_opened_desc', ['event' => $event->title])
        ];
        Mail::send('kctadmin::email_templates.event_published', $data, function ($message) use ($user, $event) {
            $message->to($user->email, $user->email)->subject(__('kctadmin::messages.event_reg_opened',
                ['event' => $event->title]));
            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        });
    }

    /**
     * @inheritDoc
     */
    public function sendEventInviteEmail($request, $data, $event) {
        if ($request->event_team) {
            $users = $this->adminServices()->userService->getUsersById($data['event_team']);
            $view = 'kctadmin::email_templates.event_published';
            $desc = [
                'description' => "Hii. You are invited in $event->title event as event team member."
            ];
            $subject = __('kctadmin::messages.event_invitation');
            InviteEventUsers::dispatch($users, $desc, $subject, $view);
        }
        if ($request->attendee) {
            $users = $this->adminServices()->userService->getUsersById($data['attendee_and_vip']);
            $view = 'kctadmin::email_templates.event_published';
            $desc = [
                'description' => "Hii. You are invited in $event->title event as event participants."
            ];
            $subject = __('kctadmin::messages.event_invitation');
            InviteEventUsers::dispatch($users, $desc, $subject, $view);
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will send the event registration update email
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     */
    public function sendEventRegUpdated($event) {
        $user = Auth::user();
        $data = [
            'description' => __('kctadmin::messages.event_reg_updated_desc', ['event' => $event->title])
        ];
        Mail::send('kctadmin::email_templates.event_published', $data, function ($message) use ($user, $event) {
            $message->to($user->email, $user->email)->subject(__('kctadmin::messages.event_reg_updated',
                ['event' => $event->title]));
            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        });
    }

    /**
     * @inheritDoc
     */
    public function sendGroupCreationEmail($request, $coPilots) {
        $users = [];
        //get users for sending email
        $pilot = $this->adminRepo()->groupRepository->getUser([$request['pilot'][0]]);
        $coPilot = $this->adminRepo()->groupRepository->getUser($coPilots);
        $createdByPilot = $this->adminRepo()->groupRepository->getUser([Auth::id()]);

        //get emails from users data
        $users['pilot'] = $pilot[0]['email'];
        $users['co-pilot'] = ($coPilot->pluck('email'))->toArray();
        $users['created-by-pilot'] = $createdByPilot[0]['email'];

        //send email for pilot and created by pilot
        $this->sendEmailToPilot($users['pilot'], $request);
        $this->sendEmailToCreatedByPilot($users['created-by-pilot'], $request);

        //send email for co-pilots
        $view = 'kctadmin::email_templates.group_creation_for_co_pilots';
        $desc = [
            'description' => "Hii. You are added in $request->group_name group as co-pilot."
        ];
        $subject = __('Welcome to ' . $request['group_name'] . ' group');
        GroupCreationForCopilots::dispatch($users['co-pilot'], $desc, $subject, $view);
    }

    /**
     * @inheritDoc
     */
    public function sendGroupModificationEmail($request) {
        $pilot = $this->adminRepo()->groupRepository->getUser([$request['pilot'][0]]);
        $createdByPilot = $this->adminRepo()->groupRepository->getUser([Auth::id()]);
        $data = [];
        //send email for pilot
        $pilotEmail = $pilot[0]['email'];
        Mail::send('kctadmin::email_templates.group_modification_for_pilot',
            $data, function ($message) use ($pilotEmail, $request) {
                $message->to($pilotEmail, $pilotEmail)->subject('Welcome to ' . $request['group_name'] . ' group');
                $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            }
        );
        // send email to created by pilot
        $createdByPilotEmail = $createdByPilot[0]['email'];
        Mail::send('kctadmin::email_templates.group_modification_for_added_new_pilot',
            $data, function ($message) use ($createdByPilotEmail, $request) {
                $message->to($createdByPilotEmail, $createdByPilotEmail)->subject('Welcome to ' . $request['group_name'] . ' group');
                $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            }
        );
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will be used to sending a mail to pilot of group.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $pilot
     * @param $request
     */
    public function sendEmailToPilot($pilot, $request) {
        $data = [];
        Mail::send('kctadmin::email_templates.group_creation_for_pilot', $data, function ($message) use ($pilot, $request) {
            $message->to($pilot, $pilot)->subject('Welcome to ' . $request['group_name'] . ' group');
            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        });
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method will be used to send mail who has created group
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $createdBy
     * @param $request
     */
    public function sendEmailToCreatedByPilot($createdBy, $request) {
        $data = [];
        Mail::send('kctadmin::email_templates.group_creation_success', $data, function ($message) use ($createdBy, $request) {
            $message->to($createdBy, $createdBy)->subject('kctadmin::messages.new_group');
            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        });
    }


}
