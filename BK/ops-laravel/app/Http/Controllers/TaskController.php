<?php
    
    namespace App\Http\Controllers;
    
    use App\Entity;
    use App\Model\Contact;
    use App\Model\TaskDependency;
    use App\Scopes\ProjectScope;
    use App\Task;
    use App\TaskUser;
    use App\User;
    use App\Workshop;
    use App\Project;
    use App\WorkshopMeta;
    
    use Auth;
    use Carbon\Carbon;
    use DB;
    use Hash;
    use Illuminate\Http\Request;
    use Modules\Crm\Entities\Assistance;
    use Modules\Crm\Entities\CrmTask;
    use Validator;
    use Hyn\Tenancy\Models\Website;
    use Hyn\Tenancy\Contracts\Repositories\WebsiteRepository;
    use Hyn\Tenancy\Models\Hostname;
    use Hyn\Tenancy\Contracts\Repositories\HostnameRepository;
    use Hyn\Tenancy\Environment;
    use App\Model\TaskComment;
    
    class TaskController extends Controller
    {
        
        private $core, $tenancy;
        
        public function __construct()
        {
            $this->tenancy = app(\Hyn\Tenancy\Environment::class);
            $this->core = app(\App\Http\Controllers\CoreController::class);
        }
        
        
        public function saveTask(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'task_text'     => 'required',
//            'milestone_id' => 'required',
                'assign_for'    => 'required',
                'field_id'      => 'sometimes|integer',
                'type'          => 'required_with:field_id',
                'task_type'     => 'in:0,1|required_with:field_id,type',
                'assistance_id' => 'required_if:task_type,1',
            ]);
            
            //chek if validator fails
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 400);
            }
            
            if ($request->assign_for == 2) {
                if (isset($request->task_type) && in_array($request->task_type, [0, 1])) {
                    $fieldData = $this->getFieldType($request);
                    if (is_array($fieldData)) {
                        $request->merge(['workshop_id' => $fieldData[0]->id, 'milestone_id' => $fieldData[0]->projects[0]->milestone[0]->id, 'field_id' => $fieldData[1]->id, 'type' => $request->type, 'task_userable_id' => $fieldData[1]->id, 'task_userable_type' => get_class($fieldData[1])]);
                    } else {
                        return response()->json(['status' => 0]);
                    }
                    
                }
                return response()->json(['status' => $this->saveCollectiveTask($request)]);
            }
            
            if (isset($request->task_type) && in_array($request->task_type, [0, 1])) {
                $fieldData = $this->getFieldType($request);
                if (is_array($fieldData)) {
                    $request->merge(['workshop_id' => $fieldData[0]->id, 'milestone_id' => $fieldData[0]->projects[0]->milestone[0]->id]);
                } else {
                    return response()->json(['status' => 0]);
                }
            }
            
            $workshop_data = Workshop::withoutGlobalScopes()->with('meta')->find($request->workshop_id);
            $date_convert = str_replace('/', '-', $request->end_date);
            $status = 0;
            $taskUsers = [];
            $task_email = [];
            $lastTaskId = Task::insertGetId([
                'workshop_id'        => $request->workshop_id,
                'meeting_id'         => NULL,
                'topic_id'           => NULL,
                'task_text'          => $request->task_text,
                'milestone_id'       => $request->milestone_id,
                'task_created_by_id' => Auth::user()->id,
                'activity_type_id'   => $request->activity_type,
                'start_date'         => date('Y-m-d'),
                'end_date'           => (empty($request->end_date)) ? date('Y-m-d') : date('Y-m-d', strtotime($date_convert)),
                'assign_for'         => $request->assign_for,
                'status'             => 1,
            ]);
            if ($lastTaskId > 0) {
                if (isset($request->task_type) && in_array($request->task_type, [0, 1])) {
                    $fieldData = $this->getFieldType($request);
                    if (is_array($fieldData)) {
                        CrmTask::create([
                            'task_id'                   => $lastTaskId,
                            'crm_object_tasksable_id'   => $fieldData[1]->id,
                            'crm_object_tasksable_type' => get_class($fieldData[1]),
                        ]);
                    } else {
                        return response()->json(['status' => 0]);
                    }
                    
                }
                
                if (!empty(json_decode($request->user)) || json_decode($request->user) != 0 || json_decode($request->user_id) != NULL) {
                    foreach (json_decode($request->user) as $values) {
                        $task_email[] = $values->text;
                        $taskUsers[] = ['task_id' => $lastTaskId, 'user_id' => $values->value, 'task_status' => 1];
                    }
                    TaskUser::insert($taskUsers);
                } elseif (isset($request->assign_for) && ($request->assign_for == 3)) {
                    $task_email[] = Auth::user()->email;
                    $taskUsers[] = ['task_id' => $lastTaskId, 'user_id' => Auth::user()->id, 'task_status' => 1];
                    TaskUser::insert($taskUsers);
                } else {
                    $detail = WorkshopMeta::with('user:id,email')->where('workshop_id', $request->workshop_id)->get(['id', 'user_id']);
                    foreach ($detail as $key => $value) {
                        if (isset($value->user))
                            $task_email[] = $value->user->email;
                    }
                }
                if (!isset($request->task_type)) {
                    $projectDetail = Project::find($request->project_id);
                    if (isset($projectDetail->email_disable) && ($projectDetail->email_disable)) {
        
                        $dataMail = $this->getMailData($workshop_data, $projectDetail, 'send_project_task', $request->milestone_id);
                        $subject = $dataMail['subject'];
                        $route_task = $dataMail['route_task'];
                        $mailData['mail'] = ['subject' => $subject, 'emails' => $task_email, 'workshop_data' => $workshop_data, 'current_user_fn' => Auth::user()->fname, 'current_user_ln' => Auth::user()->lname, 'current_user_email' => Auth::user()->email, 'url_task' => $route_task, 'project_name' => $projectDetail->project_label];
                        $this->core->SendMassEmail($mailData, 'send_project_task');
                    }
                }
                $status = 1;
            }
            return response()->json(['status' => $status]);
        }
        
        public
        function saveCollectiveTask($request)
        {
            // return response()->json($request->all());
            $date_convert = str_replace('/', '-', $request->end_date);
            $status = 0;
            $taskUsers = [];
            $task_email = [];
            $workshop_data = Workshop::withoutGlobalScopes()->with(['meta' => function ($query) {
                $query->groupBy('user_id')->where('role', '!=', 3);
            }])->find($request->workshop_id);
            
            if (isset($workshop_data) && !empty($workshop_data->meta)) {
                foreach ($workshop_data->meta as $values) {
                    $lastTaskId = Task::insertGetId([
                        'workshop_id'        => $request->workshop_id,
                        'meeting_id'         => NULL,
                        'topic_id'           => NULL,
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
                        if (isset($request->task_userable_id)) {
                            CrmTask::create([
                                'task_id'                   => $lastTaskId,
                                'crm_object_tasksable_id'   => $request->task_userable_id,
                                'crm_object_tasksable_type' => $request->task_userable_type,
                            ]);
                        }
                        $taskUsers[] = ['task_id' => $lastTaskId, 'user_id' => $values->user_id, 'task_status' => 0];
                        $task_email[] = $values->user->email;
                    }
                }
                
                $status = TaskUser::insert($taskUsers);
                if (!isset($request->task_userable_id)) {
                    $projectDetail = Project::find($request->project_id);
                    if ($status) {
                        if (isset($projectDetail->email_disable) && ($projectDetail->email_disable)) {
        
                            $dataMail = $this->getMailData($workshop_data, $projectDetail, 'send_project_task', $request->milestone_id);
                            $subject = $dataMail['subject'];
                            $route_task = $dataMail['route_task'];
                            $mailData['mail'] = ['subject' => $subject, 'emails' => $task_email, 'workshop_data' => $workshop_data, 'current_user_fn' => Auth::user()->fname, 'current_user_ln' => Auth::user()->lname, 'current_user_email' => Auth::user()->email, 'url_task' => $route_task, 'project_name' => $projectDetail->project_label];
                            $this->core->SendMassEmail($mailData, 'send_project_task');
                        }
                    }
                }
            }
            return $status;
        }
        
        public function sendTaskEmail()
        {
            $dataMail = $this->getMailData($workshop_data, $meeting_data, 'send_project_task');
            $subject = $dataMail['subject'];
            $mailData['mail'] = ['subject' => $subject, 'emails' => $emails, 'email_to' => $email_to, 'workshop_data' => $workshop_data, 'meeting_data' => $meeting_data, 'current_user_fn' => Auth::user()->fname, 'current_user_ln' => Auth::user()->lname, 'current_user_email' => Auth::user()->email, 'url_repd' => $route_repd, 'url_task' => $route_task, 'task_check' => $taskCheck];
            if (!isset($email_to)) {
                unset($mailData['mail']['email_to']);
            }
            $this->core->SendMassEmail($mailData, 'send_project_task');
        }
        
        public function getAllTask(Request $request)
        {
            
            $data = [];
            if ($request->status == 0) {
                if (Auth::user()->role == 'M1') {
                    $data = Task::with('user', 'task_user', 'workshop', 'taskTag')->where('workshop_id', $request->wid)->get();
                } else {
                    $task_data = Task::with('user', 'workshop', 'taskTag')->with(['task_user' => function ($query) use ($request) {
                        $query->where('user_id', $request->userId);
                    }])->where('workshop_id', $request->wid)->get();
                    foreach ($task_data as $key => $value) {
                        $task_user = $value->task_user;
                        if (count($task_user) > 0 || $value->assign_for == '1') {
                            $data[] = $value;
                        }
                    }
                }
            } else {
                
                if (Auth::user()->role == 'M1') {
                    $data = Task::with('user', 'task_user', 'workshop', 'taskTag')->where('workshop_id', $request->wid)->where('status', $request->status)->get();
                } else {
                    
                    $data = Task::with('user', 'workshop', 'taskTag')->with(['task_user' => function ($query) use ($request) {
                        $query->where('user_id', $request->userId);
                    }])->where('workshop_id', $request->wid)->where('status', $request->status)->get();
                    return response($data);
                }
                
                // $data = Task::with('user', 'task_user', 'workshop')->where('workshop_id', $request->wid)->where('status', $request->status)->get();
            }
            return response()->json($data);
        }
        
        public function deleteTask(Task $task)
        {
            $res = 0;
            if (isset($task->id)) {
                
                $res = $task->task_user()->delete();
                $task->taskDocument()->delete();
                $task->taskComment()->delete();
                $task->taskDependency()->delete();
                $task->taskDependent()->delete();
                $res = $task->delete();
            }
            return response()->json($res);
        }
        
        public function updateTaskStatus(Request $request)
        {
            $status = '1';
            if ($request->status == 'Pending' || $request->status == 'En cours') {
                $status = '1';
            } elseif ($request->status == 'Behind Schedule' || $request->status == 'En retard') {
                $status = '2';
            } else {
                $status = '3';
            }
            return response()->json(Task::where('id', $request->id)->update(['status' => $status]));
        }
        
        public function removeTaskUser(Request $request)
        {
            if ($request->user_count == 1) {
                return $this->deleteTask(Task::find($request->task_id));
            } else {
                $data = TaskUser::where('id', $request->task_user_id)->delete();
                return response()->json($data);
            }
        }
        
        public function addTaskUser(Request $request)
        {
            $response = FALSE;
            if ($request->assign_for == 2) {
                $response = $this->saveCollectiveTask($request);
            }
            if ($request->assign_for == 0) {
                if (!empty(json_decode($request->user)) || json_decode($request->user) != 0 || json_decode($request->user_id) != NULL) {
                    foreach (json_decode($request->user) as $values) {
                        $task_email[] = $values->text;
                        $taskUsers[] = ['task_id' => $request->id, 'user_id' => $values->value, 'task_status' => 0];
                    }
                    $response = TaskUser::insert($taskUsers);
                }
                $workshop_data = Workshop::withoutGlobalScopes()->with('meta')->find($request->workshop_id);
                $dataMail = $this->getMailData($workshop_data, 'send_project_task');
                $subject = $dataMail['subject'];
                $route_task = $dataMail['route_task'];
                $mailData['mail'] = ['subject' => $subject, 'emails' => $task_email, 'workshop_data' => $workshop_data, 'current_user_fn' => Auth::user()->fname, 'current_user_ln' => Auth::user()->lname, 'current_user_email' => Auth::user()->email, 'url_task' => $route_task];
                $response = $this->core->SendMassEmail($mailData, 'send_project_task');
                
            }
            
            return response()->json($response);
        }
        
        public function getTaskByMilestone(Request $request)
        {
            $data = [];
            $filter_array = [];
            $getWorkshopRole = WorkshopMeta::where(['workshop_id' => $request->wid, 'user_id' => Auth::user()->id])->get(['role'])->pluck('role');
            if (Auth::user()->role == 'M0' || Auth::user()->role == 'M1' || in_array(1, $getWorkshopRole->toArray()) || in_array(2, $getWorkshopRole->toArray())) {
                $data = Task::with('user', 'taskTag', 'task_user:id,task_id,user_id,task_status', 'workshopRelate', 'color', 'activityType')->with('taskDependency.dependency.task_user_info', 'taskDependency.dependency.milestone:id,project_id', 'taskDependency.dependency.workshopRelate', 'taskDependent.dependent.task_user_info', 'taskDependent.dependent.milestone:id,project_id', 'taskDependent.dependent.workshopRelate', 'activityStatus')->withCount('taskComment', 'taskDocument', 'taskDependency', 'taskDependent')->where(['milestone_id' => $request->milestone_id, 'workshop_id' => $request->wid])->orderBy('end_date', 'asc')->get(['id', 'task_text', 'end_date', 'assign_for', 'activity_type_id', 'status', 'workshop_id']);
            } else {
                $data = Task::with('user', 'taskTag', 'workshopRelate', 'color', 'activityType')->with('taskDependency.dependency.task_user_info', 'taskDependency.dependency.workshopRelate', 'taskDependency.dependency.milestone:id,project_id', 'taskDependent.dependent.milestone:id,project_id', 'taskDependent.dependent.task_user_info', 'taskDependent.dependent.workshopRelate', 'activityStatus')->withCount('taskComment', 'taskDocument', 'taskDependency', 'taskDependent')->with(['task_user' => function ($query) {
                    $query->where('user_id', Auth::user()->id);
                }])->where(['milestone_id' => $request->milestone_id, 'workshop_id' => $request->wid])->orderBy('end_date', 'asc')->get(['id', 'task_text', 'end_date', 'assign_for', 'activity_type_id', 'status', 'workshop_id']);
                foreach ($data as $key => $value) {
                    
                    if ($value->assign_for == 1) {
                        $filter_array[] = $value;
                    }
                    if ($value->assign_for == 0 || $value->assign_for == 2) {
                        if ($value->task_user->count() > 0) {
                            $filter_array[] = $value;
                        }
                    }
                }
                $data = $filter_array;
            }
            return response()->json(['status' => TRUE, 'data' => $data]);
        }
        
        public function updateProjectTaskStatus(Request $request)
        {
            
            return response()->json(Task::where('id', $request->id)->update(['status' => $request->
            status]));
        }
        
        public function updateTaskActiveType(Request $request)
        {
            return response()->json(Task::where('id', $request->task_id)->update(['activity_type_id' => $request->activity_id]));
        }
        
        public function updateTaskDate(Request $request)
        {
            //this is for update start and end_date by single function
            $update = [isset($request->start_date) ? 'start_date' : 'end_date' => isset($request->start_date) ? $request->start_date : $request->end_date];
            return response()->json(Task::where('id', $request->task_id)->update($update));
        }
        
        public function updateTaskColor(Request $request)
        {
            return response()->json(Task::where('id', $request->id)->update(['task_color_id' => $request->colorId]));
        }
        
        public function getMailData($workshop_data, $project_data, $key, $milestone_id = 0)
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
                '[[OrgShortName]]', '[[ProjectName]]',
            ];
            $values = [
                $currUserFname, $currUserLname, $currUserEmail, $workshop_data->workshop_name, $workshop_data->code1, $member['p']['fullname'], $member['v']['fullname'],
                '', '', '', '', $member['v']['email'], $member['p']['email'], $member['p']['phone'], $orgDetail->name_org, $orgDetail->acronym, $project_data->project_label,
            ];
            
            $subject = ((str_replace($keywords, $values, $settings->email_subject)));
            $this->tenancy->website();
            $hostname = $this->tenancy->hostname();
            $acc_id = $hostname->id;
            // $acc_id = 1;
            $superAdminPermission = DB::connection('mysql')->table('account_settings')->where('account_id', $acc_id)->first(['project_enable']);
            if ($superAdminPermission->project_enable == 1) {
                if ($milestone_id > 0) {
                    $route_task = route('redirect-app-url', ['url' => str_rot13('organiser/commissions/' . $workshop_data->id . '/projects/' . $project_data->id . "/milestone/" . $milestone_id)]);
                } else {
                    $route_task = route('redirect-app-url', ['url' => str_rot13('organiser/commissions/' . $workshop_data->id . '/projects/' . $project_data->id)]);
                }
            } else {
                $route_task = route('redirect-app-url', ['url' => str_rot13('organiser/commissions/' . $workshop_data->id . '/tasks/all-tasks')]);
            }
            
            
            return ['subject' => $subject, 'route_task' => $route_task,];
        }
        
        
        public
        function getFilterdTask(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'value' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 400);
            }
            //adding query type with fetch field
            if ($request->type == 'parent') {
                $type = 'parent_id';
                $getType = 'child_id';
            } else {
                $type = 'child_id';
                $getType = 'parent_id';
            }
            //fetching already added task to filter
            $taskDep = TaskDependency::where($type, $request->task)->get([$getType]);
            
            // $data = Task::whereNotIn('id', $taskDep->pluck($getType))->where('id', '!=', $request->task)->where('workshop_id', $request->wid)->where('milestone_id', $request->milestone_id)->where('task_text', 'like', '%'.$request->value . '%')->groupBy('id')->get();
            $data = Task::with('task_user')->whereNotIn('id', $taskDep->pluck($getType))->where('id', '!=', $request->task)->where('workshop_id', $request->wid)->where('milestone_id', $request->milestone_id)->where('task_text', 'like', $request->value . '%')->groupBy('id')->get();
            
            return response()->json($data);
        }
        
        public
        function updateTaskTitle(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'task_title' => 'required',
                'id'         => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 200);
            }
            $data = Task::where('id', $request->id)->update(['task_text' => $request->task_title]);
            if ($data) {
                return response()->json([
                    'status' => TRUE,
                    'data'   => $data,
                ], 200);
            }
            return response()->json([
                'status' => FALSE,
                'msg'    => 'Error',
            ], 200);
        }
        
        public function getFieldType($request)
        {
            if ($request->task_type == 0) {
                $workshop = Workshop::where('code1', 'CRM')->with(['projects'=>function($q){
                    $q->withoutGlobalScopes([ProjectScope::class]);
                },'projects.milestone'])->withoutGlobalScopes()
                    ->first(['id']);
            } else {
                $assistance = Assistance::find($request->assistance_id);
                $workshop = Workshop::where('code1', $assistance->assistance_type_short_name)->with('projects.milestone')->withoutGlobalScopes()
                    ->first(['id']);
            }
            // Create Data into database after data validate successfully
            if (isset($workshop->projects[0]->id) && isset($workshop->projects[0]->milestone[0]->id)) {
                //checking type is User
                if ($request->type == 'user') {
                    $field = User::find($request->field_id, ['id']);
                } elseif ($request->type == 'contact') {
                    //checking type is Contact
                    $field = Contact::find($request->field_id, ['id']);
                    
                } elseif (($request->type == 'company' || $request->type == 'union' || $request->type == 'instance' || $request->type == 'press')) {
                    //checking type is Entity
                    $field = Entity::find($request->field_id, ['id']);
                }
            } else {
                return 0;
            }
            
            return [$workshop, $field];
        }
        
    }
