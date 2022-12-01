<?php

namespace App\Http\Controllers;

use App\Message;
use App\MessageCategory;
use App\MessageReply;
use App\MessageLike;
use App\Notification;
use App\PersonalMessage;
use App\PersonalMessageReply;
use App\User;
use App\Workshop;
use App\WorkshopMeta;
use Auth;
use Carbon\Carbon;
use DB;
use Hash;
use Illuminate\Http\Request;
use Validator;
class MessageController extends Controller
{
    private $core;

    public function __construct(PushNotificationController $PushNotification)
    {
        $this->PushNotification = $PushNotification;
        $this->core = app(\App\Http\Controllers\CoreController::class);
    }

    public function addMessage(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'messages_text' => 'required',
            'category_id' => 'required',
            'workshop_id' => 'required',


        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 400);
        }
        $notification = [];
        $tokens = [];
        $newRec = Message::updateOrCreate(['id' => $request->id], ['workshop_id' => $request->workshop_id, 'category_id' => $request->category_id, 'messages_text' => $request->messages_text, 'user_id' => Auth::user()->id]);
        $newRec['message_replies'] = [];
        $count=0;
        if(!empty($newRec->messageReplies)){
            $count=$newRec['messageReplies']->groupBy('user_id')->count();
        }

        $newRec['replyCount'] = ($count==0)?1:$count;
        $newRec['user'] = User::where('id', $newRec->user_id)->first();
         $this->sendMessageMail($request, 'add');
        //if sentTo is 1 send massage to secretary else send to all user member in workshop
        if($request->sendTo==1){
            $workshopUser = WorkshopMeta::with(['workshop', 'user'])->where('workshop_id', $request->workshop_id)->where('user_id', '!=', Auth::user()->id)->where('role', '!=', 3)->where('role', '!=', 0)->get();
        }else{
            $workshopUser = WorkshopMeta::with(['workshop', 'user'])->where('workshop_id', $request->workshop_id)->where('user_id', '!=', Auth::user()->id)->where('role', '!=', 3)->get();
        }
        if (count($workshopUser) > 0) {
            $orgDetail = getOrgDetail();
            $userObj = [];
            foreach ($workshopUser as $item) {
                if (!empty($item->user)) {
                    $data[] = ["user_id" => $item->user->id, "orgname" => $orgDetail->name_org, "type" => "convo", "meeting_id" => "", "workshop_id" => $request->workshop_id, "workshop_name" => $item->workshop->workshop_name, "cell_id" => $request->category_id];
                    $userObj[] = $item->user;
                    $tokens[] = $item->user->fcm_token;
                    @$lang[] = json_decode($item->user->setting);

                }
            }
            $msgCategory = MessageCategory::find($request->category_id);
            $notiKeywords = [
                '[[WorkshopLongName]]',
                '[[MessageCategory]]',
                '[[WorkshopShortName]]',
                '[[WorkshopPresidentFullName]]',
                '[[WorkshopvalidatorFullName]]',
                '[[PresidentEmail]]',
                '[[ValidatorEmail]]',
                '[[UserFirstName]]',
                '[[UserLastName]]',
                '[[OrgShortName]]',
                '[[OrgName]]',
                '[[PresidentPhone]]'
            ];
            $tokens = array_unique($tokens);
            $workshop = Workshop::withoutGlobalScopes()->find($request->input('workshop_id'));
            $president = WorkshopMeta::with('user')->where('workshop_id', $request->input('workshop_id'))->where('role', 1)->first();
            $validator = WorkshopMeta::with('user')->where('workshop_id', $request->input('workshop_id'))->where('role', 2)->first();
            foreach ($data as $k => $datum) {
                if (isset($tokens[$k]) && !empty($tokens[$k])) {
                    $notiSettings = getSettingData('msg_push_setting', 0, @$lang[$k]->lang);
                    $notiValues = [
                        $workshop ? $workshop->workshop_name : "",
                        $msgCategory->category_name,
                        $workshop ? $workshop->code1 : "",
                        $president ? "{$president->user->fname} {$president->user->lname}" : "",
                        $validator ? "{$validator->user->fname} {$validator->user->lname}" : "",
                        $president ? $president->user->email : "",
                        $validator ? $validator->user->email : "",
                        isset($userObj[$k]) ? $userObj[$k]->fname : "",
                        isset($userObj[$k]) ? $userObj[$k]->lname : "",
                        $orgDetail->acronym,
                        $orgDetail->name_org,
                        $president ? $president->user->phone : "",
                    ];
                    //For remove extra br space By vijay
                    $notificationText=preg_replace('/(<p>&nbsp;<\/p>)+$/','',preg_replace('/\s\s+/', ' ',$notiSettings->notification_text));

                    $msg = $this->core->Unaccent(html_entity_decode(strip_tags(((str_replace($notiKeywords, $notiValues, $notificationText)))), ENT_QUOTES));

                    $heading = $notiSettings->title;

                    $send = $this->PushNotification->sendNotificationForAll($datum, $heading, @$tokens[$k], $msg);
//                    if ($send) {
                    $notification[] = [
                        'from_id' => Auth::user()->id,
                        'to_id' => $datum['user_id'],
                        'title' => $heading,
                        'message' => @$msg,
                        'json_message_data' => json_encode($datum),
                        'type' => 'convo',
                        'created_at' => Carbon::now('Europe/Paris')->format('Y-m-d H:i:s'),
                    ];
//                    }
                }

            }
            if (count($notification) > 0) {
                // var_dump($notification);
                Notification::insert($notification);
            }
        }
        return response()->json($newRec);
    }


    public function addReply(Request $request)
    {
        $notification = [];
        $email = [];
        $newRec = MessageReply::updateOrCreate(['id' => $request->id], ['message_id' => $request->message_id, 'reply_text' => $request->reply_text, 'user_id' => $request->user_id]);
        $newRec['user'] = User::where('id', $newRec->user_id)->first();

        $messageData = Message::with('user')->find($request->message_id);
        $count=0;
        if(!empty($messageData->messageReplies)){
            $count=$messageData['messageReplies']->groupBy('user_id')->count();
        }
        $newRec['replyCount'] = ($count==0)?1:$count;
        $email[] = Auth::user()->email;
        $checkReply = MessageReply::with('user')->where('message_id', $request->message_id)->get();
        if ($checkReply != null && $checkReply->count() > 0 && $request->sendTo==0) {
            foreach ($checkReply as $key => $value) {
                $email[] = $value->user->email;
            }
        }
        //if sentTo is 1 send massage to secretary else send to all user member in workshop
        if($request->sendTo==1){
        $workshopUser = WorkshopMeta::with('workshop')->where('workshop_id', $request->workshop_id)->where('user_id', '!=', Auth::user()->id)->where('role', '!=', 3)->where('role', '!=', 0)->get();
        }
        else{
        $workshopUser = WorkshopMeta::with('workshop')->where('workshop_id', $request->workshop_id)->where('user_id', '!=', Auth::user()->id)->where('role', '!=', 3)->get();
        }
        $email[] = $newRec['user']->email;
        if($request->sendTo==1){
            foreach ($workshopUser as $k => $val) {
                if ($val->user->email != '') {
                    $email[] = $val->user->email;
                }
            }
        }
        $request['email'] = array_unique($email);
        $request['category_id'] = $messageData->category_id;
        $this->sendMessageMail($request, 'msg-reply');
        $workshopUser = WorkshopMeta::with(['workshop', 'user'])->where('workshop_id', $request->workshop_id)->where('user_id', '!=', Auth::user()->id)->where('role', '!=', 3)->get();
        $userObj = [];
        if (count($workshopUser) > 0) {
            $orgDetail = getOrgDetail();
            foreach ($workshopUser as $item) {
                if (!empty($item->user)) {
                    $data[] = ["user_id" => $item->user->id, "orgname" => $orgDetail->name_org, "type" => "convo", "meeting_id" => "", "workshop_id" => $request->workshop_id, "workshop_name" => $item->workshop->workshop_name, "cell_id" => $messageData->category_id];
                    $userObj[] = $item->user;
                    $tokens[] = $item->user->fcm_token;
                    @$lang[] = json_decode($item->user->setting);
                }
            }

            $msgCategory = MessageCategory::find($request->category_id);
            $notiKeywords = [
                '[[WorkshopLongName]]',
                '[[MessageCategory]]',
                '[[WorkshopShortName]]',
                '[[WorkshopPresidentFullName]]',
                '[[WorkshopvalidatorFullName]]',
                '[[PresidentEmail]]',
                '[[ValidatorEmail]]',
                '[[UserFirstName]]',
                '[[UserLastName]]',
                '[[OrgShortName]]',
                '[[OrgName]]',
                '[[PresidentPhone]]'
            ];

            $tokens = array_unique($tokens);
    
            $workshop = Workshop::withoutGlobalScopes()->find($request->input('workshop_id'));
            $president = WorkshopMeta::with('user')->where('workshop_id', $request->input('workshop_id'))->where('role', 1)->first();
            $validator = WorkshopMeta::with('user')->where('workshop_id', $request->input('workshop_id'))->where('role', 2)->first();
    
            foreach ($data as $k => $datum) {
                if (isset($tokens[$k]) && !empty($tokens[$k])) {
                    $notiSettings = getSettingData('message_reply_push_setting', 0, @$lang[$k]->lang);
                    $notiValues = [
                        $workshopUser[0]->workshop->workshop_name,
                        $msgCategory->category_name,
                        $workshop ? $workshop->code1 : "",
                        $president ? "{$president->user->fname} {$president->user->lname}" : "",
                        $validator ? "{$validator->user->fname} {$validator->user->lname}" : "",
                        $president ? $president->user->email : "",
                        $validator ? $validator->user->email : "",
                        isset($userObj[$k]) ? $userObj[$k]->fname : "",
                        isset($userObj[$k]) ? $userObj[$k]->lname : "",
                        $orgDetail->acronym,
                        $orgDetail->name_org,
                        $president ? $president->user->phone : "",
                    ];
                    $notificationText=preg_replace('/(<p>&nbsp;<\/p>)+$/','',preg_replace('/\s\s+/', ' ',$notiSettings->notification_text));

                    $msg = $this->core->Unaccent(html_entity_decode(strip_tags(((str_replace($notiKeywords, $notiValues, $notificationText)))), ENT_QUOTES));
                    $heading = $notiSettings->title;
                    $send = $this->PushNotification->sendNotificationForAll($datum, $heading, @$tokens[$k], $msg);
//                    if ($send) {
                    $notification[] = [
                        'from_id' => Auth::user()->id,
                        'to_id' => $datum['user_id'],
                        'title' => $heading,
                        'message' => $msg,
                        'json_message_data' => json_encode($datum),
                        'type' => 'convo',
                        'created_at' => Carbon::now('Europe/Paris')->format('Y-m-d H:i:s'),
                    ];
//                    }
                }
            }
            if (count($notification) > 0) {
                Notification::insert($notification);
            }
        }
        return response()->json($newRec);
    }

    /*add reply Email changes by vijay*/
    public function sendMessageMail($request, $type = null)
    {
        //if sentTo is 1 send massage to secretary else send to all user member in workshop
        if($request->sendTo==1){
            $workshop_data = Workshop::with(['meta'=>(function($q){
                $q->where('role', '!=', 3)->where('role', '!=', 0);
            })])->withoutGlobalScopes()->find($request->workshop_id);
        }
        else{
            $workshop_data = Workshop::with('meta')->withoutGlobalScopes()->find($request->workshop_id);
        }
        if(empty($workshop_data)){
            return false;
        }
        $message_category = MessageCategory::find($request->category_id);
        $emails = [];
        $email_to = '';
        switch ($type) {
            case 'msg-reply':
                $settings = getSettingData('msg_replies_email_setting');
                $emails = $request->email;
                $route = route('redirect-app-url', ['url' => str_rot13('organiser/commissions/' . $request->workshop_id . '/messages')]);
                break;
            case 'personal-msg':
                $settings = getSettingData('msg_replies_email_setting');
                $emails = $request->email;
                break;
            default:
                if (isset($workshop_data->meta) && (count($workshop_data->meta) > 0)) {
                    foreach ($workshop_data->meta as $k => $val) {
                        if ($val->user->email != '') {
                            $emails[] = $val->user->email;
                        }
                    }
                    //adding as dan said user should know what other are seeing
                    /*6:44 PM should i get email if i'm a person who replying to a message
                     *Dan, 6:44 PM yes so that I know what others are receiving
                    */
                    $email[] = Auth::user()->email;
                }
                $settings = getSettingData('msg_email_setting');
                $route = route('redirect-app-url', ['url' => str_rot13('organiser/commissions/' . $request->workshop_id . '/messages')]);
                break;
        }
        $member=workshopValidatorPresident($workshop_data);
        $orgDetail=getOrgDetail();

        $keywords = ['[[WorkshopLongName]]',
            '[[WorkshopShortName]]',
            '[[WorkshopPresidentFullName]]',
            '[[WorkshopvalidatorFullName]]',
            '[[UserFirstName]]',
            '[[UserLastName]]',
            '[[UserEmail]]',
            '[[WorkshopMeetingName]]',
            '[[WorkshopMeetingDate]]',
            '[[WorkshopMeetingTime]]',
            '[[WorkshopMeetingAddress]]',
            '[[ValidatorEmail]]',
            '[[PresidentEmail]]',
            '[[PresidentPhone]]',
            '[[MessageCategory]]',
            '[[OrgName]]',
            '[[OrgShortName]]',
            '[[MessageSenderFname]]',
            '[[MessageSenderLname]]',
            '[[MessageSenderEmail]]',
            ];
        $values = [$workshop_data->workshop_name, $workshop_data->code1,
            $member['p']['fullname'], $member['v']['fullname'], Auth::user()->fname, Auth::user()->lname, Auth::user()->email, '', '', '', '', $member['v']['email'],
            $member['p']['email'],$member['p']['phone'], $message_category->category_name,
            $orgDetail->name_org, $orgDetail->acronym,Auth::user()->fname,
            Auth::user()->lname,
            Auth::user()->email];
        $subject = ((str_replace($keywords, $values, $settings->email_subject)));


        // return $mailData['mail'];die;
        if ($type == 'msg-reply') {


            foreach ($emails as $key => $emailAddress) {
                $mailData['mail'] = ['subject' => $this->core->Unaccent($subject), 'email' => $emailAddress, 'message_text' => $request->messages_text, 'workshop_data' => $workshop_data, 'message_catagory' => $message_category->category_name, 'msg_url' => $route, 'message_text' => $request->reply_text];
                $this->core->SendEmail($mailData, 'message_reply_mail');
            }
        } else {
            foreach ($emails as $key => $emailAddress) {
                $mailData['mail'] = ['subject' => $this->core->Unaccent($subject), 'email' => $emailAddress, 'message_text' => $request->messages_text, 'workshop_data' => $workshop_data, 'message_catagory' => $message_category->category_name, 'msg_url' => $route, 'message_text' => $request->messages_text];
                $this->core->SendEmail($mailData, 'message_mail');
            }
        }
    }

    public function getMessage(Request $request)
    {
        $msgIds = MessageLike::where('workshop_id', $request->wid)->where('status', 1)->where('user_id', Auth::user()->id)->whereNull('message_reply_id')->pluck('message_id', 'id');
        $query = Message::with('user', 'messageReplies', 'messageLikes', 'replyLikes', 'countLikesMessage', 'countLikesReply')->where('workshop_id', $request->wid);
        if ($request->cid > 0 && $request->liked == 0) {
            $query->where('category_id', $request->cid);
        } else if ($request->cid > 0 && $request->liked == 1) {
            $query->where('category_id', $request->cid)->whereIn('id', $msgIds);
        } else if ($request->liked == 1) {
            $query->whereIn('id', $msgIds);
        } else if ($request->cid > 0) {
            $query->where('category_id', $request->cid);
        }
        $data['msg'] = $query->orderBy('id', 'desc')->get();
        // for geting reply user count
        foreach($data['msg'] as $k=>$v){
            $count=0;
            if($v['messageReplies']!=null){
                $count=$v['messageReplies']->groupBy('user_id')->count();
            }

            $data['msg'][$k]['replyCount'] = ($count==0)?1:$count;
        }
        //end
        $memberCount = Workshop::with(['workshop_meta'=>function($q){
            $q->groupBy('user_id');
        }])->where('id',$request->wid)->withoutGlobalScopes()->first(['id','workshop_name']);
        $data['memberCount']=['workshop_meta_count'=>count($memberCount->workshop_meta),'workshop_name'=>$memberCount->workshop_name];
        // $data['likes'] =$query->orderBy('id','desc')->get();
        return response()->json($data);
    }

    // public function getMessageByCategory(Request $request)
    // {
    //      $data=Message::with('user','messageReplies','messageLikes','replyLikes')->where('workshop_id',$request->wid)->where('category_id',$request->cid)->orderBy('id','desc')->get();
    //     return response()->json($data);
    // }

    // public function getLikeUnlikedMessage(Request $request)
    // {
    //     if($request->type==1){
    //         $msgIds = MessageLike::where('workshop_id',$request->wid)->pluck('message_id','id');
    //         $data=Message::with('user','messageReplies','messageLikes','replyLikes')->where('workshop_id',$request->wid)->whereIn('id',$msgIds)->orderBy('id','desc')->get();
    //     } else {
    //         $data=Message::with('user','messageReplies','messageLikes','replyLikes')->where('workshop_id',$request->wid)->orderBy('id','desc')->get();
    //     }
    //     return response()->json($data);
    // }

    public function messageLikeUnlike(Request $request)
    {
        if ($request->level == 1) {
            MessageLike::updateOrCreate(['message_id' => $request->message_id, 'user_id' => Auth::user()->id], ['workshop_id' => $request->workshop_id, 'status' => $request->status]);
        } else {
            MessageLike::updateOrCreate(['message_id' => $request->message_id, 'message_reply_id' => Null, 'user_id' => Auth::user()->id], ['workshop_id' => $request->workshop_id]);
            MessageLike::updateOrCreate(['message_id' => $request->message_id, 'message_reply_id' => $request->message_reply_id, 'user_id' => Auth::user()->id], ['workshop_id' => $request->workshop_id, 'status' => $request->status]);
        }
        return response()->json(1);
    }

    public function deleteMessage($id)
    {
        $res = 0;
        if (Message::where('id', $id)->delete())
            $res = 1;
        return response()->json($res);
    }

    public function deleteMessageReply($id)
    {
        $res = 0;
        if (MessageReply::where('id', $id)->delete())
            $res = 1;
        return response()->json($res);
    }

    public function addMessageCategory(Request $request)
    {
        $data = MessageCategory::insertGetId($request->except('id'));
        return response()->json($data);
    }

    public function updateMessageCategory(Request $request)
    {
        $data = MessageCategory::where('id', $request->id)->update(['category_name' => $request->category_name]);
        return response()->json($data);
    }

    public function getMessageCategory(Request $request)
    {
        $data = MessageCategory::where('workshop_id', $request->wid)->where('status', 1)->get();
        return response()->json($data);
    }

    public function deleteMessageCategory($id)
    {
        $res = 0;
        if (MessageCategory::where('id', $id)->update(['status' => 0]))
            $res = 1;
        return response()->json($res);
    }

    public function updateMessage(Request $request)
    {
        if ($request->update_type == 'message') {
            $status = Message::where('id', $request->id)->update(['messages_text' => $request->messages_text]);
        } else {
            $status = MessageReply::where('id', $request->id)->update(['reply_text' => $request->messages_text]);
        }
        return response()->json($status);
    }

    //perosnal message
    public function personalMessageAdd(Request $request)
    {

        if ($request->reply_message_id == null || $request->reply_message_id == 0) {
            $data = PersonalMessage::insertGetId(['to_user_id' => $request->msg_to, 'from_user_id' => Auth::user()->id, 'message_text' => $request->msg_text, 'is_read' => 0]);

            $orgDetail = getOrgDetail();
            $dataValue = ["user_id" => $request->msg_to, "orgname" => $orgDetail->name_org, "type" => "personal", "meeting_id" => "", "workshop_id" => '', "workshop_name" => '', "cell_id" => '', 'msg_id' => $data];

            $user = User::find($request->msg_to);
            $tokens = $user->fcm_token;

            //for notification

            $notiKeywords = ['[[WorkshopLongName]]',
                '[[WorkshopShortName]]',
                '[[WorkshopPresidentFullName]]',
                '[[WorkshopvalidatorFullName]]',
                '[[UserSenderFirstName]]',
                '[[UserSenderLastName]]',
                '[[UserEmail]]',
                '[[WorkshopMeetingName]]',
                '[[WorkshopMeetingDate]]',
                '[[WorkshopMeetingTime]]',
                '[[WorkshopMeetingAddress]]',
                '[[ValidatorEmail]]',
                '[[PresidentEmail]]',
                '[[MessageCategory]]',
                '[[OrgName]]',
                '[[OrgShortName]]'];
            $res = User::find($request->msg_to);
            $lang = json_decode($res->setting);
            $notiSettings = getSettingData('personal_message_push_setting', 0, @$lang->lang);
            $notiValues = ['', '',
                '', '', Auth::user()->fname, Auth::user()->lname, '', '', '', '', '', '', '', '',
                '', ''];
            $notificationText=preg_replace('/(<p>&nbsp;<\/p>)+$/','',preg_replace('/\s\s+/', ' ',$notiSettings->notification_text));

            $msg = $this->core->Unaccent(html_entity_decode(strip_tags(((str_replace($notiKeywords, $notiValues, $notificationText))))));
            $heading = $notiSettings->title;

            if (isset($tokens) && !empty($tokens)) {

                $send = $this->PushNotification->sendNotificationForAll($dataValue, $heading, @$tokens, $msg);
//                    if ($send) {
//
//                    }
            }
            $notification[] = [
                'from_id' => Auth::user()->id,
                'to_id' => $dataValue['user_id'],
                'title' => $heading,
                'message' => $msg,
                'json_message_data' => json_encode($dataValue),
                'type' => 'personal',
                'created_at' => Carbon::now('Europe/Paris')->format('Y-m-d H:i:s'),
            ];
            if (count($notification) > 0) {
                Notification::insert($notification);
            }

            $notification = [];
            /* foreach ($dataValue as $k => $datum) {
                 if (isset($tokens[$k]) && !empty($tokens[$k])) {

                     $send = $this->PushNotification->sendNotificationForAll($datum, $heading, @$tokens[$k], $msg);
 //                    if ($send) {
 //
 //                    }
                 }
                 $notification[] = [
                     'from_id' => Auth::user()->id,
                     'to_id' => $datum['user_id'],
                     'title' => $heading,
                     'message' => $msg,
                     'json_message_data' => json_encode($datum),
                     'type' => 'personal',
                     'created_at' => Carbon::now('Europe/Paris')->format('Y-m-d H:i:s'),
                 ];
             }
             if (count($notification) > 0) {
                 Notification::insert($notification);
             }*/
        } else {
            $data = PersonalMessageReply::insert([
                'personal_message_id' => $request->reply_message_id,
                'message_text' => $request->msg_text,
                'to_user_id' => $request->msg_to,
                'from_user_id' => Auth::user()->id,
                'is_read' => 0,
            ]);
        }
        if ($data) {
            $settings = getSettingData('personal_email_setting');
            $emails[] = $request->recieve_user_email;
            $keywords = [
                '[[UserFirstName]]',
                '[[UserLastName]]',
                '[[UserEmail]]',
                '[[OrgName]]',
                '[[OrgShortName]]',
                '[[MessageSenderFname]]',
                '[[MessageSenderLname]]',
                '[[MessageSenderEmail]]',
            ];
            $values = [$res->fname??'', $res->lname??'', $res->email??'',  $orgDetail->name_org, $orgDetail->acronym,Auth::user()->fname,
                Auth::user()->lname,
                Auth::user()->email];

            $subject = ((str_replace($keywords, $values, $settings->email_subject)));
            $route = route('redirect-app-url', ['url' => str_rot13('personal-message')]);

            $mailData['mail'] = ['subject' => $this->core->Unaccent($subject), 'email' => $res->email, 'message_text' => $request->msg_text, 'msg_url' => $route, 'send_user_fname' => Auth::user()->fname, 'send_user_lname' => Auth::user()->lname, 'recieve_user_fname' => $res->fname, 'recieve_user_lname' => $res->lname, 'recieve_user_email' => $res->email];
// TODO UNCOMMENT GOURAV VERMA
            $this->core->SendEmail($mailData, 'personal_message');

        }
        return response(($data) ? 1 : 0);
    }

    public function getUnreadMessageCount()
    {
        $messageCount = PersonalMessage::where(['to_user_id' => Auth::user()->id, 'is_read' => 0])->count();
        if ($messageCount == 0) {
            $messageCount = PersonalMessageReply::where(['to_user_id' => Auth::user()->id, 'is_read' => 0])->count();
        }

        return $messageCount;
    }

    public function getUnreadMessage(Request $request)
    {
        $message = PersonalMessage::with(['fromUser:id,avatar,fname,lname,email', 'message_reply' => function ($query) {
            $query->where('inbox_delete', 0)->with('user:id,avatar,fname,lname,email');
        }])
            ->where(['to_user_id' => Auth::user()->id, 'inbox_delete' => 0])
            ->orderBy('created_at', 'desc')->paginate(15);

        PersonalMessage::where(['to_user_id' => Auth::user()->id])->update(['is_read' => 1]);
        PersonalMessageReply::where(['to_user_id' => Auth::user()->id])->update(['is_read' => 1]);
        return response()->json($message);
    }

    public function getSentPersonalMessage(Request $request)
    {
        $message = PersonalMessage::with(['fromUser:id,avatar,fname,lname,email', 'message_reply' => function ($query) {
            $query->where('outbox_delete', 0)->with('user:id,avatar,fname,lname,email');
        }])
            ->where(['from_user_id' => Auth::user()->id, 'outbox_delete' => 0])
            ->orderBy('created_at', 'desc')->paginate(15);
        return response()->json($message);
    }

    public function deletePersonalMessage(Request $request)
    {
        $res = 0;
        if ($request->type == 'reply') {
            $delete_type = ($request->deleteFrom == 'inbox') ? 'inbox_delete' : 'outbox_delete';
            $res = PersonalMessageReply::where('id', $request->id)->update([$delete_type => 1]);
        } else {
            $delete_type = ($request->deleteFrom == 'inbox') ? 'inbox_delete' : 'outbox_delete';
            $res = PersonalMessage::where('id', $request->id)->update([$delete_type => 1]);
            //delete notification
            $getMessage = PersonalMessage::where('id', $request->id)->first(['created_at']);
            $allNoti = Notification::where(['to_id' => Auth::user()->id, 'type' => 'personal'])->whereDate('created_at', Carbon::parse($getMessage->created_at)->format('Y-m-d'))->get(['id', 'json_message_data']);
            if (count($allNoti) > 0) {
                foreach ($allNoti as $item) {
                    $jsonDecode = json_decode($item->json_message_data, true);
                    if (count($jsonDecode) > 0) {
                        if ($jsonDecode['msg_id'] == $request->id) {
                            Notification::where('id', $item->id)->delete();
                            break;
                        }
                    }
                }
            }

        }
        return response()->json($res);
    }


}
