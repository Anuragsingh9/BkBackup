<?php

namespace Modules\Crm\Http\Controllers;

use App\Entity;

use App\Project;
use App\Scopes\ProjectScope;
use App\Task;
use App\User;
use App\Workshop;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Crm\Entities\Assistance;
use App\Model\Contact;
use Modules\Crm\Entities\AssistanceReport;
use Modules\Crm\Entities\CrmNote;
use Validator;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->core = app(\App\Http\Controllers\CoreController::class);
        $this->task = app(\App\Http\Controllers\TaskController::class);
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('crm::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('crm::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            //validate data with Validator
            $validator = Validator::make($request->all(), [
                'field_id' => 'sometimes|integer',
                'type' => 'required_with:field_id',
                'task_type' => 'in:0,1|required_with:field_id,type',
                'assistance_id' => 'required_if:task_type,1',
            ]);

            //chek if validator fails
            if ($validator->fails()) {
                return response()->json(['status' => false, 'msg' => implode(',', $validator->errors()->all())], 400);
            }

            if ($request->task_type == 0) {
                $workshop = Workshop::where('code1', 'CRM')->with(['projects'=>function($q){
                    $q->withoutGlobalScopes([ProjectScope::class]);
                },'projects.milestone'])->withoutGlobalScopes()
                    ->first(['id']);
            } else {
                $assistance = Assistance::find($request->assistance_id);
                $workshop = Workshop::where('code1', $assistance->assistance_type_short_name)->with('projects.milestone')/*->withoutGlobalScope()*/
                    ->first(['id']);
            }

            //checking type is User
            if ($request->type == 'user') {
                $undone = User::with(['tasks.task' => function ($a) use ($workshop) {
                    $a->where('workshop_id', $workshop->id)->select('id', 'task_text','assign_for', 'milestone_id', 'workshop_id', 'status', 'activity_type_id', 'task_description', 'created_at', 'task_created_by_id');
                }, 'tasks.task.activityType', 'tasks.task.activityStatus', 'tasks.task.milestone', 'tasks.task.createdBy', 'tasks.task.task_user_info',])/*->whereHas('tasks', function ($b) use ($request) {
                    if (isset($request->user_id)) {
                        $b->where('user_id', $request->user_id);
                    }
                })*/->find($request->field_id, ['id']);
            }
            elseif ($request->type == 'contact') {
                //checking type is Contact
                $undone = Contact::with([/*'tasks.user', */
                    'tasks.task' => function ($a) use ($workshop) {
                        $a->where('workshop_id', $workshop->id)->select('id', 'task_text','assign_for', 'milestone_id', 'workshop_id', 'status', 'activity_type_id', 'task_description', 'created_at', 'task_created_by_id');
                    }, 'tasks.task.activityType', 'tasks.task.activityStatus', 'tasks.task.milestone', 'tasks.task.createdBy', 'tasks.task.task_user_info'
                ])/*->whereHas('tasks', function ($b) use ($request) {
                    if (isset($request->user_id)) {
                        $b->where('user_id', $request->user_id);
                    }
                })*/->find($request->field_id, ['id']);
            }
            elseif (($request->type == 'company' || $request->type == 'union' || $request->type == 'instance' || $request->type == 'press')) {

                //checking type is Entity
                $undone = Entity::with([/*'tasks.user',*/
                    'tasks.task' => function ($a) use ($workshop) {
                        $a->where('workshop_id', $workshop->id)->select('id', 'task_text', 'assign_for','milestone_id', 'workshop_id', 'status', 'activity_type_id', 'task_description', 'created_at','task_created_by_id');
                    }, 'tasks.task.activityType', 'tasks.task.activityStatus', 'tasks.task.milestone', 'tasks.task.createdBy', 'tasks.task.task_user_info'
                ])/*->whereHas('tasks', function ($b) use ($request) {
                    if (isset($request->user_id)) {
                        $b->where('user_id', $request->user_id);
                }
                })*/->find($request->field_id, ['id']);
            }

            return response()->json(['status' => true, 'msg' => '', 'data' => $undone], 200);
        } catch (\Exception $e) {

            return response()->json(['status' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('crm::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('crm::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
    
    public function getTimeLine(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'field_id' => 'required',
            'type' => 'required',
            'task_for' => 'required|in:0,1',
            'user_id' => 'required_if:task_for,1',
        ]);
        $undone = [];
        $done = [];
        $permissions = \Auth::user()->permissions;
        $crmAdmin = (isset($permissions['crmAdmin']) && $permissions['crmAdmin'] == 1) ?1: 0;
        $crmEditor = (isset($permissions['crmEditor']) && $permissions['crmEditor'] == 1) ?1: 0;
        $crmAssistance = (isset($permissions['crmAssistance']) && $permissions['crmAssistance'] == 1) ?1: 0;
        $crmRecruitment = (isset($permissions['crmRecruitment']) && $permissions['crmRecruitment'] == 1) ?1: 0;
        $target = null;
        //checking type is User
        if ($request->type == 'user') {
            $target = User::class;
        }
        elseif ($request->type == 'contact') {
            $target= Contact::class;
            //checking type is Contact
        }
        elseif (($request->type == 'company' || $request->type == 'union' || $request->type == 'instance' || $request->type == 'press')) {
            $target = Entity::class;
            //checking type is Entity
        }
        if($target) {
            $taskBuilder = Task::
            with('createdBy', 'workshop', 'activityType', 'task_user_info')->
            select(
                'tasks.id as id',                   // to fetch id (common for all - task, notes, assistance)
                'tasks.task_text as text',                  // to fetch text (common for all - task, notes, assistance)
                DB::raw("'task' as type"),                  // to fetch identify which type is of data
                'end_date as date',                         // to fetch date (common for all - task, notes, assistance)
                'task_created_by_id',                       // to fetch created_by_id (common for all - task, notes, assistance)
                'workshop_id',                              // for task only
                'activity_type_id',                         // for task only
                'status',                                   // for task only
                'task_description',                         // for task only
                'assign_for',                               // for task only
                DB::raw('0 as crm_assistance_type_id'),     // to equal no. of select fields - for assistance row
                DB::raw("0 as assistance_type_name"),       // to equal no. of select fields - for assistance row
                DB::raw("0 as assistance_type_short_name")  // to equal no. of select fields - for assistance row
            )
                ->join('crm_object_tasks as ct', function ($join) use ($request, $target) {
                    $join->on('ct.task_id', '=', 'tasks.id')
                        ->where('ct.crm_object_tasksable_id', $request->field_id)
                        ->where('ct.crm_object_tasksable_type', $target);
                });
            // undone part
            $undoneBuilder = clone $taskBuilder;
            $undone = $undoneBuilder
                ->where('status', '!=', 3)
                ->orderBy('date', 'DESC')
                ->get();
            // done part
            $tasks = $taskBuilder->where('status' , 3);
            $notes = CrmNote::
            with('createdBy')->
            select(
                'crm_notes.id as id',                // to fetch id (common for all - task, notes, assistance)
                'notes as text',                            // to fetch text (common for all - task, notes, assistance)
                DB::raw("'notes' as type"),                 // to fetch identify which type is of data
                DB::raw("date(crm_notes.created_at)"),      // to fetch date (common for all - task, notes, assistance)
                'created_by',                               // to fetch created_by_id (common for all - task, notes, assistance)
                DB::raw('0 as workshop_id'),                // to equal no. of select fields - for task row
                DB::raw('0 as activity_type_id'),           // to equal no. of select fields - for task row
                DB::raw('0 as status'),                     // to equal no. of select fields - for task row
                DB::raw('0 as task_description'),           // to equal no. of select fields - for task row
                DB::raw('0 as assign_for'),                 // to equal no. of select fields - for task row
                DB::raw('0 as crm_assistance_type_id'),     // to equal no. of select fields - for assistance row
                DB::raw("0 as assistance_type_name"),       // to equal no. of select fields - for assistance row
                DB::raw("0 as assistance_type_short_name")  // to equal no. of select fields - for assistance row
            )
                ->where('crm_noteable_id', $request->field_id)
                ->where('crm_noteable_type', $target);
            $assistance = AssistanceReport::
            with('createdBy','assistanceType')->
            select(
                'assistance_reports.id as id',              // to fetch id (common for all - task, notes, assistance)
                'reports as text',                                  // to fetch text (common for all - task, notes, assistance)
                DB::raw("'assistance_reports' as type"),            // to fetch identify which type is of data
                DB::raw("date(assistance_reports.created_at)"),     // to fetch date (common for all - task, notes, assistance)
                'created_by',                                       // to fetch created_by_id (common for all - task, notes, assistance)
                DB::raw('0 as workshop_id' ),                       // to equal no. of select fields - for task row
                DB::raw('0 as activity_type_id'),                   // to equal no. of select fields - for task row
                DB::raw('0 as status'),                             // to equal no. of select fields - for task row
                DB::raw('0 as task_description'),                   // to equal no. of select fields - for task row
                DB::raw('0 as assign_for'),                         // to equal no. of select fields - for task row
                'crm_assistance_type_id',                           // for assistance row
                'crm_assistance_type.assistance_type_name',         // for assistance row
                'crm_assistance_type.assistance_type_short_name'    // for assistance row
            )
                ->where('assistance_reportable_id', $request->field_id)
                ->where('assistance_reportable_type', $target)
                ->join('crm_assistance_type', 'crm_assistance_type_id' ,'=', 'crm_assistance_type.id')
            ;
            if ((!$crmAdmin)) {
                if ((!$crmEditor) || $crmAssistance || $crmRecruitment) {
                    $assistance->where('created_by', Auth::user()->id);
                }
            }
            $done = $tasks
                ->union($assistance)
                ->union($notes)
                ->orderBy('date', 'desc')
                ->get();
        }
        return response()->json(['status' => true, 'data' => [
            'planned' => $this->customMapTimelineTasks($undone),
            'done' => $this->customMapTimelineTasks($done)
        ]], 200);
    }
    
    public function customMapTimelineTasks($data) {
        $result = [];
        $data->map(function ($row) use(&$result, &$count){
            switch($row->type) {
                case 'task':
                    $result[] = [
                        'id' => $row->id,
                        'text' => $row->text,
                        'task_description' => $row->task_description,
                        'type' => $row->type,
                        'date' => $row->date,
                        'status' => $row->status,
                        'assign_for' => $row->assign_for,
                        'created_by_id' => $row->task_created_by_id,
                        'workshop_id' => $row->workshop_id,
                        'activity_type_id' => $row->activity_type_id,
                        'created_by' => $row->createdBy,
                        'workshop' => $row->workshop,
                        'activity_type' => $row->activityType,
                        'task_user_info' => $row->task_user_info,
                    ];
                    break;
                case 'notes':
                    $result[] = [
                        'id' => $row->id,
                        'text' => $row->text,
                        'type' => $row->type,
                        'date' => $row->date,
                        'created_by_id' => $row->task_created_by_id, // because fetched from union that is why name include task
                        'crm_assistance_type_id' => $row->crm_assistance_type_id,
                        'created_by' => $row->createdBy,
                    ];
                    break;
                case 'assistance_reports':
                    $result[] = [
                        'id' => $row->id,
                        'text' => $row->text,
                        'type' => $row->type,
                        'date' => $row->date,
                        'created_by_id' => $row->task_created_by_id, // because fetched from union that is why name include task
                        'crm_assistance_type_id' => $row->crm_assistance_type_id,
                        'assistance_type' => [
                            'id'=> $row->crm_assistance_type_id,
                            'assistance_type_name' => $row->assistance_type_name,
                            'assistance_type_short_name' => $row->assistance_type_short_name,
                        ],
                        'created_by' => $row->createdBy,
                    ];
                    break;
            }
        });
        return $result;
    }

}
