<?php

namespace App\Http\Controllers;

use Mail;
use App\Meeting;
use App\Workshop;
use App\WorkshopMeta;
use Auth;
use Carbon\Carbon;
use DB;
use File;
use Hash;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->core = app(\App\Http\Controllers\CoreController::class);
    }
    
    function getWorkshopIds($condition=0)
    {
        if (Auth::user()->role == 'M1') {
            return DB::connection('tenant')->select(DB::raw("select w.id from workshops w left join workshop_metas wm ON wm.workshop_id = w.id where w.is_qualification_workshop=0  group by w.id order by w.id desc "));
        } else {
            //checking
            if($condition==1){
                return DB::connection('tenant')->select(DB::raw("select w.id from workshops w left join workshop_metas wm ON wm.workshop_id = w.id where wm.user_id = '" . Auth::user()->id . "' and w.is_qualification_workshop=0  group by w.id"));
            }else{
                return DB::connection('tenant')->select(DB::raw("select w.id from workshops w left join workshop_metas wm ON wm.workshop_id = w.id where wm.user_id = '" . Auth::user()->id . "' OR w.is_private = 0  and w.is_qualification_workshop=0  group by w.id"));
            }
            
        }
    }

    public function getDashboardWorkshop()
    {
        $workshop_data = $this->getWorkshopIds(1);
        $wids = array_column($workshop_data, 'id');
        $workshops = Workshop::with(['meetings' => function ($a) {
            $a->orderBy('date', 'asc')->where('status', 1)->select(['id', 'date', 'name', 'meeting_date_type', 'workshop_id','meeting_type', 'validated_repd', 'validated_prepd', 'start_time', 'end_time', 'with_meal']);
        }, 'meetings.presences' => function ($q) {
            $q->where('user_id', Auth::user()->id)->groupBy('meeting_id')->pluck('id', 'presence_status');
        }])->whereIn('id', $wids)->get(['id', 'workshop_name','is_qualification_workshop']);
        $finalWorkshops = [];
        foreach ($workshops as $k => $value) {
            if (count($value->meetings) > 0) {
                $finalWorkshops[] = $value;
            }
        }
        $doodleMeeting = Workshop::with(['meetings' => function ($query) {
            $query->where('meeting_date_type', 0)->where('status', 1)->select(['id', 'name', 'workshop_id', 'with_meal']);
        }, 'meetings.presences' => function ($q) {
            $q->where('user_id', Auth::user()->id)->groupBy('meeting_id')->select(['meeting_id', 'id', 'presence_status', 'register_status', 'user_id', 'with_meal_status']);
        }, 'meetings.doodleDates' => function ($query) {
            $query->where(DB::raw('concat(date," ",start_time)'), '>=', Carbon::now('Europe/Paris')->format('Y-m-d H:i:s'));
        }, 'meetings.doodleDates.doodleVotes' => function ($query) {
            $query->where('user_id', Auth::user()->id);
        },
        ])->whereIn('id', array_column($finalWorkshops, 'id'))->get(['id', 'workshop_name','is_qualification_workshop']);
        $data['workshops'] = $finalWorkshops;
        $data['workshops_doodle'] = $doodleMeeting;
       
        return response()->json($data);
    }

    public function getMeetingByDate(Request $request)
    {
        $workshop_data = $this->getWorkshopIds();
        $wids = array_column($workshop_data, 'id');
        $workshop = WorkshopMeta::where('user_id', Auth::user()->id)->whereIn('role', [0, 1, 2])->get(['workshop_id']);
        if ($request->flag == 'by_month') {
            $exp_month = explode('/', $request->date);
            $meetingsArr = [];
            $meetings = Meeting::whereNotNull('date')->whereMonth('date', $exp_month[1])->whereYear('date', '=', $exp_month[0])->orderBy('date', 'ASC')->whereIn('workshop_id', $wids)->get();
            $prev_date = '';
            foreach ($meetings as $val) {

                if ($val->date != null) {
                    $diffArr = array_diff($wids, array_column($workshop->toArray(), 'workshop_id'));
                    if (in_array($val->workshop_id, $diffArr)) {

                        if ($val->visibility == 1) {
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
                    } else {

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
            }
            $data['meeting'] = $meetingsArr;
            $data['meetingData'] = $meetings;
        } else {
            $data['meetingData'] = Meeting::where('date', $request->date)->whereIn('workshop_id', $wids)->get();
        }
        return response()->json($data);
    }

    public function getDashboardTask()
    {
        $taskData = [];
        $workshop_data = $this->getWorkshopIds(1);

        $wids = array_column($workshop_data, 'id');
        $workshopTask = Workshop::with(['task' => function ($query) {
            $query->with('task_user_info', 'milestone:id,project_id');
        }])->whereIn('id', $wids)->get();

        foreach ($workshopTask as $key => $value) {
            if (count($value->task) > 0) {
                $task = [];

                foreach ($value->task as $taskkey => $task_data) {

                    if ($task_data->assign_for == 0) {

                        foreach ($task_data->task_user_info as $key => $task_user_value) {
                            if ($task_user_value->user_id == Auth::user()->id) {
                                $task[] = ['milestone_id' => $task_data->milestone_id, 'id' => $task_data->id, 'end_date' => $task_data->end_date, 'task_text' => $task_data->task_text, 'milestone' => $task_data->milestone];
                            }
                        }

                    } else {
                        $task[] = ['milestone_id' => $task_data->milestone_id, 'task_text' => $task_data->task_text, 'id' => $task_data->id, 'end_date' => $task_data->end_date, 'milestone' => $task_data->milestone];
                    }
                }

                if (count($task) > 0) {
                    $taskData[] = ['id' => $value->id, 'workshop_name' => $value->workshop_name, 'task' => $task];
                }
            }

        }

        return response()->json($taskData);
    }

    public function getDashboardDoc()
    {
        $doc = [];
        $workshop_data = $this->getWorkshopIds(1);
        $wids = array_column($workshop_data, 'id');
        $docs = Workshop::withCount(['document' => function ($query) {
            $query->whereDate('created_at', '>', Carbon::now()->subDays(30))->where(['is_active' => 1, 'uncote' => 0]);
        }])->whereIn('id', $wids)->get();

        foreach ($docs as $key => $value) {
            if ($value->document_count != 0) {
                $doc[] = $value;
            }
        }

        return response($doc);
    }


    public function sendDummyMail()
    {

        $data = array('name' => "Pebibits Technologies");
        Mail::raw('Text to e-mail', function ($message) {
            $message->to('sourabh@sharabh.com', 'Pebibits Technologies')->subject
            ('TESTING');
            $message->from('ooionline@opsimplify.com', 'Sourabh Pancharia');
        });
        dd('dd');
        $data['mail']['email'] = 'sourabh@sharabh.com';
        $data['mail']['subject'] = 'TESTING';
        $data['mail']['msg'] = 'JUST For TEST';
        $response = $this->core->SendEmail($data);
        dd($response);
    }
}