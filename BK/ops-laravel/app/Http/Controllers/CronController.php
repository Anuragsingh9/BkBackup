<?php

    namespace App\Http\Controllers;

    use App\Entity;
    use App\Industry;
    use App\Mail\SendMailable;
    use App\Meeting;
    use App\Presence;
    use App\Project;
    use App\Task;
    use App\Union;
    use App\User;
    use App\DoodleDates;
    use App\DoodleVote;
    use App\Workshop;
    use App\WorkshopMeta;
    use App\Role;
    use App\RegularDocument;
//use Artisan;
    use Auth;
    use Carbon\Carbon;
    use DB;
    use Hash;
    use Hyn\Tenancy\Models\Hostname;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Artisan;
    use Illuminate\Support\Facades\Mail;
    use View;
    use App\SuperadminSetting;
    use App\Milestone;
    use App\AccountSettings;
    use App\Model\LabelCustomization;

    class CronController extends Controller
    {
        private $tenancy, $core;

        public function __construct()
        {
            $this->tenancy = app(\Hyn\Tenancy\Environment::class);
            $this->core = app(\App\Http\Controllers\CoreController::class);
        }

        public function updateTaskStatus(Request $request)
        {
            Task::where('end_date', '<', date('Y-m-d'))->update(['status' => 2]);
        }

        public function getHostdata()
        {
            $carbonTime = Carbon::now()->setTimezone('Europe/Paris');
            $today = $carbonTime->dayOfWeek;
            $hour = $carbonTime->hour;

//        if($today==7){
//            $today=1;
//        }else{
//            $today=$today+1;
//        }//dd($today, $hour);
            //dd($today, $hour);
            //getting no of hostnames
            $weekly = DB::table('weekly_reminders')/*->where('fqdn','afnor.pasimplify.com')*/
            ->where(['weekday' => $today, 'time_frame' => $hour, 'status' => 0, 'on_off' => 1])->get(['fqdn'])->pluck('fqdn');
            $hostnames = Hostname::whereIn('fqdn', $weekly)->orderBy('id', 'desc')->get();
//        dd($hostnames);
            $this->getCurl($hostnames);
        }

        public function getCurl($hostnames)
        {

            if (!empty($hostnames)) {
                //dividing the first time data
                $count = (count($hostnames) <= 8) ? count($hostnames) : intval(count($hostnames) / 8);

                for ($i = 0; $i < $count; $i++) {
                    $this->tenancy->hostname($hostnames[$i]);
                    $hostname = $this->getHostNameData();
                    $this->getEmailData();

                }

            }
        }

        public function getEmailData()
        {
            $users = User::where('on_off', 1)->orderBy('id', 'asc')->get(['id', 'fname', 'lname', 'email', 'mobile', 'role']);
            //dd($users);
            foreach ($users as $k => $val) {
                //get workshopData
//            $workshop_data = $this->getWorkshopIds($val);
//            $wids = array_column($workshop_data, 'id');
//            $workshops = Workshop::with(['meetings', 'meetings.presences' => function ($q) use ($val) {
//                $q->where('user_id', $val->id)->groupBy('meeting_id')->pluck('id', 'presence_status');
//            }])->whereIn('id', $wids)->get(['id', 'workshop_name']);
//
//            $finalWorkshops = [];
//            foreach ($workshops as $k => $value) {
//                if (count($value->meetings) > 0) {
//                    $finalWorkshops[] = $value;
//                }
//            }
//
//            $doodleMeeting = Workshop::with(['meetings' => function ($query) {
//                $query->where('meeting_date_type', 0)->pluck('id', 'name');
//            }, 'meetings.doodleDates' => function ($query) {
//                $query->where(DB::raw('concat(date," ",start_time)'), '>=', Carbon::now('Europe/Paris')->format('Y-m-d H:i:s'));
//            }])->whereIn('id', array_column($finalWorkshops, 'id'))->get(['id', 'workshop_name']);
                $workshop_data = $this->getWorkshopIds($val);
                $wids = array_column($workshop_data, 'id');
                $workshops = Workshop::with(['meetings' => function ($a) {
                    $a->orderBy('id', 'desc');
                }, 'meetings.presences'                 => function ($q) use ($val) {
                    $q->where('user_id', $val->id)->groupBy('meeting_id')->pluck('id', 'presence_status');
                }])->whereIn('id', $wids)->get(['id', 'workshop_name']);
                $finalWorkshops = [];
                foreach ($workshops as $k => $value) {
                    if (count($value->meetings) > 0) {
                        $finalWorkshops[] = $value;
                    }
                }

                $doodleMeeting = Workshop::with(['meetings' => function ($query) use ($val) {
                    $query->where('meeting_date_type', 0)->pluck('id', 'name');
                }, 'meetings.presences'                     => function ($q) use ($val) {
                    $q->where('user_id', $val->id)->groupBy('meeting_id');
                }, 'meetings.doodleDates'                   => function ($query) {
                    $query->where(DB::raw('concat(date," ",start_time)'), '>=', Carbon::now('Europe/Paris')->format('Y-m-d H:i:s'));
                }])->whereIn('id', array_column($finalWorkshops, 'id'))->get(['id', 'workshop_name']);

                //generate last and next meeting
                $doodle_workshop = $this->getNextLastMeeting($finalWorkshops, $doodleMeeting);
                $task = $this->getDashboardTask($val);
                $docs = $this->getDashboardDoc($val);
                $project = $this->getWorkshopProjectOverview($val);
//            if($val->id==7){
//                dump($doodle_workshop,$val->id);
//            }

//             dd($project->count(),$docs,count($doodle_workshop['workshop']) > 0 , count($doodle_workshop['workshop_doodle']) > 0 , count($task) > 0 , count($docs) > 0);
                if ($project->count() > 0 || count($doodle_workshop['workshop']) > 0 || count($doodle_workshop['workshop_doodle']) > 0 || count($task) > 0 || count($docs) > 0) {
                    //dd($doodle_workshop);
                    //echo $val->email.'<br/>';
                    $sendMail = $this->sendEmail($doodle_workshop, $task, $docs, $val, $project);
                }

            }
            // exit;
            // dd($sendMail);
            $hostname = $this->tenancy->hostname();
            //dd($sendMail);
            DB::table('weekly_reminders')
                ->where('fqdn', trim($hostname->fqdn))
                ->update(['status' => 1]);

        }

        //this function called by all domain one by one

        /*function getWorkshopIds($val)
        {
            if ($val->role == 'M1') {
                return DB::connection('tenant')->select(DB::raw("select w.id from workshops w left join workshop_metas wm ON wm.workshop_id = w.id  group by w.id order by w.id desc "));
            } else {
                return DB::connection('tenant')->select(DB::raw("select w.id from workshops w left join workshop_metas wm ON wm.workshop_id = w.id where wm.user_id = '" . $val->id . "' group by w.id"));
            }
        }*/
        function getWorkshopIds($val)
        {
            if ($val->role == 'M1') {
                return DB::connection('tenant')->select(DB::raw("select w.id from workshops w left join workshop_metas wm ON wm.workshop_id = w.id  group by w.id order by w.id desc "));
            } else {
                return DB::connection('tenant')->select(DB::raw("select w.id from workshops w left join workshop_metas wm ON wm.workshop_id = w.id where wm.user_id = '" . $val->id . "'  group by w.id"));
            }
        }

        public function getWorkshopProjectOverview($val)
        {
            $data = collect([]);
            if ($val->role == 'M1' || $val->role == 'M0') {
                $data = Project::with('milestone.tasks', 'milestone.doneTasks', 'workshop')->withCount('milestone')->withCount('user_permission')->get(['id', 'project_label']);
            } else {

                $workshop = WorkshopMeta::where('user_id', $val->id)->where('role', [1, 2, 0])->get(['workshop_id']);
                if (count($workshop) > 0) {
                    $data = Project::whereIn('wid', $workshop->pluck('workshop_id'))->with('milestone.tasks', 'milestone.doneTasks', 'workshop')->withCount('milestone')->withCount('user_permission')->get(['id', 'project_label']);
                }
            }
            return ($data);
        }

        function getNextLastMeeting($finalWorkshops, $doodleMeeting)
        {
            $workshop_data = [];
            $workshop_doodle = [];
            $tDate = Carbon::now('Europe/Paris')->format('Y/m/d H:m:s');
            $next_meeting = [];
            $last_meeting = [];
            foreach ($finalWorkshops as $key => $value) {
                $flag = 0;
                $flag2 = 0;
                $next_meeting = [];
                $last_meeting = [];
                $count = count($value->meetings);
                $meeting = $value->meetings;
                if ($count > 0) {
                    for ($i = 0; $i < $count; $i++) {
                        if ($meeting[$i]->date != '') {
                            $mParse = $meeting[$i]->date . ' ' . $meeting[$i]->start_time;
                            $mDate = Carbon::parse($mParse)/*->format('Y/m/d H:m:s')*/
                            ;

                            if (Carbon::parse($tDate)->lessThan($mDate) && $flag == 0) {
                                $flag = 1;
                                $next_meeting = $meeting[$i];
                            }
                        }
                    }
                    //dump(Carbon::parse($tDate)->lt($mDate),$tDate,$mDate,$next_meeting );
                    for ($j = 0; $j < $count; $j++) {
                        if ($meeting[$j]->date != '') {

                            $mParse = $meeting[$j]->date . ' ' . $meeting[$j]->start_time;
                            $mDate = Carbon::parse($mParse);
                            // dump($mDate,Carbon::parse($tDate));
                            if (Carbon::parse($tDate)->greaterThan($mDate) && $flag2 == 0) {
                                $flag2 = 1;

                                $last_meeting = $meeting[$j];
                            }
                        }
                    }

                    if (count($last_meeting) !== 0 || count($next_meeting) !== 0) {
                        $workshop_data[] = [
                            'id'            => $value->id,
                            'workshop_name' => $value->workshop_name,
                            'next_meeting'  => $next_meeting,
                            'last_meeting'  => $last_meeting,
                        ];
                    }
                }


            }
            // dd($workshop_data);
            foreach ($doodleMeeting as $k => $val) {

                if (count($val->meetings) !== 0) {
                    foreach ($val->meetings as $meeting) {

                        if ($meeting->doodleDates->count() > 0) {

                            $workshop_doodle[] = [
                                'id'            => $val->id,
                                'workshop_name' => $val->workshop_name,
                                'meetings'      => $val->meetings,
                            ];
                        }
                    }

                }
            }

            return ['workshop' => $workshop_data, 'workshop_doodle' => $workshop_doodle];
        }

        public function getDashboardTask($val)
        {
            $taskData = [];
            $workshop_data = $this->getWorkshopIds($val);
            $wids = array_column($workshop_data, 'id');
            $workshopTask = Workshop::with('task.task_user_info')->whereIn('id', $wids)->get();
            foreach ($workshopTask as $key => $value) {
                if (count($value->task) > 0) {
                    $task = [];

                    foreach ($value->task as $taskkey => $task_data) {

                        if ($task_data->assign_for == 0) {

                            foreach ($task_data->task_user_info as $key => $task_user_value) {
                                if ($task_user_value->user_id == $val->id) {
                                    $task[] = $task_data;
                                }
                            }

                        } else {
                            $task[] = $task_data;
                        }
                    }

                    if (count($task) > 0) {
                        $taskData[] = ['id' => $value->id, 'workshop_name' => $value->workshop_name, 'task' => $task];
                    }
                }

            }
            return ($taskData);
        }

        public function getDashboardDoc($val)
        {
            $doc = [];
            $workshop_data = $this->getWorkshopIds($val);
            $wids = array_column($workshop_data, 'id');
            $docs = Workshop::withCount(['document' => function ($query) {
                $query->where(DB::raw("MONTH(created_at)"), DB::raw("MONTH(CURDATE())"))->where(['is_active' => 1, 'uncote' => 0]);
            }])->whereIn('id', $wids)->get();

            foreach ($docs as $key => $value) {
                if ($value->document_count != 0) {
                    $doc[] = $value;
                }
            }

            return ($doc);
        }

        public function sendEmail($doodle_workshop = [], $task = [], $docs = [], $user, $project)
        {
            /*return view('email_template.weekly_reminder')->with([
                'doodle_workshop' => $doodle_workshop,
                'task' => $task,
                'docs' => $docs,
                'user' => $user,
                'project' => $project,
            ]);*/

            //echo $user->email;
            return Mail::to($user->email)->send(new SendMailable($doodle_workshop, $task, $docs, $user, $project));
            return Mail::to(['ido26@live.fr', 'nkojha94@outlook.com', 'sourabh@sharabh.com'])/*->bcc(['nkojha94@outlook.com','sourabh@sharabh.com'])*/
            ->send(new SendMailable($doodle_workshop, $task, $docs, $user, $project));


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

        public function updateWeeklyReminderTable()
        {
            $weekly = DB::table('weekly_reminders')->update(['status' => 0]);
        }

        // Updating new crm role in roles table
        public function updateRoles()
        {

            $data = [
                ['role_key' => 'M0', 'fr_text' => '', 'eng_text' => 'Super Admin', 'status' => 0],
                ['role_key' => 'M1', 'fr_text' => '', 'eng_text' => 'Organisation Admin', 'status' => 1],
                ['role_key' => 'M2', 'fr_text' => '', 'eng_text' => 'User', 'status' => 1],
                ['role_key' => 'M3', 'fr_text' => '', 'eng_text' => 'Guest', 'status' => 0],
                ['role_key' => 'W0', 'fr_text' => '', 'eng_text' => 'Workshop Secretary', 'status' => 1],
                ['role_key' => 'W1', 'fr_text' => '', 'eng_text' => 'Workshop Deputy', 'status' => 0],
                ['role_key' => 'W2', 'fr_text' => '', 'eng_text' => 'Workshop Member', 'status' => 0],
                ['role_key' => 'K0', 'fr_text' => '', 'eng_text' => 'Wiki Admin', 'status' => 0],
                ['role_key' => 'K1', 'fr_text' => '', 'eng_text' => 'Wiki Editor', 'status' => 0],
                ['role_key' => 'U0', 'fr_text' => '', 'eng_text' => 'Union Admin', 'status' => 0],
                ['role_key' => 'U1', 'fr_text' => '', 'eng_text' => 'Union Member', 'status' => 0],
                ['role_key' => 'C1', 'fr_text' => 'CRM  Administrateur', 'eng_text' => 'CRM Administrator', 'status' => 0],
                ['role_key' => 'C2', 'fr_text' => 'CRM Editeur', 'eng_text' => 'CRM Editor', 'status' => 0],
                ['role_key' => 'C3', 'fr_text' => 'CRM Finance Team', 'eng_text' => 'CRM Finance Team', 'status' => 0],
                ['role_key' => 'C4', 'fr_text' => 'CRM Dev Team', 'eng_text' => 'CRM Dev Team', 'status' => 0],
                ['role_key' => 'C5', 'fr_text' => 'CRM Assistance Team', 'eng_text' => 'CRM Assistance Team', 'status' => 0],

            ];
            foreach ($data as $value) {
                Role::updateOrCreate(['role_key' => $value['role_key']], $value);
            }
            Role::where('role_key', 'W0')->update(['eng_text' => 'Workshop Secretary']);
            Role::where('role_key', 'W1')->update(['eng_text' => 'Workshop Deputy']);
        }

        public function updateLableCustmizationTable()
        {
            LabelCustomization::where('id', 1)->update(['default_fr' => "Famille d'industries"]);
        }

        public function migrationScript($id, $seed = NULL)
        {

            if ($id == "all") {
                $hostnames = Hostname::get();

                foreach ($hostnames as $key => $value) {

                    $host = Hostname::find($value->id);
                    $this->tenancy->hostname($host);
                    $check = 0;
                    $check = Artisan::call('migrate', ['--database' => 'tenant']);
                    $check2 = Artisan::call('module:migrate Newsletter', ['--database' => 'tenant']);
                    $check2 = Artisan::call('module:migrate', ['--database' => 'tenant']);
                    $check1 = Artisan::call('db:seed', ['--database' => 'tenant']);
                    $check1 = Artisan::call('module:seed', ['--database' => 'tenant']);
                    $this->updateHeaderColor();
                    $this->core->updateMilestoneStartDate();
                    $this->core->updateTaskStatus();
                    $this->core->addOldWorkshopDefaultTask();
                    $this->updateRoles();
//                dd(($value),'if');
                }
            } else {
                $hostname = Hostname::find($id);
                $check = 0;

                $host = $this->tenancy->hostname($hostname);

                $check = Artisan::call('migrate', ['--database' => 'tenant']);
                $check2 = Artisan::call('module:migrate', ['--database' => 'tenant']);//dd($check2->outout());
                if(!empty($seed)){
                    $check1 = Artisan::call('db:seed', ['--database' => 'tenant']);
                    $check11 = Artisan::call('module:seed', ['--database' => 'tenant']);
                }

                $this->updateHeaderColor();
                $this->core->updateMilestoneStartDate();
                $this->core->updateTaskStatus();
                $this->core->addOldWorkshopDefaultTask();
                $this->updateRoles();
                $this->updateLableCustmizationTable();
                dd($check, 'dd');
            }

        }

        public function migrationSeederScript($id, $class)
        {

            $hostname = Hostname::find($id);

            $check = 0;
            //Modules\Resilience\Database\Seeders\SeedSettingTableTableSeeder
            $host = $this->tenancy->hostname($hostname);
            $check = Artisan::call('db:seed', ['--database' => 'tenant', '--class' => $class]);
            dd(Artisan::output());
        }

        /**
         * To run the module wise migrations
         *
         * @param $id
         * @param $module
         * @return JsonResponse|string
         */
        public function migrationSeederForModule($id, $module) {
            try {
                $hostname = Hostname::find($id);
                if(!$hostname) {
                    return "Invalid Hostname";
                }
                $this->tenancy->hostname($hostname);
                
                $result = Artisan::call("module:seed", ['module' => $module, '--force' => 1]);
                $output = Artisan::output();
                return response()->json([
                    'modules' => $result,
                    'output'  => $output,
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTrace()
                ]);
            }
        }
        

        //
        public function updateHeaderColor()
        {
            $graphicsSetting = getSettingData('graphic_config', 1);

            if (!isset($graphicsSetting->headerColor1)) {
                $extended = ['headerColor1' => (object)['r' => 255, 'g' => 255, 'b' => 255, 'a' => 1]];
                $graphicsSetting = (object)array_merge((array)$graphicsSetting, (array)$extended);
            }
            if (!isset($graphicsSetting->headerColor2)) {
                $extended = ['headerColor2' => (object)['r' => 0, 'g' => 0, 'b' => 0, 'a' => 1]];
                $graphicsSetting = (object)array_merge((array)$graphicsSetting, (array)$extended);
            }
            $last_rec = SuperadminSetting::where('setting_key', 'graphic_config')->update(['setting_value' => json_encode($graphicsSetting)]);

        }

        public function getDashboardWorkshop()
        {
            $workshop_data = $this->getWorkshopIds();
            $wids = array_column($workshop_data, 'id');
            $workshops = Workshop::with(['meetings', 'meetings.presences' => function ($q) {
                $q->where('user_id', Auth::user()->id)->groupBy('meeting_id')->pluck('id', 'presence_status');
            }])->whereIn('id', $wids)->get(['id', 'workshop_name']);
            $finalWorkshops = [];
            foreach ($workshops as $k => $value) {
                if (count($value->meetings) > 0) {
                    $finalWorkshops[] = $value;
                }
            }
            $doodleMeeting = Workshop::with(['meetings' => function ($query) {
                $query->where('meeting_date_type', 0)->pluck('id', 'name');
            }, 'meetings.presences'                     => function ($q) {
                $q->where('user_id', Auth::user()->id)->groupBy('meeting_id');
            }, 'meetings.doodleDates'                   => function ($query) {
                $query->where(DB::raw('concat(date," ",start_time)'), '>=', Carbon::now('Europe/Paris')->format('Y-m-d H:i:s'));
            }])->whereIn('id', array_column($finalWorkshops, 'id'))->get(['id', 'workshop_name']);

            $data['workshops'] = $finalWorkshops;
            $data['workshops_doodle'] = $doodleMeeting;
            return response()->json($data);
        }

        public function scriptForPassCode($id)
        {

            if ($id == "all") {
                $hostnames = Hostname::get(); //dd($hostnames);

                foreach ($hostnames as $key => $value) {
                    # code...
                    $host = Hostname::find($value->id);
                    $this->tenancy->hostname($host);
                    $hostname = $this->getHostNameData();
                    $hostCode = DB::connection('mysql')->table('hostname_codes')->where(['fqdn' => $hostname->fqdn])->first(['hash']);
                    if ($hostCode) {

                        $allUsers = User::get(['id']);
                        foreach ($allUsers as $val) {
                            $randCode = generateRandomValue(3);

                            $newCode = setPasscode($hostCode->hash, $randCode);

                            $updateUser = User::where('id', $val->id)->update([
                                'login_code' => $newCode['userCode'],
                                'hash_code'  => $newCode['hashCode'],
                            ]);
                        }
                    }

                }
                dd('done');
            }
            $hostnames = Hostname::find($id); //dd($hostnames);
            $host = Hostname::find($hostnames->id);
            $this->tenancy->hostname($host);
            $hostname = $this->getHostNameData();
            $hostCode = DB::connection('mysql')->table('hostname_codes')->where(['fqdn' => $hostname->fqdn])->first(['hash']);
            $allUsers = User::whereNull('login_code')->get(['id']);
            foreach ($allUsers as $val) {
                $randCode = generateRandomValue(3);
                $newCode = setPasscode($hostCode->hash, $randCode);
                $updateUser = User::where('id', $val->id)->update([
                    'login_code' => $newCode['userCode'],
                    'hash_code'  => $newCode['hashCode'],
                ]);
            }

            dd('done');
        }

        //PREPD Final via cron
        public function doFinalPREPD($id)
        {
            // 'start_time'=>date('h:i:s')
            if (empty($id)) {
                $hostnames = Hostname::orderBy('id', 'desc')->get(); //dd($hostnames);
                foreach ($hostnames as $key => $value) {
                    $host = Hostname::find($value->id);
                    $this->tenancy->hostname($host);
                    $hostname = $this->getHostNameData();
                    $meetingIDS = Meeting::where(DB::raw('concat(date," ",start_time)'), '<=', Carbon::now('Europe/Paris')->format('Y-m-d H:i:s'))/*whereRaw("CONCAT(`date`,' ',`start_time`)<NOW()")*/
                    ->where('date', '!=', NULL)->where('is_prepd_final', 0)->pluck('id');

                    if (!empty($meetingIDS)) {
                        $meetingUpdate = Meeting::whereIn('id', $meetingIDS)->update(['validated_prepd' => 1, 'is_prepd_final' => 1]);
                        if ($meetingUpdate) {
                            foreach ($meetingIDS as $key => $value) {
                                $meeting = meeting::find($value);
                                $data = ['wid' => $meeting->workshop_id, 'mid' => $value, 'version' => '-FINAL'];
                                $workshopData = workshop::find($meeting->workshop_id);
                                if (!empty($workshopData)) {
                                    // generate prepd pdf
                                    $pdfData = $this->core->prepdPdf($data);
                                    $domain = strtok($_SERVER['SERVER_NAME'], '.');
                                    $WorkshopName = $this->core->Unaccent(str_replace(' ', '-', $workshopData->workshop_name));
                                    //saving file to s3
                                    $fileName = $this->core->localToS3Upload($domain, $WorkshopName, 'PREPD', $pdfData['pdf_name']);
                                    //save in regular documents
                                    //issuer id  , document type id pending :: ask
                                    $updateDoc = RegularDocument::create([
                                        'workshop_id'        => $meeting->workshop_id,
                                        'event_id'           => $value,
                                        'created_by_user_id' => $workshopData->validator_id,
                                        'issuer_id'          => 1,
                                        'document_type_id'   => 2,
                                        'document_title'     => $pdfData['title'],
                                        'document_file'      => $fileName,
                                        'increment_number'   => $pdfData['inc_number'],
                                    ]);
                                }
                            }

                        }

                        $this->doFinalREPD($meetingIDS);
                    }
                }
            } else {
                $hostnames = Hostname::find($id); //dd($hostnames);
                $host = Hostname::find($hostnames->id);
                $this->tenancy->hostname($host);
                $hostname = $this->getHostNameData();
                // $meetingIDS = Meeting::where(DB::raw('concat(date," ",start_time)'), '<=', Carbon::now('Europe/Paris')->format('Y-m-d H:i:s'))/*whereRaw("CONCAT(`date`,' ',`start_time`)<NOW()")*/
                // ->where('date', '!=', null)->where('is_prepd_final', 0)->pluck('id');

                $meetingIDS = Meeting::where(DB::raw('concat(date," ",start_time)'), '<=', Carbon::now('Europe/Paris')->format('Y-m-d H:i:s'))/*whereRaw("CONCAT(`date`,' ',`start_time`)<NOW()")*/
                ->where('date', '!=', NULL)->where('is_prepd_final', 0)->pluck('id');
//            $this->doFinalREPD($meetingIDS);


                if (!empty($meetingIDS)) {
                    $meetingUpdate = Meeting::whereIn('id', $meetingIDS)->update(['validated_prepd' => 1, 'is_prepd_final' => 1]);
                    if ($meetingUpdate) {
                        foreach ($meetingIDS as $key => $value) {
                            $meeting = meeting::find($value);
                            $data = ['wid' => $meeting->workshop_id, 'mid' => $value, 'version' => '-FINAL'];
                            $workshopData = workshop::find($meeting->workshop_id);
                            if (!empty($workshopData)) {
                                // generate prepd pdf
                                $pdfData = $this->core->prepdPdf($data);
                                $domain = strtok($_SERVER['SERVER_NAME'], '.');
                                $WorkshopName = $this->core->Unaccent(str_replace(' ', '-', $workshopData->workshop_name));
                                //saving file to s3
                                $fileName = $this->core->localToS3Upload($domain, $WorkshopName, 'PREPD', $pdfData['pdf_name']);
                                //save in regular documents
                                //issuer id  , document type id pending :: ask
                                $updateDoc = RegularDocument::create([
                                    'workshop_id'        => $meeting->workshop_id,
                                    'event_id'           => $value,
                                    'created_by_user_id' => $workshopData->validator_id,
                                    'issuer_id'          => 1,
                                    'document_type_id'   => 2,
                                    'document_title'     => $pdfData['title'],
                                    'document_file'      => $fileName,
                                    'increment_number'   => $pdfData['inc_number'],
                                ]);
                            }
                        }

                    }

                    $this->doFinalREPD($meetingIDS, $hostnames);
                }
                //dd('done');
            }
        }

        //PREPD Final via cron

        public function doFinalREPD($meetingID, $hostnames)
        {
            $host = Hostname::find($hostnames->id);
            $this->tenancy->hostname($host);
            $hostname = $this->getHostNameData();
            // 'start_time'=>date('h:i:s')

            if (count($meetingID) > 0) {
                $meetingIDS = Meeting::whereNotIn('id', $meetingID)->where(DB::raw('concat(date," ",start_time)'), '<=', Carbon::now('Europe/Paris')->format('Y-m-d H:i:s'))/*whereRaw("CONCAT(`date`,' ',`start_time`)<NOW()")*/
                ->where('date', '!=', NULL)/*->where('workshop_id', 40)->orderBy('id','desc')*/
                ->where('is_repd_final', 0)->pluck('id');

                if (!empty($meetingIDS)) {
                    $meetingUpdate = Meeting::whereIn('id', $meetingIDS)->update(['validated_repd' => 1, 'is_repd_final' => 1]);
                    if ($meetingUpdate) {
                        foreach ($meetingIDS as $key => $value) {
                            $meeting = meeting::find($value);
                            $data = ['wid' => $meeting->workshop_id, 'mid' => $value, 'version' => '-FINAL'];
                            $workshopData = workshop::find($meeting->workshop_id);
                            if (!empty($workshopData)) {

                                // generate prepd pdf
                                $pdfData = $this->core->repdPdf($data);
                                $domain = strtok($_SERVER['SERVER_NAME'], '.');
                                $WorkshopName = $this->core->Unaccent(str_replace(' ', '-', $workshopData->workshop_name));
                                //saving file to s3
                                $fileName = $this->core->localToS3Upload($domain, $WorkshopName, 'REPD', $pdfData['pdf_name']);
                                //save in regular documents
                                //issuer id  , document type id pending :: ask
                                $updateDoc = RegularDocument::create([
                                    'workshop_id'        => $meeting->workshop_id,
                                    'event_id'           => $value,
                                    'created_by_user_id' => $workshopData->validator_id,
                                    'issuer_id'          => 1,
                                    'document_type_id'   => 3,
                                    'document_title'     => $pdfData['title'],
                                    'document_file'      => $fileName,
                                    'increment_number'   => $pdfData['inc_number'],
                                ]);
                            }
                        }

                    }
                }
            }
        }
        // public function reminder(Request $request)
        // {
        //     // $weekly = DB::table('weekly_reminders')->get(['fqdn'])->pluck('fqdn');
        //     // $hostnames = Hostname::whereIn('fqdn', $weekly)->orderBy('id', 'desc')->get(); //dd($hostnames);
        //     // $getMemberDoodle = $this->getDoodleMember($hostnames);
        //     // foreach ($hostnames as $key => $value) {
        //             $host=Hostname::find(132);
        //                 $this->tenancy->hostname($host);
        //                 $hostname = $this->getHostNameData();
        //                     $this->getDoodleMember();
        //                     $this->doodleReminderAdmin();
        //                     $this->PrepdMeetingReminderAll();
        //                     $this->PrepdMeetingReminderforMemeber();
        //                     $this->MeetingPrepdRepdReminder();
        //     // }

        // }
        public function reminder(Request $request)
        {
            // $weeklyFqdn = DB::table('weekly_reminders')->get(['fqdn'])->pluck('fqdn');
            // $hostnames = Hostname::whereIn('fqdn', $weeklyFqdn)->orderBy('id', 'desc')->get(); //dd($hostnames);
            // $getMemberDoodle = $this->getDoodleMember($hostnames);
            // foreach ($hostnames as $key => $value) {
            $host = Hostname::find(117);
            $weekly = DB::table('weekly_reminders')->where('fqdn', $host->fqdn)->first();

            $this->tenancy->hostname($host);
            $hostname = $this->getHostNameData();
            if ($weekly->doodle_2) {
                $this->getDoodleMember();
            }
            if ($weekly->doodle_10) {
                $this->doodleReminderAdmin();
            }

            if ($weekly->meeting_prepd_2) {
                $this->PrepdMeetingReminderAll();

            }

            if ($weekly->meeting_repd_2) {
                $this->PrepdMeetingReminderforMemeber($weekly);
            }

            $this->MeetingPrepdRepdReminder($weekly);
            // }

        }

        //Doodle reminder to memeber who not voted
        public function getDoodleMember()
        {
            $meeting_ids = Meeting::where(['date' => NULL, 'meeting_date_type' => 0])->where('status', 1)->orderBy('id', 'desc')->get(['id'])->pluck('id');
            $userVoteCount = [];
            $userEmail = [];
            foreach ($meeting_ids as $key => $value) {
                $presencesUser = Presence::where('meeting_id', $value)->groupBy('user_id')->get(['user_id'])->pluck('user_id');
                foreach ($presencesUser as $key => $userId) {
                    $voteCount = DoodleVote::where(['user_id' => $userId, 'meeting_id' => $value])->count();
                    if ($voteCount == 0) {
                        if (isset($userVoteCount[$userId])) {
                            $userVoteCount[$userId] = $userVoteCount[$userId] + 1;
                        } else {
                            $userVoteCount[$userId] = 1;
                        }
                    }
                }
            }
            if (!empty($userVoteCount)) {
                foreach ($userVoteCount as $key => $value) {
                    if ($value > 1) {
                        $user = User::find($key);
                        if ($user != NULL && isset($user->email)) {
                            $userEmail[] = $user->email;
                        }
                    }
                }
            }
            foreach (array_unique($userEmail) as $val) {
                $mailData['mail'] = ['subject' => 'Doodle reminder to Memeber Remaining', 'email' => $val, 'message_text' =>
                    'Doodle vote remaining'];
                $this->core->SendEmail($mailData, 'reminder_email');
            }

        }

        //Doodle reminder to Admin who not finalize 10+ doodle
        public function doodleReminderAdmin()
        {
            $userMeta = WorkshopMeta::where('role', 1)->get();
            $userWorkshopDetail = [];
            $email = [];
            foreach ($userMeta as $key => $value) {
                if (isset($userWorkshopDetail[$value->user->email])) {
                    array_push($userWorkshopDetail[$value->user->email], $value->workshop_id);
                } else {
                    $userWorkshopDetail[$value->user->email] = [$value->workshop_id];
                }
            }
            foreach ($userWorkshopDetail as $key => $value) {
                $meeting_ids = Meeting::where(['date' => NULL, 'meeting_date_type' => 0])->where('status', 1)->whereIn('workshop_id', $value)->count();
                if ($meeting_ids > 10) {
                    $email[] = $key;
                }
            }
            foreach (array_unique($email) as $val) {
                $mailData['mail'] = ['subject' => 'Doodle reminder to Admin', 'email' => $val, 'message_text' =>
                    'Doodle reminder to Admin who not finalize 10+ doodle'];
                $this->core->SendEmail($mailData, 'reminder_email');
            }
        }

        //Prepd reminder meeting 2 day before to all memeber for register/excus
        public function PrepdMeetingReminderAll()
        {
            $email = [];
            $meeting = Meeting::where(['date' => date('Y-m-d', strtotime(date('Y-m-d') . "+2 days"))])->where('status', 1)->get(['id'])->pluck('id');

            $presence = Presence::whereIn('meeting_id', $meeting)->groupBy('user_id')->with('user')->get();
            foreach ($presence as $key => $value) {
                $email[] = $value->user->email;
            }
            foreach (array_unique($email) as $val) {
                $mailData['mail'] = ['subject' => 'Prepd reminder Register Excuses before date', 'email' => $val, 'message_text' =>
                    'Prepd reminder Register Excuses'];
                $this->core->SendEmail($mailData, 'reminder_email');
            }
        }

        //Prepd reminder meeting 2 day after to all memeber for register/excuses
        public function PrepdMeetingReminderforMemeber()
        {
            $email = [];
            $meeting = Meeting::whereRaw('DATE(prepd_published_on) = ?', [date('Y-m-d', strtotime(date('Y-m-d') . "-2 days"))])->get(['id'])->where('status', 1)->pluck('id');

            $presence = Presence::whereIn('meeting_id', $meeting)->where(['register_status' => 'NI', 'presence_status' => 'ANE'])->groupBy('user_id')->with('user')->get();
            foreach ($presence as $key => $value) {
                $email[] = $value->user->email;
            }
            foreach (array_unique($email) as $val) {
                $mailData['mail'] = ['subject' => 'Prepd reminder Register Excuses after date', 'email' => $val, 'message_text' =>
                    'Prepd reminder Register Excuses'];
                $this->core->SendEmail($mailData, 'reminder_email');
            }
        }

        /*Agenda reminder to workshopadmin&deputy  if agenda has not been sent
        At DayOfMeeting - 15
         */
        public function MeetingPrepdRepdReminder($weekly)
        {

            $userMeta = WorkshopMeta::whereIn('role', [1, 2])->get();

            $userWorkshopDetail = [];
            $prepdEmail15 = [];
            $prepdEmail7 = [];
            $repdEmail2 = [];
            $repdEmail7 = [];
            foreach ($userMeta as $key => $value) {
                if (isset($userWorkshopDetail[$value->user->email])) {
                    array_push($userWorkshopDetail[$value->user->email], $value->workshop_id);
                } else {
                    $userWorkshopDetail[$value->user->email] = [$value->workshop_id];
                }
            }

            /*Agenda reminder to workshopadmin&deputy  if agenda has not been sent
            At DayOfMeeting - 15*/
            foreach ($userWorkshopDetail as $key => $value) {
                $meeting_ids = Meeting::where('validated_prepd', 0)->whereDate('created_at', date('Y-m-d', strtotime(date('Y-m-d') . "+15 days")))->whereIn('workshop_id', $value)->where('status', 1)->count();
                if ($meeting_ids) {
                    $prepdEmail15[] = $key;
                }
            }
            /*Agenda reminder to workshopadmin&deputy  if agenda has not been sent
            At DayOfMeeting - 7*/
            foreach ($userWorkshopDetail as $key => $value) {
                $meeting_ids = Meeting::whereIn('workshop_id', $value)->where('validated_prepd', 0)->whereDate('created_at', date('Y-m-d', strtotime(date('Y-m-d') . "+7 days")))->where('status', 1)->count();
                if ($meeting_ids) {
                    $prepdEmail7[] = $key;
                }
            }
            /*Report reminder to workshopadmin&deputy  if Report has not been sent
            At DayOfMeeting+ 2 */
            foreach ($userWorkshopDetail as $key => $value) {

                $meeting_ids = Meeting::whereIn('workshop_id', $value)->where('validated_repd', 0)->where(['date' => date('Y-m-d', strtotime(date('Y-m-d') . "-2 days"))])->where('status', 1)->count();

                if ($meeting_ids) {
                    $repdEmail2[] = $key;
                }
            }
            /*Report reminder to workshopadmin&deputy  if Report has not been sent
            At DayOfMeeting + 7
             */
            foreach ($userWorkshopDetail as $key => $value) {
                $meeting_ids = Meeting::whereIn('workshop_id', $value)->where('validated_repd', 0)->where(['date' => date('Y-m-d', strtotime(date('Y-m-d') . "-7 days"))])->where('status', 1)->count();

                if ($meeting_ids) {
                    $repdEmail7[] = $key;
                }
            }

            //Sending Mail
            if (!empty($prepdEmail15) && $weekly->agenda_15) {
                foreach ($prepdEmail15 as $val) {
                    $mailData['mail'] = ['subject' => 'Agenda Reminder 15', 'email' => $val, 'message_text' =>
                        'Agenda reminder to workshopadmin&deputy  if agenda has not been sent At DayOfMeeting - 15'];
                    $this->core->SendEmail($mailData, 'reminder_email');
                }
            }
            if (!empty($prepdEmail7) && $weekly->agenda_7) {
                foreach (array_unique($prepdEmail7) as $val) {
                    $mailData['mail'] = ['subject' => 'Agenda Reminder 7', 'email' => $val, 'message_text' =>
                        'Agenda reminder to workshopadmin&deputy  if agenda has not been sent At DayOfMeeting - 15'];
                    $this->core->SendEmail($mailData, 'reminder_email');
                }
            }
            if (!empty($repdEmail2) && $weekly->report_2) {
                foreach (array_unique($repdEmail2) as $val) {
                    $mailData['mail'] = ['subject' => 'Repd reminder 2', 'email' => $val, 'message_text' =>
                        'Agenda reminder to workshopadmin&deputy  if agenda has not been sent At DayOfMeeting - 15'];
                    $this->core->SendEmail($mailData, 'reminder_email');
                }
            }
            dump($repdEmail7, $weekly->report_7);
            if (!empty($repdEmail7) && $weekly->report_7) {
                foreach (array_unique($repdEmail7) as $val) {

                    $mailData['mail'] = ['subject' => 'Repd reminder 7', 'email' => $val, 'message_text' =>
                        'Agenda reminder to workshopadmin&deputy  if agenda has not been sent At DayOfMeeting - 15'];
                    $this->core->SendEmail($mailData, 'reminder_email');
                }
            }

        }

        public function getReportForTwo($hostnames)
        {
            if (!empty($hostnames)) {
                for ($i = 0; $i < count($hostnames); $i++) {
                    $this->tenancy->hostname($hostnames[$i]);
                    $hostname = $this->getHostNameData();
                    $user = User::get(['id', 'role']);
                    foreach ($user as $item) {
                        WorkshopMeta::where('user_id', $item->id)->first();
                    }
                }

            }
        }

        public function deleteNSL($id)
        {
            $rec = AccountSettings::where('account_id', $id)->first();
            $setting = $rec->setting;
            if ($setting['news_letter_enable'] == 1) {
                $setting['news_letter_enable'] = 0;
                AccountSettings::where('account_id', $id)->update(['setting' => json_encode($setting)]);
            }
            $hostname = Hostname::find($id);
            $check = 0;
            $host = $this->tenancy->hostname($hostname);
            $workshop = Workshop::where('code1', "NSL")->first();
            if ($workshop) {
                $project = Project::where('wid', $workshop->id);
                if ($project->get()) {
                    $milestone = Milestone::whereIn('project_id', $project->pluck('id'))->delete();
                    $project->delete();
                }
                $workshop->delete();

            }
            return TRUE;
        }

        public function migrationModuleSeederScript($module, $class)
        {
            $script = shell_exec("php artisan db:seed --class=" . DIRECTORY_SEPARATOR . "Modules" . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . "Database" . DIRECTORY_SEPARATOR . "Seeders" . DIRECTORY_SEPARATOR . $class . " --force");

            dd($script);
        }


        public function unionToEntityMigration($id=Null)
        {
            if(!empty($id)){
                $hostnames = Hostname::where('id',$id)->get();
            }else{
                $hostnames = Hostname::get();
            }

            foreach ($hostnames as $key => $value) {
                $entityIns = [];
                $host = Hostname::find($value->id);
                $this->tenancy->hostname($host);
                $entities = Entity::where('entity_type_id', 3)->get(['id', 'long_name', 'short_name']);
                $industries = Industry::whereNotNull('parent')->get(['id', 'name']);
                $unions = Union::with('unionContacts')->orderBy('id', 'asc')->groupBy('union_code')->get();
                $longName = array_map('strtolower', $entities->pluck('long_name')->toArray());
                $shortName =array_map('strtolower', $entities->pluck('short_name')->toArray());
                foreach ($unions as $union) {
                    if ((!in_array(strtolower($union->union_name), $longName) && !in_array(strtolower($union->union_code), $shortName))) {
                        $entityIns[] = [
                            'long_name'          => $union->union_name,
                            'short_name'         => $union->union_code,
                            'address1'           => $union->address1,
                            'address2'           => $union->address2,
                            'zip_code'           => $union->postal_code,
                            'city'               => $union->city,
                            'country'            => $union->country,
                            'phone'              => $union->telephone,
                            'email'              => $union->email,
                            'entity_type_id'     => 3,
                            'created_by'         => 0,
                            'is_active'          => 1,
                            'created_at'         => $union->created_at,
                            'updated_at'         => $union->updated_at,
                            'entity_description' => $union->union_description,
                            'entity_logo'        => $union->logo,
                            'entity_website'     => $union->website,
                            'industry_id'        => (in_array($union->industry_id, $industries->pluck('id')->toArray()) ? $union->industry_id : NULL),
                            'fax'                => $union->fax,
                            'entity_ref_type'    => 0,
                            'is_internal'        => $union->is_internal,
                            'entity_sub_type'    => ($union->union_type == 0) ? 1 : 2,
                        ];
                        // For many relations:
                        if (!$union->unionContacts->isEmpty()) {
                            foreach ($union->unionContacts as $val) {
                                $contacts[] = ['fname' => $val['f_name'], 'lname' => $val['l_name'], 'position' => $val['position'], 'avator' => $val['photo']];
                            }
                        }
                    }
                }

                if (count($entityIns) > 0) {
                    Entity::insert($entityIns);
                }
            }
        }
    }
