<?php


namespace Modules\Cocktail\Services\Contracts;


use App\User;
use Modules\Events\Entities\Event;

interface EmailFactory {
    /**
     * @param $event
     * @param $userId
     * @param $tags
     * @return mixed
     */
    public function sendIntRegistration($event, $userId, $tags);
    
    /**
     * @param $event
     * @param $userId
     * @param $tags
     * @return mixed
     */
    public function sendIntModification($event, $userId, $tags);
    
    /**
     * @param $event
     * @param $userId
     * @param $data
     * @return mixed
     */
    public function sendVirtualRegistration($event, $userId, $data);
    
    /**
     * @param $event
     * @param $userId
     * @param $tags
     * @return mixed
     */
    public function sendVirtualModification($event, $userId, $tags);
    
    /**
     * @param $event
     * @param $user
     * @return mixed
     */
    public function sendModeratorInfo($event, $user);
    
    /**
     * @param $user
     * @param $request
     * @param null $eventUuid
     * @return mixed
     */
    public function sendOtp($user, $request=null, $eventUuid=null);
    
    /**
     * @param $email
     * @param $rootLink
     * @return mixed
     */
    public function sendForgetPassword($email, $rootLink);
    
    /**
     * @param $reminderKey
     * @param $tags
     * @param $users
     * @return mixed
     */
    public function sendReminderEmailToEvent($reminderKey, $tags, $users);
    
    /**
     * @param Event $event
     * @param User $user
     * @return mixed
     */
    public function sendInvitationEmail($event, $user);
    
    /**
     * @param Event $event
     * @param User $user // target email id
     * @return mixed
     */
    public function sendInviteToExistingUser($event, $user);
}
