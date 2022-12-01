<?php
    
    namespace App\Http\Controllers\Project;
    
    use App\Color;
    use App\Http\Controllers\Controller;
    use App\Milestone;
    use App\Model\TaskComment;
    use App\Model\TaskDependency;
    use App\Model\TaskDocument;
    use App\Model\UserTaskPermission;
    use App\Project;
    use App\Rules\FrenchName;
    use App\Task;
    use App\TaskUser;
    use App\WorkshopMeta;
    use App\Workshop;
    use Auth;
    use DB;
    use Illuminate\Http\Request;
    use Validator;
    
    class ProjectController extends Controller
    {
        /**
         * Display a listing of the resource.
         *
         * @return \Illuminate\Http\Response
         */
        protected $task, $core;
        
        public function __construct(\App\Http\Controllers\TaskController $taskController,
                                    \App\Http\Controllers\CoreController $coreController
        )
        {
            $this->task = $taskController;
            $this->core = $coreController;
        }
        
        public function index($id)
        {
            $projects = Project::where('wid', $id)->with('milestone.tasks', 'milestone.doneTasks', 'workshop:id,code1')->withCount('milestone')->withCount('user_permission')->get();
            
            return response()->json([
                'status' => TRUE,
                'data'   => $projects,
            ], 200);
        }
        
        public function getWorkshopProjectOverview($wid)
        {
            $data = [];
            if ($wid > 0) {
                
                $data = Project::where('wid', $wid)->with('milestone.tasks', 'milestone.doneTasks', 'workshop')->withCount('milestone')->get(['id', 'project_label']);
            } else {
                if (Auth::user()->role == 'M1' || Auth::user()->role == 'M0') {
                    $data = Project::with('milestone.tasks', 'milestone.doneTasks', 'workshop')->withCount('milestone')->withCount('user_permission')->get(['id', 'project_label']);
                } else {
                    
                    $workshop = WorkshopMeta::where('user_id', Auth::user()->id)->whereIn('role', [0, 1, 2])->get(['workshop_id']);
                    if (count($workshop) > 0) {
                        $data = Project::whereIn('wid', $workshop->pluck('workshop_id'))->with('milestone.tasks', 'milestone.doneTasks', 'workshop')->withCount('milestone')->withCount('user_permission')->get(['id', 'project_label']);
                    }
                    
                }
            }
            return response()->json([
                'status' => TRUE,
                'data'   => $data,
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
         * @param \Illuminate\Http\Request $request
         * @return \Illuminate\Http\Response
         */
        public function store(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'project_label'       => ['required', 'max:255', new FrenchName],
                'milestone_name'      => ['required', 'max:255', new FrenchName],
                'project_description' => 'sometimes|max:1000',
                'project_goal'        => 'sometimes|max:255',
                'project_result'      => 'sometimes|max:255',
                'end_date'            => 'required',
                'milestone_end_date'  => 'required',
                'color_id'            => 'required|numeric',
                'wid'                 => 'required|numeric|exists:tenant.workshops,id',
            ]);
            
            
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())]);
            }
            $project = Project::create(['project_label' => $request->project_label, 'user_id' => Auth::user()->id, 'wid' => $request->wid
                , 'color_id'                            => $request->color_id
                , 'project_description'                 => $request->project_description
                , 'project_goal'                        => $request->project_goal
                , 'project_result'                      => $request->project_result,
                 'end_date'      => date('Y-m-d', strtotime(str_replace('/', '-', $request->end_date))),
            ]);
            
            
            $milestone = Milestone::create(['project_id' => $project->id, 'label' => $request->milestone_name, 'end_date' => date('Y-m-d', strtotime(str_replace('/', '-', $request->milestone_end_date))), 'user_id' => Auth::user()->id, 'start_date' => date('Y-m-d')]);
            
            if ($milestone->count() > 0) {
                $flag = $project->id;
            }
            
            return response($flag);
        }
        
        /**
         * Display the specified resource.
         *
         * @param int $id
         * @return \Illuminate\Http\Response
         */
        public function show(Request $request)
        {
            $getWorkshopRole = WorkshopMeta::where(['workshop_id' => $request->wid, 'user_id' => Auth::user()->id])->get(['role'])->pluck('role');
            if (Auth::user()->role == 'M0' || Auth::user()->role == 'M1' || in_array(1, $getWorkshopRole->toArray()) || in_array(2, $getWorkshopRole->toArray())) {
                $project = Project::with('milestone.color', 'milestone.tasks.task_user', 'milestone.tasks.taskPermission')->where(['id' => $request->pid, 'wid' => $request->wid])->first(/*['id', 'project_label','email_disable']*/);
            } else {
                $project = Project::with(['milestone.color', 'milestone.tasks.task_user' => function ($query) {
                    $query->where('user_id', Auth::user()->id);
                }, 'milestone.tasks.taskPermission'])->where(['id' => $request->pid, 'wid' => $request->wid])->first(/*['id', 'project_label','email_disable']*/);
                foreach ($project->milestone as $index => $row) {
                    $filter_array = [];
                    foreach ($row->tasks as $key => $value) {
                        
                        if ($value->assign_for == 0 || $value->assign_for == 2) {
                            if ($value->task_user->count() > 0) {
                                $filter_array[] = $value;
                            }
                        } else {
                            $filter_array[] = $value;
                        }
                    }
                    unset($row['tasks']);
                    $row['tasks'] = $filter_array;
                }
            }
            return response()->json([
                'status' => TRUE,
                'data'   => $project,
            ], 200);
        }
        
        /**
         * Show the form for editing the specified resource.
         *
         * @param int $id
         * @return \Illuminate\Http\Response
         */
        public function edit($id)
        {
            //
        }
        
        /**
         * Update the specified resource in storage.
         *
         * @param \Illuminate\Http\Request $request
         * @param int $id
         * @return \Illuminate\Http\Response
         */
        
        
        public function update(Request $request, $id)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'project_label'       => ['sometimes', 'required', 'max:255', new FrenchName],
                    'project_description' => 'sometimes|required|max:1000',
                    'project_goal'        => 'sometimes|required|max:255',
                    'project_result'      => 'sometimes|required|max:255',
                    'end_date'            => 'sometimes|required',
                    'color_id'            => 'sometimes|required|numeric',
                    'wid'                 => 'sometimes|required|numeric|exists:tenant.workshops,id',
                ]);
                
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())]);
                }
                $project = Project::findOrFail($id);
                $project->update([
                    'project_label'         => $request->project_label
                    , 'user_id'             => Auth::user()->id
                    , 'wid'                 => $request->wid
                    , 'color_id'            => $request->color_id
                    , 'project_description' => $request->project_description
                    , 'project_goal'        => $request->project_goal
                    , 'project_result'      => $request->project_result,
                ]);
                $flag = $project->id;
                return response($flag);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
            
            
        }
        
        /**
         * Remove the specified resource from storage.
         *
         * @param int $id
         * @return \Illuminate\Http\Response
         */
        public function destroy(Project $project)
        {
            // delete related
            $workshop = Workshop::where('id', $project->wid)->where('code1', 'NSL')->first();
            $Project = Project::where('wid', $project->wid)->first();
            if (config('constants.NEWSLETTER') == 1 && $Project->id == $project->id) {
                return response()->json([
                    'status' => FALSE,
                ], 200);
            } else {
                $milestone = Milestone::where('project_id', $project->id)->get(['id'])->pluck('id');
                $task = Task::whereIn('milestone_id', $milestone)->get(['id'])->pluck('id');
                TaskUser::whereIn('task_id', $task)->delete();
                TaskDocument::whereIn('task_id', $task)->delete();
                TaskComment::whereIn('task_id', $task)->delete();
                TaskDependency::whereIn('parent_id', $task)->delete();
                TaskDependency::whereIn('child_id', $task)->delete();
                UserTaskPermission::whereIn('task_id', $task)->delete();
                Task::whereIn('milestone_id', $milestone)->delete();
                $project->milestone()->delete();
                $project->delete();
                return response()->json([
                    'status' => TRUE,
                ], 200);
            }
        }
        
        public function projectByWid($wid)
        {
            
            $defaultProjects = Project::where('wid', $wid)->where('is_default_project', 1)->get(['id', 'project_label', 'is_default_project']);
            
            $projects = Project::where('wid', $wid)->where('is_default_project', 0)->orWhereIn('id', $defaultProjects->pluck('id'))->get(['id', 'project_label', 'is_default_project']);
            
            return response()->json([
                'status' => TRUE,
                'data'   => $projects,
            ], 200);
        }
        
        public function getColor()
        {
            $color = Color::all();
            return response()->json([
                'status' => TRUE,
                'data'   => $color,
            ], 200);
        }
        
        public function getMileStoneDate($milestone_id)
        {
            $milestone = Milestone::where('id', $milestone_id)->first(['id', 'start_date', 'end_date', 'label']);
            return response()->json([
                'status' => TRUE,
                'data'   => $milestone,
            ], 200);
        }
        
        public function updateMilestoneDate(Request $request)
        {
            
            $data = [];
            if ($request->type == 'start_date') {
                $data = ['start_date' => date('Y-m-d', strtotime(str_replace('/', '-', $request->start_date)))];
            } elseif ($request->type == 'name') {
                $data = ['label' => $request->label];
            } else {
                $data = ['end_date' => date('Y-m-d', strtotime(str_replace('/', '-', $request->end_date)))];
            }
            if (count($data) > 0) {
                $milestone = Milestone::where('id', $request->id)->update($data);
                return response()->json([
                    'status' => TRUE,
                    'data'   => $milestone,
                ], 200);
            } else {
                return response()->json([
                    'status' => FALSE,
                    'data'   => [],
                ], 201);
            }
        }
        
        public function deleteMilestone(Request $request)
        {
            
            $milestone = Milestone::where('id', $request->id)->first(['id']);
            $task = Task::where('milestone_id', $milestone->id)->get(['id'])->pluck('id');
            TaskUser::whereIn('task_id', $task)->delete();
            TaskDocument::whereIn('task_id', $task)->delete();
            TaskComment::whereIn('task_id', $task)->delete();
            TaskDependency::whereIn('parent_id', $task)->delete();
            TaskDependency::whereIn('child_id', $task)->delete();
            UserTaskPermission::whereIn('task_id', $task)->delete();
            Task::where('milestone_id', $milestone->id)->delete();
            $data = Milestone::where('id', $request->id)->delete();
            if ($data) {
                return response()->json([
                    'status' => TRUE,
                    'data'   => $data,
                ], 200);
            } else {
                return response()->json([
                    'status' => FALSE,
                    'data'   => $data,
                ], 201);
            }
        }
        
        public function projectTaskTransformation(Request $request)
        {
            $response = 0;
            $task_email = [];
            $workshop_data = Workshop::with('meta')->find($request->workshop_id);
            switch ($request->to_type) {
                case 0:
                    
                    break;
                case 1:
                    if ($request->from_type == 2) {
                        // $response=Task::where('id',$request->task_id)->update(['assign_for'=>$request->to_type]);
                    }
                    if ($request->from_type == 0) {
                        $task = Task::where('id', $request->task_id)->update(['assign_for' => $request->to_type]);
                        if ($task) {
                            $response = TaskUser::where('task_id', $request->task_id)->delete();
                            $detail = WorkshopMeta::with('user:id,email')->where('workshop_id', $request->workshop_id)->get(['id', 'user_id']);
                            foreach ($detail as $key => $value) {
                                if (isset($value->user))
                                    $task_email[] = $value->user->email;
                            }
                            $detail = $this->sendTaskMail($workshop_data, $task_email);
                            
                        }
                        return response()->json($response);
                    }
                    break;
                case 2:
                    
                    break;
            }
            
        }
        
        function sendTaskMail($workshop_data, $task_email)
        {
            $dataMail = $this->task->getMailData($workshop_data, 'job_email_setting');
            $subject = $dataMail['subject'];
            $route_task = $dataMail['route_task'];
            $mailData['mail'] = ['subject' => $subject, 'emails' => $task_email, 'workshop_data' => $workshop_data, 'current_user_fn' => Auth::user()->fname, 'current_user_ln' => Auth::user()->lname, 'current_user_email' => Auth::user()->email, 'url_task' => $route_task];
            $res = $this->core->SendMassEmail($mailData, 'repd_task_mail');
            return $res;
        }
        
        
        public function updateTaskMilestone(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'current_milestone_id' => 'required',
                'future_milestone_id'  => 'required',
                'task_id'              => 'required',
            ]);
            
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())]);
            }
            
            $task = Task::where('id', $request->task_id)->update(['milestone_id' => $request->future_milestone_id]);
            if ($task) {
                return response()->json([
                    'status' => TRUE,
                    'data'   => '',
                ], 200);
            } else {
                return response()->json([
                    'status' => FALSE,
                    'data'   => '',
                ], 200);
            }
        }
        
        public function getDefaultProject($wid)
        {
            $project = Project::where('wid', $wid)->where('is_default_project', 1)->first(['id']);
            if ($project) {
                @$project->milestone = Milestone::where(['project_id' => $project->id])->where('is_default_milestone', 1)->first(['id'])->id;
            }
            if ($project) {
                return response()->json([
                    'status' => TRUE,
                    'data'   => $project,
                ], 200);
            } else {
                return response()->json([
                    'status' => FALSE,
                    'data'   => [],
                ], 200);
            }
        }
        
        public function projectMailUpdate(Request $request)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'project_id' => 'required|exists:tenant.projects,id',
                    'status'     => 'required',
                ]);
                
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())]);
                }
                
                $project = Project::where('id', $request->project_id)->update(['email_disable' => $request->status]);
                if ($project)
                    return response()->json(['status' => TRUE, 'msg' => 'Status Updated Successfully.'], 200);
                else
                    return response()->json(['status' => FALSE, 'msg' => 'Status Not Updated.'], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
    }
