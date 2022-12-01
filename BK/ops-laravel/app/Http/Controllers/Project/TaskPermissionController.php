<?php

namespace App\Http\Controllers\Project;

use App\Model\UserTaskPermission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Auth;
use App\Workshop;
use App\Task;
use App\Project;
use App\AccountSettings;
use Hyn\Tenancy\Models\Website;
use Hyn\Tenancy\Contracts\Repositories\WebsiteRepository;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Contracts\Repositories\HostnameRepository;
use Hyn\Tenancy\Environment;
use DB;
class TaskPermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $core,$tenancy;

    public function __construct()
    {
        $this->tenancy=app(\Hyn\Tenancy\Environment::class);
        $this->core = app(\App\Http\Controllers\CoreController::class);
    }
    public function index($id,$pid){
        $userTaskPermissions = UserTaskPermission::where(['workshop_id'=>$id,'project_id'=>$pid])->with('user', 'task')->orderBy('created_at','desc')->paginate(10);
        return response()->json([
            'status' => TRUE,
            'data' => $userTaskPermissions
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // |exists:mysql.tasks,id
            'task_id' => 'required',
            'action_type' => 'required',
            'project_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 400);
        }

        $userTaskPermission=UserTaskPermission::create([
            'task_id'=>$request->task_id,
            'user_id'=>Auth::user()->id,
            'workshop_id'=>$request->workshop_id,
            'action_type'=>$request->action_type,
            'project_id'=>$request->project_id,
        ]);
        $userTaskPermission->status=0;

        $taskDetail=Task::select(['task_text','id'])->find($request->task_id);
        $workshopDetail=Workshop::find($request->workshop_id);
        $projectDetail=Project::select('project_label','id')->find($request->project_id);
        $workhopAdmin=workshopValidatorPresident($workshopDetail)['p'];
        // $workhopAdmin['email']
        if($userTaskPermission){
                $dataMail = $this->getMailData('admin',$workshopDetail,$taskDetail->task_text,$projectDetail,'modify_task_permission_request_admin');
                $subject = $dataMail['subject'];
                $route_task = $dataMail['route_task'];
                $mailData['mail'] = ['subject' => $subject, 'emails' => [$workhopAdmin['email']], 'workshop_data' => $workshopDetail, 'current_user_fn' => '', 'current_user_ln' => '', 'current_user_email' => '','from_user'=>Auth::user()->fname.' '.Auth::user()->lname ,'url_task' => $route_task,'task_name'=>$taskDetail->task_text,'project_name'=>$projectDetail->project_label];
                $this->core->SendMassEmail($mailData,'task_permission_request_send');
        }
        return response()->json([
            'status' => TRUE,
            'data' => $userTaskPermission
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // |exists:tenant.user_task_permissions,id
            'id' => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 400);
        }
        $permision=UserTaskPermission::with('task:id,task_text','user:id,fname,lname,email')->find($request->id);
        $workshop_data=Workshop::find($permision->workshop_id);
        $projectDetail=Project::select('project_label','id')->find($permision->project_id);
        if ($request->status == 1) {
            $taskPermission  = UserTaskPermission::where('id', $request->id)->update(['status' => $request->status]);
            
            if($taskPermission){

                $dataMail =$this->getMailData('accept',$workshop_data,isset($permision->task->task_text)?$permision->task->task_text:'',$projectDetail,'modify_task_permission_accept');
               
                $subject = $dataMail['subject'];
                $route_task = $dataMail['route_task'];
                $mailData['mail'] = ['subject' => $subject, 'emails' => [$permision->user->email], 'workshop_data' => $workshop_data, 'current_user_fn' => Auth::user()->fname, 'current_user_ln' => $permision->user->lname, 'current_user_email' => $permision->user->email,'from_user'=>Auth::user()->fname.' '.Auth::user()->lname ,'url_task' => $route_task,'task_name'=>$permision->task->task_text,'project_name'=>$projectDetail->project_label];
                $this->core->SendMassEmail($mailData, 'modify_task_permission_accept');
               
            }
            return response()->json([
                'status' => TRUE,
                'data' => UserTaskPermission::find($request->id)
            ], 200);
        } 
        if ($request->status == 2) {
            $taskPermission = UserTaskPermission::where('id', $request->id)->delete();
            if($taskPermission){
                    $dataMail =$this->getMailData('reject',$workshop_data,isset($permision->task->task_text)?$permision->task->task_text:'',$projectDetail, 'modify_task_permission_reject');
                $subject = $dataMail['subject'];
                $route_task = $dataMail['route_task'];
                $mailData['mail'] = ['subject' => $subject, 'emails' => [$permision->user->email], 'workshop_data' => $workshop_data, 'current_user_fn' => Auth::user()->fname, 'current_user_ln' => $permision->user->lname, 'current_user_email' => $permision->user->email,'from_user'=>Auth::user()->fname.' '.Auth::user()->lname ,'url_task' => $route_task,'task_name'=>$permision->task->task_text,'project_name'=>$projectDetail->project_label];
                $this->core->SendMassEmail($mailData, 'modify_task_permission_reject');
            }
            return response()->json([
                'status' => TRUE,
                'data' => ['id'=>$request->id]
            ], 200);
        }


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function getMailData($type,$workshop_data,$task_name,$project, $key)
    {
        
        $settings = getSettingData($key);
        $member = workshopValidatorPresident($workshop_data);
        $orgDetail = getOrgDetail();
        $keywords = [
            '[[UserFirstName]]', '[[UserLastName]]', '[[UserEmail]]', '[[WorkshopLongName]]', '[[WorkshopShortName]]', '[[WorkshopPresidentFullName]]',
            '[[WorkshopvalidatorFullName]]', '[[WorkshopMeetingName]]', '[[WorkshopMeetingDate]]', '[[WorkshopMeetingTime]]', '[[WorkshopMeetingAddress]]',
            '[[ValidatorEmail]]', '[[PresidentEmail]]', '[[PresidentPhone]]', '[[OrgName]]',
            '[[OrgShortName]]','[[ProjectName]]','[[TaskName]]',
        ];
        $values = [
            Auth::user()->fname, Auth::user()->lname, Auth::user()->email, $workshop_data->workshop_name, $workshop_data->code1, $member['p']['fullname'], $member['v']['fullname'],
            '', '', '', '', $member['v']['email'], $member['p']['email'], $member['p']['phone'], $orgDetail->name_org, $orgDetail->acronym,$project->project_label,$task_name
        ];

        $subject = ((str_replace($keywords, $values, $settings->email_subject)));
        $this->tenancy->website();
        $hostname =$this->tenancy->hostname();
        $acc_id = $hostname->id;
        // $acc_id = 117;
        $superAdminPermission= DB::connection('mysql')->table('account_settings')->where('account_id', $acc_id)->first();
        if($superAdminPermission!=null && $superAdminPermission->project_enable==1){
                if($type=='admin'){
                $route_task = route('redirect-app-url', ['url' => str_rot13('organiser/commissions/' . $workshop_data->id . '/projects/'.$project->id.'/user-permission')]); 
                }else{
                    $route_task = route('redirect-app-url', ['url' => str_rot13('organiser/commissions/' . $workshop_data->id . '/projects/'.$project->id)]); 
                }  
        }else{
            $route_task = route('redirect-app-url', ['url' => str_rot13('organiser/commissions/' . $workshop_data->id . '/tasks/all-tasks')]); 
        }
        
         return ['subject' => $subject, 'route_task' => $route_task,];   

        
    }

}
