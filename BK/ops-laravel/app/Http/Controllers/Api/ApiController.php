<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Meeting;
use App\Message;
use App\MessageCategory;
use App\MessageLike;
use App\Notification;
use App\Organisation;
use App\PersonalMessage;
use App\PersonalMessageReply;
use App\Presence;
use App\RegularDocument;
use App\Services\MeetingService;
use App\User;
use App\Workshop;
use App\WorkshopMeta;
use Auth;
use Carbon\Carbon;
use DB;
use Hyn\Tenancy\Models\Hostname;
use Illuminate\Http\Request;
use Validator;


class ApiController extends Controller
{
    private $core, $tenancy, $meeting;
    public $successStatus = 200;

    public function __construct()
    {
        $this->tenancy = app(\Hyn\Tenancy\Environment::class);
        $this->core = app(\App\Http\Controllers\CoreController::class);
        $this->meeting = app(\App\Http\Controllers\MeetingController::class);
        $this->message = app(\App\Http\Controllers\MessageController::class);
        $this->document = app(\App\Http\Controllers\DocumentController::class);
        $this->workshop = app(\App\Http\Controllers\WorkshopController::class);

    }

    public function login2(Request $request)
    {
        return 'hello';
    }

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|max:255',
            'password' => 'required|numeric|min:8,max:8',
            // 'domain' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())]);
        } else {
            $email = strtolower($request->email);
            $passCode = $request->password;
            $orignalCode = getPasscode($passCode);
 
            //new working for login using code

            $hostName = DB::connection('mysql')->table('hostname_codes')->where(['hash' => $orignalCode['mainHash']])->first(['fqdn']);

//           var_dump($orignalCode,$hostName);//exit;
            if (isset($hostName->fqdn)) {
                $getHostname = Hostname::where('fqdn', $hostName->fqdn)->first();

                $host=$this->tenancy->hostname($getHostname);
               // dump($hostName,$getHostname,$host);
                //$hostname = $this->getHostNameData();
                $getUser = User::where(['email' => $email, 'hash_code' => $request->password])->first();
                
                // var_dump($getUser);exit;
                if (Auth::loginUsingId(isset($getUser->id) ? $getUser->id : 0)) {
                    $user = Auth::user();

                    $success['token'] = $user->createToken('check')->accessToken;
                    $user->accessToken = $success['token'];
                    // $success['token'] =  '084e6c9219738b96c6b87c8d3ecb6ec3745cafa4191cf94ed65cb71c9b7493aa4ce67a005694bfb7';

                    $getUser->fcm_token = $request->fcm_token;
                    $getUser->save();
                    $graphic_setting = getSettingData('graphic_config', 1);
                    $user->graphic_setting = $graphic_setting;
                    $user->orgname = Organisation::first()->name_org;
                    $user->orgFname = Organisation::first();
                    $user->url = env("AWS_PATH", 'https://s3-eu-west-2.amazonaws.com/opsimplify.com/');
                    return response()->json(['success' => $success, 'user' => $user, 'hostname' => env('HOST_TYPE') . $getHostname->fqdn], $this->successStatus);
                } else {
                    return response()->json(['error' => 'Unauthorised'], 401);
                }
            } else {
                return response()->json(['error' => 'Unauthorised'], 401);
            }
        }
    }

    function getHostData()
    {
        $this->tenancy->website();
        $hostdata = $this->tenancy->hostname();
//        $domain = @explode('.' . config('constants.HOST_SUFFIX'), $hostdata->fqdn)[0];
        $domain = env('HOST_SUFFIX');
        //dd($hostdata);
        //session('hostdata', ['subdomain' => $domain]);
        return $this->tenancy->hostname();
    }

    public function getCommissionList(Request $request)
    {
        if (Auth::user() == null) {
            Auth::loginUsingId(Auth::guard('api')->user()->id);
        }
        //return response()->json($request->header());
        $workshopData = [];
        $workshopMeta = [];
        $user = Auth::guard('api')->user();
        // return response()->json(Auth::guard('api')->user());
        if (isset($user->id)) {
            if ($user->role == 'M1') {
                $workshopMeta = Workshop::orderBy('id', 'DESC')->get(['id', 'workshop_name']);
            } else {
                $workshopMeta_ids = WorkshopMeta::where('user_id', $user->id)->pluck('workshop_id', 'id');
                $workshopMeta = Workshop::whereIn('id', $workshopMeta_ids)->orderBy('id', 'DESC')->get(['id', 'workshop_name']);
            }
//        foreach ($workshopMeta as $value) {
//            $user_records = WorkshopMeta::where('workshop_id', $value->id)->groupBy('user_id','role')->where('role', '!=', '3')->get()->toArray();
//            $meta = WorkshopMeta::where('workshop_id', $value->id)->groupBy('user_id')->where('role', '!=', '3')->get()->toArray();
//            $value['count'] = count($meta);
//            $value['meta'] = $user_records;
//
//            $workshopData[] = $value;
//        }
        }
        $this->tenancy->website();
        $hostname = $this->tenancy->hostname();
        $mobileEnabled = DB::connection('mysql')->table('account_settings')->where('account_id', $hostname->id)->first();

        if (isset($user->setting) && !empty($user->setting)) {
            $lang = json_decode($request->user()->setting);

        } else {
            $lang = 'FR';
        }

        if (count($workshopMeta) > 0) {

            return response()->json(['status' => 200, 'data' => $workshopMeta, 'meeting_meal_enable' => $mobileEnabled->meeting_meal_enable, 'multiLoginEnabled' => $mobileEnabled->multiLoginEnabled, 'mobileAcess' => $mobileEnabled->mobile_enable, 'lang' => $lang->lang]);
        } else {
            return response()->json(['status' => 201, 'data' => $workshopData, 'mobileAcess' => $mobileEnabled->mobile_enable, 'lang' => $lang->lang]);
        }

    }

   public function getPastMettingList(Request $request)
    {
        $user = Auth::guard('api')->user();
        $isUserWAdminOrHigher = MeetingService::getInstance()->isUserWAdminOrHigher($request->input('wid'), $user);
    
        $builder = Meeting::where(
            [
                [DB::raw('concat(date," ",start_time)'), '<=', Carbon::now('Europe/Paris')->format('Y-m-d H:i:s')],
                ['workshop_id', $request->wid]
            ])
            ->orderBy('date', 'desc');
    
        // checking if user is workshop admin or higher
        if($isUserWAdminOrHigher) {
            // user is admin so remove global scope so deleted meeting can also sent to front
            $builder = $builder->withoutGlobalScopes();
        } else {
            // user is neither admin nor workshop admin then show only active meetings
            $builder = $builder->where('status', 1);
        }
        $data = $builder
            ->get(['id', 'name', 'description', 'place', 'date', 'workshop_id', 'start_time', 'validated_prepd', 'validated_repd','with_meal']);
        /*$data = Meeting::where([[DB::raw('concat(date," ",start_time)'), '<=', DB::raw('NOW()')], ['workshop_id', $request->wid]])->orderBy('date','desc')->get(['id', 'name', 'description', 'place', 'date', 'workshop_id', 'start_time','validated_prepd','validated_repd']);*/
        $data2 = [];
        foreach ($data as $key => $item) {
            $value = Presence::where('workshop_id', $request->wid)->where('meeting_id', $item->id)->where('user_id', $user->id)->first(['presence_status', 'register_status','with_meal_status']);
            $data2[$key] = $item;
            $data2[$key] = $item;
            if (!empty($value)) {
                $data2[$key]['userStatus'] = ($value->presence_status == 'AE' && $value->register_status == 'E') ? 0 : 1;
                $data2[$key]['register_status'] = $value->register_status;
                $data2[$key]['presence_status'] = $value->presence_status;
                 $data2[$key]['with_meal_status'] = $value->with_meal_status;
            } else {
                //5 for no user existence in presence
                $data2[$key]['userStatus'] = 5;
                $data2[$key]['register_status'] = '';
                $data2[$key]['with_meal_status'] = 0;

            }
        }

        if (count($data2) > 0) {

            return response()->json(['status' => 200, 'data' => $data2]);
        } else {
            return response()->json(['status' => 201, 'data' => []]);
        }
    }

    public function getfutureMettingList(Request $request)
    {
        $user = Auth::guard('api')->user();

            //New code for sorting future meeting
        // $data = Meeting::with(['doodleDates' => function ($query) {
        //     $query->/*whereDate('date', '>', Carbon::now('Europe/Paris')->format('Y-m-d'))->*/
        //     orderBy('date', 'asc');
        // }])->where('workshop_id', $request->wid)->where(function ($q) {
        //     $q->where(DB::raw('concat(date," ",start_time)'), '>=', Carbon::now('Europe/Paris')->format('Y-m-d H:i:s'))
        //         ->orWhere('meeting_date_type', 0);
        // })->orderBy('date', 'asc')->orderBy('start_time','asc')->get(['id', 'name', 'description', 'place', 'date', 'workshop_id', 'meeting_date_type', 'start_time', 'validated_prepd', 'validated_repd','with_meal']);

        /**
        	Filter Meeting when all doodle dates went past
        	New Code Update 27/03/2019
         */
        $isUserWAdminOrHigher = MeetingService::getInstance()->isUserWAdminOrHigher($request->input('wid'), $user);
    
        $builder = Meeting::with([
            'doodleDates' => function ($query) {
                $query->/*whereDate('date', '>', Carbon::now('Europe/Paris')->format('Y-m-d'))->*/
                orderBy('date', 'asc');
            }])
            ->where('workshop_id', $request->wid)
            ->where(function ($q) {
                $q->where(DB::raw('concat(date," ",start_time)'), '>=', Carbon::now('Europe/Paris')->format('Y-m-d H:i:s'))
                    ->orWhere('meeting_date_type', 0)
                    ->whereHas('doodleDates', function ($query) {
                        $query->where(DB::raw('concat(date," ",start_time)'), '>=', Carbon::now('Europe/Paris')->format('Y-m-d H:i:s'));
                    });
            })
            ->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc');
    
        // checking if user is workshop admin or higher
        if($isUserWAdminOrHigher) {
            // user is admin so remove global scope so deleted meeting can also sent to front
            $builder = $builder->withoutGlobalScopes();
        } else {
            // user is neither admin nor workshop admin then show only active meetings
            $builder = $builder->where('status', 1);
        }
        $data = $builder
            ->get([
                'id', 'name', 'description', 'place', 'date', 'workshop_id', 'meeting_date_type', 'start_time',
                'validated_prepd', 'validated_repd', 'with_meal', 'meeting_type'
            ]);
        $finalData = [];
        foreach ($data as $key => $item) {

            if ($item->meeting_date_type == 1) {
                $finalData[$key] = $item;
                $finalData[$key] = $item;
               
                $value = Presence::where('workshop_id', $request->wid)->where('meeting_id', $item->id)->where('user_id', $user->id)->first(['presence_status', 'register_status','with_meal_status']);
                if (!empty($value)) {
                    $finalData[$key]['userStatus'] = ($value->presence_status == 'AE' && $value->register_status == 'E') ? 0 : 1;
                    $finalData[$key]['register_status'] = $value->register_status;
                    $finalData[$key]['presence_status'] = $value->presence_status;
                    $finalData[$key]['with_meal_status'] = $value->with_meal_status;
                } else {
                    //5 for no user existence in presence
                    $finalData[$key]['userStatus'] = 5;
                    $finalData[$key]['register_status'] = '';
                    $finalData[$key]['presence_status'] = '';
                     $finalData[$key]['with_meal_status'] = isset($value->with_meal_status)?$value->with_meal_status:null;
                }
            } else {

                if (count($item->doodleDates) > 0) {
                    $finalData[$key] = $item;
                    $value = Presence::where('workshop_id', $request->wid)->where('meeting_id', $item->id)->where('user_id', $user->id)->first(['presence_status', 'register_status','with_meal_status']);
                    if (!empty($value)) {
                        $finalData[$key]['userStatus'] = ($value->presence_status == 'AE' && $value->register_status == 'E') ? 0 : 1;
                        $finalData[$key]['register_status'] = $value->register_status;
                        $finalData[$key]['presence_status'] = $value->presence_status;
                         $finalData[$key]['with_meal_status'] = $value->with_meal_status;
                    } else {
                        //5 for no user existence in presence
                        $finalData[$key]['userStatus'] = 5;
                        $finalData[$key]['register_status'] = '';
                        $finalData[$key]['presence_status'] = '';
                        $finalData[$key]['with_meal_status'] = 0;
                    }
                }
            }

        }
        //var_dump( $finalData);exit;
        if (count($finalData) > 0) {

            return response()->json(['status' => 200, 'data' => ($finalData)]);
        } else {
            return response()->json(['status' => 201, 'data' => []]);
        }

    }

    public function getMessageTab(Request $request)
    {
        $data = MessageCategory::where('workshop_id', $request->wid)->where('status', 1)->get();
        if (count($data) > 0) {

            return response()->json(['status' => 200, 'data' => $data]);
        } else {
            return response()->json(['status' => 201, 'data' => []]);
        }
    }

    public function getMessageList(Request $request)
    {
        // $user=Auth::user();
        $msgIds = MessageLike::where('workshop_id', $request->wid)->where('status', 1)->where('user_id', $request->user_id)->whereNull('message_reply_id')->pluck('message_id', 'id');
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

        $data = $query->orderBy('id', 'desc')->get();
        $url = 'https://s3-eu-west-2.amazonaws.com/'.env('AWS_BUCKET').'/';
        if (count($data) > 0) {
            return response()->json(['status' => 200, 'data' => $data, 'url' => $url]);
        } else {
            return response()->json(['status' => 201, 'data' => [], 'url' => $url]);
        }
    }

    public function checkHostName(Request $request)
    {
        $data = Hostname::where('fqdn', $request->hostname)->get();
        if (count($data) > 0) {
            return response()->json(['status' => 200, 'data' => $data]);
        } else {
            return response()->json(['status' => 201, 'data' => []]);
        }
    }

    public function addMessageTab(Request $request)
    {
        $data = 0;
        if ($request->workshop_id > 0) {
            $data = MessageCategory::insertGetId(['workshop_id' => $request->workshop_id, 'category_name' => $request->category_name]);
        }
        if ($data > 0) {
            $tab_list = MessageCategory::where('workshop_id', $request->workshop_id)->where('status', 1)->get();
            return response()->json(['status' => 200, 'data' => $tab_list]);
        } else {
            return response()->json(['status' => 201, 'msg' => "Tab can't be added"]);
        }

    }

    public function deleteMessageTab(Request $request)
    {
        $data = MessageCategory::where('id', $request->cid)->delete();
        if ($data > 0) {
            $tab_list = MessageCategory::where('workshop_id', $request->workshop_id)->where('status', 1)->get();
            return response()->json(['status' => 200, 'data' => $tab_list]);
        } else {
            return response()->json(['status' => 201, 'msg' => "Tab can't be deleted"]);
        }
    }

    public function updateMessageTab(Request $request)
    {
        $data = MessageCategory::where('id', $request->cid)->update(['category_name' => $request->category_name, 'workshop_id' => $request->workshop_id]);
        if ($data > 0) {
            $tab_list = MessageCategory::where('workshop_id', $request->workshop_id)->where('status', 1)->get();
            return response()->json(['status' => 200, 'data' => $tab_list]);
        } else {
            return response()->json(['status' => 201, 'msg' => "Tab can't be updated"]);
        }
    }

    public function updateMessage(Request $request)
    {
        $msg = $this->message->updateMessage($request);
        return response()->json(['status' => 200, 'msg' => $msg]);
    }

    public function addMessage(Request $request)
    {

        $msg = $this->message->addMessage($request);

        return response()->json(['status' => 200, 'msg' => $msg]);
    }

    public function addDocs(Request $request)
    {
        $file = $this->document->addFiles($request);

        return response()->json(['status' => 200, 'msg' => $file]);
    }

    public function deleteMessage(Request $request)
    {
        $msg = $this->message->deleteMessage($request->id);
        return response()->json(['status' => 200, 'msg' => $msg]);
    }

    public function deleteMessageReply(Request $request)
    {
        $msg = $this->message->deleteMessageReply($request->id);
        return response()->json(['status' => 200, 'msg' => $msg]);
    }

    public function addReplyMesssage(Request $request)
    {
        $msg = $this->message->addReply($request);

        return response()->json(['status' => 200, 'msg' => $msg]);
    }

    public function memberList(Request $request)
    {
        $data = $this->workshop->getWorkshopMemberArray($request);
        if (count($data) > 0) {
            return response()->json(['status' => 200, 'data' => $data]);
        } else {
            return response()->json(['status' => 201, 'data' => []]);
        }
    }

    public function saveVote(Request $request)
    {
        $data = $this->meeting->saveUserResponse($request);
        if ($data->getData()->status > 0) {
            return response()->json(['status' => 200, 'msg' => 'vote save successfully']);
        } else {
            return response()->json(['status' => 201, 'msg' => "vote can't save successfully"]);
        }
    }

    public function getMettingDetail(Request $request)
    {

        $data = $this->meeting->viewMeeting($request);
        if (isset($data->getData()->meeting) && $data->getData()->meeting != null) {
            return response()->json(['status' => 200, 'data' => $data]);
        } else {
            return response()->json(['status' => 201, 'data' => []]);
        }
    }

    public function dateChoiceDoodle(Request $request)
    {
        $data = $this->meeting->updateMeetingFinalDate($request);
        if ($data->getData()->status > 0) {
            return response()->json(['status' => 200, 'msg' => 'Date save successfully']);
        } else {
            return response()->json(['status' => 201, 'msg' => 'Metting not found']);
        }
    }

    public function likeUnlikeMsg(Request $request)
    {
        $data = $this->message->messageLikeUnlike($request);

        if ($data->getData() > 0) {
            return response()->json(['status' => 200, 'msg' => 'like save successfully']);
        } else {
            return response()->json(['status' => 201, 'msg' => 'Metting not found']);
        }
    }

    public function agendaList(Request $request)
    {
        $data = $this->meeting->getTopics($request->mid, $request->type);
        $wid = Meeting::where('id', $request->mid)->first()->workshop_id;

        if (strtolower($request->type) == 'repd') {
            // to get the next 3 meetings upcoming
            $data->next3Meetings =  MeetingService::getInstance()->getUpcomingMeetingForRepd($request->mid, $wid);
        }
        return response()->json(['status' => 200, 'data' => $data]);
    }

    public function getParticipantList(Request $request)
    {
        $data = $this->meeting->getPresence($request);
        //return $data;
        return response()->json(['status' => 200, 'data' => $data]);

    }

    public function sendDoodleReminder(Request $request)
    {
        $data = $this->meeting->sendMeetingInvitation($request);
        //return $data;
        return response()->json(['status' => 200, 'data' => $data]);

    }

    public function updateUserStatus(request $request)
    {
        $user = Auth::guard('api')->user();
        if ($request->status > 0) {
            $regStatus = 'I';
            $preStatus = 'P';
            $mealStatus = isset($request->with_meal_status) ? $request->with_meal_status : 0;
        } else {
            $regStatus = 'E';
            $preStatus = 'AE';
            $mealStatus = 0;
        }

        $value = Presence::updateOrCreate(['workshop_id' => $request->workshop_id, 'user_id' => $user->id, 'meeting_id' => $request->meeting_id], ['register_status' => $regStatus, 'presence_status' => $preStatus, 'with_meal_status' => $mealStatus]);
        // var_dump($value);exit;
        $data['userStatus'] = ($value->presence_status == 'AE' && $value->register_status == 'E') ? 0 : 1;
        return response()->json(['status' => 200, 'data' => $data]);
    }

    public function getAdminWorkshop(Request $request)
    {
        $res = WorkshopMeta::with('workshop')->where('user_id', $request->user()->id)->whereIn('role' , [1,2])->get();
        if (count($res) > 0)
            return response()->json(['status' => 200, 'data' => $res]);
        else
            return response()->json(['status' => 201, 'data' => []]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNotifications(Request $request)
    {
        $res['noti'] = Notification::where(['to_id' => $request->user()->id])->orderBy('id', 'desc')->paginate();

        if (count($res) > 0) {
            $graphic_setting = getSettingData('graphic_config', 1);
            $res['graphic_setting'] = $graphic_setting;
            return response()->json(['status' => 200, 'data' => $res]);
        } else
            return response()->json(['status' => 201, 'data' => []]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markNotificationRead(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())]);
        } else {
            $res = Notification::where(['id' => $request->id, 'to_id' => $request->user()->id])->update(['read' => 1]);
            if ($res)
                return response()->json(['status' => 200, 'data' => []]);
            else
                return response()->json(['status' => 201, 'data' => []]);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())]);
        } else {
            $res = Notification::where(['id' => $request->id])->delete();
            if ($res)
                return response()->json(['status' => 200, 'data' => []]);
            else
                return response()->json(['status' => 201, 'data' => []]);
        }
    }

    public function getPersonalMessage(Request $request)
    {
        $message = PersonalMessage::with('message_reply.user:id,avatar,fname,lname,email')
            ->where(['id' => $request->id, 'to_user_id' => $request->user()->id, 'inbox_delete' => 0])->first();
        if (count($message))
            return response()->json(['status' => 200, 'data' => $message]);
        else
            return response()->json(['status' => 201, 'data' => []]);
    }

    public function addPersonalMessageReply(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'reply_message_id' => 'required|numeric',
            'msg_text' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())]);
        } else {
            $data = PersonalMessageReply::insert([
                'personal_message_id' => $request->reply_message_id,
                'message_text' => $request->msg_text,
                'to_user_id' => $request->user()->id,
                'from_user_id' => Auth::user()->id,
                'is_read' => 0,
                'created_at' => Carbon::now('Europe/Paris')->format('Y-m-d H:i:s'),
            ]);
            if ($data)
                return response()->json(['status' => 200, 'data' => []]);
            else
                return response()->json(['status' => 201, 'data' => []]);
        }
    }

    public function getUnreadCount(Request $request)
    {
        $res = Notification::where(['to_id' => $request->user()->id, 'read' => 0])->count();
        if ($res)
            return response()->json(['status' => 200, 'data' => $res]);
        else
            return response()->json(['status' => 201, 'data' => []]);
    }

    public function updatePresentStatus(Request $request)
    {
        $data = Presence::where('id', $request->id)->update(array(
            'presence_status' => $request->pStatus,
            'register_status' => $request->rStatus
            ));
        return response()->json(Presence::find($request->id));
    }

    public function updateRegister(Request $request)
    {
     
       $data = Presence::where('id', $request->id)->update(array('presence_status' => $request->pStatus,'register_status'=>$request->rStatus));
        return response()->json(Presence::find($request->id));
    }

    public function updateUserPresence(Request $request)
    {
        if ($request->regStatus == 'NI') {
            $register_status = 'NI';
            $presence_status = 'ANE';
        } elseif ($request->regStatus == 'I') {
            $register_status = 'I';
            $presence_status = 'P';
        } else {
            $register_status = 'E';
            $presence_status = 'AE';
        }
        $data = Presence::updateOrCreate(['workshop_id' => $request->wid, 'user_id' => Auth::user()->id, 'meeting_id' => $request->meetingid], ['register_status' => $request->regStatus, 'presence_status' => $request->preStatus]);
        return response()->json($data);
    }

    public function finalDateMeeting(Request $request)
    {
        $res = $this->meeting->updateMeetingFinalDate($request);
        return response()->json($res);
    }

    public function downloadDocument(Request $request)
    {

        $rdData = RegularDocument::whereId($request->docid)->first();

        if (empty($rdData)) {
            return response()->json(['status' => 400, 'data' => 'url not found']);
        } else {
            $file_name = str_replace(' ', '-', trim($rdData->document_title));
            $ext = pathinfo($rdData->document_file, PATHINFO_EXTENSION);
            $download_url = $this->core->getS3Parameter($rdData->document_file, 1, $file_name . '.' . $ext); //var_dump($download_url);
            if ($download_url != null) {
                return response()->json(['status' => 200, 'data' => $download_url]);
            } else {
                return response()->json(['status' => 400, 'data' => 'url not found']);
            }

        }
    }

    public function logout(Request $request)
    {
        return response()->json(['status' => 200, 'data' => User::where('id', $request->user()->id)->update(['fcm_token' => ''])]);
    }

    public function languageChange(Request $request)
    {
        if (isset($request->lang) && !empty($request->lang)) {
            $user = User::where('id', $request->user()->id)->update(['setting' => '{"lang":"' . $request->lang . '"}']);
            $lang = json_decode(User::find($request->user()->id)->setting);
            return response($lang->lang);
        } else {
            if (isset($request->user()->setting) && !empty($request->user()->setting)) {
                $lang = json_decode($request->user()->setting);
                return response($lang->lang);
            } else {
                $lang = 'FR';
                return response($lang);
            }

        }

    }
}