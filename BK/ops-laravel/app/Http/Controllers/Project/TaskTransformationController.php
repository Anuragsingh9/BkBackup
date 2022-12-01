<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Model\TaskComment;
use App\Model\TaskDependency;
use App\Model\TaskDocument;
use App\Task;
use App\TaskUser;
use App\Workshop;
use App\Model\UserTaskPermission;
use App\WorkshopMeta;
use Auth;
use Illuminate\Http\Request;
use Modules\Crm\Entities\CrmTask;

class TaskTransformationController extends Controller
{
    public function projectTaskTransformation(Request $request)
    {
        // return response()->json($request->all());
        $response = 0;
        $task_email = [];
        // return response()->json($request->all());
        // $workshop_data = Workshop::with('meta')->find($request->workshop_id);
        //check to transform

        switch ($request->to_type) {
            //transform to mutiple users
            case 0:
                if (!empty(json_decode($request->user)) || json_decode($request->user) != 0 || json_decode($request->user_id) != NULL) {
                    $userArray = json_decode($request->user);
                    //request from multiple
                    if ($request->from_type == 0) {
                        $task_user_ids = TaskUser::where('task_id', $request->task_id)->pluck('user_id');
                        foreach ($userArray as $key => $value) {
                            $incomming_user_id[] = $value->value;
                            $response = TaskUser::updateOrcreate(['task_id' => $request->task_id, 'user_id' => $value->value], ['task_id' => $request->task_id, 'user_id' => $value->value, 'task_status' => 0]);
                            if ($response) {
                                $response = 1;
                            }
                        }
                        TaskUser::whereNotIn('user_id', $incomming_user_id)->where('task_id', $request->task_id)->delete();
                        return response()->json($response);
                    }
                    //request from collective task
                    if ($request->from_type == 2) {
                        $response = Task::where('id', $request->task_id)->update(['assign_for' => $request->to_type]);
                        $task_user_ids = TaskUser::where('task_id', $request->task_id)->pluck('user_id');
                        $incomming_user_id = [];
                        foreach ($userArray as $key => $value) {
                            $incomming_user_id[] = $value->value;
                            $response = TaskUser::updateOrcreate(['task_id' => $request->task_id, 'user_id' => $value->value], ['task_id' => $request->task_id, 'user_id' => $value->value, 'task_status' => 0]);
                            if ($response) {
                                $response = 1;
                            }
                        }
                        TaskUser::whereNotIn('user_id', $incomming_user_id)->where('task_id', $request->task_id)->delete();
                        return response()->json($response);
                    }
                    //request from all workshop task
                    if ($request->from_type == 1) {
                        // $detail = WorkshopMeta::with('user:id,email')->where('workshop_id', $request->workshop_id)->where('role','!=',3)->groupBy('user_id')->get(['id', 'user_id']);
                        $response = Task::where('id', $request->task_id)->update(['assign_for' => $request->to_type]);
                        if ($response) {
                            $taskUser = [];
                            foreach ($userArray as $key => $value) {
                                $taskUser[] = ['task_id' => $request->task_id, 'user_id' => $value->value, 'task_status' => 0];
                            }
                            if (!empty($taskUser)) {
                                $response = TaskUser::insert($taskUser);
                            }

                        }
                        return response()->json($response);
                    }
                } else {
                    return response()->json(false);
                }
                break;
            //transform to all workshop
            case 1:
                //request from collective task
                if ($request->from_type == 2) {

                    $task = Task::where('id', $request->task_id)->update(['assign_for' => $request->to_type]);
                    if ($task) {
                        $response = TaskUser::where('task_id', $request->task_id)->delete();
                    }
                    return response()->json($response);
                }
                //request from all multiple users
                if ($request->from_type == 0) {
                    $task = Task::where('id', $request->task_id)->update(['assign_for' => $request->to_type]);
                    if ($task) {
                        $response = TaskUser::where('task_id', $request->task_id)->delete();
                        $detail = WorkshopMeta::with('user:id,email')->where('workshop_id', $request->workshop_id)->groupBy('user_id')->where('role', '!=', 3)->get(['id', 'user_id']);
                        foreach ($detail as $key => $value) {
                            if (isset($value->user))
                                $task_email[] = $value->user->email;
                        }
                        // $detail=$this->sendTaskMail($workshop_data,$task_email);

                    }
                    return response()->json($response);
                }
                break;
            //transform to collective task
            case 2:
                //request from all multiple users
                if ($request->from_type == 0) {
                    return response()->json($this->workshopToCollectiveTransform($request, 'multiple'));
                }
                //request from all workshop task
                if ($request->from_type == 1) {
                    return response()->json($this->workshopToCollectiveTransform($request, 'workshop'));
                }
                break;
        }
    }

