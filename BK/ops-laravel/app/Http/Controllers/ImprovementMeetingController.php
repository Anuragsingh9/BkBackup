<?php

namespace App\Http\Controllers;

use App\Entity;
use App\EntityUser;
use App\Guest;
use App\Meeting;
use App\Presence;
use App\RegularDocument;
use App\Task;
use App\Topic;
use App\User;
use App\Workshop;
use App\WorkshopMeta;
use Auth;
use DB;
use Exception;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Response,Validator;
use ZipArchive;

class ImprovementMeetingController extends Controller
{


    private $core, $tenancy;

    public function __construct(PushNotificationController $PushNotification,
                                MeetingController $MeetingController
    )
    {
        $this->PushNotification = $PushNotification;
        $this->meeting = $MeetingController;
        $this->core = app(\App\Http\Controllers\CoreController::class);
        $this->tenancy = app(\Hyn\Tenancy\Environment::class);
    }

    public function getTopicsDocument($mid = 1, $type = 'repd')
    {
        $taskData = [];
        $prepdData = [];
        $order_by = "CAST(list_order AS UNSIGNED) ASC";
        $topics = Topic::where('meeting_id', $mid)->with(['docs'])->orderByRaw($order_by)->get();

        return response()->json($topics);
    }

    private function restructureRecursive($array, $parentId = 0)
    {
        $branch = array();
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

    public function zipTopicsDocument(Request $request)
    {
        $decoded = json_decode($request->links);
        $archiveFile = '';
        if (count($decoded) > 0) {
            $topics = RegularDocument::with('workshop:id,workshop_name')->whereIn('id', json_decode($request->links))->get(['document_file', 'document_title']);

            if (!file_exists('public/tempDoc')) {
                mkdir('public/tempDoc', 0777, true);
            }

            if (file_exists('public/tempDoc')) {
                //mkdir('public/tempDoc', 0777, true);

                @unlink(base_path('public' . DIRECTORY_SEPARATOR . 'files.zip'));
                foreach ($topics as $v) {
                    $file_name = str_replace(' ', '-', trim($v->document_title));
                    $ext = pathinfo($v->document_file, PATHINFO_EXTENSION);
                    if(Storage::disk('s3')->exists($v->document_file)){
                        $s3_file = Storage::disk('s3')->get($v->document_file);
                        $s3 = Storage::disk('localDocPdf');
                        $s3->put("./" . $file_name . '.' . $ext, $s3_file);
                    }
                }

// create a list of files that should be added to the archive.
                $files = glob(base_path("public" . DIRECTORY_SEPARATOR . "tempDoc/*"));

                // define the name of the archive and create a new ZipArchive instance.
                $archiveFile = base_path("public" . DIRECTORY_SEPARATOR . "files.zip");
                $archive = new ZipArchive();

                // check if the archive could be created.
                if ($archive->open($archiveFile, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
                    // loop trough all the files and add them to the archive.
                    foreach ($files as $file) {
                        if ($archive->addFile($file, basename($file))) {
                            // do something here if addFile succeeded, otherwise this statement is unnecessary and can be ignored.
                            continue;
                        }
                    }

                    // close the archive.
                    if ($archive->close()) {

                        // archive is now downloadable ...
                        array_map('unlink', array_filter((array)$files));
                        if(isset($request->typeDownload) && $request->typeDownload=='repd'){
                            return basename($archiveFile);
                        }
                        return response(basename($archiveFile));

                    } else {
                        throw new Exception("could not close zip file: " . $archive->getStatusString());
                    }
                } else {
                    throw new Exception("zip file could not be created: " . $archive->getStatusString());
                }

                //return response()->json($topics);
            }
        } else {
            return response($archiveFile);
        }
    }

    public function reValidatePREPD(Request $request)
    {
        $guest_email = [];
        $new_member_email = [];
        $data = ['wid' => $request->workshop_id, 'mid' => $request->event_id];
        $pdfData = $this->core->prepdPdf($data);
        $meetingUpdate = Meeting::where('id', $request->event_id)->update(['prepd_published_on' => dateConvert(null, 'Y-m-d H:i:s'), 'prepd_published_by_user_id' => Auth::user()->id, 'validated_prepd' => 1]);
        $workshop_data = Workshop::with(['meta_data' => function ($query) use ($request) {
            $query->whereIn('role', array(0, 1, 2))->orWhere('role', 3)->where('meeting_id', $request->event_id);
        }])->where('id', $request->workshop_id)->first();

        if ($meetingUpdate) {

            $data = ['wid' => $request->workshop_id, 'mid' => $request->event_id, 'version' => '-V' . date('Ymd')];

            $pdfData = $this->core->prepdPdf($data);
            $domain = strtok($_SERVER['SERVER_NAME'], '.');
            $WorkshopName = $this->core->Unaccent(str_replace(' ', '-', $workshop_data->workshop_name));
            //saving file to s3
            $fileName = $this->core->localToS3Upload($domain, $WorkshopName, 'PREPD', $pdfData['pdf_name']);
            //save in regular documents
            if ($fileName != null) {
                RegularDocument::create( [
                    'workshop_id' => $request->workshop_id,
                    'event_id' => $request->event_id,
                    'created_by_user_id' => Auth::user()->id,
                    'issuer_id' => 1,
                    'document_type_id' => 2,
                    'document_title' => $pdfData['title'],
                    'document_file' => $fileName,
                    'increment_number' => $pdfData['inc_number'],
                ]);
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
                $this->meeting->sendNewMemberInvitationEmail($params);
            }
            if (!empty($guest_email)) {
                $currUserFname = Auth::user()->fname;
                $currUserLname = Auth::user()->lname;
                $currUserEmail = Auth::user()->email;
                $params = ['emails' => $guest_email, 'workshop_id' => $request->workshop_id, 'meeting_id' => $request->event_id, 'user_id' => 0, 'user_type' => 'g', 'url_type' => 'prepd', 'token' => $token, 'current_user_fn' => $currUserFname, 'current_user_ln' => $currUserLname, 'current_user_email' => $currUserEmail];

                $this->meeting->sendGuestInvitationEmail($params);
            }

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
                            'with_meal_status' => $this->meeting->addMealStatus($val, $request),
                        ];
                    }
                    if (isset($val->user->email) && $val->user->email != '') {
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


            $meeting_data = Meeting::find($request->event_id);
            if (count($workshop_data->meta_data) > 0) {
                foreach ($workshop_data->meta_data as $k => $val) {
                    if (isset($val->user->email) && $val->user->email != '') {
                        if ($val->role == 1)
                            $email_to = $val->user->email;
                        elseif ($val->role == 2)
                            $emails[] = $val->user->email;
                        elseif ($val->role == 0)
                            $emails[] = $val->user->email;
                    }
                }
            }

            if (count($emails) > 0) {
                //send prepd mail
                $currUserFname = Auth::user()->fname;
                $currUserLname = Auth::user()->lname;
                $currUserEmail = Auth::user()->email;
                $dataMail = $this->meeting->getMailData($workshop_data, $meeting_data, 'agenda_email_setting');
                $subject = $dataMail['subject'];
                $route_prepd = $dataMail['route_prepd'];
                $mailData['mail'] = ['subject' => $subject, 'emails' => $emails, 'email_to' => $email_to, 'workshop_data' => $workshop_data, 'meeting_data' => $meeting_data, 'current_user_fn' => $currUserFname, 'current_user_ln' => $currUserLname, 'current_user_email' => $currUserEmail, 'url' => $route_prepd];
                $this->core->SendMassEmail($mailData, 'prepd_validate');
                $workshopUser = WorkshopMeta::with('workshop')->where('workshop_id', $request->workshop_id)/*->where('user_id', '!=', Auth::user()->id)*/
                ->where('role', '!=', 3)->get();
                $heading = 'Envoi de l\'ordre du jour';
                $msg = $this->meeting->getPushData($request->workshop_id, $request->event_id, 'agenda_push_setting');
                $msgEN = $this->meeting->getPushData($request->workshop_id, $request->event_id, 'agenda_push_setting', 'EN');
                $type = 'agenda';
//                var_dump($workshopUser, $request->event_id, $msg, $msgEN, $type);exit;
                $this->meeting->sendNoti($workshopUser, $request->event_id, $msg, $msgEN, $type);
            }
            return response()->json($meetingUpdate);
        }

        return response()->json(0);
    }

     public function reValidateREPD(Request $request){
         $guest_email = [];

        $meetingUpdate=Meeting::where('id', $request->event_id)->update(['repd_published_on' => dateConvert(null, 'Y-m-d H:i:s'), 'repd_published_by_user_id' => Auth::user()->id, 'validated_repd' => 1]);
        $workshop_data = Workshop::with(['meta_data' => function($query) use($request) {
            $query->whereIn('role', array(0, 1, 2))->orWhere('role', 3)->where('meeting_id', $request->event_id);
        }])->where('id', $request->workshop_id)->first();

        if($meetingUpdate){
            $data = ['wid' => $request->workshop_id, 'mid' => $request->event_id,'version'=>'-V'.date('Ymd')];

            // generate prepd pdf
            $pdfData = $this->core->repdPdf($data);
            
            $domain = strtok($_SERVER['SERVER_NAME'], '.');
            $WorkshopName = $this->core->Unaccent(str_replace(' ', '-', $workshop_data->workshop_name));
            //var_dump($pdfData['pdf_name']);exit;
            //saving file to s3
            $fileName=$this->core->localToS3Upload($domain,$WorkshopName,'REPD',$pdfData['pdf_name']);
            
            //save in regular documents
            //issuer id  , document type id pending :: ask
            RegularDocument::create([
                'workshop_id' => $request->workshop_id,
                'event_id' => $request->event_id,
                'created_by_user_id' => Auth::user()->id,
                'issuer_id' => 1,
                'document_type_id' =>3,
                'document_title' => $pdfData['pdf_name'],
                'document_file' => $fileName,
                'increment_number' => $pdfData['inc_number'],
            ]);
        }
        $meeting_data = Meeting::find($request->event_id);
        if (count($workshop_data->meta_data) > 0) {
            foreach ($workshop_data->meta_data as $k => $val) {
                if (isset($val->user->email) && $val->user->email != '') {
                    if ($val->role == 1)
                        $email_to = $val->user->email;
                    elseif ($val->role == 2)
                        $emails[] = $val->user->email;
                    elseif ($val->role == 0)
                        $emails[] = $val->user->email;
                    elseif ($val->role == 3) {
                        $random_string = generateRandomString();

                        $guest = ['user_id' => $val->user->id, 'meeting_id' => $request->event_id, 'workshop_id' => $request->workshop_id, 'url_type' => 'repd', 'identifier' => $random_string];
                        $guestId = Guest::insertGetId($guest);

                        $guest_email[] = $val->user->email;
                        $token[strtolower($val->user->email)] = $random_string;
                    }
                }
            }
        }
         $taskCheck = Task::where('workshop_id', $request->workshop_id)->where('meeting_id', $request->event_id)->count();

         if ($taskCheck > 0) {
             $res = Task::where('workshop_id', $request->workshop_id)->where('meeting_id', $request->event_id)->update(['status' => 1]);
         }
         if (!empty($guest_email)) {
             $dataMail = $this->meeting->getMailData($workshop_data, $meeting_data, 'decision_email_setting');
             $subject = $dataMail['subject'];

             $currUserFname = Auth::user()->fname;
             $currUserLname = Auth::user()->lname;
             $currUserEmail = Auth::user()->email;
             $params = ['subject' => $subject, 'emails' => $guest_email, 'workshop_id' => $request->workshop_id, 'meeting_id' => $request->event_id, 'user_id' => 0, 'user_type' => 'g', 'url_type' => 'repd', 'token' => $token, 'current_user_fn' => $currUserFname, 'current_user_ln' => $currUserLname, 'current_user_email' => $currUserEmail, 'task_check' => $taskCheck];

             $this->meeting->sendGuestInvitationEmail($params);
         }
        if (count($emails) > 0) {
            // update task status
            $taskCheck=Task::where('workshop_id', $request->workshop_id)->where('meeting_id', $request->event_id)->count();

            if($taskCheck>0){
                $res = Task::where('workshop_id', $request->workshop_id)->where('meeting_id', $request->event_id)->update(['status' => 1]);
            }
            $WorkshopName = $workshop_data->workshop_name;
            $startDdate = dateConvert($meeting_data->date, 'd M Y');
            //send repd mail
            $dataMail = $this->meeting->getMailData($workshop_data, $meeting_data, 'decision_email_setting');
            $subject = $dataMail['subject'];
            $route_repd = $dataMail['route_repd'];
            $route_task = $dataMail['route_task'];
            $mailData['mail'] = ['subject' => $subject, 'emails' => $emails, 'email_to' => $email_to, 'workshop_data' => $workshop_data, 'meeting_data' => $meeting_data, 'current_user_fn' => Auth::user()->fname, 'current_user_ln' => Auth::user()->lname, 'current_user_email' => Auth::user()->email, 'url_repd' => $route_repd, 'url_task' => $route_task,'task_check'=>$taskCheck];
            $this->core->SendMassEmail($mailData, 'repd_validate');
            //send task mail
            $workshopUser = WorkshopMeta::with('workshop')->where('workshop_id', $request->workshop_id)->where('role', '!=', 3)->get();
            $heading = 'Envoi de relevé de décisions';
            $msg = $this->meeting->getPushData($request->workshop_id, $request->event_id, 'decision_push_setting');
            $msgEN = $this->meeting->getPushData($request->workshop_id, $request->event_id, 'decision_push_setting', 'EN');
            $type = 'repd_past';
            $this->meeting->sendNoti($workshopUser, $request->event_id, $msg, $msgEN, $type);
            return response()->json($meetingUpdate);
        }
        return response()->json(0);
    }

    public function updateMealPresence(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'status' => 'required',
                'mid' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 201);//validation false return errors
            }
            //logic for changing status
            $meeting=Meeting::find($request->mid);
            $status = $this->updateMealStatus($request,$meeting);
            $data = Presence::where('id', $request->id)->update(array('with_meal_status' => $status));
            return response()->json(['status' => true, 'data' => $status], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 201);
        }
    }

    public function updateMealStatus($request,$meeting)
    {

        $type = $request->status;
        if($type==0){
         if($meeting->with_meal==3){
             switch ($type) {
                 case 0:
                     return 1;
                     break;
                 case 1:
                     return 2;
                     break;
                 case 2:
                     return 0;
                     break;

             }
         }elseif ($meeting->with_meal==4){
             switch ($type) {
                 case 0:
                     return 3;
                     break;
                 case 3:
                     return 4;
                     break;
                 case 4:
                     return 0;
                     break;

             }
         }elseif ($meeting->with_meal==5){
             switch ($type) {
                 case 0:
                     return 5;
                     break;
                 case 5:
                     return 6;
                     break;
                 case 6:
                     return 0;
                     break;

             }
         }
        }else {
            switch ($type) {
                case 1:
                    return 2;
                    break;
                case 2:
                    return 1;
                    break;
                case 3:
                    return 4;
                    break;
                case 4:
                    return 3;
                    break;
                case 5:
                    return 6;
                    break;
                case 6:
                    return 5;
                    break;
                default:
                    return 0;

            }
        }
    }
}

