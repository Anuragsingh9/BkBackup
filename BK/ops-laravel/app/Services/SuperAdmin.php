<?php
    /**
     * Created by PhpStorm.
     * User: Sourabh Pancharia
     * Date: 5/28/2019
     * Time: 12:14 PM
     */

    namespace App\Services;

    use App\AccountSettings;
    use App\Grdp;
    use App\MessageCategory;
    use App\Milestone;
    use App\Organisation;
    use App\Project;
    use App\Setting;
    use App\Task;
    use App\User;
    use App\WorkshopCode;
    use App\WorkshopMeta;
    use DB, Validator;
    use App\Workshop;
    use Excel;
    use Hyn\Tenancy\Models\Hostname;
    use Illuminate\Http\Request;
    use Illuminate\Validation\Rule;
    use Modules\Crm\Entities\Assistance;
    use Auth;
    use Illuminate\Support\Facades\Storage;
    use Carbon;
    use App\Imports\ProjectImport;
    use Modules\Crm\Entities\CrmFilterType;
    use Modules\Crm\Rules\CrmFilterComponentExist;
    use Modules\Crm\Rules\CrmFilterTypeExist;
    use Modules\Crm\Services\CrmServices;

    class SuperAdmin
    {
        /**
         * SuperAdminSingleton constructor.
         */
        private $tenancy, $core, $import;
        protected static $instance;

        public function __construct()
        {
            $this->tenancy = app(\Hyn\Tenancy\Environment::class);
            $this->core = app(\App\Http\Controllers\CoreController::class);
            $this->import = app(\App\Http\Controllers\import\ImportController::class);
        }

        /**
         * Make instance of SuperAdmin singleton class
         * @return SuperAdmin|null
         */
        public static function getInstance()
        {
            static $instance = NULL;
            if (NULL === $instance) {
                $instance = new static();
            }
            return $instance;
        }

        /**
         * create a default commission for Module wise
         * we use $type for determine that what type of
         * commission we need to create we have commission or workshop.
         * @param int $type
         * @param string $name
         * @param mixed $code1
         * @return \Illuminate\Http\JsonResponse
         */
        public function createCommission($type = 1, $name = 'Newsletter', $code1 = 'NSL', $projectEnable)
        {

            // ? comment this for add workshop for a member
            //if (session()->has('superadmin')) {
            try {
                //start transaction for skip the wrong entry
                //DB::connection('tenant')->beginTransaction();
                $user = User::first(['id']);
                $workshop = ['president_id' => $user->id, 'validator_id' => $user->id, 'workshop_name' => $name, 'workshop_desc' => '', 'code1' => $code1, 'code2' => '', 'workshop_type' => $type, 'is_private' => 1];
                $codeId = WorkshopCode::insertGetId(['code' => $code1]);
                $id = Workshop::insertGetId($workshop);
                if ($id) {
                    WorkshopCode::where('id', $codeId)->update(['workshop_id' => $id]);
                    $workshop_meta[0] = ['workshop_id' => $id, 'role' => '1', 'user_id' => $user->id];
                    $workshop_meta[1] = ['workshop_id' => $id, 'role' => '2', 'user_id' => $user->id];
                    $newRec = WorkshopMeta::insert($workshop_meta);
                    //add first message category under new created workshop
                    $msg = MessageCategory::insert(['category_name' => 'General', 'workshop_id' => $id, 'status' => 1]);
                    if ($code1 == 'NSL') {
                        //check that project template exists or not
                        $template = $this->checkProjectTemplate();
                        if (!empty($template))
                            $this->createProjectAndMilestone($id, $template, $projectEnable);
                    }
                    if ($code1 == 'CRM') {
                        $this->createDefaultProject('CRM', $id, $projectEnable, 'CRM');
                    }
                    $assistance = Assistance::orderBy('id', 'desc')->first();

                    // DD($assistance);
                    if ($code1 == $assistance->assistance_type_short_name) {

                        $this->createDefaultProject($assistance->assistance_type_name, $id, $projectEnable, 'Assistance');

                    }

//                    DB::connection('tenant')->commit();
                    return TRUE;
                }
            } catch (\Exception $e) {
//            dd($e->getMessage());
                return FALSE;
//                DB::connection('tenant')->rollBack();
            }
            //}
        }

        /**
         * this function return what is the final
         * setting for newsletter or crm module(on/Off)
         *
         * TODO[REVIEW] can not add multiple values for first time
         * @param $rec
         * @param $request
         * @param $postData
         * @return int|string
         */
        public function setAccountSetting($rec, $request, &$postData)
        {
            // dd($request->all());

            $hostname = Hostname::find($rec->account_id);
            $host = $this->tenancy->hostname($hostname);
            $code = 'NSL';
            $workshopName = 'Newsletter';
            if ((!isset($rec->setting['news_letter_enable'])) && ($request->news_letter_enable == 1)) {
                $code = 'NSL';
                $workshopName = 'Newsletter';
                $returnType = 'setting';
                $returnValue =
                    '{'
                    . '"news_letter_enable":1,"manage_template":' . (($rec->setting['manage_template']) ?? 0)
                    /*. ',"crm_enable":' . (($rec->setting['crm_enable']) ?? 0)*/
                    . '}';
                $this->checkAndAddWorkshop($code, $workshopName, $rec->account_id, $rec->project_enable);
                $postData["$returnType"] = $returnValue;
            } elseif (isset($rec->setting['news_letter_enable'])) {
                $code = 'NSL';
                $workshopName = 'Newsletter';
                $returnType = 'setting->news_letter_enable';
                $returnValue = checkValSet($request->news_letter_enable);
                //display off for CRM commission
                Workshop::where('code1', 'NSL')
                    ->withoutGlobalScopes()
                    ->update(['display' => checkValSet($request->news_letter_enable)]);
                $postData['newsletter_menu_enable'] = $returnValue;
                $this->checkAndAddWorkshop($code, $workshopName, $rec->account_id, $rec->project_enable);
                $postData["$returnType"] = $returnValue;

            }
            if (isset($rec->setting['manage_template'])) {
                $code = 'NSL';
                $workshopName = 'Newsletter';
                $returnType = 'setting->manage_template';
                $returnValue = checkValSet($request->manage_template);
                $postData["$returnType"] = $returnValue;
            }
            if ((!isset($rec->setting['crm_enable'])) && ($request->crm_enable == 1)) {

                $code = 'CRM';
                $workshopName = 'CRM';
                $returnType = 'setting';
                $returnValue = '{"news_letter_enable":' . (($rec->setting['news_letter_enable']) ?? 0)
                    . ',"manage_template":' . (($rec->setting['manage_template']) ?? 0)
                    . ',"crm_enable":1'
                    . '}';
                $this->checkAndAddWorkshop($code, $workshopName, $rec->account_id, $rec->project_enable);
                $postData["$returnType"] = $returnValue;
                $postData['crm_menu_enable'] = 1;
                $postData['custom_profile_enable'] = 1;
                $this->defaultFilter();
                //display off for CRM commission
                Workshop::where('code1', 'CRM')
                    ->withoutGlobalScopes()
                    ->update(['display' => checkValSet($request->crm_enable)]);

            } elseif (isset($rec->setting['crm_enable'])) {
                $code = 'CRM';
                $workshopName = 'CRM';
                $returnType = 'setting->crm_enable';
                $returnValue = checkValSet($request->crm_enable);
                //display off for CRM commission
                Workshop::where('code1', 'CRM')
                    ->withoutGlobalScopes()
                    ->update(['display' => checkValSet($request->crm_enable)]);
                $postData['crm_menu_enable'] = $returnValue;
                $postData["$returnType"] = $returnValue;


            }
            if ((isset($rec->setting['news_letter_enable']) && checkValSet($request->news_letter_enable) == 1) || (isset($rec->setting['crm_enable']) && checkValSet($request->crm_enable) == 1)) {
                //checking need to add commission or not
                $this->checkAndAddWorkshop($code, $workshopName, $rec->account_id, $rec->project_enable);
                $workshops = Workshop::whereIn('code1', [$code])->withoutGlobalScopes()->get(['id']);
                if (count($workshops) > 0) {
                    Project::where('wid', $workshops->pluck('id'))->withoutGlobalScopes()->update(['display' => 1]);
                }
            }
            if ((isset($rec->setting['news_letter_enable']) && checkValSet($request->news_letter_enable) == 0) || (isset($rec->setting['crm_enable']) && checkValSet($request->crm_enable) == 0)) {
                $workshops = Workshop::whereIn('code1', [$code])->withoutGlobalScopes()->get(['id']);
                if (count($workshops) > 0) {
                    Project::where('wid', $workshops->pluck('id'))->withoutGlobalScopes()->update(['display' => 0]);
                }
            }
            $postData['setting->workshop_graphic_enable'] = isset($request->workshop_graphic_enable) ? checkValSet($request->workshop_graphic_enable) : 0;
            $postData['setting->workshops_enable'] = isset($request->workshops_enable) ? checkValSet($request->workshops_enable) : 0;
            $postData['setting->documents_enable'] = isset($request->documents_enable) ? checkValSet($request->documents_enable) : 0;

            /*
               * adding condition for instance and press enable/disable
               * */
            if (empty($rec->setting) && $request->instance_enable == 1) {
                if (isset($postData["setting"])) {
                    $consist = json_decode($postData["setting"]);
                    $consist->instance_enable = checkValSet($request->instance_enable);
                    $postData["setting"] = (json_encode($consist));
                } else {
                    $returnType = '{"instance_enable":' . checkValSet($request->instance_enable) . '}';
                    $returnValue = $returnType;
                    $postData["setting"] = $returnValue;

                }
                $this->enableCrmForInstancePress(1, $rec);
            } else {
                $postData['setting->instance_enable'] = checkValSet($request->instance_enable);
                $this->enableCrmForInstancePress(0, $rec, checkValSet($request->instance_enable));
            }

            if (empty($rec->setting) && $request->press_enable == 1) {
                if (isset($postData["setting"])) {
                    $consist = json_decode($postData["setting"]);
                    $consist->press_enable = checkValSet($request->press_enable);
                    $postData["setting"] = (json_encode($consist));
                } else {
                    $returnType = '{"press_enable":' . checkValSet($request->press_enable) . '}';
                    $returnValue = $returnType;
                    $postData["setting"] = $returnValue;
                }
                $this->enableCrmForInstancePress(1, $rec);
            } else {
                $postData['setting->press_enable'] = checkValSet($request->press_enable);
                $this->enableCrmForInstancePress(0, $rec, checkValSet($request->press_enable));
            }
            //this function is for running migration when we on any Module
            // $postData['setting->lang'] = (isset($request->lang) && !empty($request->lang)) ? $request->lang : config('constants.DEFAULT_LANG');
            // session()->put('lang', $request->lang);
            // if (isset($request->lang) && !empty($request->lang)) {
            //      $user = User::whereRaw('1=1')->update(['setting' => '{"lang":"' . $request->lang . '"}']);
            // }
            //for direct video conversation enable
            $postData['setting->direct_video_enable'] = isset($request->direct_video_enable) ? $request->direct_video_enable : 0;
            // dd($postData);
            /*
    +             * */
            //  $this->setResilience($rec, $postData, $request);
            return $postData;
        }
        
        /**
         * this function is used to check that commission
         * already created or not and id for identifying the account.
         * @param string $code
         * @param int $id
         * @param string $workShopName
         */
        public function checkAndAddWorkshop($code = 'NSL', $workShopName = 'Newsletter', $id = 0, $projectEnable)
        {
            /*@todo need to check that connecion is good or not as set in parent funtion*/
            $hostname = Hostname::find($id);
            $host = $this->tenancy->hostname($hostname);
            //getting  count of Newsletter workshop
            $checkCommission = Workshop::where('code1', $code)->withoutGlobalScopes()->count();
            //checking workshop created or not
            if ($checkCommission == 0) {
                //here adding the first time workshop with method default value
                $this->createCommission(1, $workShopName, $code, $projectEnable);
            }


        }

        /**
         * this function is used for creating project and milestone with task
         * same as project import but only first entry from uploaded
         * project template if template exists in s3.
         * @param $id
         * @param $template
         */
        // public function createProjectAndMilestone($id, $template, $projectEnable)
        // {

        //     if (!empty($template)) {
        //         $excelData = Excel::load(public_path('public/tempDoc/') . $template, function ($reader) {
        //         })->get();
        //         $count = 0;
        //         $errors = [];
        //         $scuess = [];

        //         if (!empty($excelData) && count($excelData) > 0) {
        //             $records = $excelData->toArray();
        //             $recordCollection = collect($records);
        //             $validRecord = collect();
        //             $totalrecord = count($records);
        //             $error = $this->import->validationExcel($records);
        //             if ($this->import->wrongFileValidation($records[0], 'project')) {
        //                 $recordCollection->each(function ($item, $k) use (&$errors, $validRecord) {
        //                     if (!checkValidDate($item['milestone_start_date'])) {
        //                         $errors[] = ['line' => ($k + 2), 'msg' => 'Invalid milestone start date "' . $item['milestone_start_date'] . '"'];
        //                     } else if (!checkValidDate($item['milestone_end_date'])) {
        //                         $errors[] = ['line' => ($k + 2), 'msg' => 'Invalid milestone end date "' . $item['milestone_end_date'] . '"'];
        //                     } else if (!checkValidDate($item['task_start_date'])) {
        //                         $errors[] = ['line' => ($k + 2), 'msg' => 'Invalid task start date "' . $item['task_start_date'] . '"'];
        //                     } else if (!checkValidDate($item['task_end_date'])) {
        //                         $errors[] = ['line' => ($k + 2), 'msg' => 'Invalid task end date "' . $item['task_end_date'] . '"'];
        //                     } else {
        //                         $validRecord->push($item);
        //                     }
        //                 });

        //                 foreach ($validRecord->toArray() as $k => $val) {
        //                     $flag = 0;
        //                     {
        //                         $project = Project::where(['project_label' => $val['project_name'], 'wid' => $id])->first(['id']);
        //                         if (!$project) {
        //                             $project = Project::create([
        //                                 'project_label' => $val['project_name'],
        //                                 'wid' => $id,
        //                                 'user_id' => 0,
        //                                 'color_id' => 1,
        //                                 'display' => $projectEnable,
        //                             ]);
        //                             $flag = 1;
        //                         } else {
        //                             $milestone = Milestone::where(['project_id' => $project->id, 'label' => $val['milestone_name']])->first(['id', 'label']);
        //                             if (!$milestone) {
        //                                 $flag = 1;
        //                             } else {
        //                                 $flag = 2;
        //                             }
        //                         }
        //                         switch ($flag) {
        //                             case 1:
        //                                 $milestone = Milestone::create([
        //                                     'project_id' => $project->id,
        //                                     'label' => $val['milestone_name'],
        //                                     'user_id' => 0,
        //                                     'color_id' => 1,
        //                                     'start_date' => (isset($val['milestone_start_date']) && !empty($val['milestone_start_date'])) ? Carbon\Carbon::parse($val['milestone_start_date'])->format('Y-m-d') : Carbon::now()->format('Y-m-d'),
        //                                     'end_date' => Carbon\Carbon::parse($val['milestone_end_date'])->format('Y-m-d'),
        //                                 ]);
        //                             case 2:
        //                                 $scuess[$count] = Task::create([
        //                                     'workshop_id' => $id,
        //                                     'task_created_by_id' => 0,
        //                                     'task_text' => $val['task_name'],
        //                                     'milestone_id' => $milestone->id,
        //                                     'start_date' => Carbon\Carbon::parse($val['task_start_date'])->format('Y-m-d'),
        //                                     'end_date' => Carbon\Carbon::parse($val['task_end_date'])->format('Y-m-d'),
        //                                     'assign_for' => 1,
        //                                     'activity_type_id' => 1,
        //                                     'status' => 1,
        //                                     'task_color_id' => 1,
        //                                 ]);
        //                                 break;
        //                         }
        //                         Storage::disk('localDocPdf')->delete('project.xls');
        //                         break;
        //                     }
        //                 }
        //             }
        //         }
        //     }
        // }


        public function createProjectAndMilestone($id, $template, $projectEnable)
        {

            if (!empty($template)) {
                $excelData = Excel::toArray(new ProjectImport($id, $projectEnable), public_path('public/tempDoc/') . $template);

                $count = 0;
                $errors = [];
                $scuess = [];

                if (!empty($excelData) && count($excelData) > 0) {
                    // $records = $excelData->toArray();
                    $records = $excelData[0];
                    $recordCollection = collect($records);
                    $validRecord = collect();
                    $totalrecord = count($records);
                    // $error = $this->import->validationExcel($records);
                    // dd($records);

                    // foreach ($records as $k => $val) {
                    for ($i = 1; $i < $totalrecord; $i++) {
                        $val = $records[$i];
                        $flag = 0;

                        $project = Project::where(['project_label' => $val[0], 'wid' => $id])->first(['id']);

                        if ($project == NULL) {
                            $project = Project::create([
                                'project_label' => $val[0],
                                'wid'           => $id,
                                'user_id'       => 0,
                                'color_id'      => 1,
                                'display'       => $projectEnable,
                            ]);
                            // dd($project);
                            $flag = 1;
                        } else {
                            $milestone = Milestone::where(['project_id' => $project->id, 'label' => $val[1]])
                                ->first(['id', 'label']);
                            if (!$milestone) {
                                $flag = 1;
                            } else {
                                $flag = 2;
                            }
                        }

                        switch ($flag) {
                            case 1:
                                $milestone = Milestone::create([
                                    'project_id' => $project->id,
                                    'label'      => $val[1],
                                    'user_id'    => (Auth::check()) ? Auth::user()->id : 0,
                                    'color_id'   => 1,
                                    'start_date' => (isset($val[2]) && !empty($val[2])) ? Carbon\Carbon::parse($val[2])
                                        ->format('Y-m-d') : Carbon::now()->format('Y-m-d'),
                                    'end_date'   => Carbon\Carbon::parse($val[3])->format('Y-m-d'),
                                ]);
                            case 2:
                                $scuess[$count] = Task::create([
                                    'workshop_id'        => $id,
                                    'task_created_by_id' => (Auth::check()) ? Auth::user()->id : 0,
                                    'task_text'          => $val[4],
                                    'milestone_id'       => $milestone->id,
                                    'start_date'         => Carbon\Carbon::parse($val[5])->format('Y-m-d'),
                                    'end_date'           => Carbon\Carbon::parse($val[6])->format('Y-m-d'),
                                    'assign_for'         => 1,
                                    'activity_type_id'   => 1,
                                    'status'             => 1,
                                    'task_color_id'      => 1,
                                ]);
                                break;
                        }
                        Storage::disk('localDocPdf')->delete('project.xls');
                        break;


                    }
                }
            }
        }

        /**
         * this function is used to check that template is uploaded
         * or not if yes then download in local storage
         * @return null|string
         */
        public function checkProjectTemplate()
        {
            $project = Grdp::where(['type' => 1])->whereNotNull('upload_en')->first();
            if (!empty($project)) {
                $file_name = str_replace(' ', '-', $project->title_en);
                $ext = pathinfo($project->upload_en, PATHINFO_EXTENSION);
                if (Storage::disk('localDocPdf')->put('project.xls', Storage::disk('s3')->get($project->upload_en)))
                    $download_url = 'project.xls';
                else
                    $download_url = NULL;
                return $download_url;
            } else {
                return $download_url = NULL;
            }
        }

        public function createDefaultProject($projectLabel, $wid, $projectEnable, $mileStone)
        {

            $flag = 0;
            $project = Project::where(['project_label' => $projectLabel, 'wid' => $wid])->first(['id']);
            if (!$project) {
                $project = Project::create([
                    'project_label' => $projectLabel,
                    'wid'           => $wid,
                    'user_id'       => 0,
                    'color_id'      => 1,
                    'display'       => (!empty($projectEnable) ? $projectEnable : 0),
                ]);
                $flag = 1;
            } else {
                $milestone = Milestone::where(['project_id' => $project->id, 'label' => $mileStone])
                    ->first(['id', 'label']);
                if (!$milestone) {
                    $flag = 1;
                }
            }
            switch ($flag) {
                case 1:
                    $milestone = Milestone::create([
                        'project_id' => $project->id,
                        'label'      => $mileStone,
                        'user_id'    => 0,
                        'color_id'   => 1,
                        'start_date' => Carbon\Carbon::now()->format('Y-m-d'),
                        'end_date'   => Carbon\Carbon::now()->format('Y-m-d'),
                    ]);
                    break;
            }
        }

        public function setIcontactAccountSetting($rec, $request, &$postData)
        {
            $postData['setting->ICONTACT_API_APP_ID'] = isset($request->ICONTACT_API_APP_ID) ? $request->ICONTACT_API_APP_ID : '';
            $postData['setting->ICONTACT_API_PASSWORD'] = isset($request->ICONTACT_API_PASSWORD) ? $request->ICONTACT_API_PASSWORD : '';
            $postData['setting->ICONTACT_API_USERNAME'] = isset($request->ICONTACT_API_USERNAME) ? $request->ICONTACT_API_USERNAME : '';
            $postData['setting->ICONTACT_CLIENT_FOLDER_ID'] = isset($request->ICONTACT_CLIENT_FOLDER_ID) ? $request->ICONTACT_CLIENT_FOLDER_ID : '';

            $postData['setting->ICONTACT_ACCOUNT_ID'] = isset($request->ICONTACT_ACCOUNT_ID) ? $request->ICONTACT_ACCOUNT_ID : '';
            $postData['setting->qualification_enable'] = isset($request->qualification_module_enable) ? $request->qualification_module_enable : 0;
            $postData['setting->messenger_enable'] = isset($request->messenger_enable) ? $request->messenger_enable : 0;
            $postData['setting->organiser_setting_enable'] = isset($request->organiser_setting_enable) ? $request->organiser_setting_enable : 0;

            return $postData;
        }


        public function setVideoMeetingAccountSetting($rec, $request, &$postData)
        {
            $settingValues = [
                'client_id'         => isset($request->client_id) ? $request->client_id : NULL,
                'client_secret'     => isset($request->client_secret) ? $request->client_secret : NULL,
                'number_of_license' => isset($request->vm_bluejeans_licenses) ? $request->vm_bluejeans_licenses : NULL,
            ];
            if ((!isset($request->client_id) || $request->client_id == '')
                && (!isset($request->client_secret) || $request->client_secret == '')) {
                $settingValues['client_id'] = env('BLUEJEANS_DEFAULT_ID');
                $settingValues['client_secret'] = env('BLUEJEANS_DEFAULT_SECRET');
                $settingValues['number_of_license'] = 1;
            }
            if (checkValSet($request->video_meeting_enable) && empty($rec->setting)) {
                $returnType = 'setting';
                $returnValue = '{'
                    . '"video_meeting_enable":' . (checkValSet($request->video_meeting_enable))
                    . ',"news_letter_enable":' . (checkValSet($request->news_letter_enable))
                    . ',"manage_template":' . (checkValSet($request->manage_template))
                    /* . ',"crm_enable":' . (checkValSet($request->crm_enabled))*/
                    . ',"event_enabled":' . (checkValSet($request->event_enable))
                    . ',"workshop_graphic_enable":' . (isset($request->workshop_graphic_enable) ? checkValSet($request->workshop_graphic_enable) : 0)
                    . ',"workshops_enable":' . (isset($request->workshops_enable) ? checkValSet($request->workshops_enable) : 0)
                    . ',"documents_enable":' . (isset($request->documents_enable) ? checkValSet($request->documents_enable) : 0)
                    . '}';
                $postData[$returnType] = $returnValue;
            } else {
                $postData['setting->video_meeting_enable'] = (checkValSet($request->video_meeting_enable));
            }
            $data = [
                'setting_key'   => 'video_meeting_api_setting',
                'setting_value' => json_encode($settingValues),
            ];
            $key = 'video_meeting_api_setting';
            $this->setSettingTable($key, $data);
            return $postData;
        }

        public function setSettingTable($key, $data = [])
        {
            return Setting::updateOrCreate(['setting_key' => $key], $data);
        }

        public function setLanguageSetting($request)
        {
            if (isset($request->langs) && is_array($request->langs)) {
                $data = [
                    'setting_key'   => 'languages_to_show',
                    'setting_value' => json_encode($request->langs),
                ];
                $key = 'languages_to_show';
                $this->setSettingTable($key, $data);
            }
        }

        protected function defaultFilter()
        {
            $filterType = CrmFilterType::all(['id', 'name']);
            $crmService = CrmServices::getInstance();
            foreach ($filterType as $item) {
                $request = new Request([]);
                $request->merge(['filter_name' => 'ALL', 'filter_type_id' => $item->id, 'is_default' => 1, 'save_selected_fields' => FALSE, 'selected_fields' => ['custom' => [], 'default' => []], 'conditions' => [['component' => "persons", 'field_name' => "email", 'condition' => "is", 'value' => "x", 'condition_type' => "and", 'condition' => "all", 'condition_type' => "all", 'field_name' => "all", 'is_default' => TRUE, 'value' => "all", 'component' => "persons",
                ],
                ],
                ]);
                $crmService->request = $request;
                $rules = [
                    'filter_type_id'         => ['required', new CrmFilterTypeExist],
                    'filter_name'            => ['required', Rule::unique('tenant.crm_filters', 'name')
                        ->where('filter_type_id', $request->filter_type_id)],
                    'conditions.*.component' => ['required', new CrmFilterComponentExist],
                ];
                $crmService->validation = Validator::make($request->all(), $rules);
                if (!$crmService->validation->fails()) {
                    $crmService->previewBeforeSave();
                    $response = $crmService->saveFiler();
                }
            }
        }

        protected function enableCrmForInstancePress($condition = 0, $rec)
        {
            if ($condition == 0)
                $condition = 0;
            else
                $condition = 1;

            $this->checkAndAddWorkshop('CRM', 'CRM', $rec->account_id, $rec->project_enable, $condition);

            $this->defaultFilter();
            if (isset($rec->setting['crm_enable']) && $rec->setting['crm_enable'] == 0) {
                $condition = 0;
                //display off for CRM commission
                Workshop::where('code1', 'CRM')->withoutGlobalScopes()->update(['display' => $condition]);
            } elseif (!isset($rec->setting['crm_enable'])) {
                //display off for CRM commission
                Workshop::where('code1', 'CRM')->withoutGlobalScopes()->update(['display' => 0]);
            }
        }

        public function verticalBarSetting($rec, $request, &$postData)
        {
            $array = ['vertical_bar_enable', 'add_module_enable', 'messenger_enable', 'feature_request_enable', 'help_enable', 'share_enable', 'others_enable', 'vertical_messenger_enable', 'vertical_event_enabled', 'vertical_news_letter_enable'];
            foreach ($array as $item) {
                if (!isset($request->$item))
                    $request->$item = checkValSet($request->$item);
//                if (array_key_exists($item, $request->all())) {
                $this->setSetting($rec, $request->$item, $postData, $item);
//                }
            }
            if (checkValSet($request->vertical_bar_enable) == 0) {
                $postData['setting->vertical_bar_enable'] = 0;
                $postData['setting->add_module_enable'] = 0;
                $postData['setting->vertical_messenger_enable'] = 0;
                $postData['setting->feature_request_enable'] = 0;
                $postData['setting->help_enable'] = 0;
                $postData['setting->share_enable'] = 0;
                $postData['setting->others_enable'] = 0;
                $postData['setting->vertical_event_enabled'] = 0;
                $postData['setting->vertical_news_letter_enable'] = 0;
                $postData['setting->direct_video_enable'] = 0;
            }
            return $postData;
        }

        protected function setSetting($emptyCheck, $requestField, &$postData, $field)
        {
            if (empty($emptyCheck->setting) && $requestField == 1) {
                if (isset($postData["setting"])) {
                    $consist = json_decode($postData['setting']);
                    $consist->$field = checkValSet($requestField);
                    $postData["setting"] = (json_encode($consist));
                } else {
                    $returnType = '{"' . $field . '":' . checkValSet($requestField) . '}';
                    $returnValue = $returnType;
                    $postData["setting"] = $returnValue;
                }
            } else {

                $postData['setting->' . $field] = checkValSet($requestField);

            }

            return $postData;
        }

        public function setResilience($rec, $request, &$postData)
        {
            $array = ['consultation_enable', 'reinvent_enable'];
            foreach ($array as $item) {
                if (!isset($request->$item))
                    $request->$item = checkValSet($request->$item);
                $this->setSetting($rec, $request->$item, $postData, $item);
            }
            //Youtube Setting
            if (isset($request->clientid) || isset($request->clientsecret) || isset($request->youtube_api_key) || isset($request->youtube_channel_key)) {
                $data = [
                    'setting_key'   => 'youtube_api_setting',
                    'setting_value' => json_encode([
                        'clientid'            => isset($request->clientid) ? $request->clientid : NULL,
                        'clientsecret'        => isset($request->clientsecret) ? $request->clientsecret : NULL,
                        'youtube_api_key'     => isset($request->youtube_api_key) ? $request->youtube_api_key : NULL,
                        'youtube_channel_key' => isset($request->youtube_channel_key) ? $request->youtube_channel_key : NULL,
                    ]),
                ];
                $key = 'youtube_api_setting';
                $this->setSettingTable($key, $data);
            }

            return $postData;
        }

        public function setAdditionalData($request)
        {
            // to set the setting table data for event module
            SettingService::getInstance()->setEventSettings($request);
            $this->setNewsLetterModuleSettings($request);

            $stockData = $this->prepareStockData($request);
            $transcribeData = $this->prepareTranscribeData($request);

            $rec = AccountSettings::where('account_id', $request->account_id)->first();
            if ($rec) {
                $setting = $rec->setting;
                // event module settings
                $setting['event_enabled'] = checkValSet($request->event_enable);
                $setting['event_settings'] = [
                    "wp_enabled"               => checkValSet($request->event_wp_enabled),
                    'event_conference_enabled' => checkValSet($request->event_conference_enabled),
                    "keep_contact_enable"      => checkValSet($request->event_keep_contact_enabled),
                ];
                // newsletter module settings here
                $setting['stock_setting'] = $stockData;
                $setting['news_moderation_enable'] = checkValSet($request->news_moderation_enable);
                // crm module settings here
                $setting['transcribe_setting'] = $transcribeData;
                $setting = json_encode($setting);
                AccountSettings::where('account_id', $request->account_id)->update(['setting' => $setting]);
            }
        }
        
        public function setNewsLetterModuleSettings($request)
        {
            // ADOBE DATA
            $data = [
                'setting_key'   => 'adobe_stock_api_setting',
                'setting_value' => json_encode([
                    'access_key' => isset($request->adobe_access_key) ? $request->adobe_access_key : NULL,
                    'app_name'   => isset($request->adobe_app_name) ? $request->adobe_app_name : NULL,
                ]),
            ];
            $key = 'adobe_stock_api_setting';
            $this->setSettingTable($key, $data);
        }

        /**
         * @param Request $request
         * @return array
         */
        public function prepareStockData($request)
        {
            $stock_max = $request->input('stock_allowed_number', 0);
            $stock_available_credit = $request->input('stock_available_credit', 0);
            $stock_renewal = $request->input('stock_renewal', 1);
            return [
                "enabled"          => checkValSet($request->stock_setting_enabled),
                "max_allowed"      => $stock_max,
                "available_credit" => $stock_available_credit,
                "renewal_date"     => $stock_renewal,
            ];
        }

        /**
         * @param Request $request
         * @return array
         */
        public function prepareTranscribeData($request)
        {
            $transcribe_max =
                ($request->input('transcribe_allowed_h', 0) * 3600)
                + ($request->input('transcribe_allowed_m', 0) * 60);
            $transcribe_available_credit =
                ($request->input('transcribe_available_h') * 3600)
                + ($request->input('transcribe_available_m') * 60);
            return [
                "enabled"          => checkValSet($request->transcribe_setting_enabled),
                "max_allowed"      => $transcribe_max,
                "available_credit" => $transcribe_available_credit,
            ];
        }
    
        /**
         * To check if bluejeans credentials are available from super admin or not
         *
         * @return false
         */
        public function isBjDetailsAvailable() {
            $setting = Setting::where('setting_key', 'video_meeting_api_setting')->first();
            if(!$setting) {
                return false;
            }
            
            $decode = json_decode($setting->setting_value, 1);
            if(isset($decode['client_id']) && isset($decode['client_secret']) && $decode['client_id'] && $decode['client_secret'] ) {
                return true;
            }
            return false;
        }
    }