    function workshopToCollectiveTransform($request, $type = 'workshop')
    {
        $data = 0;
        $task = Task::with('crmTask')->find($request->task_id);

        $taskComment = TaskComment::where('task_id', $request->task_id)->get();
        $taskDoc = TaskDocument::where('task_id', $request->task_id)->get();
        $taskDep = TaskDependency::where('parent_id', $request->task_id)->orWhere('child_id', $request->task_id)->get();
        $task_per = UserTaskPermission::where('task_id', $request->task_id)->get();
        if ($type == 'multiple') {
            $detail = TaskUser::where('task_id', $request->task_id)->get();
        } else {
            $detail = WorkshopMeta::with('user:id,email')->where('role', '!=', 3)->where('workshop_id', $request->workshop_id)->groupBy('user_id')->get(['id', 'user_id']);
        }

        if (count($detail) > 0) {

            foreach ($detail as $key => $value) {
                $lastTaskId = Task::insertGetId([
                    'workshop_id' => $task->workshop_id,
                    'task_created_by_id' => Auth::user()->id,
                    'task_text' => $task->task_text,
                    'start_date' => $task->start_date,
                    'milestone_id' => $task->milestone_id,
                    'end_date' => $task->end_date,
                    'assign_for' => 2,
                    'activity_type_id' => $task->activity_type_id,
                    'description' => $task->description,
                    'status' => $task->status,
                    'task_color_id' => $task->task_color_id
                ]);
                if ($lastTaskId > 0) {

                    $data = TaskUser::insert([
                        'task_id' => $lastTaskId,
                        'user_id' => $value->user_id,
                        'task_status' => 0,

                    ]);
                    $comment = [];
                    $document = [];
                    $dependancy = [];
                    $taskPermission = [];
                    foreach ($taskComment as $key => $value) {
                        $comment[] = ['task_id' => $lastTaskId, 'user_id' => $value->user_id, 'workshop_id' => $value->workshop_id, 'comment' => $value->comment, 'created_at' => $value->created_at];
                    }
                    foreach ($taskDoc as $key => $value) {
                        $document[] = ['task_id' => $lastTaskId, 'document_id' => $value->document_id];
                    }
                    foreach ($task_per as $key => $value) {
                        // $taskPermission[] = ['task_id' => $lastTaskId, 'user_id' => $value->user_id,'workshop_id'=>$value->workshop_id,'action_type'=>$value->action_type,'status'=>$value->status,'created_at'=>$value->created_at,'updated_at'=>$value->updated_at];
                        $data = UserTaskPermission::where('task_id', $value->task_id)->update(['task_id' => $lastTaskId]);
                    }
                    // foreach ($taskDep as $key => $value) {
                    //     $dependancy[] = ['parent_id' => ($value->parent_id == $request->task_id) ? $lastTaskId : $value->parent_id, 'child_id' => ($value->child_id == $request->task_id) ? $lastTaskId : $value->child_id];
                    // }
                    if ($data) {
                        $data = TaskComment::insert($comment);
                        $data = TaskDependency::insert($dependancy);
                        $data = TaskDocument::insert($document);
                        // $data= UserTaskPermission::insert($taskPermission);
                    }
                }
            }
            TaskDependency::where('parent_id', $request->task_id)->delete();
            TaskDependency::where('child_id', $request->task_id)->delete();

            $data = Task::where('id', $request->task_id)->delete();
            if (isset($task->crmTask->id)) {
                CrmTask::where('task_id', $request->task_id)->delete();
            }
            if ($type == 'multiple') {
                $data = TaskUser::where('task_id', $request->task_id)->delete();
            }
        }
        return $data;
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
}
