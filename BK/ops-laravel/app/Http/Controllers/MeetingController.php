<?php

    namespace App\Http\Controllers;

    use Mail;
    use App\Events\MeetingEvent;
    use App\Http\Requests\MeetingStoreRequest;
    use App\Services\MeetingService;
    use Illuminate\Http\Request;
    use Hash;
    use App\Notification;
    use DB,
        Auth, App;
    use App\Meeting;
    use App\Presence;
    use App\User;
    use App\Guest;
    use App\DoodleDates,
        App\DoodleVote;
    use App\WorkshopMeta,
        App\Workshop;
    use App\Topic,
        App\TopicDocuments,

        App\TopicNote;
    use App\Task,
        App\Color,
        App\TaskUser;
    use App\RegularDocument;
    use App\Entity;
    use App\EntityUser;
    use App\Model\TopicAdminNote;
    use Modules\Resilience\Services\ResilienceService;
    use Validator;
    use Carbon\Carbon;

    class MeetingController extends Controller
    {

        private $core, $tenancy, $meetingService;

        public function __construct(PushNotificationController $PushNotification)
        {
            $this->PushNotification = $PushNotification;
            $this->core = app(\App\Http\Controllers\CoreController::class);
            $this->tenancy = app(\Hyn\Tenancy\Environment::class);
            $this->meetingService = MeetingService::getInstance();
        }

        public function addMeeting(MeetingStoreRequest $request)
        {
            try {
                \DB::connection('tenant')->beginTransaction();
                $checkMemberCommison = [];
                array_map('ucfirst', $request->all());
                if (($request->create_mail == 0) || ($request->create_mail == '')) {

                    $presenceData = $emails = $meetingEmails = $guest_email = $new_member_email = $newUsers = $workshopMetaData = $dVotes = $dVoteUserIds = [];

                    if ($request->meeting_date_type == 0) {
                        $request->merge(['date' => NULL, 'start_time' => NULL, 'end_time' => NULL]);
                    } else {
                        $request->merge(['start_time' => timeConvert($request->start_time), 'end_time' => timeConvert($request->end_time)]);
                    }
//            var_dump($request->except(['create_mail', 'meetingDates', 'invite_emails']));exit;
                    $lastMeetingId = Meeting::insertGetId($request->except(['create_mail', 'meetingDates', 'invite_emails']));
                    if ($lastMeetingId > 0) {
                        $meeting = Meeting::find($lastMeetingId);
                        $workshop = Workshop::find($request->workshop_id);
                        //adding meeting service methad

                        if (in_array($request->meeting_type, [2, 3]) && ($request->meeting_date_type == 1)) {
                            $checkRes1 = $this->meetingService->registerBlueJeansUser($request, $lastMeetingId);
                            $checkRes = $this->meetingService->createVideoMeeting($request, $lastMeetingId);
                            if (empty($checkRes) || empty($checkRes1)) {
                                throw new App\Exceptions\CustomValidationException(__('message.meeting_not_created'));
                                DB::connection('tenant')->rollback();
                            }
                        }
//external users insert and send emails
                        if ($request->invite_emails != NULL) {
                            $decode_emails = json_decode($request->invite_emails);
                            foreach ($decode_emails as $val) {
//$emails[]=$val->email;
                                $random_string = generateRandomString();
                                if (!is_int($val->id)) {
                                    if ($val->member_type == 'G' || $val->member_type == 'g') {
                                        $get_ids = User::where('email', strtolower($val->email))->pluck('id')->first();
                                        if (is_array($get_ids) && count($get_ids) > 0) {
                                            $guest = ['user_id' => $get_ids, 'meeting_id' => $lastMeetingId, 'workshop_id' => $request->workshop_id, 'url_type' => 'doodle', 'identifier' => $random_string];
                                            $user_Id = Guest::insertGetId($guest);
                                            $userId = $get_ids;
                                        } else {
                                            $newUsers = ['fname' => $val->fname, 'lname' => $val->lname, 'email' => strtolower($val->email), 'password' => Hash::make(strtolower($val->email)), 'role' => 'M3'];
                                            $userId = User::insertGetId($newUsers);
                                            $guest = ['user_id' => $userId, 'meeting_id' => $lastMeetingId, 'workshop_id' => $request->workshop_id, 'url_type' => 'doodle', 'identifier' => $random_string];
                                            $guestId = Guest::insertGetId($guest);
                                        }
                                        $workshopMetaData[] = ['workshop_id' => $request->workshop_id, 'meeting_id' => $lastMeetingId, 'user_id' => $userId, 'role' => '3'];
                                        $guest_email[] = strtolower($val->email);
                                        $token[strtolower($val->email)] = $random_string;
                                    } else {
                                        $get_ids = User::where('email', strtolower($val->email))->first();
                                        if (isset($get_ids->id)) {
                                            $result = User::where('email', strtolower($val->email))->update(['password' => Hash::make(strtolower($val->email)), 'role' => 'M2', 'login_count' => '0']);
                                            $userId = $get_ids->id;
                                        } else {
                                            $hostname = $this->getHostNameData();
                                            $hostCode = DB::connection('mysql')->table('hostname_codes')->where(['fqdn' => $hostname->fqdn])->first(['hash']);
                                            $randCode = generateRandomValue(4);
                                            $newCode = setPasscode($hostCode->hash, $randCode);

                                            $newUsers = ['fname' => $val->fname, 'lname' => $val->lname, 'email' => strtolower($val->email), 'password' => Hash::make(strtolower($val->email)), 'role' => 'M2', 'login_code' => $newCode['userCode'], 'hash_code' => $newCode['hashCode']];
                                            $userId = User::insertGetId($newUsers);

                                            if ($userId) {
                                                $user = User::find($userId);
                                                $entity = Entity::create(['long_name' => $user->society, 'short_name' => $user->society, 'address1' => $user->address, 'address2' => $user->fqdn, 'zip_code' => $user->postal, 'city' => $user->city, 'country' => $user->country, 'phone' => $user->phone, 'email' => $user->email, 'entity_type_id' => 2]);

                                                EntityUser::create(['user_id' => $user->id, 'entity_id' => $entity->id, 'entity_label' => '']);
                                            }
                                        }

                                        $new_member_email[] = strtolower($val->email);
                                        $new_workshop_user[] = strtolower($val->email);
                                        $memberType[] = $val->member_type;
                                        $workshopMetaData[] = ['workshop_id' => $request->workshop_id, 'meeting_id' => $lastMeetingId, 'user_id' => $userId, 'role' => '0'];
                                    }
//$params=['emails'=>$val->email,'workshop_id'=>$request->workshop_id,'meeting_id'=>$lastMeetingId,'user_id'=>$userId,'user_type'=>$val->member_type];
//$this->sendMeetingInvitationEmail($params);
                                } else {
                                    $checkMemberCommison = WorkshopMeta::where('user_id', $val->id)->where('workshop_id', $request->workshop_id)->first();
                                    if (empty($checkMemberCommison)) {
                                        $new_workshop_user[] = $val->email;
                                    }
                                    $workshopMetaData[] = ['workshop_id' => $request->workshop_id, 'meeting_id' => $lastMeetingId, 'user_id' => $val->id, 'role' => '0'];
                                }
                            }
                            if (!empty($workshopMetaData)) {
                                WorkshopMeta::insert($workshopMetaData);
                            }
                            if (!empty($new_member_email)) {
                                $params = ['emails' => $new_member_email, 'workshop_id' => $request->workshop_id, 'meeting_id' => $lastMeetingId, 'user_id' => 0, 'user_type' => 'm'];
                                $this->sendNewMemberInvitationEmail($params);
                            }
                            if (!empty($new_workshop_user)) {
                                $params = ['emails' => $new_workshop_user, 'workshop_id' => $request->workshop_id, 'meeting_id' => $lastMeetingId, 'user_id' => 0, 'user_type' => 'm'];
                                $this->sendNewWorkshopMemberInvitationEmail($params);
                            }
                            if (!empty($guest_email)) {

                                $params = ['emails' => $guest_email, 'workshop_id' => $request->workshop_id, 'meeting_id' => $lastMeetingId, 'user_id' => 0, 'user_type' => 'g', 'url_type' => 'doodle', 'token' => $token];
                                $this->sendGuestInvitationEmail($params);
                            }
                        }

                        $wikiUsers = WorkshopMeta::whereIn('role', [0, 1, 2])->where('workshop_id', $request->workshop_id)->groupBy('user_id', 'role')->orderBy(DB::raw("FIELD(role,'1','2','0','3')"))->get();
                        $guestUserMeeting = WorkshopMeta::where('workshop_id', $request->workshop_id)->where('meeting_id', $lastMeetingId)->where('role', 3)->groupBy('user_id', 'role')->get();

                        if (count($wikiUsers) > 0) {
                            foreach ($wikiUsers as $val) {
                                if ($val->role == 1) {
                                    $email_to = $val->user->email;
                                } else {
                                    $emails[] = $val->user->email;
                                }
                                if ($val->role == 1 || $val->role == 2) {
                                    $presenceData[] = [
                                        'workshop_id'           => $request->workshop_id,
                                        'meeting_id'            => $lastMeetingId,
                                        'user_id'               => $val->user->id,
                                        'register_status'       => ($val->role == 1 || $val->role == 2) ? 'I' : 'NI',
                                        'presence_status'       => ($val->role == 1 || $val->role == 2) ? 'P' : 'ANE',
                                        'with_meal_status'      => $this->addMealStatus($meeting, $request),
                                        'video_presence_status' => 'AE',
                                    ];
                                    $dVoteUserIds[] = ['user_id' => $val->user_id];
                                } else {
                                    $presenceData[] = [
                                        'workshop_id'           => $request->workshop_id,
                                        'meeting_id'            => $lastMeetingId,
                                        'user_id'               => $val->user->id,
                                        'register_status'       => ($val->role == 1 || $val->role == 2) ? 'I' : 'NI',
                                        'presence_status'       => ($val->role == 1 || $val->role == 2) ? 'P' : 'ANE',
                                        'with_meal_status'      => $this->addMealStatus($meeting, $request),
                                        'video_presence_status' => 'ANE',
                                    ];
                                }
                            }
                            if (count($guestUserMeeting) > 0) {
                                foreach ($guestUserMeeting as $vals) {
                                    $presenceData[] = [
                                        'workshop_id'           => $request->workshop_id,
                                        'meeting_id'            => $lastMeetingId,
                                        'user_id'               => $vals->user->id,
                                        'register_status'       => 'NI',
                                        'presence_status'       => 'ANE',
                                        'with_meal_status'      => $this->addMealStatus($meeting, $request),
                                        'video_presence_status' => ($val->role == 1 || $val->role == 2) ? 'AE' : 'ANE',
                                    ];
                                }
                            }
// insert presense data
                            if (!empty($presenceData)) {
                                Presence::insert($presenceData);
                            }

//start reuse topics code
                            $preMeetingId = DB::connection('tenant')->select('SELECT `meeting_id` FROM `topics` WHERE workshop_id = ' . $request->workshop_id . ' ORDER BY `meeting_id` DESC LIMIT 1');
                            if ($preMeetingId) {
                                $topicIds = Topic::where('workshop_id', $request->workshop_id)->where('meeting_id', $preMeetingId[0]->meeting_id)->where('reuse', 1)->whereNull('parent_id')->pluck('id');
                                $series = 1;
                                foreach ($topicIds as $val) {
                                    DB::connection('tenant')->statement('CALL copyTopic(' . $val . ',' . $lastMeetingId . ',' . $preMeetingId[0]->meeting_id . ',' . $series . ')');
                                    $series++;
                                }
                            }
//end reuse topics code
                        }
//            $guest = Guest::where('workshop_id', $request->workshop_id)->where('meeting_id', $lastMeetingId)->get();
//            foreach ($guest as $key => $value) {
//                $dVoteUserIds[] = ['user_id' => $value->user_id];
//            }

                        if ($request->meeting_date_type == 0) {
                            if ($request->meetingDates != NULL) {
                                $getLastIds = [];
                                $userCount = count($dVoteUserIds);
                                $jsonDecode = json_decode($request->meetingDates);
                                foreach ($jsonDecode as $val) {
                                    $mDates = ['meeting_id' => $lastMeetingId, 'date' => dateConvert($val->date), 'start_time' => timeConvert($val->s_time), 'end_time' => timeConvert($val->e_time)];
                                    $lastId = DoodleDates::insertGetId($mDates);
                                    if ($userCount > 0) {
                                        for ($i = 0; $i < $userCount; $i++) {
                                            $dVotes[] = ['user_id' => $dVoteUserIds[$i]['user_id'], 'meeting_id' => $lastMeetingId, 'doodle_id' => $lastId, 'available' => 1, 'updated_id' => NULL];
                                        }
                                    }
                                }
                                if (!empty($dVotes) && $userCount > 0) {
                                    DoodleVote::insert($dVotes);
                                }
                            }

                            if (!empty($emails)) {


                                $params = ['emails' => $emails, 'email_to' => ($email_to) ?? '', 'workshop_id' => $request->workshop_id, 'meeting_id' => $lastMeetingId, 'user_id' => 0, 'user_type' => 'm'];
                                // return response($this->sendMeetingInvitationEmail($params));
                                $this->sendMeetingInvitationEmail($params);
                            }

                            //for notification
                            $workshopUser = WorkshopMeta::with('workshop')->where('workshop_id', $request->workshop_id)->where('role', '!=', 3)->get();
                            $heading = 'Envoi de Doodle';
                            $msg = $this->getPushData($request->workshop_id, $lastMeetingId, 'doodle_push_setting', '', ['meeting' => $meeting, 'workshop' => $workshop]);
                            $msgEN = $this->getPushData($request->workshop_id, $lastMeetingId, 'doodle_push_setting', 'EN', ['meeting' => $meeting, 'workshop' => $workshop]);
                            $type = 'meetingDoodle';
                            $this->sendNoti($workshopUser, $lastMeetingId, $msg, $msgEN, $type);

                        }

                        if ($request->meeting_date_type == 1) {
                            $workshopUser = WorkshopMeta::with('workshop')->where('workshop_id', $request->workshop_id)->where('role', '!=', 3)->get();
                            $heading = 'Save the date';
                            $msg = $this->getPushData($request->workshop_id, $lastMeetingId, 'save_metting_push_setting', '', ['meeting' => $meeting, 'workshop' => $workshop]);
                            $msgEN = $this->getPushData($request->workshop_id, $lastMeetingId, 'save_metting_push_setting', 'EN', ['meeting' => $meeting, 'workshop' => $workshop]);
                            $type = 'meetingDoodle';
                            $this->sendNoti($workshopUser, $lastMeetingId, $msg, $msgEN, $type);
                        }


                        DB::connection('tenant')->commit();
                    }

                    return response()->json($lastMeetingId);
                } else {

                    $presenceData = $emails = $meetingEmails = $guest_email = $new_member_email = $newUsers = $workshopMetaData = $dVotes = $dVoteUserIds = [];

                    if ($request->meeting_date_type == 0) {
                        $request->merge(['date' => NULL, 'start_time' => NULL, 'end_time' => NULL]);
                    } else {
                        $request->merge(['start_time' => timeConvert($request->start_time), 'end_time' => timeConvert($request->end_time)]);
                    }


                    $lastMeetingId = Meeting::insertGetId($request->except(['create_mail', 'meetingDates', 'invite_emails']));
                    if ($lastMeetingId > 0) {
                        $meeting = Meeting::find($lastMeetingId);
                        $workshop = Workshop::find($request->workshop_id);
                        //adding meeting service methad
                        if (in_array($request->meeting_type, [2, 3]) && ($request->meeting_date_type == 1)) {
                            $checkRes1 = $this->meetingService->registerBlueJeansUser($request, $lastMeetingId);
                            $checkRes = $this->meetingService->createVideoMeeting($request, $lastMeetingId);
                            if (empty($checkRes) || empty($checkRes1)) {
                                throw new App\Exceptions\CustomValidationException(__('message.meeting_not_created'));
                                DB::connection('tenant')->rollback();
                            }

                        }
//external users insert and send emails
                        if ($request->invite_emails != NULL) {
                            $decode_emails = json_decode($request->invite_emails);
                            foreach ($decode_emails as $val) {
//$emails[]=$val->email;
                                $random_string = generateRandomString();
                                if (!is_int($val->id)) {
                                    if ($val->member_type == 'G' || $val->member_type == 'g') {
                                        $get_ids = User::where('email', $val->email)->pluck('id')->first();
                                        if ($get_ids != NULL && count($get_ids) > 0) {
                                            $guest = ['user_id' => $get_ids, 'meeting_id' => $lastMeetingId, 'workshop_id' => $request->workshop_id, 'url_type' => 'doodle', 'identifier' => $random_string];
                                            $user_Id = Guest::insertGetId($guest);
                                            $userId = $get_ids;
                                        } else {
                                            $newUsers = ['fname' => $val->fname, 'lname' => $val->lname, 'email' => strtolower($val->email), 'password' => Hash::make(strtolower($val->email)), 'role' => 'M3'];
                                            $userId = User::insertGetId($newUsers);
                                            $guest = ['user_id' => $userId, 'meeting_id' => $lastMeetingId, 'workshop_id' => $request->workshop_id, 'url_type' => 'doodle', 'identifier' => $random_string];
                                            $guestId = Guest::insertGetId($guest);
                                        }
                                        $workshopMetaData[] = ['workshop_id' => $request->workshop_id, 'meeting_id' => $lastMeetingId, 'user_id' => $userId, 'role' => '3'];
                                        $guest_email[] = $val->email;
                                        $token[strtolower($val->email)] = $random_string;
                                    } else {
                                        $get_ids = User::where('email', strtolower($val->email))->first();
                                        if (isset($get_ids->id)) {
                                            $result = User::where('email', strtolower($val->email))->update(['role' => 'M2']);
                                            $userId = $get_ids->id;
                                        } else {
                                            $hostname = $this->getHostNameData();
                                            $hostCode = DB::connection('mysql')->table('hostname_codes')->where(['fqdn' => $hostname->fqdn])->first(['hash']);
                                            $randCode = generateRandomValue(4);
                                            $newCode = setPasscode($hostCode->hash, $randCode);

                                            $newUsers = ['fname' => $val->fname, 'lname' => $val->lname, 'email' => strtolower($val->email), 'password' => Hash::make(strtolower($val->email)), 'role' => 'M2', 'login_code' => $newCode['userCode'], 'hash_code' => $newCode['hashCode']];
                                            $userId = User::insertGetId($newUsers);

                                            if ($userId) {
                                                $user = User::find($userId);
                                                $entity = Entity::create(['long_name' => $user->society, 'short_name' => $user->society, 'address1' => $user->address, 'address2' => $user->fqdn, 'zip_code' => $user->postal, 'city' => $user->city, 'country' => $user->country, 'phone' => $user->phone, 'email' => $user->email, 'entity_type_id' => 2]);

                                                EntityUser::create(['user_id' => $user->id, 'entity_id' => $entity->id, 'entity_label' => '']);
                                            }
                                        }

                                        $new_member_email[] = $val->email;
                                        $memberType[] = $val->member_type;
                                        $workshopMetaData[] = ['workshop_id' => $request->workshop_id, 'meeting_id' => $lastMeetingId, 'user_id' => $userId, 'role' => '0'];
                                    }
//$params=['emails'=>$val->email,'workshop_id'=>$request->workshop_id,'meeting_id'=>$lastMeetingId,'user_id'=>$userId,'user_type'=>$val->member_type];
//$this->sendMeetingInvitationEmail($params);
                                } else {
                                    $role_check = WorkshopMeta::whereIn('role', [1, 2])->where('workshop_id', $request->workshop_id)->pluck('user_id')->toArray();
                                    $workshopMetaData[] = ['workshop_id' => $request->workshop_id, 'meeting_id' => $lastMeetingId, 'user_id' => $val->id, 'role' => '0'];
                                }
                            }
                            if (!empty($workshopMetaData)) {
                                WorkshopMeta::insert($workshopMetaData);
                            }
                            if (!empty($new_member_email)) {
                                $params = ['emails' => $new_member_email, 'workshop_id' => $request->workshop_id, 'meeting_id' => $lastMeetingId, 'user_id' => 0, 'user_type' => 'm'];
                                $this->sendNewMemberInvitationEmail($params);
                            }
                            if (!empty($guest_email)) {

                                $params = ['emails' => $guest_email, 'workshop_id' => $request->workshop_id, 'meeting_id' => $lastMeetingId, 'user_id' => 0, 'user_type' => 'g', 'url_type' => 'doodle', 'token' => $token];
                                $this->sendGuestInvitationEmail($params);
                            }
                        }
                        $wikiUsers = WorkshopMeta::whereIn('role', [0, 1, 2])->where('workshop_id', $request->workshop_id)->groupBy('user_id', 'role')->orderBy(DB::raw("FIELD(role,'1','2','0','3')"))->get();
//            return response($wikiUsers);
//            die;
                        $guestUserMeeting = WorkshopMeta::where('workshop_id', $request->workshop_id)->where('meeting_id', $lastMeetingId)->where('role', 3)->groupBy('user_id', 'role')->get();
                        if (count($wikiUsers) > 0) {
                            foreach ($wikiUsers as $val) {
                                $emails[] = $val->user->email;
                                if ($val->role == 1 || $val->role == 2) {
                                    $presenceData[] = [
                                        'workshop_id'           => $request->workshop_id,
                                        'meeting_id'            => $lastMeetingId,
                                        'user_id'               => $val->user->id,
                                        'register_status'       => ($val->role == 1 || $val->role == 2) ? 'I' : 'NI',
                                        'presence_status'       => ($val->role == 1 || $val->role == 2) ? 'P' : 'ANE',
                                        'with_meal_status'      => $this->addMealStatus($meeting, $request),
                                        'video_presence_status' => 'AE',
                                    ];
                                    $dVoteUserIds[] = ['user_id' => $val->user_id];
                                } else {
                                    $presenceData[] = [
                                        'workshop_id'           => $request->workshop_id,
                                        'meeting_id'            => $lastMeetingId,
                                        'user_id'               => $val->user->id,
                                        'register_status'       => ($val->role == 1 || $val->role == 2) ? 'I' : 'NI',
                                        'presence_status'       => ($val->role == 1 || $val->role == 2) ? 'P' : 'ANE',
                                        'with_meal_status'      => $this->addMealStatus($meeting, $request),
                                        'video_presence_status' => 'ANE',
                                    ];

                                }
                            }
                            if (count($guestUserMeeting) > 0) {
                                foreach ($guestUserMeeting as $vals) {
                                    $presenceData[] = [
                                        'workshop_id'           => $request->workshop_id,
                                        'meeting_id'            => $lastMeetingId,
                                        'user_id'               => $vals->user->id,
                                        'register_status'       => 'NI',
                                        'presence_status'       => 'ANE',
                                        'with_meal_status'      => $this->addMealStatus($meeting, $request),
                                        'video_presence_status' => ($val->role == 1 || $val->role == 2) ? 'AE' : 'ANE',
                                    ];
                                }
                            }
// insert presense data
                            if (!empty($presenceData)) {
                                Presence::insert($presenceData);
                            }

//start reuse topics code
                            $preMeetingId = DB::connection('tenant')->select('SELECT `meeting_id` FROM `topics` WHERE workshop_id = ' . $request->workshop_id . ' ORDER BY `meeting_id` DESC LIMIT 1');
                            if ($preMeetingId) {
                                $topicIds = Topic::where('workshop_id', $request->workshop_id)->where('meeting_id', $preMeetingId[0]->meeting_id)->where('reuse', 1)->whereNull('parent_id')->pluck('id');
                                $series = 1;
                                foreach ($topicIds as $val) {
                                    DB::connection('tenant')->statement('CALL copyTopic(' . $val . ',' . $lastMeetingId . ',' . $preMeetingId[0]->meeting_id . ',' . $series . ')');
                                    $series++;
                                }
                            }
//end reuse topics code
                        }

                        if ($request->meeting_date_type == 0) {
                            if ($request->meetingDates != NULL) {
                                $getLastIds = [];
                                $userCount = count($dVoteUserIds);
                                $jsonDecode = json_decode($request->meetingDates);
                                foreach ($jsonDecode as $val) {
                                    $mDates = ['meeting_id' => $lastMeetingId, 'date' => dateConvert($val->date), 'start_time' => timeConvert($val->s_time), 'end_time' => timeConvert($val->e_time)];
                                    $lastId = DoodleDates::insertGetId($mDates);
                                    if ($userCount > 0) {
                                        for ($i = 0; $i < $userCount; $i++) {
                                            $dVotes[] = ['user_id' => $dVoteUserIds[$i]['user_id'], 'meeting_id' => $lastMeetingId, 'doodle_id' => $lastId, 'available' => 1, 'updated_id' => NULL];
                                        }
                                    }
                                }
                                if (!empty($dVotes) && $userCount > 0) {
                                    DoodleVote::insert($dVotes);
                                }
                            }

                            if (!empty($emails)) {
                                $params = ['emails' => $emails, 'workshop_id' => $request->workshop_id, 'meeting_id' => $lastMeetingId, 'user_id' => 0, 'user_type' => 'm'];
                                $this->sendMeetingInvitationEmail($params);
                            }
                            $workshopUser = WorkshopMeta::with('workshop')->where('workshop_id', $request->workshop_id)->where('role', '!=', 3)->get();
                            $heading = 'Envoi de Doodle';
                            $msg = $this->getPushData($request->workshop_id, $lastMeetingId, 'doodle_push_setting', '', ['meeting' => $meeting, 'workshop' => $workshop]);
                            $msgEN = $this->getPushData($request->workshop_id, $lastMeetingId, 'doodle_push_setting', 'EN', ['meeting' => $meeting, 'workshop' => $workshop]);

                            $type = 'meetingDoodle';
                            $this->sendNoti($workshopUser, $lastMeetingId, $msg, $msgEN, $type);
                        }

                        if ($request->meeting_date_type == 1) {
                            $workshopUser = WorkshopMeta::with('workshop')->where('workshop_id', $request->workshop_id)->where('role', '!=', 3)->get();
                            $heading = 'Save the date';
                            $msg = $this->getPushData($request->workshop_id, $lastMeetingId, 'save_metting_push_setting', '', ['meeting' => $meeting, 'workshop' => $workshop]);
                            $msgEN = $this->getPushData($request->workshop_id, $lastMeetingId, 'save_metting_push_setting', 'EN', ['meeting' => $meeting, 'workshop' => $workshop]);

                            $type = 'saveDate';
                            $this->sendNoti($workshopUser, $lastMeetingId, $msg, $msgEN, $type);
                        }
                        $meeting_email = WorkshopMeta::whereIn('role', [0, 1, 2])->where('workshop_id', $request->workshop_id)->get();
                        if (count($meeting_email) > 0) {
                            foreach ($meeting_email as $val) {
                                $meetingEmails[] = $val->user->email;
                            }
                        }
                        if (!empty($meetingEmails)) {
                            $params = ['emails' => $meetingEmails, 'workshop_id' => $request->workshop_id, 'meeting_id' => $lastMeetingId, 'user_id' => 0, 'user_type' => 'm', 'type' => 'save_meeting'];
                            $this->sendNewMeetingInvitation($params);
                        }


                        DB::connection('tenant')->commit();
                    }
                    /*//for notification
                    $workshopUser = WorkshopMeta::with('workshop')->where('workshop_id', $request->workshop_id)->where('role', '!=', 3)->get();
                    $heading = 'Save the date';
                    $msg = $this->getPushData($request->workshop_id, $lastMeetingId, 'save_metting_push_setting');
                    $msgEN = $this->getPushData($request->workshop_id, $lastMeetingId, 'save_metting_push_setting', 'EN');
                    $type = 'saveDate';
                    $this->sendNoti($workshopUser, $lastMeetingId, $msg, $msgEN, $type);*/
                    DB::connection('tenant')->commit();
                    return response()->json($lastMeetingId);
                }
            } catch (App\Exceptions\CustomValidationException $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 422);

            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
//                   dd($e);
                return response()->json(['status' => FALSE, 'msg' => 'Internal server error ' . $e->getMessage()], 500);
            }
        }


        function getPushData($workshop_id, $meeting_id, $key, $lang = '', $parm = [])
        {
            if (!empty($parm)) {
                $meeting_data = $parm['meeting'];
                $workshop_data = $parm['workshop'];
            } else {
                $workshop_data = Workshop::find($workshop_id);
                $meeting_data = Meeting::find($meeting_id);
            }

            $currUserFname = Auth::user()->fname;
            $currUserLname = Auth::user()->lname;
            $currUserEmail = Auth::user()->email;
            if (empty($lang)) {
                $settings = getSettingData($key);
            } else {
                $settings = getSettingData($key, 0, $lang);
            }

            $member = workshopValidatorPresident($workshop_data);
            $orgDetail = getOrgDetail();
            $keywords = [
                '[[UserFirstName]]',
                '[[UserLastName]]',
                '[[UserEmail]]',
                '[[WorkshopLongName]]',
                '[[WorkshopShortName]]',
                '[[WorkshopPresidentFullName]]',
                '[[WorkshopvalidatorFullName]]',
                '[[WorkshopMeetingName]]',
                '[[WorkshopMeetingDate]]',
                '[[WorkshopMeetingTime]]',
                '[[WorkshopMeetingAddress]]',
                '[[ValidatorEmail]]',
                '[[PresidentEmail]]',
                '[[PresidentPhone]]',
                '[[OrgName]]',
                '[[OrgShortName]]',
            ];
            $values = [
                $currUserFname,
                $currUserLname,
                $currUserEmail,
                $workshop_data->workshop_name,
                $workshop_data->code1,
                $member['p']['fullname'],
                $member['v']['fullname'],
                $meeting_data->name,
                dateConvert($meeting_data->date, 'l d/m/Y', $lang),
                timeConvert($meeting_data->date . ' ' . $meeting_data->start_time, ' h\hi'),
                $meeting_data->place,
                $member['v']['email'],
                $member['p']['email'],
                $member['p']['phone'],
                $orgDetail->name_org,
                $orgDetail->acronym,
            ];

            $msg = str_replace($keywords, $values, preg_replace('/\s\s+/', ' ', $settings->notification_text));

            //   $msg = ((str_replace($keywords, $values, 'Notification Text')));

            return ['msg' => strip_tags(html_entity_decode($msg, ENT_QUOTES)), 'title' => $settings->title];
            // return ['msg' => strip_tags(html_entity_decode($msg)), 'title' => 'OPSimplify'];
        }

        public function editMeeting(Request $request)
        {
            \DB::connection('tenant')->beginTransaction();
            $meeting_data = Meeting::find($request->meeting_id);

            if (($request->create_mail == 0) || ($request->create_mail == '')) {
                $mDates = [];
                if ($request->meeting_date_type == 0) {
                    $request->merge(['date' => NULL, 'start_time' => NULL, 'end_time' => NULL]);
                } else {
                    $request->merge(['start_time' => timeConvert($request->start_time), 'end_time' => timeConvert($request->end_time)]);
                }

                $updateRes = Meeting::where('id', $request->meeting_id)->update($request->except(['create_mail', 'meetingDates', 'remove_ids', 'meeting_id']));

                // as we are updating meeting meal so its getting overriding
                if (isset($meeting_data->meeting_type) && ($meeting_data->meeting_type != $request->meeting_type)) {
                    $resilience = $this->meetingService->checkMeetingInResilience($request->meeting_id);
                    if ($resilience) {
                        return response()->json(['status' => FALSE, 'msg' => __('message.resilience_meeting_exists')], 422);
                    }
                    $this->meetingService->changeMeetingType($request, $meeting_data);
                }
                if ($updateRes > 0) {
                    if ($request->meeting_date_type == 0) {
                        $removeDoodleIds = json_decode($request->remove_ids);
                        if (count($removeDoodleIds) > 0) {
                            DoodleDates::whereIn('id', $removeDoodleIds)->delete();
                        }

                        if ($request->meetingDates != NULL) {
                            $jsonDecode = json_decode($request->meetingDates);
                            foreach ($jsonDecode as $val) {
                                if ($val->id == 0)
                                    $mDates[] = ['meeting_id' => $request->meeting_id, 'date' => dateConvert($val->date), 'start_time' => timeConvert($val->s_time), 'end_time' => timeConvert($val->e_time)];
                            }
                            DoodleDates::insert($mDates);
                            // after adding new doodles , to mark the sec/dep as voted for doodle dates
                            $this->meetingService->markAdminVotesForDoodles($meeting_data);
                        }
                        $wikiUsers = WorkshopMeta::with('user')->where('workshop_id', $request->workshop_id)->where('role', '!=', '3')->get();
                        if (count($wikiUsers) > 0) {
                            $emails = [];
                            foreach ($wikiUsers as $val) {
                                $emails[] = $val->user->email;
                            }
                            $params = ['emails' => $emails, 'workshop_id' => $request->workshop_id, 'meeting_id' => $request->meeting_id, 'user_id' => 0, 'user_type' => 'm'];
                            $this->sendMeetingInvitationEmail($params);
                        }
                        $wikiGuest = WorkshopMeta::with('user')->where(['workshop_id' => $request->workshop_id, 'meeting_id' => $request->meeting_id])->where('role', '=', '3')->get();
                        if (count($wikiGuest) > 0) {
                            foreach ($wikiGuest as $val) {
                                $random_string = generateRandomString();
                                if ($val->user->role == 'M3') {
                                    $guestEmail[] = $val->user->email;
                                    $guest = Guest::updateOrCreate(
                                        ['user_id' => $val->user->id, 'meeting_id' => $request->meeting_id, 'workshop_id' => $request->workshop_id],
                                        ['url_type' => 'doodle', 'identifier' => $random_string]
                                    );
                                    $guest->url_type = 'doodle';
                                    $guest->identifier = $random_string;
                                    $guest->save();

                                    $token[strtolower($val->user->email)] = $random_string;
                                } else {
                                    $meetingEmails[] = $val->user->email;
                                }
                            }
                        }
                        if (!empty($guestEmail)) {
                            $params = ['emails' => $guestEmail, 'workshop_id' => $request->workshop_id, 'meeting_id' => $request->meeting_id, 'user_id' => 0, 'user_type' => 'g', 'url_type' => 'modify_meeting', 'token' => $token];
                            $this->sendGuestInvitationEmail($params);
                        }
                        $workshopUser = WorkshopMeta::with('workshop')->where('workshop_id', $request->workshop_id)->where('role', '!=', 3)->get();

                        $heading = 'Envoi de Doodle';
                        $msg = $this->getPushData($request->workshop_id, $request->meeting_id, 'doodle_push_setting');
                        $msgEN = $this->getPushData($request->workshop_id, $request->meeting_id, 'doodle_push_setting', 'EN');
                        $type = 'meetingDoodle';
                        $this->sendNoti($workshopUser, $request->meeting_id, $msg, $msgEN, $type);
                    } else {
                        
                        // adding the bj (external meeting update here) because the meeting update takes time
                        // and if we send notification first and wait to update bj meeting the mobile users will fetch
                        // unread count before the new notification data is commit
                        
                        //adding meeting service function for video/hybrid type
                        if (in_array($request->meeting_type, [2, 3]) && ($request->meeting_date_type == 1)) {
                            $checkRes = $this->meetingService->updateVideoMeeting($request, $request->meeting_id);
                            if (empty($checkRes)) {
                                throw new App\Exceptions\CustomValidationException(__('message.meeting_not_updated'));
                                DB::connection('tenant')->rollback();
                            }
                        }
                        
                        DoodleDates::where('meeting_id', $request->meeting_id)->delete();
                        //for notification
                        $workshopUser = WorkshopMeta::with('workshop')->where('workshop_id', $request->workshop_id)->where('role', '!=', 3)->get();
                        $heading = 'Date modifiée';
                        $msg = $this->getPushData($request->workshop_id, $request->meeting_id, 'save_modify_metting_push_setting');
                        $msgEN = $this->getPushData($request->workshop_id, $request->meeting_id, 'save_modify_metting_push_setting', 'EN');
                        $type = 'editMeeting';
                        $this->sendNoti($workshopUser, $request->meeting_id, $msg, $msgEN, $type);
                    }

                    DB::connection('tenant')->commit();
                } else {
                    DB::connection('tenant')->rollback();
                }
                return response()->json($request->meeting_id);
            } else {

                $mDates = [];
                if ($request->meeting_date_type == 0) {
                    $request['date'] = NULL;
                }
                $request->merge(['start_time' => timeConvert($request->start_time), 'end_time' => timeConvert($request->end_time)]);
                $updateRes = Meeting::where('id', $request->meeting_id)->update($request->except(['create_mail', 'meetingDates', 'remove_ids', 'meeting_id']));
                // as we are updating meeting meal so its getting overriding
                if (isset($meeting_data->meeting_type) && ($meeting_data->meeting_type != $request->meeting_type)) {
                    $resilience = $this->meetingService->checkMeetingInResilience($request->meeting_id);
                    if ($resilience) {
                        return response()->json(['status' => FALSE, 'msg' => __('message.resilience_meeting_exists')], 422);
                    }
                    $this->meetingService->changeMeetingType($request, $meeting_data);
                }
                if ($updateRes > 0) {

                    $meeting_email = WorkshopMeta::whereIn('role', [0, 1, 2])->where('workshop_id', $request->workshop_id)->get();

                    if (count($meeting_email) > 0) {
                        foreach ($meeting_email as $val) {
                            if ($val->user->role == 'M3') {
                                $guestEmail[] = $val->user->email;
                            } else {
                                $meetingEmails[] = $val->user->email;
                            }
                        }

                    }

                    //getting guest user
                    $meetingGuestUsers = WorkshopMeta::where('role', 3)->where('workshop_id', $request->workshop_id)->where('meeting_id', $request->meeting_id)->get();

                    if (count($meetingGuestUsers) > 0) {
                        foreach ($meetingGuestUsers as $val) {
                            $random_string = generateRandomString();
                            if ($val->user->role == 'M3') {
                                $guestEmail[] = $val->user->email;
                                $guest = Guest::updateOrCreate(
                                    ['user_id' => $val->user->id, 'meeting_id' => $request->meeting_id, 'workshop_id' => $request->workshop_id],
                                    ['url_type' => 'doodle', 'identifier' => $random_string]
                                );
                                $guest->url_type = 'doodle';
                                $guest->identifier = $random_string;
                                $guest->save();

                                $token[strtolower($val->user->email)] = $random_string;
                            } else {
                                $meetingEmails[] = $val->user->email;
                            }
                        }

                    }
    
                    // adding the bj (external meeting update here) because the meeting update takes time
                    // and if we send notification first and wait to update bj meeting the mobile users will fetch
                    // unread count before the new notification data is commit
                    
                    //adding meeting service function for video/hybrid type
    
                    if (in_array($request->meeting_type, [2, 3]) && ($request->meeting_date_type == 1)) {
                        $checkRes = $this->meetingService->updateVideoMeeting($request, $request->meeting_id);
                        if (empty($checkRes)) {
                            throw new App\Exceptions\CustomValidationException(__('message.meeting_not_updated'));
                            DB::connection('tenant')->rollback();
                        }
                    }
                    
                    // this must be at the most end and method should not process anything after sending push
                    // because after pushing notification mobile app immediately hit api to get unread count and
                    // as changes are not commit because this method is still processing the unread count api will return
                    // previous count
                    if (!empty($meetingEmails)) {
                        $workshopUser = WorkshopMeta::with('workshop')->where('workshop_id', $request->workshop_id)->where('role', '!=', 3)->get();
                        $heading = 'Date modifiée';
                        $msg = $this->getPushData($request->workshop_id, $request->meeting_id, 'save_modify_metting_push_setting');
                        $msgEN = $this->getPushData($request->workshop_id, $request->meeting_id, 'save_modify_metting_push_setting', 'EN');
                        $type = 'editMeeting';
                        $this->sendNoti($workshopUser, $request->meeting_id, $msg, $msgEN, $type);
                        $params = ['emails' => $meetingEmails, 'workshop_id' => $request->workshop_id, 'meeting_id' => $request->meeting_id, 'user_id' => 0, 'user_type' => 'm', 'type' => 'modify_meeting'];
                        $this->sendNewMeetingInvitation($params);
                    }

                    if (!empty($guestEmail)) {

                        $params = ['emails' => $guestEmail, 'workshop_id' => $request->workshop_id, 'meeting_id' => $request->meeting_id, 'user_id' => 0, 'user_type' => 'g', 'url_type' => 'modify_meeting', 'token' => $token];
                        $this->sendGuestInvitationEmail($params);
                    }


                    DB::connection('tenant')->commit();
                } else {
                    DB::connection('tenant')->rollback();
                }

                return response()->json($request->meeting_id);
            }
        }

        public function inviteMeeting(Request $request)
        {
            $userId = 0;
            if ($request->data != NULL) {
                $json_decode = json_decode($request->data);
                $emails[] = $json_decode->text;
            } else {
                $emails[] = $request->email;
                if ($request->member_type == 'g' || $request->member_type == 'G') {
                    $userId = Guest::insertGetId(['fname' => $request->firstname, 'lname' => $request->lastname, 'email' => $request->email, 'workshop_id' => $request->workshop_id, 'meeting_id' => $request->meeting_id, 'url-type' => 'doodle']);
                } else {
                    $hostname = $this->getHostNameData();
                    $hostCode = DB::connection('mysql')->table('hostname_codes')->where(['fqdn' => $hostname->fqdn])->first(['hash']);
                    $randCode = generateRandomValue(4);
                    $newCode = setPasscode($hostCode->hash, $randCode);

                    $newUsers = ['fname' => $request->firstname, 'lname' => $request->lastname, 'email' => strtolower($request->email), 'password' => Hash::make(strtolower($request->email)), 'role' => 'M2', 'login_code' => $newCode['userCode'], 'hash_code' => $newCode['hashCode']];
                    $userId = User::insertGetId($newUsers);

                    if ($userId) {
                        $user = User::find($userId);
                        $entity = Entity::create(['long_name' => $user->society, 'short_name' => $user->society, 'address1' => $user->address, 'address2' => $user->fqdn, 'zip_code' => $user->postal, 'city' => $user->city, 'country' => $user->country, 'phone' => $user->phone, 'email' => $user->email, 'entity_type_id' => 2]);

                        EntityUser::create(['user_id' => $user->id, 'entity_id' => $entity->id, 'entity_label' => '']);
                    }
                }
            }
            $params = ['emails' => $emails, 'workshop_id' => $request->workshop_id, 'meeting_id' => $request->meeting_id, 'user_id' => $userId, 'user_type' => $request->member_type];
            $this->sendMeetingInvitationEmail($params);
            return response()->json(['status' => 1]);
        }

        function sendNewMeetingInvitation($params)
        {
            $workshop_data = Workshop::with('meta')->find($params['workshop_id']);
            $meeting_data = Meeting::find($params['meeting_id']);
            if ($params['type'] == 'modify_meeting') {
                $route = route(
                    'redirect-meeting-view', [
                        'userid' => base64_encode($params['user_id']),
                        'type'   => $params['user_type'],
                        'url'    => str_rot13('organiser/commissions/' . $params['workshop_id'] . '/meeting/' . $params['meeting_id'] . '/view'),
                    ]
                );
            } else {
                $route = route(
                    'redirect-meeting-view', [
                        'userid' => base64_encode($params['user_id']),
                        'type'   => $params['user_type'],
                        'url'    => str_rot13('organiser/commissions/' . $params['workshop_id'] . '/meeting/' . $params['meeting_id'] . '/view'),
                    ]
                );
            }
            $link['url'] = $route;
            $key = $this->sendVideoHybridMail($meeting_data, $params['type'], $params['emails'], $link);
            if ($key) {
                return TRUE;
            }
            if ($params['type'] == 'modify_meeting') {
                $route = route(
                    'redirect-meeting-view', [
                        'userid' => base64_encode($params['user_id']),
                        'type'   => $params['user_type'],
                        'url'    => str_rot13('organiser/commissions/' . $params['workshop_id'] . '/meeting/' . $params['meeting_id'] . '/view'),
                    ]
                );


                //changed the line to fix the bug dated 21March19, changed the save_meeting_date_email_setting to save_new_meeting_date_email_setting
                $dataMail = $this->getMailData($workshop_data, $meeting_data, 'save_new_meeting_date_email_setting');

                $subject = $dataMail['subject'];

                $mailData['mail'] = ['subject' => $subject, 'emails' => $params['emails'], 'workshop_data' => $workshop_data, 'meeting_data' => $meeting_data, 'url' => $route];

                $this->core->SendMassEmail($mailData, 'modify_meeting_date');
            } else {
                $route = route(
                    'redirect-meeting-view', [
                        'userid' => base64_encode($params['user_id']),
                        'type'   => $params['user_type'],
                        'url'    => str_rot13('organiser/commissions/' . $params['workshop_id'] . '/meeting/' . $params['meeting_id'] . '/view'),
                    ]
                );
                //changed the line to fix the bug dated 21March19, changed the save_new_meeting_date_email_setting to save_meeting_date_email_setting
                $dataMail = $this->getMailData($workshop_data, $meeting_data, 'save_meeting_date_email_setting');
                $subject = $dataMail['subject'];
                $mailData['mail'] = ['subject' => $subject, 'emails' => $params['emails'], 'workshop_data' => $workshop_data, 'meeting_data' => $meeting_data, 'url' => $route];

                $this->core->SendMassEmail($mailData, 'save_meeting_date');
            }
        }

        function sendMeetingInvitationEmail($params)
        {
            $workshop_data = Workshop::with('meta')->find($params['workshop_id']);
            $meeting_data = Meeting::find($params['meeting_id']);
            $route = route(
                'redirect-meeting-view', [
                    'userid' => base64_encode($params['user_id']),
                    'type'   => $params['user_type'],
                    'url'    => str_rot13('organiser/commissions/' . $params['workshop_id'] . '/meeting/' . $params['meeting_id'] . '/view'),
                ]
            );
            $link['url'] = $route;
            if(isset($params['email_to']) && !empty($params['email_to'])){
                array_push($params['emails'],$params['email_to']);
            }
            $key = $this->sendVideoHybridMail($meeting_data, 'doodle_email_setting', $params['emails'], $link);
            if ($key) {
                return TRUE;
            }


            $dataMail = $this->getMailData($workshop_data, $meeting_data, 'doodle_email_setting');
            $subject = $dataMail['subject'];

            $mailData['mail'] = ['subject' => $subject, 'emails' => $params['emails'], 'email_to' => ($params['email_to']) ?? '', 'workshop_data' => $workshop_data, 'meeting_data' => $meeting_data, 'url' => $route];

            $this->core->SendMassEmail($mailData, 'doodle_meeting');
            // return $this->core->SendMassEmail($mailData, 'doodle_meeting');
        }

        public function sendNewMemberInvitationEmail($params)
        {
            if (isset($params['workshop_id'])) {
                $route = route(
                    'redirect-meeting-view', [
                        'userid' => base64_encode($params['user_id']),
                        'type'   => $params['user_type'],
                        'url'    => str_rot13('organiser/commissions/' . $params['workshop_id'] . '/meeting/' . $params['meeting_id'] . '/view'),
                    ]
                );
                $workshop_data = Workshop::with('meta')->find($params['workshop_id']);
                $meeting_data = Meeting::find($params['meeting_id']);

                $dataMail = $this->getMailData($workshop_data, $meeting_data, 'user_email_setting');
                $subject = $dataMail['subject'];

                $mailData['mail'] = ['subject' => $subject, 'emails' => $params['emails'], 'workshop_data' => $workshop_data, 'meeting_data' => $meeting_data, 'url' => $route];

                if (isset($params['template'])) {
                    $this->core->SendMassEmail($mailData, 'final_date_prepd');
                } else {

                    $this->core->SendMassEmail($mailData, 'new_user');

                }
            } else {
                $route = route(
                    'redirect-meeting-view', [
                        'userid' => base64_encode($params['user_id']),
                        'type'   => $params['user_type'],
                        'url'    => str_rot13('dashboard'),
                    ]
                );
                $dataMail = $this->getUserMailData('user_email_setting');
                $subject = utf8_encode($dataMail['subject']);

                $mailData['mail'] = ['subject' => $subject, 'email' => $params['emails'], 'password' => $params['user_password'], 'url' => $route];
                $this->core->SendEmail($mailData, 'new_user');
            }
        }

        public function sendGuestInvitationEmail($params)
        {

            $route = route('guest-meeting-view');
            $workshop_data = Workshop::with('meta')->find($params['workshop_id']);
            $meeting_data = Meeting::find($params['meeting_id']);
            $dataMail = $this->getMailData($workshop_data, $meeting_data, 'doodle_email_setting');
            $subject = $dataMail['subject'];
            if ($params['url_type'] == 'prepd') {
                $dataMail = $this->getMailData($workshop_data, $meeting_data, 'agenda_email_setting');
                $mailData['mail'] = ['subject' => $dataMail['subject'], 'emails' => $params['emails'], 'workshop_data' => $workshop_data, 'meeting_data' => $meeting_data, 'current_user_fn' => $params['current_user_fn'], 'current_user_ln' => $params['current_user_ln'], 'current_user_email' => $params['current_user_email'], 'url' => $route];
                $this->core->SendGuestMassEmail($mailData, 'prepd_validate', $params['token']);
            } else if ($params['url_type'] == 'final_date_doodle') {
                $link['url'] = $route;
                $link['token'] = $params['token'];
                $key = $this->sendVideoHybridMail($meeting_data, 'doodle_final_date', $params['emails'], $link);
                if ($key) {
                    return TRUE;
                }
                $dataMail = $this->getMailData($workshop_data, $meeting_data, 'doodle_final_date');
                $mailData['mail'] = ['subject' => $dataMail['subject'], 'emails' => $params['emails'], 'workshop_data' => $workshop_data, 'meeting_data' => $meeting_data, 'url' => $route];

                $this->core->SendGuestMassEmail($mailData, 'doodle_final_meeting', $params['token']);
            } else if ($params['url_type'] == 'repd') {
                $dataMail = $this->getMailData($workshop_data, $meeting_data, 'decision_email_setting');
                $mailData['mail'] = ['subject' => $dataMail['subject'], 'emails' => $params['emails'], 'workshop_data' => $workshop_data, 'meeting_data' => $meeting_data, 'current_user_fn' => $params['current_user_fn'], 'current_user_ln' => $params['current_user_ln'], 'current_user_email' => $params['current_user_email'], 'url_repd' => $route];
                $this->core->SendGuestMassEmail($mailData, 'guest_repd_validate ', $params['token']);
            } else if ($params['url_type'] == 'modify_meeting') {
                $link['url'] = $route;
                $link['token'] = $params['token'];
                $key = $this->sendVideoHybridMail($meeting_data, $params['url_type'], $params['emails'], $link);
                if ($key) {
                    return TRUE;
                }
                $dataMail = $this->getMailData($workshop_data, $meeting_data, 'save_new_meeting_date_email_setting');
                $mailData['mail'] = ['subject' => $dataMail['subject'], 'emails' => $params['emails'], 'workshop_data' => $workshop_data, 'meeting_data' => $meeting_data, 'url' => $route];
                $this->core->SendGuestMassEmail($mailData, 'modify_meeting_date ', $params['token']);
            }else if ($params['url_type'] == 'save_meeting'){
                $link['url'] = $route;
                $link['token'] = $params['token'];
                $key = $this->sendVideoHybridMail($meeting_data, $params['url_type'], $params['emails'], $link);
                if ($key) {
                    return TRUE;
                }
                $dataMail = $this->getMailData($workshop_data, $meeting_data, 'save_meeting_date_email_setting');
                $mailData['mail'] = ['subject' => $dataMail['subject'], 'emails' => $params['emails'], 'workshop_data' => $workshop_data, 'meeting_data' => $meeting_data, 'url' => $route];
                $this->core->SendGuestMassEmail($mailData, 'save_meeting_date', $params['token']);
            } else {
                $link['url'] = $route;
                $link['token'] = $params['token'];
                $key = $this->sendVideoHybridMail($meeting_data, 'doodle_email_setting', $params['emails'], $link);
                if ($key) {
                    return TRUE;
                }
                $mailData['mail'] = ['subject' => $subject, 'emails' => $params['emails'], 'workshop_data' => $workshop_data, 'meeting_data' => $meeting_data, 'url' => $route];
                $this->core->SendGuestMassEmail($mailData, 'doodle_meeting', $params['token']);
            }
        }

        public function sendNewWorkshopMemberInvitationEmail($params)
        {
            if (isset($params['workshop_id'])) {
                $route = route(
                    'redirect-meeting-view', [
                        'userid' => base64_encode($params['user_id']),
                        'type'   => $params['user_type'],
                        'url'    => str_rot13('organiser/commissions/' . $params['workshop_id'] . '/meeting/' . $params['meeting_id'] . '/view'),
                    ]
                );
                $workshop_data = Workshop::with('meta')->find($params['workshop_id']);
                $meeting_data = Meeting::find($params['meeting_id']);

                $dataMail = $this->getMailData($workshop_data, $meeting_data, 'commission_new_user');
                $subject = $dataMail['subject'];

                $mailData['mail'] = ['subject' => $subject, 'emails' => $params['emails'], 'workshop_data' => $workshop_data, 'meeting_data' => $meeting_data, 'url' => $route];

                $this->core->SendMassEmail($mailData, 'new_commission_user');

            }
        }

        public function saveUserResponse(Request $request)
        {
            $data = [];
            if ($request->meeting_id > 0) {
                $res = json_decode($request->user_res);
                $count = count($res);
                if ($count > 0) {
                    DoodleVote::where('user_id', Auth::user()->id)->where('meeting_id', $request->meeting_id)->delete();
                    for ($i = 0; $i < $count; $i++) {
                        $data[] = ['meeting_id' => $request->meeting_id, 'user_id' => Auth::user()->id, 'doodle_id' => $res[$i]->id, 'available' => $res[$i]->status, 'updated_id' => date('Y-m-d h:i:s')];
                    }
                    DoodleVote::insert($data);
                }

                return response()->json(['status' => 1]);
            }
            return response()->json(['status' => 0]);
        }

        public function getFutureMeetings(Request $request)
        {
    
            if ($this->meetingService->isUserWAdminOrHigher($request->input('wid'), Auth::user())) {
                $builder = Meeting::with(['doodleDates', 'consultation:uuid,name'])
                // as global scope have if not org or super fetch status 1 only
                ->withoutGlobalScopes();
            } else {
                $builder = Meeting::with(['doodleDates' => function ($query) {
                    $query->/*whereDate('date', '>', Carbon::now('Europe/Paris')->format('Y-m-d'))->*/ orderBy('date', 'asc');
                }, 'consultation:uuid,name'])
                    ->where('status', 1);
            }
            
            $data = $builder->where('workshop_id', $request->wid)->where(function ($q) {
                $q->where(DB::raw('concat(meetings.date," ",meetings.start_time)'), '>=', Carbon::now('Europe/Paris')->format('Y-m-d H:i:s'))
                    ->orWhere('meeting_date_type', 0);
            })
                ->selectRaw('meetings.*, COALESCE(meetings.date, doodle_dates.date) as sorting_date '
                    . ', COALESCE(meetings.start_time, doodle_dates.start_time) as sorting_time')
                ->leftJoin('doodle_dates', 'doodle_dates.meeting_id', '=', 'meetings.id')
                ->groupBy('meetings.id')
                ->orderBy('sorting_date')
                ->orderBy('sorting_time')
                ->get();
            return response()->json($data);
        }

        public function getPastMeetings(Request $request)
        {
            $builder = Meeting::where([
                    [DB::raw('concat(date," ",start_time)'), '<=', Carbon::now('Europe/Paris')->format('Y-m-d H:i:s')],
                    ['workshop_id', $request->wid]
                ]);
            if($this->meetingService->isUserWAdminOrHigher($request->input('wid'), Auth::user())) {
                // if user is workshop sec/dep or higher then removing global scope
                // as current global scope allow to fetch deleted (status = 0) meeting to org/super only
                // so if user is sec/dep, also allowed to fetch the status =0 | deleted meeting
                $builder = $builder->withoutGlobalScopes();
            }
              $data = $builder->orderBy('date', 'desc')
                ->orderBy('start_time', 'desc')
                ->get();
            return response()->json($data);
        }

        public function getMeetingById(Request $request)
        {
            try {
                $data['meeting'] = Meeting::with('doodleDates')->where('id', $request->meetingid)->first();
                if (empty($data['meeting'])) {
                    return response()->json(['status' => FALSE, 'msg' => 'No meeting Exists'], 400);
                } else
                    return response()->json($data);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        public function getMeetingByDate(Request $request)
        {

            if ($request->flag == 'by_month') {
                $exp_month = explode('/', $request->date);
                $meetingsArr = [];
                $meetings = Meeting::whereNotNull('date')->whereMonth('date', $exp_month[1])->orderBy('date', 'ASC')->get();

                $prev_date = '';
                foreach ($meetings as $val) {
                    if ($val->date != NULL) {
                        $exp_date = explode('-', $val->date);
                        if (strtotime($prev_date) != strtotime($val->date)) {
                            $count = 1;
                        } else {
                            $count++;
                        }
                        $prev_date = $val->date;
                        $meeting_type = (strtotime($val->date) > strtotime(date('Y-m-d'))) ? 'feature_meeting' : 'past_meeting';
                        $meetingsArr[$exp_date[2]] = ['count' => $count, 'date' => $val->date, 'meeting_type' => $meeting_type];
                    }
                }
                $data['meeting'] = $meetingsArr;
                $data['meetingData'] = $meetings;
            } else {
                $data['meetingData'] = Meeting::where('date', $request->date)->get();
            }
            return response()->json($data);
        }

        public function viewMeeting(Request $request)
        {
            try {
                $data['meeting'] = Meeting::find($request->meetingid);

//        return response($this->doodleResponseList($request));
                $data['doodle_data'] = $this->doodleResponseList($request);
                if (empty($data['meeting'])) {
                    return response()->json(['status' => FALSE, 'msg' => 'No meeting Exists'], 400);
                } else
                    return response()->json($data);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        public function doodleResponseList($request)
        {
            $workshopUserIds = [];
            $countUserVote = [];
            $voting = [];
            $totalUsers = 0;
            $meetingId = $request->meetingid;
//            $doodleDates = DoodleDates::where('meeting_id', $meetingId)->whereDate('date', '>=', Carbon::now('Europe/Paris')->format('Y-m-d'))->orderBy('date', 'asc')->get();
            $doodleDates = DoodleDates::where('meeting_id', $meetingId)->where(function ($q) {
                $q->whereRaw("concat(date,' ',start_time) >= '" . Carbon::now('Europe/Paris')->format('Y-m-d H:i:00') . "'");
                $q->whereRaw("concat(date,' ',end_time) >= '" . Carbon::now('Europe/Paris')->format('Y-m-d H:i:00') . "'");
            })->orderBy('date', 'asc')->get();

            $allUsers = WorkshopMeta::where('workshop_id', $request->workshopid)->where('role', '!=', 3)->groupBy('user_id')->get();
            $users = WorkshopMeta::where('workshop_id', $request->workshopid)->where('meeting_id', $meetingId)->where('role', 3)->groupBy('user_id')->get();
            $finalUsers = array_merge($allUsers->toArray(), $users->toArray());

            if (count($finalUsers) > 0) {
                foreach ($finalUsers as $key => $value) {
                    //dump($users,'meet');
                    $array = [];
                    $meetingDoodleDate = doodleVote::where('meeting_id', $meetingId)->where('user_id', $value['user_id'])->get();
                    if (count($meetingDoodleDate) > 0) {
                        foreach ($meetingDoodleDate as $k => $v) {
//$i=0;
//$countUserVote[$v->doodle_id]=$i;
                            $array[$v->doodle_id] = $v->available;
                        }
                    } else {
                        foreach ($doodleDates as $k => $v) {
                            $array[$v->id] = 3;
                        }
                    }
                    $totalUsers++;

                    $workshopUserIds[] = $value['user']['id'];
                    $voting[] = ['id' => $value['user']['id'], 'name' => ucfirst($value['user']['fname']) . ' ' . strtoupper($value['user']['lname']), 'email' => $value['user']['email'], 'vote' => $array, 'role' => $value['role'], 'lname' => $value['user']['lname']];


                }

            }
//        $doodleVotesGuestUser = WorkshopMeta::where('workshop_id', $request->workshopid)->where('meeting_id', $meetingId)->whereIn('role', array(3))->get();
            $doodleVotesGuestUser = User::with(['doodleVote' => function ($query) use ($meetingId) {
                $query->where('meeting_id', $meetingId);
            }])->whereNotIn('id', $workshopUserIds)->get();
//        return response($workshopUserIds);
            if (count($doodleVotesGuestUser) > 0) {
                foreach ($doodleVotesGuestUser as $key => $value) {
                    $array = [];
                    if (count($value->doodleVote) > 0) {
                        foreach ($value->doodleVote as $k => $v) {
                            $array[$v->doodle_id] = 3;
                        }
                        $totalUsers++;
                        $voting[] = ['id' => $value->id, 'name' => $value->fname . ' ' . $value->lname, 'email' => $value->email, 'vote' => $array];
                    }
                }
            }
            $data['total_uesrs'] = $totalUsers;
            $data['votes'] = $voting;
            $data['dates'] = $doodleDates;
            return $data;
        }

        public function updateMeetingFinalDate(Request $request)
        {
            $emails = $guest_email = [];
            $token = [];
            $presenceData = [];
            $postData = ['meeting_date_type' => 1, 'date' => $request->date, 'start_time' => $request->start_time, 'end_time' => $request->end_time];
            \DB::connection('tenant')->beginTransaction();
            if (Meeting::where('id', $request->meeting_id)->update($postData)) {

                $workshop_data = Workshop::with(['meta_data' => function ($query) use ($request) {
                    $query->whereIn('role', [0, 1, 2])->orWhere('role', 3)->where('meeting_id', $request->meeting_id);
                }])->where('id', $request->wid)->first();
                $presenceList = Presence::where('workshop_id', $request->wid)->where('meeting_id', $request->meeting_id)->groupBy('user_id')->pluck('user_id', 'id')->toArray();
                if (count($workshop_data->meta_data) > 0) {
                    foreach ($workshop_data->meta_data as $k => $val) {
                        if (!in_array($val->user_id, $presenceList)) {
                            $presenceData[] = [
                                'workshop_id'      => $request->wid,
                                'meeting_id'       => $request->meeting_id,
                                'user_id'          => $val->user_id,
                                'register_status'  => ($val->role == 1 || $val->role == 2) ? 'I' : 'NI',
                                'presence_status'  => ($val->role == 1 || $val->role == 2) ? 'P' : 'ANE',
                                'with_meal_status' => $this->addMealStatus($val, $request),
                            ];
                        }
                        if ($val->user->email != '') {

                            if ($val->role == 1) {
                                $email_to = $val->user->email;
                            } elseif ($val->role == 3) {
                                $random_string = generateRandomString();
                                $token[$val->user->email] = $random_string;
                                $guest = ['user_id' => $val->user->id, 'meeting_id' => $request->meeting_id, 'workshop_id' => $request->wid, 'url_type' => 'final_date_doodle', 'identifier' => $random_string];
                                $user_Id = Guest::insertGetId($guest);
                                $guest_email[] = $val->user->email;
                            } else {
                                $emails[] = $val->user->email;
                            }
                        }
                    }

                    if (!empty($presenceData)) {
                        Presence::insert($presenceData);
                    }
                }

                if (!empty($guest_email)) {

                    $params = ['emails' => $guest_email, 'workshop_id' => $request->wid, 'meeting_id' => $request->meeting_id, 'user_id' => 0, 'user_type' => 'g', 'url_type' => 'final_date_doodle', 'token' => $token];
                    $this->sendGuestInvitationEmail($params);
                }

                $meeting_data = Meeting::find($request->meeting_id);
                //adding video meeting code here
                if (in_array($meeting_data->meeting_type, [2, 3])) {
                    $reqForBJ = $this->meetingService->prepareRequestForBJCreate($request, $meeting_data);
                    $checkRes1 = $this->meetingService->registerBlueJeansUser($reqForBJ, $meeting_data->id);
                    $checkRes = $this->meetingService->createVideoMeeting($reqForBJ, $meeting_data->id);
                    if (empty($checkRes) || empty($checkRes1)) {
                        throw new App\Exceptions\CustomValidationException(__('message.meeting_not_created'));
                        DB::connection('tenant')->rollback();
                    }
                }

                $dataMail = $this->getMailData($workshop_data, $meeting_data, 'doodle_final_date');
                $subject = $dataMail['subject'];
                $route_prepd = $dataMail['route_prepd'];
                $mailData['mail'] = ['subject' => $subject, 'email_to' => $email_to, 'emails' => $emails, 'meeting_data' => $meeting_data, 'workshop_data' => $workshop_data, 'url' => $route_prepd];

                $workshopUser = WorkshopMeta::with('workshop')->where('workshop_id', $request->wid)->where('role', '!=', 3)->get();

                $heading = 'Doodle Date Finale';

                $msg = $this->getPushData($workshop_data->id, $meeting_data->id, 'doodle_final_push_setting');
                $msgEN = $this->getPushData($workshop_data->id, $meeting_data->id, 'doodle_final_push_setting', 'EN');
                $type = 'finalDate';
                $this->sendNoti($workshopUser, $meeting_data->id, $msg, $msgEN, $type);
                //remove doodle dates
                DoodleDates::where('meeting_id', $meeting_data->id)->delete();
                //adding Video Meeting emails
                $link['url'] = $route_prepd;

                if(isset($email_to) && !empty($email_to)){
                    array_push($emails,$email_to);
                }
                $key = $this->sendVideoHybridMail($meeting_data, 'doodle_final_date', $emails, $link);

                if (!$key) {
                    $this->core->SendMassEmail($mailData, 'doodle_final_meeting');
                }
                DB::connection('tenant')->commit();
                return response()->json(['status' => 1]);
            }
            DB::connection('tenant')->rollback();
            return response()->json(['status' => 0]);
        }

        public function sendMeetingInvitation(Request $request)
        {
            $workshop_data = Workshop::with('meta')->find($request->workshop_id);
            $meeting_data = Meeting::find($request->meeting_id);
            $redirect_url = str_rot13('organiser/commissions/' . $request->workshop_id . '/meeting/' . $request->meeting_id . '/view');
            $params = route('redirect-meeting-view', ['userid' => base64_encode(4), 'url' => ($redirect_url), 'type' => 'm']);
            $link['url'] = $params;
            $key = $this->sendVideoHybridMail($meeting_data, 'doodle_reminder_email_setting', [$request->email], $link);
            if ($key) {
                $this->sendInviteNotification($request);
                return response()->json(['status' => 1]);
            }


            $dataMail = $this->getMailData($workshop_data, $meeting_data, 'doodle_reminder_email_setting');
            $subject = $dataMail['subject'];
            $mailData['mail'] = ['subject' => $subject, 'emails' => [$request->email], 'workshop_data' => $workshop_data, 'meeting_data' => $meeting_data, 'url' => $params];
            if ($this->core->SendMassEmail($mailData, 'doodle_meeting')) {
                $this->sendInviteNotification($request);
                return response()->json(['status' => 1]);
            }
            return response()->json(['status' => 0]);
        }
    
        public function sendInviteNotification($request) {
            $user = User::where('email', $request->email)->first(['id']);
            //for notification
            $workshopUser = WorkshopMeta::with('workshop')->where('workshop_id', $request->workshop_id)->where('user_id', $user->id)->where('role', '!=', 3)->get();
            $heading = 'Rappel de Doodle';
            $msg = $this->getPushData($request->workshop_id, $request->meeting_id, 'doodle_reminder_push_setting');
            $msgEN = $this->getPushData($request->workshop_id, $request->meeting_id, 'doodle_reminder_push_setting', 'EN');
            $type = 'remindDate';
            $this->sendNoti($workshopUser, $request->meeting_id, $msg, $msgEN, $type);
        }

        public function getPresence(Request $request)
        {

            $data = Presence::with('user')->select('presences.*', 'workshop_metas.role', 'workshop_metas.meeting_id as mid')->where('presences.meeting_id', $request->mid)->where(['workshop_metas.workshop_id' => $request->wid])
                ->leftjoin('workshop_metas', 'presences.user_id', '=', 'workshop_metas.user_id')->groupBy('user_id', 'register_status', 'role')->orderBy('role', 'asc')->get();
            // SELECT * from presences where user_id not in(SELECT user_id from workshop_metas where workshop_metas.workshop_id=6) and meeting_id=39

            $user = [];
            foreach ($data as $k => $val) {

                if (isset($user[$val->user_id]) && $val->role == 3 && $val->mid != $request->mid) {
                    unset($data[$k]);
                    continue;
                }
                if (isset($user[$val->user_id])) {

                    $count++;
                    if (in_array(2, $roles) || in_array(1, $roles)) {
                        if ($val->role !== 0 && !in_array($val->role, $roles)) {
                            $roles[] = $val->role;
                            $user[$val->user_id] = ['id'                    => $val->id, 'role' => ($roles), 'user' => $val->user, 'user_id' => $val->user->id, 'count' => $count, 'presence_status' => $val->presence_status, 'register_status' => $val->register_status, 'with_meal_status' => $val->with_meal_status,
                                                    'video_presence_status' => $val->video_presence_status,

                            ];
                        }
                    } else {
                        if (($key = array_search(0, $roles)) !== FALSE) {
                            if ($val->role !== 0 && !in_array($val->role, $roles)) {
                                $roles[$key] = $val->role;
                                $user[$val->user_id] = ['id'                    => $val->id, 'role' => ($roles), 'user' => $val->user, 'user_id' => $val->user->id, 'count' => $count, 'presence_status' => $val->presence_status, 'register_status' => $val->register_status, 'with_meal_status' => $val->with_meal_status,
                                                        'video_presence_status' => $val->video_presence_status,
                                ];
                            }
                            //unset($roles[$key]);
                        }

                    }


                } else {

                    $roles = [];
                    $count = 1;
                    $roles[] = $val->role;
                    $user[$val->user_id] = ['id'                    => $val->id, 'role' => ($roles), 'user' => $val->user, 'user_id' => $val->user->id, 'count' => $count, 'presence_status' => $val->presence_status, 'register_status' => $val->register_status, 'with_meal_status' => $val->with_meal_status,
                                            'video_presence_status' => $val->video_presence_status,
                    ];
                }

            }

            $restUser = Presence::with('user')->whereRaw('user_id not in(SELECT user_id from workshop_metas where workshop_metas.workshop_id=' . $request->wid . ')')->where('meeting_id', $request->mid)->get();

            if ($restUser->count() > 0) {
                foreach ($restUser as $k => $val) {

                    if (gettype($val->user) == 'object') {

                        $roles = [];
                        $roles[] = 0;
                        $count = 1;
                        $user[$val->user_id] = ['id'                    => $val->id, 'role' => $roles, 'user' => $val->user, 'user_id' => $val->user->id, 'count' => $count, 'presence_status' => $val->presence_status, 'register_status' => $val->register_status, 'with_meal_status' => $val->with_meal_status,
                                                'video_presence_status' => $val->video_presence_status,
                        ];
                    }

                }

            }

            //old code without all member but single role
            /* $data = Presence::where('presences.meeting_id', $request->mid)->get();
       $metaUser=WorkshopMeta::with('user')->whereIn('user_id',$data->pluck('user_id'))->where(['workshop_id'=>$request->wid])->havingRaw('meeting_id IS NULL OR meeting_id='.$request->mid)->get();
    
       $user = [];
       foreach ($metaUser as $k=>$val) {
           if (isset($user[$val->user_id])) {
               $count++;
               $roles[] = $val->role;
               $user[$val->user_id] = ['id' => $data[$k]->id, 'role' => ($roles), 'user' => $val->user, 'user_id' => $val->user->id, 'count' => $count, 'presence_status' => $data[$k]->presence_status, 'register_status' => $data[$k]->register_status];
           } else {
               $roles = [];
               $count = 1;
               $roles[] = $val->role;
               $user[$val->user_id] = ['id' => $data[$k]->id, 'role' => ($roles), 'user' => $val->user, 'user_id' => $val->user->id, 'count' => $count, 'presence_status' => $data[$k]->presence_status, 'register_status' => $data[$k]->register_status];
           }
       }*/

// $data = Presence::with('user')->where('workshop_id', $request->wid)->where('meeting_id', $request->mid)->groupBy('user_id')->orderBy('id', 'desc')->get();
            $data = array_values($user);
            //reodering as per role
            $order = [
                1, 2, 0, 3];
            usort($data, function ($a, $b) use ($order) {
                $a = array_search($a["role"][0], $order);
                $b = array_search($b["role"][0], $order);
                if ($a === FALSE && $b === FALSE) { // both items are dont cares
                    return 0;                      // a == b
                } else if ($a === FALSE) {           // $a is a dont care item
                    return 1;                      // $a > $b
                } else if ($b === FALSE) {           // $b is a dont care item
                    return -1;                     // $a < $b
                } else {
                    return $a - $b;
                }
            });
            //var_dump($array); // after
            return response($data);
        }


        public function inviteMemberFinalMeeting(Request $request)
        {
            $flag = 0;
            $lastMeetingId = $request->meeting_id;
            $workshopMetaData = [];
            if ($request->userData != NULL) {
                $inviteData = json_decode($request->userData);
                foreach ($inviteData as $val) {
                    $random_string = generateRandomString();
                    if (!is_int($val->id)) {
                        if ($val->member_type == 'G' || $val->member_type == 'g') {
                            $get_ids = User::where('email', $val->email)->pluck('id');
                            if ($get_ids != NULL && count($get_ids) > 0) {
                                $guest = ['user_id' => $get_ids[0], 'meeting_id' => $lastMeetingId, 'workshop_id' => $request->workshop_id, 'url_type' => 'doodle', 'identifier' => $random_string];
                                $userId = Guest::insertGetId($guest);
                                $userId = $get_ids[0];

                                $flag = ($userId > 0) ? 1 : 0;
                            } else {
                                $newUsers = ['fname' => $val->fname, 'lname' => $val->lname, 'email' => $val->email, 'password' => Hash::make($val->email), 'role' => 'M3'];
                                $userId = User::insertGetId($newUsers);
                                $guest = ['user_id' => $userId, 'meeting_id' => $lastMeetingId, 'workshop_id' => $request->workshop_id, 'url_type' => 'doodle', 'identifier' => $random_string];
                                $guestId = Guest::insertGetId($guest);
                                $flag = ($guestId > 0) ? 1 : 0;
                            }
                            $workshopMetaData[] = ['workshop_id' => $request->workshop_id, 'meeting_id' => $lastMeetingId, 'user_id' => $userId, 'role' => '3'];
                            $guest_email[] = $val->email;
                            $token[strtolower($val->email)] = $random_string;
                        } else {
                            $get_ids = User::where('email', $val->email)->first();
                            if (isset($get_ids->id)) {
                                $result = User::where('email', $val->email)->update(['role' => 'M2']);
                                $userId = $get_ids->id;
                            } else {
                                $hostname = $this->getHostNameData();
                                $hostCode = DB::connection('mysql')->table('hostname_codes')->where(['fqdn' => $hostname->fqdn])->first(['hash']);
                                $randCode = generateRandomValue(4);
                                $newCode = setPasscode($hostCode->hash, $randCode);

                                $newUsers = ['fname' => $val->fname, 'lname' => $val->lname, 'email' => strtolower($val->email), 'password' => Hash::make(strtolower($val->email)), 'role' => 'M2', 'login_code' => $newCode['userCode'], 'hash_code' => $newCode['hashCode']];
                                $userId = User::insertGetId($newUsers);

                                if ($userId) {
                                    $user = User::find($userId);
                                    $entity = Entity::create(['long_name' => $user->society, 'short_name' => $user->society, 'address1' => $user->address, 'address2' => $user->fqdn, 'zip_code' => $user->postal, 'city' => $user->city, 'country' => $user->country, 'phone' => $user->phone, 'email' => $user->email, 'entity_type_id' => 2]);

                                    EntityUser::create(['user_id' => $user->id, 'entity_id' => $entity->id, 'entity_label' => '']);
                                }
                            }

                            $new_member_email[] = $val->email;
                            $memberType[] = $val->member_type;
                            $workshopMetaData[] = ['workshop_id' => $request->workshop_id, 'meeting_id' => $lastMeetingId, 'user_id' => $userId, 'role' => '0'];
                        }
                    } else {
                        $metaCheck = WorkshopMeta::where('workshop_id', $request->workshop_id)->where('user_id', $val->id)->first();
                        if (!$metaCheck) {
                            $workshopMetaData[] = ['workshop_id' => $request->workshop_id, 'meeting_id' => $lastMeetingId, 'user_id' => $val->id, 'role' => '0'];
                            $emails[] = $val->email;
                        } else {
                            $flag = 1;
                        }
                    }
                }
                if (!empty($workshopMetaData)) {

                    $workShopInsert = WorkshopMeta::insert($workshopMetaData);
                    $flag = ($workShopInsert > 0) ? 1 : 0;
                    if (!empty($new_member_email)) {
                        $params = ['emails' => $new_member_email, 'workshop_id' => $request->workshop_id, 'meeting_id' => $lastMeetingId, 'user_id' => 0, 'user_type' => 'm'];
                        $this->sendNewMemberInvitationEmail($params);
                        $this->sendMeetingInvitationEmail($params);
                    }
                }
            }
            return response($flag);
        }

        public function updateRegister(Request $request)
        {
            if ($request->status == 'NI') {
                $register_status = 'NI';
                $presence_status = 'ANE';
            } elseif ($request->status == 'I') {
                $register_status = 'I';
                $presence_status = 'P';
            } else {
                $register_status = 'E';
                $presence_status = 'AE';
            }
            $data = Presence::where('id', $request->id)->update(['register_status' => $register_status, 'presence_status' => $presence_status]);
            return response()->json($data);
        }

        public function updatePresence(Request $request)
        {
            $data = Presence::where('id', $request->id)->update(['presence_status' => $request->status]);
            return response()->json($data);
        }

        public function updateStatus(Request $request)
        {
            \DB::connection('tenant')->beginTransaction();
            $data = Meeting::where('id', $request->id);
            $status = $data->update(['status' => $request->status]);
            
            // as after status 0 , global scope may restrict to fetch that meeting again
            $data = Meeting::withoutGlobalScopes()->where('id', $request->id)->first();
            
            if(!$data) {
                return response()->json(['status' => false, 'msg' => 'Meeting not found'], 422);
            }
            
            if (in_array($data->meeting_type, [2, 3]) && ($data->meeting_date_type == 1)) {
                $checkRes = $this->meetingService->deleteVideoMeeting($data->id);
            }
            DB::connection('tenant')->commit();
            return response()->json($status);
        }

        public function updateUserPresence(Request $request)
        {
            //var_dump($request->all());exit;
            $prenseData = [
                'register_status'  => $request->regStatus,
                'with_meal_status' => $request->with_meal_status];
            if (isset($request->video_status)) {
                $prenseData['video_presence_status'] = $request->video_status;
                $prenseData['presence_status'] = ($request->video_status == 1) ? 'ANE' : $request->preStatus;
            } else {
                $prenseData['presence_status'] = $request->preStatus;
            }

            $data = Presence::updateOrCreate(['workshop_id' => $request->wid, 'user_id' => Auth::user()->id, 'meeting_id' => $request->meetingid], $prenseData);
            return response()->json($data);
        }

        public function getUserPresence(Request $request)
        {
            $data = Presence::where('workshop_id', $request->workshop_id)->where('meeting_id', $request->meeting_id)->where('user_id', Auth::user()->id)->first();
            return response()->json($data);
        }

        public function deleteMeeting($id)
        {
            $res = 0;
            if (Meeting::where('id', $id)->delete())
                $res = 1;
            return response()->json($res);
        }

        public function saveTask(Request $request)
        {

            $validator = Validator::make($request->all(), [
                'task_text'    => 'required',
                'milestone_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 201); //validation false return errors
            }
            if ($request->assign_for == 2) {
                return response()->json(['status' => $this->saveCollectiveTask($request)]);
            }
            $date_convert = str_replace('/', '-', $request->end_date);
            $status = 0;
            $taskUsers = [];
            $lastTaskId = Task::insertGetId([
                'workshop_id'        => $request->workshop_id,
                'meeting_id'         => $request->meeting_id,
                'topic_id'           => $request->topic_id,
                'task_text'          => $request->task_text,
                'milestone_id'       => $request->milestone_id,
                'task_created_by_id' => Auth::user()->id,
                'activity_type_id'   => $request->activity_type,
                'start_date'         => date('Y-m-d'),
                'end_date'           => (empty($request->end_date)) ? date('Y-m-d') : date('Y-m-d', strtotime($date_convert)),
                'assign_for'         => (!empty($request->user_id)) ? 0 : 1,
                'status'             => 1,
            ]);
            if ($lastTaskId > 0) {
                if (!empty(json_decode($request->user_id)) || json_decode($request->user_id) != 0 || json_decode($request->user_id) != NULL) {
                    foreach (json_decode($request->user_id) as $values) {
                        $taskUsers[] = ['task_id' => $lastTaskId, 'user_id' => $values, 'task_status' => 1];
                    }
                }
                TaskUser::insert($taskUsers);
                $status = 1;
            }
            return response()->json(['status' => $status]);
        }

        public function saveCollectiveTask($request)
        {

            $date_convert = str_replace('/', '-', $request->end_date);
            $status = 0;
            $taskUsers = [];
            if (!empty(json_decode($request->user_id)) || json_decode($request->user_id) != 0 || json_decode($request->user_id) != NULL) {
                foreach (json_decode($request->user_id) as $values) {
                    $lastTaskId = Task::insertGetId([
                        'workshop_id'        => $request->workshop_id,
                        'meeting_id'         => $request->meeting_id,
                        'topic_id'           => $request->topic_id,
                        'task_text'          => $request->task_text,
                        'milestone_id'       => $request->milestone_id,
                        'task_created_by_id' => Auth::user()->id,
                        'activity_type_id'   => $request->activity_type,
                        'start_date'         => date('Y-m-d'),
                        'end_date'           => (empty($request->end_date)) ? date('Y-m-d') : date('Y-m-d', strtotime($date_convert)),
                        'assign_for'         => 2,
                        'status'             => 1,
                    ]);
                    if ($lastTaskId > 0) {
                        $taskUsers[] = ['task_id' => $lastTaskId, 'user_id' => $values, 'task_status' => 1];
                    }
                }
                $status = TaskUser::insert($taskUsers);
            } else {
                if (isset($request->taskFrom) && $request->taskFrom == 'REPD') {
                    $users = WorkshopMeta::where('role', '!=', 3)->where(['workshop_id' => $request->workshop_id])->groupBy('user_id')->get(['user_id']);
                    foreach ($users as $values) {

                        $lastTaskId = Task::insertGetId([
                            'workshop_id'        => $request->workshop_id,
                            'meeting_id'         => $request->meeting_id,
                            'topic_id'           => $request->topic_id,
                            'task_text'          => $request->task_text,
                            'milestone_id'       => $request->milestone_id,
                            'task_created_by_id' => Auth::user()->id,
                            'activity_type_id'   => $request->activity_type,
                            'start_date'         => date('Y-m-d'),
                            'end_date'           => (empty($request->end_date)) ? date('Y-m-d') : date('Y-m-d', strtotime($date_convert)),
                            'assign_for'         => 2,
                            'status'             => 1,
                        ]);
                        if ($lastTaskId > 0) {
                            $taskUsers[] = ['task_id' => $lastTaskId, 'user_id' => $values->user_id, 'task_status' => 1];
                        }
                    }
                    $status = TaskUser::insert($taskUsers);
                }
            }
            return $status;
        }

        public function getTopics($mid = 1, $type = 'repd')
        {
            try {
                $taskData = [];
                $result['meeting_data'] = Meeting::find($mid);
                $result['workshop_data'] = Workshop::with('meta_by_role')->find($result['meeting_data']->workshop_id);
                $prepdData = [];
                $order_by = "CAST(list_order AS UNSIGNED) ASC";
                $topics = Topic::where('meeting_id', $mid)->with(['docs'])->orderByRaw($order_by)->get();
                $result['topic'] = $this->restructureRecursive($topics);
                $result['count'] = DB::connection('tenant')->select("select count(id) as total,sum(reuse) as reuse_total from topics where meeting_id = " . $mid);
                $notes = TopicNote::where('user_id', Auth::user()->id)->where('meeting_id', $mid)->get();
                //New admin notes count for workshop admin
                $adminNotes = collect(TopicAdminNote::with('user')->where(['meeting_id' => $mid])->get());

                $notesArray = $notesData = $discussionData = $adminNotesArray = [];
                $result['color'] = Color::get();
                if ($type == 'repd') {
                    $tasks = Task::with('task_user', 'workshop', 'milestone.projects')->orderBy('topic_id', 'ASC')->get();
                    $i = 0;
                    foreach ($tasks as $val) {
                        if ($val->topic_id != NULL) {
                            if (isset($taskArray[$val->topic_id])) {
                                $taskArray[$val->topic_id]['data'][] = $val;
                            } else {
                                $taskArray[$val->topic_id] = ['data' => [$val]];
                                $i++;
                            }
                        }
                    }
                }
                foreach ($notes as $val) {
                    $updated_at = $val->updated_at ? ($val->updated_at)->toDateTimeString() : null;
                    $notesArray[$val->topic_id] = ['id' => $val->id, 'topic_note' => ($val->topic_note == NULL) ? '' : $val->topic_note, 'updated_at' => $updated_at];
                }
                // foreach ($adminNotes as $val) {
                //     $adminNotesArray[$val->topic_id] = ['id' => $val->id, 'admin_notes_updated_at' => $val->notes_updated_at];
                // }
                foreach ($topics as $val) {
                    // dd();
                    $adminNotesCount = $adminNotes->where('topic_id', $val->id)->where('is_archived', FALSE)->count();
                    if (isset($notesArray[$val->id])) {
                        //admin notes count by vijay 17/01/2019
                        $admin_updated = $adminNotes->where('user_id', Auth::user()->id)->where('topic_id', $val->id)->pluck('notes_updated_at')->first();
                        $notesData[$val->id] = ['id' => $notesArray[$val->id]['id'], 'topic_note' => $notesArray[$val->id]['topic_note'], 'admin_notes_updated_at' => $admin_updated, 'flag' => 1, 'admin_notes_count' => $adminNotesCount, 'updated_at' => $notesArray[$val->id]['updated_at'],];
                    } else {
                        $notesData[$val->id] = ['id' => 0, 'topic_note' => '', 'flag' => 1, 'admin_notes_updated_at' => '', 'admin_notes_count' => $adminNotesCount, 'updated_at' => ''];
                    }
                    if ($type == 'repd') {
                        $prepdData[$val->id] = ['id' => $val->id, 'discussion' => str_replace('\t', '&#0010;', nl2br($val->discussion)), 'decision' => str_replace('\t', '&#0010;', nl2br($val->decision)), 'flag_dis' => 1, 'flag_dec' => 1];


//prepare task final array
                        if (isset($taskArray[$val->id])) {
                            $taskData[$val->id] = $taskArray[$val->id];
                        } else {
                            $taskData[$val->id] = ['data' => NULL];
                        }
                    }
                }
                if ($type == 'repd') {
                    $result['prepd_data'] = $prepdData;
                    $result['task_data'] = $taskData;
                }
                $result['notes'] = $notesData;
                return response()->json($result);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        public function redacteur(Request $request)
        {
            $result['workshop_data'] = Workshop::with('meta_by_role')->find($request->wid);
            return response()->json($result);
        }

        public function restructureRecursive($array, $parentId = 0)
        {
            $branch = [];
            foreach ($array as $element) {
                if ($element->parent_id == $parentId) {
                    $children = $this->restructureRecursive($array, $element->id);
                    if ($children) {
                        $element->children = $children;
                    }
                    $branch[] = $element;
                }
            }
            return $branch;
        }

        public function delTopics($tId)
        {
            if (Topic::find($tId)->delete()) {
                return 1;
            }
            return 0;
        }

        public function reuseTopics($tId)
        {
            $topic = Topic::find($tId);

            if (!$topic->reuse) {
                $dataArray = array_filter([(int)$tId, $topic->grand_parent_id, $topic->parent_id]);

                if (Topic::whereIn('id', $dataArray)->update(['reuse' => 1])) {
                    return 1;
                } else {
                    return 0;
                }
            } else {
                $data = Topic::where('parent_id', $tId)->orWhere('grand_parent_id', $tId)->get(['id'])->toArray();
                array_push($data, $tId);
                if (Topic::whereIn('id', $data)->update(['reuse' => 0])) {
                    return 1;
                } else {
                    return 0;
                }
            }
        }

        public function reuseAll(Request $request)
        {
            if (DB::connection('tenant')->statement("update topics set reuse = 1-" . $request->reuse . " where meeting_id = " . $request->meeting_id))
                return 1;
            else
                return 0;
        }

        public function addTopic(Request $request)
        {
            $dataArray = [];
//        $lastTopic = Topic::where('meeting_id', $request->meeting_id)->latest()->first();

            $lastTopic = Topic::where('meeting_id', $request->meeting_id)->orderByRaw("CAST(list_order AS INT) desc")->first();
            if ($request->level == 1) {
                $dataArray['grand_parent_id'] = NULL;
                $dataArray['parent_id'] = NULL;
                $dataArray['level'] = 1;
            } elseif ($request->level == 2) {
                $dataArray['grand_parent_id'] = NULL;
                $dataArray['parent_id'] = $request->parent_id;
                $dataArray['level'] = 2;
            } elseif ($request->level == 3) {
                $dataArray['grand_parent_id'] = $request->grand_parent_id;
                $dataArray['parent_id'] = $request->parent_id;
                $dataArray['level'] = 3;
            }
            $dataArray['topic_title'] = $request->topic_title;
            $dataArray['meeting_id'] = $request->meeting_id;
            $dataArray['workshop_id'] = $request->workshop_id;
            $dataArray['list_order'] = ($lastTopic) ? ($lastTopic->list_order + 1) : 0;
            $topic = Topic::create($dataArray);
            return $topic;
        }

        public function addDocsToTopic(Request $request)
        {
            $document_ids = json_decode($request->document_id);
            foreach ($document_ids as $key => $value) {
                $dataArray[] = ['document_id' => $value, 'topic_id' => $request->topic_id, 'created_by_user_id' => Auth::user()->id];
            }
            if (DB::connection('tenant')->table('topic_documents')->insert($dataArray)) {
                return 1;
            }
            return 0;
        }

        public function removeTopicDocs(Request $request)
        {
            if (TopicDocuments::find($request->doc_id)->delete()) {
                return 1;
            }
            return 0;
        }

        public function saveTopicText(Request $request)
        {
            if (Topic::find($request->topic_id)->update(['topic_title' => $request->text])) {
                return 1;
            }
            return 0;
        }

        public function saveRedacteur(Request $request)
        {
            if (Meeting::find($request->meetingid)->update(['redacteur' => $request->redacteur])) {
                return 1;
            }
            return 0;
        }

        public function saveTopicNote(Request $request)
        {
            $status = 0;
            $res = TopicNote::updateOrCreate(
                ['meeting_id' => $request->meeting_id, 'topic_id' => $request->topic_id, 'user_id' => Auth::user()->id], ['meeting_id' => $request->meeting_id, 'topic_id' => $request->topic_id, 'user_id' => Auth::user()->id, 'topic_note' => $request->topic_note]
            );
            if ($res) {
                $status = 1;
            }
            return response()->json(['updated_rec' => $res, 'status' => $status]);
        }

        public function saveTopicDiscussion(Request $request)
        {
            $postData = [];

            $postData['decision'] = $request->decision;


            $postData['discussion'] = $request->discussion;
            $status = 0;
            $res = Topic::updateOrCreate(['id' => $request->id], $postData);
            if ($res) {
                $status = 1;
            }
            return response()->json(['updated_rec' => $res, 'status' => $status]);
        }

        public function validatePREPD(Request $request)
        {

            $presenceData = $emails = $guest_email = $token = $new_member_email = $newUsers = $workshopMetaData = [];
            $status = 0;
            $email_to = '';
            $new_member_email = [];


//        $workshop_data = WorkshopMeta::where('workshop_id', $request->workshop_id)->whereIn('role', array(0, 1, 2))->orWhere('role', 3)->where('meeting_id', $request->event_id)->get();
            $workshop_data = Workshop::with(['meta_data' => function ($query) use ($request) {
                $query->whereIn('role', [0, 1, 2])->orWhere('role', 3)->where('meeting_id', $request->event_id);
            }])->where('id', $request->workshop_id)->first();
            $meeting_data = Meeting::find($request->event_id);
// generate prepd pdf
            $data = ['wid' => $request->workshop_id, 'mid' => $request->event_id];
            $pdfData = $this->core->prepdPdf($data);
            if (empty($pdfData['pdf_name'])) {
                $status = 0;
                return response()->json($status);
            }
            // var_dump($pdfData);exit;
            $domain = strtok($_SERVER['SERVER_NAME'], '.');
            $WorkshopName = $this->core->Unaccent(str_replace(' ', '-', $workshop_data->workshop_name));
            //saving file to s3
            $fileName = $this->core->localToS3Upload($domain, $WorkshopName, 'PREPD', $pdfData['pdf_name']);
            if (empty($fileName)) {
                $status = 0;
                return response()->json($status);
            }

//external users insert and send emails
            if ($request->invite_emails != NULL) {
                $decode_emails = json_decode($request->invite_emails);
                foreach ($decode_emails as $val) {

//$emails[]=$val->email;
                    if (!is_int($val->id)) {
                        $random_string = generateRandomString();
                        if ($val->member_type == 'G' || $val->member_type == 'g') {
                            $get_ids = User::where('email', strtolower($val->email))->pluck('id')->first();
                            if (!empty($get_ids)) {
                                $guest = ['user_id' => $get_ids, 'meeting_id' => $request->event_id, 'workshop_id' => $request->workshop_id, 'url_type' => 'prepd', 'identifier' => $random_string];
                                $user_Id = Guest::insertGetId($guest);
                                $userId = $get_ids;
                                $guest_email[] = strtolower($val->email);
                            } else {
                                $newUsers = ['fname' => $val->fname, 'lname' => $val->lname, 'email' => strtolower($val->email), 'password' => Hash::make(strtolower($val->email)), 'role' => 'M3'];
                                $userId = User::insertGetId($newUsers);
                                $guest = ['user_id' => $userId, 'meeting_id' => $request->event_id, 'workshop_id' => $request->workshop_id, 'url_type' => 'prepd', 'identifier' => $random_string];
                                $guestId = Guest::insertGetId($guest);

                                $guest_email[] = strtolower($val->email);
                            }

                            $workshopMetaData[] = ['workshop_id' => $request->workshop_id, 'meeting_id' => $request->event_id, 'user_id' => $userId, 'role' => '3'];
//                        $params = ['emails' => $val->email, 'workshop_id' => $request->workshop_id, 'meeting_id' => $request->event_id, 'user_id' => $userId, 'user_type' => 'guest'];

                            $token[strtolower($val->email)] = $random_string;
//                        $this->sendGuestInvitationEmail($params);
                        } else {
                            $get_ids = User::where('email', strtolower($val->email))->first();
                            if (isset($get_ids->id)) {
                                $result = User::where('email', strtolower($val->email))->update(['password' => Hash::make(strtolower($val->email)), 'role' => 'M2', 'login_count' => '0']);
                                $userId = $get_ids->id;
                            } else {
                                $hostname = $this->getHostNameData();
                                $hostCode = DB::connection('mysql')->table('hostname_codes')->where(['fqdn' => $hostname->fqdn])->first(['hash']);
                                $randCode = generateRandomValue(4);
                                $newCode = setPasscode($hostCode->hash, $randCode);

                                $newUsers = ['fname' => $val->fname, 'lname' => $val->lname, 'email' => strtolower($val->email), 'password' => Hash::make(strtolower($val->email)), 'role' => 'M2', 'login_code' => $newCode['userCode'], 'hash_code' => $newCode['hashCode']];
                                $userId = User::insertGetId($newUsers);

                                if ($userId) {
                                    $user = User::find($userId);
                                    $entity = Entity::create(['long_name' => $user->society, 'short_name' => $user->society, 'address1' => $user->address, 'address2' => $user->fqdn, 'zip_code' => $user->postal, 'city' => $user->city, 'country' => $user->country, 'phone' => $user->phone, 'email' => $user->email, 'entity_type_id' => 2]);

                                    EntityUser::create(['user_id' => $user->id, 'entity_id' => $entity->id, 'entity_label' => '']);
                                }
                            }

                            $workshopMetaData[] = ['workshop_id' => $request->workshop_id, 'meeting_id' => $request->event_id, 'user_id' => $userId, 'role' => '0'];
                            $new_member_email[] = strtolower($val->email);
                        }
                    } else {
                        $role_check = WorkshopMeta::whereIn('role', [1, 2])->where('workshop_id', $request->workshop_id)->pluck('user_id')->toArray();
                        if (!in_array($val->id, $role_check)) {
                            $workshopMetaData[] = ['workshop_id' => $request->workshop_id, 'meeting_id' => $request->event_id, 'user_id' => $val->id, 'role' => '0'];
                        }
                    }
                }

                if (!empty($workshopMetaData)) {
                    WorkshopMeta::insert($workshopMetaData);
                }
            }

            $exsit_guest = Guest::with('user')->where('workshop_id', $request->workshop_id)->where('meeting_id', $request->event_id)->where('url_type', 'doodle')->get();
            foreach ($exsit_guest as $check) {
                $random_string = generateRandomString();
                $guest = ['user_id' => $check->user->id, 'meeting_id' => $request->event_id, 'workshop_id' => $request->workshop_id, 'url_type' => 'prepd', 'identifier' => $random_string];
                $guestId = Guest::insertGetId($guest);

                $guest_email[] = strtolower($check->user->email);
                $token[strtolower($check->user->email)] = $random_string;
            }
            if (!empty($new_member_email)) {
                $params = ['emails' => $new_member_email, 'workshop_id' => $request->workshop_id, 'meeting_id' => $request->event_id, 'user_id' => 0, 'user_type' => 'm'];
                $this->sendNewMemberInvitationEmail($params);
            }
            if (!empty($guest_email)) {
                $currUserFname = Auth::user()->fname;
                $currUserLname = Auth::user()->lname;
                $currUserEmail = Auth::user()->email;
                $params = ['emails' => $guest_email, 'workshop_id' => $request->workshop_id, 'meeting_id' => $request->event_id, 'user_id' => 0, 'user_type' => 'g', 'url_type' => 'prepd', 'token' => $token, 'current_user_fn' => $currUserFname, 'current_user_ln' => $currUserLname, 'current_user_email' => $currUserEmail];

                $this->sendGuestInvitationEmail($params);
            }

//save in regular documents

            RegularDocument::insertGetId([
                'workshop_id'        => $request->workshop_id,
                'event_id'           => $request->event_id,
                'created_by_user_id' => Auth::user()->id,
                'issuer_id'          => 1,
                'document_type_id'   => 2,
                'document_title'     => $pdfData['title'],
                'document_file'      => $fileName,
                'increment_number'   => $pdfData['inc_number'],
            ]);
// update or create presense list
            $presenceList = Presence::where('workshop_id', $request->workshop_id)->where('meeting_id', $request->event_id)->pluck('user_id', 'id')->toArray();
//        return response($workshop_data->meta_data);
            $_workshop_data = Workshop::with(['meta_data' => function ($query) use ($request) {
                $query->whereIn('role', [0, 1, 2])->orWhere('role', 3)->where('meeting_id', $request->event_id);
            }])->where('id', $request->workshop_id)->first();
            if (count($_workshop_data->meta_data) > 0) {
                foreach ($_workshop_data->meta_data as $k => $val) {
                    if (!in_array($val->user_id, $presenceList)) {
                        $presenceData[] = [
                            'workshop_id'      => $request->workshop_id,
                            'meeting_id'       => $request->event_id,
                            'user_id'          => $val->user_id,
                            'register_status'  => ($val->role == 1 || $val->role == 2) ? 'I' : 'NI',
                            'presence_status'  => ($val->role == 1 || $val->role == 2) ? 'P' : 'ANE',
                            'with_meal_status' => $this->addMealStatus($val, $request),
                        ];
                    }
                    if ($val->user->email != '') {
                        if ($val->role == 1)
                            $email_to = $val->user->email;
                        elseif ($val->role == 2)
                            $emails[] = $val->user->email;
                        elseif ($val->role == 0)
                            $emails[] = $val->user->email;
                    }
                }
                if (!empty($presenceData)) {
                    Presence::insert($presenceData);
                }
            }

//update prepd status (meeting table)
            if (Meeting::where('id', $request->event_id)->update(['prepd_published_on' => dateConvert(NULL, 'Y-m-d H:i:s'), 'prepd_published_by_user_id' => Auth::user()->id, 'validated_prepd' => 1, 'redacteur' => $request->redacteur])) {
                $status = 1;
            }

            if (count($emails) > 0) {
//send prepd mail
                $currUserFname = Auth::user()->fname;
                $currUserLname = Auth::user()->lname;
                $currUserEmail = Auth::user()->email;

                $dataMail = $this->getMailData($workshop_data, $meeting_data, 'agenda_email_setting');

                $subject = $dataMail['subject'];
                $route_prepd = $dataMail['route_prepd'];
                $mailData['mail'] = ['subject' => $subject, 'emails' => $emails, 'email_to' => $email_to, 'workshop_data' => $workshop_data, 'meeting_data' => $meeting_data, 'current_user_fn' => $currUserFname, 'current_user_ln' => $currUserLname, 'current_user_email' => $currUserEmail, 'url' => $route_prepd];
                $this->core->SendMassEmail($mailData, 'prepd_validate');
                $workshopUser = WorkshopMeta::with('workshop')->where('workshop_id', $request->workshop_id)/*->where('user_id', '!=', Auth::user()->id)*/ ->where('role', '!=', 3)->get();
                $heading = 'Envoi de l\'ordre du jour';
                $msg = $this->getPushData($request->workshop_id, $request->event_id, 'agenda_push_setting');
                $msgEN = $this->getPushData($request->workshop_id, $request->event_id, 'agenda_push_setting', 'EN');
                $type = 'agenda';
                // var_dump($workshopUser, $request->event_id, $msg, $msgEN, $type);exit;
                $this->sendNoti($workshopUser, $request->event_id, $msg, $msgEN, $type);
            }
            return response()->json($status);
        }

        public function validateREPD(Request $request)
        {
            $guest_email = [];
            $status = 0;
            $email_to = '';
            $workshop_data = Workshop::with('meta')->find($request->workshop_id);
            $meeting_data = Meeting::find($request->event_id);

// generate prepd pdf
            $data = ['wid' => $request->workshop_id, 'mid' => $request->event_id];
            $pdfData = $this->core->repdPdf($data);

            $domain = strtok($_SERVER['SERVER_NAME'], '.');
            $WorkshopName = $this->core->Unaccent(str_replace(' ', '-', $workshop_data->workshop_name));
            //saving file to s3
            $fileName = $this->core->localToS3Upload($domain, $WorkshopName, 'REPD', $pdfData['pdf_name']);

//save in regular documents

//issuer id  , document type id pending :: ask
            RegularDocument::insertGetId([
                'workshop_id'        => $request->workshop_id,
                'event_id'           => $request->event_id,
                'created_by_user_id' => Auth::user()->id,
                'issuer_id'          => 1,
                'document_type_id'   => 3,
                'document_title'     => $pdfData['title'],
                'document_file'      => $fileName,
                'increment_number'   => $pdfData['inc_number'],
            ]);


// update task status
            $taskCheck = Task::where('workshop_id', $request->workshop_id)->where('meeting_id', $request->event_id)->count();

            if ($taskCheck > 0) {
                $res = Task::where('workshop_id', $request->workshop_id)->where('meeting_id', $request->event_id)->update(['status' => 1]);
            }

//update repd status (meeting table)
            if (Meeting::where('id', $request->event_id)->update(['repd_published_on' => dateConvert(NULL, 'Y-m-d H:i:s'), 'repd_published_by_user_id' => Auth::user()->id, 'validated_repd' => 1])) {
                $status = 1;
            }

//make user mail array
            $emails = [];
            if (count($workshop_data->meta) > 0) {
                foreach ($workshop_data->meta as $k => $val) {

                    if ($val->user->email != '') {
                        if ($val->role == 1) {
                            $email_to = $val->user->email;
                        } elseif ($val->role == 3) {
                            $random_string = generateRandomString();

                            $guest = ['user_id' => $val->user->id, 'meeting_id' => $request->event_id, 'workshop_id' => $request->workshop_id, 'url_type' => 'repd', 'identifier' => $random_string];
                            $guestId = Guest::insertGetId($guest);

                            $guest_email[] = $val->user->email;
                            $token[strtolower($val->user->email)] = $random_string;
                        } else {
                            $emails[] = $val->user->email;
                        }
                    }
                }
            }
            if (!empty($guest_email)) {
                $dataMail = $this->getMailData($workshop_data, $meeting_data, 'decision_email_setting');
                $subject = $dataMail['subject'];

                $currUserFname = Auth::user()->fname;
                $currUserLname = Auth::user()->lname;
                $currUserEmail = Auth::user()->email;
                $params = ['subject' => $subject, 'emails' => $guest_email, 'workshop_id' => $request->workshop_id, 'meeting_id' => $request->event_id, 'user_id' => 0, 'user_type' => 'g', 'url_type' => 'repd', 'token' => $token, 'current_user_fn' => $currUserFname, 'current_user_ln' => $currUserLname, 'current_user_email' => $currUserEmail, 'task_check' => $taskCheck];

                $this->sendGuestInvitationEmail($params);
            }
//external users emails

            if (count($emails) > 0) {
                $WorkshopName = $workshop_data->workshop_name;
                $startDdate = dateConvert($meeting_data->date, 'd M Y');

//send repd mail
                $dataMail = $this->getMailData($workshop_data, $meeting_data, 'decision_email_setting', $taskCheck);
                $subject = $dataMail['subject'];
                $route_repd = $dataMail['route_repd'];
                $route_task = $dataMail['route_task'];
                $mailData['mail'] = ['subject' => $subject, 'emails' => $emails, 'email_to' => $email_to, 'workshop_data' => $workshop_data, 'meeting_data' => $meeting_data, 'current_user_fn' => Auth::user()->fname, 'current_user_ln' => Auth::user()->lname, 'current_user_email' => Auth::user()->email, 'url_repd' => $route_repd, 'url_task' => $route_task, 'task_check' => $taskCheck];
                $this->core->SendMassEmail($mailData, 'repd_validate');
//send task mail

                if ($taskCheck > 0) {
                    $AlltaskCheck = Task::where(['workshop_id' => $request->workshop_id, 'meeting_id' => $request->event_id, 'assign_for' => 0])->count();

                    if ($AlltaskCheck > 0) {
                        $task = Task::with('task_user')->where(['workshop_id' => $request->workshop_id, 'meeting_id' => $request->event_id, 'assign_for' => 0])->get();
                        $emails = [];
                        foreach ($task as $key => $data) {
                            foreach ($data->task_user as $key => $taskUser) {
                                $emails[] = $taskUser->user->email;
                            }
                        }
                    }
                    $dataMail = $this->getMailData($workshop_data, $meeting_data, 'job_email_setting');
                    $subject = $dataMail['subject'];
                    $mailData['mail'] = ['subject' => $subject, 'emails' => $emails, 'email_to' => $email_to, 'workshop_data' => $workshop_data, 'meeting_data' => $meeting_data, 'current_user_fn' => Auth::user()->fname, 'current_user_ln' => Auth::user()->lname, 'current_user_email' => Auth::user()->email, 'url_repd' => $route_repd, 'url_task' => $route_task, 'task_check' => $taskCheck];
                    if (!isset($email_to)) {
                        unset($mailData['mail']['email_to']);
                    }

                    $this->core->SendMassEmail($mailData, 'repd_task_mail');

                }
            }
            //for notification
            $workshopUser = WorkshopMeta::with('workshop')->where('workshop_id', $request->workshop_id)->where('role', '!=', 3)->get();
            $heading = 'Envoi de relevé de décisions';
            $msg = $this->getPushData($request->workshop_id, $request->event_id, 'decision_push_setting');
            $msgEN = $this->getPushData($request->workshop_id, $request->event_id, 'decision_push_setting', 'EN');
            $type = 'repd_past';
            $this->sendNoti($workshopUser, $request->event_id, $msg, $msgEN, $type);

            return response()->json($status);
        }

        public function repdOffline(Request $request)
        {
            $data = Meeting::where('id', $request->event_id)->update(['is_offline' => 1]);
            return response()->json($data);
        }

        public function updateTopicOrder(Request $request)
        {
            $topicIds = json_decode($request->ids);
            if (count($topicIds) > 0) {
                foreach ($topicIds as $k => $val) {
                    Topic::where('id', $val)->update(['list_order' => $k]);
                }
            }
            $result['meeting_data'] = Meeting::find($request->meeting_id);
            $result['workshop_data'] = Workshop::with('meta_by_role')->find($result['meeting_data']->workshop_id);
            $order_by = "CAST(list_order AS UNSIGNED) ASC";
            $topics = Topic::where('meeting_id', $request->meeting_id)->with(['docs'])->orderByRaw($order_by)->get();
            $result['topic'] = $this->restructureRecursive($topics);
            $notes = TopicNote::where('user_id', Auth::user()->id)->where('meeting_id', $request->meeting_id)->get();
            $notesArray = $notesData = $discussionData = [];

            foreach ($notes as $val) {
                $notesArray[$val->topic_id] = ['id' => $val->id, 'topic_note' => $val->topic_note];
            }
            foreach ($topics as $val) {
                if (isset($notesArray[$val->id])) {
                    $notesData[$val->id] = ['id' => $notesArray[$val->id]['id'], 'topic_note' => $notesArray[$val->id]['topic_note'], 'flag' => 1];
                } else {
                    $notesData[$val->id] = ['id' => 0, 'topic_note' => '', 'flag' => 1];
                }
            }

            $result['notes'] = $notesData;
            $result['status'] = 1;

            return response()->json($result);
        }

        public function addInscriptionUser(Request $request)
        {
            $presenceData = $emails = $newUsers = $workshopMetaData = [];
            $status = 0;
//external users insert
            if ($request->invite_emails != NULL) {
                $decode_emails = json_decode($request->invite_emails);
                foreach ($decode_emails as $val) {
                    $userId = $val->id;
                    if (!is_int($userId)) {
                        $hostname = $this->getHostNameData();
                        $hostCode = DB::connection('mysql')->table('hostname_codes')->where(['fqdn' => $hostname->fqdn])->first(['hash']);
                        $randCode = generateRandomValue(4);
                        $newCode = setPasscode($hostCode->hash, $randCode);

                        $newUsers = ['fname' => $val->fname, 'lname' => $val->lname, 'email' => strtolower($val->email), 'password' => Hash::make(strtolower($val->email)), 'role' => 'M2', 'login_code' => $newCode['userCode'], 'hash_code' => $newCode['hashCode']];
                        $userId = User::insertGetId($newUsers);

                        if ($userId) {
                            $user = User::find($userId);
                            $entity = Entity::create(['long_name' => $user->society, 'short_name' => $user->society, 'address1' => $user->address, 'address2' => $user->fqdn, 'zip_code' => $user->postal, 'city' => $user->city, 'country' => $user->country, 'phone' => $user->phone, 'email' => $user->email, 'entity_type_id' => 2]);

                            EntityUser::create(['user_id' => $user->id, 'entity_id' => $entity->id, 'entity_label' => '']);
                        }
                        WorkshopMeta::updateOrCreate(['workshop_id' => $request->workshop_id, 'user_id' => $userId], ['workshop_id' => $request->workshop_id, 'user_id' => $userId, 'role' => 0]);
                    } else {
                        WorkshopMeta::updateOrCreate(['workshop_id' => $request->workshop_id, 'user_id' => $userId], ['workshop_id' => $request->workshop_id, 'user_id' => $userId]);
                    }
                }
            }

            $workshop_data = Workshop::with('meta')->find($request->workshop_id);
//$meeting_data  = Meeting::find($request->event_id);
// update or create presense list
            $presenceList = Presence::where('workshop_id', $request->workshop_id)->where('meeting_id', $request->meeting_id)->pluck('user_id', 'id')->toArray();

            if (count($workshop_data->meta) > 0) {
                foreach ($workshop_data->meta as $k => $val) {
                    if (!in_array($val->user_id, $presenceList)) {
                        $presenceData[] = [
                            'workshop_id'      => $request->workshop_id,
                            'meeting_id'       => $request->meeting_id,
                            'user_id'          => $val->user_id,
                            'register_status'  => ($val->role == 1 || $val->role == 2) ? 'I' : 'NI',
                            'presence_status'  => ($val->role == 1 || $val->role == 2) ? 'P' : 'ANE',
                            'with_meal_status' => $this->addMealStatus($val, $request),
                        ];
                    }
                    if ($val->user->email != '') {
                        $emails[] = $val->user->email;
                    }
                }
                if (!empty($presenceData)) {
                    Presence::insert($presenceData);
                }
                $status = 1;
            }

            if (count($emails) > 0) {
//send  mail
//$mailData['mail']=['subject'=>'','emails'=>$emails,'workshop_data'=>$workshop_data,'meeting_data'=>''];
//$this->core->SendMassEmail($mailData,'inscription');
            }
            return response()->json($status);
        }

        public function getMailData($workshop_data, $meeting_data, $key, $taskCheck = 0)
        {
            $currUserFname = Auth::user()->fname;
            $currUserLname = Auth::user()->lname;
            $currUserEmail = Auth::user()->email;
            $settings = getSettingData($key);
            $member = workshopValidatorPresident($workshop_data);
            $orgDetail = getOrgDetail();
            $keywords = [
                '[[UserFirstName]]', '[[UserLastName]]', '[[UserEmail]]', '[[WorkshopLongName]]', '[[WorkshopShortName]]', '[[WorkshopPresidentFullName]]',
                '[[WorkshopvalidatorFullName]]', '[[WorkshopMeetingName]]', '[[WorkshopMeetingDate]]', '[[WorkshopMeetingTime]]', '[[WorkshopMeetingAddress]]',
                '[[ValidatorEmail]]', '[[PresidentEmail]]', '[[PresidentPhone]]', '[[OrgName]]',
                '[[OrgShortName]]',
            ];
            $values = [
                $currUserFname, $currUserLname, $currUserEmail, $workshop_data->workshop_name, $workshop_data->code1,$member['p']['fullname'], $member['v']['fullname'],
                $meeting_data->name, dateConvert($meeting_data->date, 'l d/m/Y'), timeConvert($meeting_data->date . ' ' . $meeting_data->start_time, ' h\hi'), $meeting_data->place, $member['v']['email'], $member['p']['email'], $member['p']['phone'], $orgDetail->name_org, $orgDetail->acronym,
            ];

            $subject = ((str_replace($keywords, $values, $settings->email_subject)));
            //$subject = htmlspecialchars_decode(utf8_decode(str_replace($keywords, $values, $settings->email_subject)));
            $route_prepd = route('redirect-app-url', ['url' => str_rot13('organiser/commissions/' . $workshop_data->id . '/meeting/' . $meeting_data->id . '/agenda/preparing-agenda')]);

            $this->tenancy->website();
            $hostdata = $this->tenancy->hostname();
            $acc_id = $hostdata->id;
            // $acc_id=1;
            $superAdminPermission = DB::connection('mysql')->table('account_settings')->where('account_id', $acc_id)->first(['project_enable']);
            if ($superAdminPermission->project_enable == 1) {

                $route_task = $this->sendNotesLink($workshop_data, $meeting_data, $taskCheck);

            } else {
                $route_task = route('redirect-app-url', ['url' => str_rot13('organiser/commissions/' . $workshop_data->id . '/tasks/all-tasks')]);
            }
            $route_repd = route('redirect-app-url', ['url' => str_rot13('organiser/commissions/' . $workshop_data->id . '/meeting/' . $meeting_data->id . '/dicision/prepare-statement')]);
            return ['subject' => $subject, 'route_repd' => $route_repd, 'route_task' => $route_task, 'route_prepd' => $route_prepd];
        }

        public function getUserMailData($key,$workshop_data=[])
        {
            $currUserFname = Auth::user()->fname;
            $currUserLname = Auth::user()->lname;
            $currUserEmail = Auth::user()->email;
            $settings = getSettingData($key);
            $orgDetail = getOrgDetail();

            $keywords = [
                '[[UserFirstName]]', '[[UserLastName]]', '[[UserEmail]]', '[[WorkshopLongName]]', '[[WorkshopShortName]]', '[[WorkshopPresidentFullName]]',
                '[[WorkshopvalidatorFullName]]', '[[WorkshopMeetingName]]', '[[WorkshopMeetingDate]]', '[[WorkshopMeetingTime]]', '[[WorkshopMeetingAddress]]',
                '[[ValidatorEmail]]', '[[PresidentEmail]]', '[[PresidentPhone]]', '[[OrgName]]',
                '[[OrgShortName]]',
            ];
            if(!empty($workshop_data)){
                $member = workshopValidatorPresident($workshop_data);
                $values = [
                    $currUserFname, $currUserLname, $currUserEmail, $workshop_data->workshop_name, $workshop_data->code1,$member['p']['fullname'], $member['v']['fullname'], '', '', '', '',$member['v']['email'], $member['p']['email'], $member['p']['phone'],$orgDetail->name_org, $orgDetail->acronym,
                ];
            }
            else{
            $values = [
                '', '', '', '', '', '', '',
                '', '', '', '', '', '','', $orgDetail->name_org, $orgDetail->acronym,
            ];
        }

            $subject = htmlspecialchars_decode(utf8_decode(str_replace($keywords, $values, $settings->email_subject)));

            return ['subject' => $subject, 'url' => url('/')];
        }

        public function sendNoti($workshopUser, $lastMeetingId = '', $msg, $msgEN, $type)
        {
            $tokens = [];
            if (count($workshopUser) > 0) {
                $notification = [];
                $orgDetail = getOrgDetail();
                $workShop = $workshopUser->unique('user_id');
                foreach ($workShop as $item) {
                    if (!empty($item->user) && !empty($item->user->fcm_token)) {
                        $data[] = ["user_id" => $item->user->id, "orgname" => $orgDetail->name_org, "type" => $type, "meeting_id" => $lastMeetingId, "workshop_id" => $item->workshop->id, "workshop_name" => $item->workshop->workshop_name, "cell_id" => ''];
                        $tokens[] = $item->user->fcm_token;
                        @$lang[] = json_decode($item->user->setting);
                    }
                }

                $tokens = array_unique($tokens);
                if (isset($data) && count($data) > 0) {
                    $useData = $this->unique_multidim_array($data, 'user_id');
                    foreach ($useData as $k => $datum) {
                        if (isset($tokens[$k]) && !empty($tokens[$k])) {

                            if (isset($lang[$k]->lang) && $lang[$k]->lang == 'EN') {
                                $send = $this->PushNotification->sendNotificationForAll($datum, $msgEN['title'], @$tokens[$k], $msgEN['msg']);

                            } else {
                                $send = $this->PushNotification->sendNotificationForAll($datum, $msg['title'], @$tokens[$k], $msg['msg']);

                            }

                            //$send = $this->PushNotification->sendNotificationForAll($datum, $heading, @$tokens[$k], $msg);
                            /* if ($send) {
    
                             }*/

                        }
                        if (isset($lang[$k]->lang) && $lang[$k]->lang == 'EN') {
                            $notification[] = [
                                'from_id'           => Auth::user()->id,
                                'to_id'             => $datum['user_id'],
                                'title'             => $msgEN['title'],
                                'message'           => $msgEN['msg'],
                                'json_message_data' => json_encode($datum)/**/,
                                'type'              => $type,
                                'created_at'        => Carbon::now('Europe/Paris')->format('Y-m-d H:i:s'),
                            ];
                        } else {
                            $notification[] = [
                                'from_id'           => Auth::user()->id,
                                'to_id'             => $datum['user_id'],
                                'title'             => $msg['title'],
                                'message'           => $msg['msg'],
                                'json_message_data' => json_encode($datum)/**/,
                                'type'              => $type,
                                'created_at'        => Carbon::now('Europe/Paris')->format('Y-m-d H:i:s'),
                            ];
                        }
                    }

                    if (count($notification) > 0) {
                        Notification::insert($this->unique_multidim_array($notification, 'to_id'));
                    }
                }
            }
        }

        public function inviteMemberPresence(Request $request)
        {
            //var_dump($request->all());exit;
            $flag = 0;
            $lastMeetingId = $request->meeting_id;
            $workshopMetaData = [];
            $allUser = [];
            $emails = [];
            $_emails = [];
            if ($request->userData != NULL) {
                $inviteData = json_decode($request->userData);
                foreach ($inviteData as $val) {
                    $random_string = generateRandomString();
                    if (!is_int($val->id)) {
                        if ($val->member_type == 'G' || $val->member_type == 'g') {
                            $get_ids = User::where('email', $val->email)->pluck('id');
                            if ($get_ids != NULL && count($get_ids) > 0) {
                                $guest = ['user_id' => $get_ids[0], 'meeting_id' => $lastMeetingId, 'workshop_id' => $request->workshop_id, 'url_type' => 'doodle', 'identifier' => $random_string];
                                $userId = Guest::insertGetId($guest);
                                $userId = $get_ids[0];
                                $allUser[] = $userId;
                                $flag = ($userId > 0) ? 1 : 0;
                            } else {
                                $newUsers = ['fname' => $val->fname, 'lname' => $val->lname, 'email' => $val->email, 'password' => Hash::make($val->email), 'role' => 'M3'];
                                $userId = User::insertGetId($newUsers);
                                $guest = ['user_id' => $userId, 'meeting_id' => $lastMeetingId, 'workshop_id' => $request->workshop_id, 'url_type' => 'doodle', 'identifier' => $random_string];
                                $guestId = Guest::insertGetId($guest);
                                $flag = ($guestId > 0) ? 1 : 0;
                                $allUser[] = $userId;
                            }
                            $workshopMetaData[] = ['workshop_id' => $request->workshop_id, 'meeting_id' => $lastMeetingId, 'user_id' => $userId, 'role' => '3'];
                            $guest_email[] = $val->email;
                            $token[strtolower($val->email)] = $random_string;
                        } else {
                            $get_ids = User::where('email', $val->email)->first();
                            if (isset($get_ids->id)) {
                                $result = User::where('email', $val->email)->update(['role' => 'M2']);
                                $userId = $get_ids->id;
                                $allUser[] = $userId;
                            } else {
                                $hostname = $this->getHostNameData();
                                $hostCode = DB::connection('mysql')->table('hostname_codes')->where(['fqdn' => $hostname->fqdn])->first(['hash']);
                                $randCode = generateRandomValue(4);
                                $newCode = setPasscode($hostCode->hash, $randCode);

                                $newUsers = ['fname' => $val->fname, 'lname' => $val->lname, 'email' => strtolower($val->email), 'password' => Hash::make(strtolower($val->email)), 'role' => 'M2', 'login_code' => $newCode['userCode'], 'hash_code' => $newCode['hashCode']];
                                $userId = User::insertGetId($newUsers);
                                $allUser[] = $userId;
                                if ($userId) {
                                    $user = User::find($userId);
                                    $entity = Entity::create(['long_name' => $user->society, 'short_name' => $user->society, 'address1' => $user->address, 'address2' => $user->fqdn, 'zip_code' => $user->postal, 'city' => $user->city, 'country' => $user->country, 'phone' => $user->phone, 'email' => $user->email, 'entity_type_id' => 2]);

                                    EntityUser::create(['user_id' => $user->id, 'entity_id' => $entity->id, 'entity_label' => '']);
                                }
                            }

                            $new_member_email[] = $val->email;
                            $_emails[] = $val->email;
                            $memberType[] = $val->member_type;
                            $workshopMetaData[] = ['workshop_id' => $request->workshop_id, 'meeting_id' => $lastMeetingId, 'user_id' => $userId, 'role' => '0'];
                        }
                    }
                    else {
                        $metaCheck = WorkshopMeta::where('workshop_id', $request->workshop_id)->where('user_id', $val->id)->first();

                        if (!$metaCheck) {
                            $workshopMetaData[] = ['workshop_id' => $request->workshop_id, 'meeting_id' => $lastMeetingId, 'user_id' => $val->id, 'role' => '0'];
                            $emails[] = $val->email;
                            $allUser[] = $val->id;
                        } else {
                            // $flag = 1;
                            $_emails[] = $val->email;
                            $allUser[] = $val->id;
                        }
                    }
                }
                if (!empty($workshopMetaData) || !empty($allUser)) {
                    //send email to new member of workshop
                    if (!empty($emails)) {
                        foreach ($emails as $mail) {
                            $params = ['emails' => $mail, 'workshop_id' => $request->workshop_id, 'meeting_id' => $lastMeetingId, 'user_id' => 0, 'user_type' => 'm'];
                            $this->sendNewWorkshopMemberInvitationEmail($params);
                        }
                    }
                    //getting meeting data
                    $meet=Meeting::find($lastMeetingId);
                    //getting emails array
                    $mailArray = array_unique(array_merge($emails, $_emails));
                    //checking that mail array should not empty
                    if (!empty($mailArray)) {
                        if (isset($meet->id) && $meet->meeting_date_type == 1) {
                            //adding mail code for member for save meeting
                            $params = ['emails' => $mailArray, 'workshop_id' => $request->workshop_id, 'meeting_id' => $lastMeetingId, 'user_id' => 0, 'user_type' => 'm', 'type' => 'save_meeting'];
                            // dump($params);
                            $this->sendNewMeetingInvitation($params);
                        } else {
                            $params = ['emails' => $mailArray, 'workshop_id' => $request->workshop_id, 'meeting_id' => $request->meeting_id, 'user_id' => 0, 'user_type' => 'm'];
                            $this->sendMeetingInvitationEmail($params);
                        }
                    }

                    if (!empty($allUser)) {
                        //adding user presense entry for that meeting
                        foreach ($allUser as $vals) {
                            $presenceData[] = [
                                'workshop_id'     => $request->workshop_id,
                                'meeting_id'      => $lastMeetingId,
                                'user_id'         => $vals,
                                'register_status' => 'NI',
                                'presence_status' => 'ANE',
                            ];
                        }
                    }
                    // insert presense data
                    if (!empty($presenceData)) {
                        Presence::insert($presenceData);
                    }


                    $workShopInsert = WorkshopMeta::insert($workshopMetaData);
                    $flag = ($workShopInsert > 0) ? 1 : 0;
                    if (!empty($new_member_email)) {
                        $params = ['emails' => $new_member_email, 'workshop_id' => $request->workshop_id, 'meeting_id' => $lastMeetingId, 'user_id' => 0, 'user_type' => 'm'];
                        $this->sendNewMemberInvitationEmail($params);
                        $this->sendNewWorkshopMemberInvitationEmail($params);
                    }

                }
                if (!empty($guest_email)) {
                    $urlType=(isset($meet->id) && $meet->meeting_date_type == 1)?'save_meeting':'doodle';
                    $params = ['emails' => $guest_email, 'workshop_id' => $request->workshop_id, 'meeting_id' => $lastMeetingId, 'user_id' => 0, 'user_type' => 'g', 'url_type' => $urlType, 'token' => $token];
                  //  dump($params);

                    $this->sendGuestInvitationEmail($params);
                }
            }
            return response($flag);
        }

        public function getHostNameData()
        {
            $this->tenancy->website();
            $hostdata = $this->tenancy->hostname();
            $domain = @explode('.' . env('HOST_SUFFIX'), $hostdata->fqdn)[0];
            //$domain = config('constants.HOST_SUFFIX');
            session('hostdata', ['subdomain' => $domain]);
            return $this->tenancy->hostname();
        }


        public function unique_multidim_array($array, $key)
        {
            $temp_array = [];
            $i = 0;
            $key_array = [];

            foreach ($array as $val) {
                if (!in_array($val[$key], $key_array)) {
                    $key_array[$i] = $val[$key];
                    $temp_array[$i] = $val;
                }
                $i++;
            }
            return $temp_array;
        }

        public function addMealStatus($val, $request)
        {
            if (isset($request->with_meal) && ($request->with_meal != 0 || $request->with_meal != 2) && ($val->role == 1 || $val->role == 2)) {
                if ($request->with_meal == 3)
                    return 1;
                elseif ($request->with_meal == 4)
                    return 3;
                else
                    return 5;
            } else {
                return 0;
            }
        }

        public function sendNotesLink($workshop_data, $meeting_data, $taskCheck)
        {
            if ($taskCheck > 0) {
                $tasks = Task::with('milestone.projects:id')->where('workshop_id', $workshop_data->id)->where('meeting_id', $meeting_data->id)->groupBy('milestone_id')->get();
                foreach ($tasks as $task) {
                    if (isset($task->milestone->projects->id)) {
                        $route_task[] = route('redirect-app-url', ['url' => str_rot13('organiser/commissions/' . $workshop_data->id . '/projects/' . $task->milestone->projects->id . "/milestone/" . $task->milestone_id)]);
                    }
                }
                return $route_task;
            } else {
                return $route_task = route('redirect-app-url', ['url' => str_rot13('organiser/commissions/' . $workshop_data->id . '/projects')]);
            }
        }

        public function fetchMeetingDates(Request $request)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'month' => 'required|integer:min:1|max:12',
                ]);

                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422);
                }
//                return $this->meetingService->fetchMeetingDates($request->all());
                return $this->meetingService->getOccupiedDates($request);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }

        }

        public function sendVideoHybridMail($meeting, $type, $toUser = [], $parms = [])
        {
            if (App::isLocale('en')) {
                $lang = 'EN';
            } else {
                $lang = 'FR';
            }
            $video = [
                'modify_meeting'                => 'save_video_meeting_date_email_setting',
                'save_meeting'                  => 'save_new_video_meeting_date_email_setting',
                'doodle_email_setting'          => 'doodle_video_meeting_email_setting',
                'doodle_reminder_email_setting' => 'doodle_video_meeting_reminder_email_setting',
                'doodle_final_date'             => 'doodle_video_meeting_final_date',
            ];
            $hybrid = [
                'modify_meeting'                => 'save_hybrid_meeting_date_email_setting',
                'save_meeting'                  => 'save_new_hybrid_meeting_date_email_setting',
                'doodle_email_setting'          => 'doodle_hybrid_meeting_email_setting',
                'doodle_reminder_email_setting' => 'doodle_hybrid_meeting_reminder_email_setting',
                'doodle_final_date'             => 'doodle_hybrid_meeting_final_date',
            ];
            if (in_array($meeting->meeting_type, [2, 3])) {
                if ($meeting->meeting_type == 2) {
                    $key = $video[$type];
                    $data = $this->meetingService->prepareEmailData($key, ['meetingId' => $meeting->id, 'participant' => $toUser, 'parms' => $parms]);
                    if (isset($parms['token'])) {
                        foreach ($toUser as $email) {
                            $data['mail']['token'] = $parms['token'][$email];
                            event(new MeetingEvent('email_template.dynamic_workshop_template', $data, $email));
                        }
                    } else {
                        event(new MeetingEvent('email_template.dynamic_workshop_template', $data, $toUser));
                    }
                    return TRUE;
                } elseif ($meeting->meeting_type == 3) {
                    $key = $hybrid[$type];
                    $data = $this->meetingService->prepareEmailData($key, ['meetingId' => $meeting->id, 'participant' => $toUser, 'parms' => $parms]);
                    if (isset($parms['token'])) {
                        foreach ($toUser as $email) {
                            $data['mail']['token'] = $parms['token'][$email];
                            event(new MeetingEvent('email_template.dynamic_workshop_template', $data, $email));
                        }

                    } else {
                        event(new MeetingEvent('email_template.dynamic_workshop_template', $data, $toUser));
                    }
                    return TRUE;
                } else {
                    return FALSE;
                }
            }

        }

        public function digitalMeetingPresense($mid)
        {
            try {
                $meeting = Meeting::findOrFail($mid);
                $url = $this->meetingService->accessVideoMeeting($meeting);
                if (!empty($url)) {
                    $presenceData = [
                        'presence_status' => 'P',
                    ];
                    if ($meeting->meeting_type == 3) {
                        $presenceData['presence_status'] = 'ANE';
                        $presenceData['video_presence_status'] = 'P';
                    }

                    Presence::where(['workshop_id' => $meeting->workshop_id, 'user_id' => Auth::user()->id, 'meeting_id' => $meeting->id])->update($presenceData);

                    return redirect()->away($url);
                } else{
                    if(Auth::check()){
                        return redirect()->route('signin');
                    }else{
                        return redirect()->route('signin');
                    }
                }
            } catch (\Exception $e) {
                return redirect()->route('signin');
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }

        public function updateVideoPresence(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'mid'    => 'required|exists:tenant.meetings,id',
                'id'     => 'required|exists:tenant.presences,id',
                'status' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 422); //validation false return errors
            }
            $prenseData['video_presence_status'] = $request->status;
            $prenseData['presence_status'] = $request->preStatus;
            $prenseData['register_status'] = $request->regStatus;
            $data = Presence::where('id', $request->id)->update($prenseData);
            return response()->json(Presence::find($request->id));
        }
    }
