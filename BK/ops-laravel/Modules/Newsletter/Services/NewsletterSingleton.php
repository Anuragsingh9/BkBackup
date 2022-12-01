<?php
    
    namespace Modules\Newsletter\Services;
    
    use Carbon\Carbon;
    use DB;
    use Illuminate\Database\Query\Builder;
    use Modules\Newsletter\Entities\Newsletter;
    use Modules\Newsletter\Entities\NewsletterBlock;
    use Modules\Newsletter\Entities\ScheduleTime;
    use Modules\Newsletter\Entities\Sender;
    use Modules\Newsletter\Entities\NewsletterList;
    use Modules\Newsletter\Entities\IcontactMeta;
    use App\Model\ListModel;
    use Auth;
    
    class NewsletterSingleton
    {
        /**
         * Add newsletter in storage
         * @param $senderId
         * @param $name
         * @param $shortName
         * @param null $description
         * @return mixed
         */
        
        public function addNewsletter($senderId, $name, $shortName, $description = NULL)
        {
            try {
                
                DB::connection('tenant')->beginTransaction();
                $insertData = ['name'        => $name,
                               'short_name'  => $shortName,
                               'description' => $description,
                               'sender_id'   => $senderId,
                ];
                $result = Newsletter::create($insertData);
                
                DB::connection('tenant')->commit();
                return $result;
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                $newsletter['status'] = 500;
                $newsletter['msg'] = $e->getMessage();
                return $newsletter;
            }
            
        }
        
        /**
         * Update newsletter in storage
         * @param $id
         * @param $senderId
         * @param $templateId
         * @param $name
         * @param $shortName
         * @param $url
         * @param $htmlCode
         * @param null $description
         * @return response
         */
        public function updateNewsletter($id, $senderId, $name, $shortName, $description, $subject)
        {
            try {
                // Update newsletter in storage
                DB::connection('tenant')->beginTransaction();
                $newsletter = Newsletter::where('id', $id)->update(['name'        => $name,
                                                                    'short_name'  => $shortName,
                                                                    'description' => $description,
                                                                    'sender_id'   => $senderId,
                                                                    'subject'     => $subject,
                
                ]);
                DB::connection('tenant')->commit();
                return $newsletter;
            } catch (\Exception $e) {
                
                DB::connection('tenant')->rollback();
                
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
                
            }
            
        }
        
        
        /**
         *   * show future records of newsletter
         * @param $size (if size has a value,it takes that value )
         * @param $field
         * @param $orderBy
         * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
         */
        
        public function getFutureNewsletterList($size, $field, $orderBy)
        {
            $defaultSize = config('newsletter.NEWSLETTER_LIST_PAGINATION_NUMBER');
            
            /*
            $query= Newsletter::query();
            if($field){
                $query->orderBy($field,$orderBy);
            }
             $date=Carbon::now('Europe/Paris')->format('Y-m-d H:i:s');
             $dataExists=ScheduleTime::where('schedule_time','<=',Carbon::now('Europe/Paris')->format('Y-m-d H:i:s'))->pluck('newsletter_id');
            $newsletter= $query->with('scheduleTime', 'sender:id,from_name')->whereNotIn('id',$dataExists)->paginate(($size) ? $size : $defaultSize);
            return $newsletter;
         */
            
            $newsletter = Newsletter::with(['scheduleTime' => function ($q) {
                $q->where('schedule_time', '>', Carbon::now('Europe/Paris')->format('Y-m-d H:i:s'));
            }, 'sender'                                    => function ($q) {
                $q->select('id', 'from_name');
            }])
                ->whereDoesntHave('scheduleTime', function ($q) {
                    $q->where('schedule_time', '<=', Carbon::now('Europe/Paris')->format('Y-m-d H:i:s'));
                });
            
            switch ($field) {
                case 'id':
                case 'short_name':
                case 'updated_at':
                    return $newsletter->orderBy($field, $orderBy)
                        ->paginate(($size) ? $size : $defaultSize);
                case 'sender':
                    return $newsletter
                        ->leftJoin('newsletter_senders', 'newsletter_senders.id', '=', 'newsletters.sender_id')
                        ->select('newsletters.*', 'newsletter_senders.from_name')
                        ->orderBy('newsletter_senders.from_name', $orderBy)
                        ->paginate(($size) ? $size : $defaultSize);
                case 'schedule_time':
                    return $newsletter
                        ->leftJoin('newsletter_schedule_timings', 'newsletter_schedule_timings.newsletter_id', '=', 'newsletters.id')
                        ->selectRaw('newsletters.* , newsletter_schedule_timings.schedule_time as sc')
                        ->orderBy('sc', $orderBy)
                        ->paginate(($size) ? $size : $defaultSize);
                
            }
        }
        
        public function getPastNewsletterListData()
        {
            $query = Newsletter::query();
            $newsletter = $query->with('scheduleTime', 'sender:id,from_name')->whereExists(function ($query) {
                $query->select(DB::raw(1))->from('newsletter_schedule_timings')
                    ->whereRaw('newsletter_schedule_timings.newsletter_id= newsletters.id')
                    ->where('newsletter_schedule_timings.schedule_time', '<=', Carbon::now('Europe/Paris')->format('Y-m-d H:i:s'))
                    ->orWhere('newsletter_schedule_timings.schedule_time', NULL);
            })->get(['id', 'name', 'short_name']);
            return $newsletter;
        }
        
        public function getPastNewsletterList($size, $field, $orderBy)
        {
            $defaultSize = config('newsletter.NEWSLETTER_LIST_PAGINATION_NUMBER');
//        $query= Newsletter::query();
//        if($field){
//            $query->orderBy($field,$orderBy);
//        }
            
            // $newsletter=$query->with('scheduleTime','sender:id,from_name')->whereExists(function($query){
            //     // $query->select(DB::raw(1))->from('newsletter_schedule_timings')
            //     //     ->whereRaw('newsletter_schedule_timings.newsletter_id= newsletters.id')
            //     //     ->where('newsletter_schedule_timings.schedule_time','<=',Carbon::now('Europe/Paris')->format('Y-m-d H:i:s'))
            //     //     ->orWhere('newsletter_schedule_timings.schedule_time',null)
            //     //     ;
            // })->paginate(($size)?$size:$defaultSize);
            
            /*
            $date=Carbon::now('Europe/Paris')->format('Y-m-d H:i:s');
          
            $dataExists=ScheduleTime::where('schedule_time','<=',Carbon::now('Europe/Paris')->format('Y-m-d H:i:s'))->pluck('newsletter_id');
            $newsletter= $query->with('scheduleTime', 'sender:id,from_name')->whereIn('id',$dataExists)->paginate(($size) ? $size : $defaultSize);
          
                return $newsletter;
        */
            $newsletter = Newsletter::with(['scheduleTime' => function ($q) {
                $q->where('schedule_time', '<=', Carbon::now('Europe/Paris')->format('Y-m-d H:i:s'));
            }, 'sender'                                    => function ($q) {
                $q->select('id', 'from_name');
            }])
                ->whereHas('scheduleTime', function ($q) {
                    $q->where('schedule_time', '<=', Carbon::now('Europe/Paris')->format('Y-m-d H:i:s'));
                });
            switch ($field) {
                case 'id':
                case 'short_name':
                case 'updated_at':
                    return $newsletter->orderBy($field, $orderBy)
                        ->paginate(($size) ? $size : $defaultSize);
                case 'sender':
                    return $newsletter
                        ->leftJoin('newsletter_senders', 'newsletter_senders.id', '=', 'newsletters.sender_id')
                        ->select('newsletters.*', 'newsletter_senders.from_name')
                        ->orderBy('newsletter_senders.from_name', $orderBy)
                        ->paginate(($size) ? $size : $defaultSize);
                case 'schedule_time':
                    return $newsletter
                        ->leftJoin('newsletter_schedule_timings', 'newsletter_schedule_timings.newsletter_id', '=', 'newsletters.id')
                        ->selectRaw('newsletters.* , newsletter_schedule_timings.schedule_time as sc')
                        ->orderBy('sc', $orderBy)
                        ->paginate(($size) ? $size : $defaultSize);
                
            }
            
        }
        
        /**
         * fetching newsletter for sending
         * @param $id
         * @return array
         */
        public function fetechNewsLetterSending($id)
        {
            $lang = session()->has('lang') ? session()->get('lang') : "FR";
            $newsletter = Newsletter::with('sender:id,from_name')->where('id', $id)->first(['id', 'short_name', 'sender_id', 'subject']);
            if (!$newsletter) {
            return response()->json(['status' => false, 'msg' => 'not found'], 200);
            }
            $sender = Sender::all(['id', 'from_name']);
            $scheduleTime = ScheduleTime::where('newsletter_id', $id)->first(['id', 'schedule_time']);
            $scheduleTimeDiff = '';
        if ($scheduleTime != null) {
                if ($lang == 'FR') {
                    $date = Carbon::parse($scheduleTime->getOriginal()['schedule_time']);
                    $now = Carbon::now('Europe/Paris');
                    $scheduleTimeDiff = $date->diffForHumans($now);
                } else {
                    $date = Carbon::parse($scheduleTime->getOriginal()['schedule_time']);
                    $now = Carbon::now('Europe/Paris');
                    $scheduleTimeDiff = $date->diffForHumans($now);
                }
            $newsletterList = Newsletter::orderBy('id', 'desc')->with('scheduleTime', 'sender:id,from_name')->whereExists(function ($query) {
                $query->select(DB::raw(1))->from('newsletter_schedule_timings')
                    ->whereRaw('newsletter_schedule_timings.newsletter_id= newsletters.id')
                    ->where('newsletter_schedule_timings.schedule_time', '<=', Carbon::now('Europe/Paris')->format('Y-m-d H:i:s'))
                    ->orWhere('newsletter_schedule_timings.schedule_time', null);
            })->take(3)->get(['id', 'name', 'short_name']);
          
            // dd($date,$now);
        }else{
            $newsletterList = Newsletter::orderBy('id', 'desc')->take(3)->get(['id', 'name', 'short_name']);
            }
            $list = NewsletterList::with('Lists')->where('newsletter_id', $id)->get(['id', 'list_id', 'newsletter_id']);
            $res = $this->countTotalDupilcate($id);
            return ['newsletter'       => $newsletter,
                    'newsletterList'   => $newsletterList,
                    'scheduleTime'     => $scheduleTime,
                    'scheduleTimeDiff' => $scheduleTimeDiff,
                    'list'             => $list,
                    'total'            => $res['total'],
            'duplicate' => $res['duplicate']
            ];
            
        }
        
        public function countTotalDupilcate($newsletterId)
        {
            $list = NewsletterList::with('Lists')->where('newsletter_id', $newsletterId)->get(['id', 'list_id', 'newsletter_id']);
//            $listIds = $list->pluck(['list_id']);
//            $listModels = ListModel::with('newsletter_contacts', 'users')->whereIn('id', $listIds)->get(['id', 'name', 'description', 'type', 'typology_id']);
            $listModels = $list->pluck('lists');
            $total = 0;
            $duplicate = 0;
//            $duplicateArray = [];
            $users = [];
            $contacts = [];
            foreach ($listModels as $k => $val) {
                if ($val->type == 1 || $val->type == 4) {
                    $idsArray = $val->newsletter_contacts->pluck(['email'])->toArray();
                    foreach ($idsArray as $key) {
                        if(isset($contacts[$key]))
                            $contacts[$key] += 1;
                        else
                            $contacts[$key] = 0;
                    }
//                    $withOutDuplicateIds = array_unique((array)$idsArray);
//                    $duplicateArray = array_merge($duplicateArray, $withOutDuplicateIds);
                } else {
                    $idsArray = $val->users->pluck(['email'])->toArray();
                    foreach ($idsArray as $key) {
                        if(isset($users[$key]))
                            $users[$key] += 1;
                        else
                            $users[$key] = 0;
                    }
//                    $withOutDuplicateIds = array_unique((array)$idsArray);
//                    $duplicateArray = array_merge($duplicateArray, $withOutDuplicateIds);
                }
            }
//            $withOutDuplicateIds = array_unique((array)$duplicateArray);
//            $duplicateIds = count((array)$duplicateArray) - count($withOutDuplicateIds);
//            $total = $total + count((array)$duplicateArray);
//            $duplicate = $duplicate + $duplicateIds;
            $total = count($users) + count($contacts);
            $duplicate = array_sum(array_values($users)) + array_sum(array_values($contacts));
//            dd(['total' => $total, 'duplicate' => $duplicate]);
            return (['total' => $total, 'duplicate' => $duplicate]);
            
        }
        
        /**
         * save newsletter schedule
         * @param $data
         * @return string time diffrence from current
         */
        public function newsletterScheduleSave($data)
        {
            try {
                // var_dump($data);die;
                // // Update newsletter in storage
                $lang = session()->has('lang') ? session()->get('lang') : "FR";
                DB::connection('tenant')->beginTransaction();
                
                $scheduleInsertData = [
                    'sender_id'     => $data['sender_id'],
                    'newsletter_id' => $data['newsletter_id'],
                    'schedule_time' => Carbon::parse($data['schedule_time'], 'Europe/Paris')->format('Y-m-d H:i:s'),
                ];
                // $NewsletterListInsertData=[];
                // if(isset($data['list_id']) && is_array($data['list_id']) && count($data['list_id'])>0){
                //     foreach($data['list_id'] as $k=>$val){
                //         $NewsletterListInsertData[] = ['newsletter_id'=>$data['newsletter_id'],'list_id'=>$val];
                //     }
                // }
                //     // add newsletter in icontact
                
                $IcontactListIds = IcontactMeta::where('type', 2)->whereIn('column_id', $data['list_id'])->pluck('icontact_id')->toArray();
                $newsletterTempIcontect = IcontactMeta::where(['type' => 4, 'column_id' => $data['newsletter_id']])->first();
                //text_for_browser_view
                
                $icontactData = $this->addnewsletterToicontact($data);
                $instance = IContactSingleton::getInstance();
                // if($newsletterTempIcontect){
                //     $icontactNewsletter= $instance->updateNewsletterInIcontact($icontactData[0],$data['newsletter_id']);
                // }
                // else{
                
                $icontactNewsletter = $instance->addTempletes($icontactData);
                // }
                // $icontactNewsletter= json_decode($icontactNewsletter);
                // var_dump($icontactNewsletter);die;
                $postData = [];
                $icontactInsertData = [];
                
                foreach ($icontactNewsletter->messages as $msg) {
                    // dd($msg->);
                    $postData[] = [
                        'messageId'      => $msg->messageId,
                        'includeListIds' => implode(',', $IcontactListIds),
                        'scheduledTime'  => date('c', strtotime($data['schedule_time'])),
                    ];
                    $icontactInsertData = [
                        'type'        => 3,
                        'column_id'   => $data['newsletter_id'],
                        'icontact_id' => $msg->messageId,
                    ];
                }
                // var_dump($icontactInsertData);die;
                $icontactInsertDataSend = [];
                $icontactNewslettersend = $instance->sendMessage($postData);
                foreach ($icontactNewslettersend->sends as $msg) {
                    // var_dump($icontactNewslettersend);die;
                    $icontactInsertDataSend = [
                        'type'        => 4,
                        'column_id'   => $data['newsletter_id'],
                        'icontact_id' => $msg->sendId,
                    ];
                }
                $scheduleTime = ScheduleTime::create($scheduleInsertData);
                $IcontactMeta = IcontactMeta::create($icontactInsertData);
                $IcontactMeta = IcontactMeta::create($icontactInsertDataSend);
                // $NewsletterList=NewsletterList::insert($NewsletterListInsertData);
                DB::connection('tenant')->commit();
                
                $scheduleTimeDiff = '';
                if ($scheduleTime != NULL) {
                    if ($lang == 'FR') {
                        $date = Carbon::parse($scheduleTime->getOriginal()['schedule_time']);
                        $now = Carbon::now('Europe/Paris');
                        $scheduleTimeDiff = $date->diffForHumans($now);
                    } else {
                        $date = Carbon::parse($scheduleTime->getOriginal()['schedule_time']);
                        $now = Carbon::now('Europe/Paris');
                        $scheduleTimeDiff = $date->diffForHumans($now);
                    }
                }
                return $scheduleTimeDiff;
            } catch (\Exception $e) {
                
                DB::connection('tenant')->rollback();
                
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
                
            }
        }
        
        public function addnewsletterToicontact($data)
        {
            $newsletter = Newsletter::with('blocks', 'template:id,footer_html_code,header_html_code,text_for_browser_view')->find($data['newsletter_id']);
            
            //text_for_browser_view $icontactSenderId = '';
            $IcontactSenderId = IcontactMeta::where(['type' => 0, 'column_id' => $data['sender_id']])->first();
            $icontactData = [[
                'campaignId'  => $IcontactSenderId->icontact_id,  //    (Sender Id)Indicates the sender property from which the message will pull its sending information.
                'messageType' => 'normal', // message type (normal, autoresponder, welcome and confirmation)
                'subject'     => ucfirst($newsletter->subject), // subject of email
                'htmlBody'    => $this->renderHtml($newsletter),// html body of templete
                'messageName' => $newsletter->name, // message name
            ]];
            return $icontactData;
        }
        
        public function renderHtml($newsletter)
        {
            
            $newsletter->url = url('/newsletter/weburl/' . base64_encode($newsletter->id . '-id'));
            $html = '';
            $html = $newsletter->template->header_html_code;
            $html = '<center style="padding: 10px 0px; text-align: center; width: 100%;"><a class="full-ext-link" style="font-size: 14px;" href="' . $newsletter->url . '">' . $newsletter->template->text_for_browser_view . '</a></center>' . $html;
            $data = [];
            foreach ($newsletter->blocks as $key => $htmlBlock) {
                if ($htmlBlock->short_order != 0) {
                    $data[] = $htmlBlock;
                } else {
                    $data[$key] = $htmlBlock;
                }
            }
            
            // ksort($data);
            usort($data, function ($a, $b) {
                return $a['short_order'] - $b['short_order'];
            });
            // dd($data);
            foreach ($data as $htmlBlock) {
                $htmlBlock->blocks = preg_replace('~<grammarly-extension(.*?)</grammarly-extension>~Usi', "", $htmlBlock->blocks);
                $html = $html . $htmlBlock->blocks;
            }
            $html = $html . $newsletter->template->footer_html_code;
            // var_export($html);
            return $html;
        }
        
        public function newsletterScheduleCancel($newsletterId)
        {
            // cancelMessage
            try {
                
                DB::connection('tenant')->beginTransaction();
                $IcontactSendId = IcontactMeta::where(['type' => 4])->where('column_id', $newsletterId)->first();
                $instance = IContactSingleton::getInstance();
                $icontactdata = $instance->cancelMessage($IcontactSendId->icontact_id);
                $IcontactSendId->delete();
                $ScheduleTime = ScheduleTime::where('newsletter_id', $newsletterId)->delete();
                DB::connection('tenant')->commit();
                
                if ($IcontactSendId && $ScheduleTime) {
                    return TRUE;
                } else {
                    return fasle;
                }
            } catch (\Exception $e) {
                
                DB::connection('tenant')->rollback();
                
                return response()->json(['status' => FALSE, 'msg' => $e->getTraceAsString()], 500);
                
            }
        }
        
        public function addListInNewsletter($data)
        {
            try {
                DB::connection('tenant')->beginTransaction();
                $NewsletterListInsertData = ['newsletter_id' => $data['newsletterId'], 'list_id' => $data['listId']];
                $NewsletterList = NewsletterList::insert($NewsletterListInsertData);
                DB::connection('tenant')->commit();
                return $NewsletterList;
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        public function removeListInNewsletter($data)
        {
            try {
                DB::connection('tenant')->beginTransaction();
                $NewsletterList = NewsletterList::where(['newsletter_id' => $data['newsletterId'], 'list_id' => $data['listId']])->delete();
                DB::connection('tenant')->commit();
                return $NewsletterList;
            } catch (\Exception $e) {
                DB::connection('tenant')->rollback();
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        public function sendToList($id)
        {
            try {
                $list = NewsletterList::with('Lists')->where('newsletter_id', $id)->get(['id', 'list_id', 'newsletter_id']);
                return $list;
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        public function getListContact($id)
        {
            try {
                $list = NewsletterList::with(['Lists' => function ($q) {
                    $q->with('newsletter_contacts', 'users');
                }])->where('newsletter_id', $id)->get(['id', 'list_id', 'newsletter_id']);
                $data = [];
                
                foreach ($list as $key => $value) {
                    if ($value->Lists->type == 0) {
                        // dd($value->Lists->users->pluck('id'),$value->list_id);
                        if (isset($data[$value->Lists->type])) {
                            $data[$value->Lists->type] = array_merge($data[$value->Lists->type], $value->Lists->users->pluck('id'));
                        } else {
                            $data[$value->Lists->type] = $value->Lists->users->pluck('id');
                        }
                    } else {
                        if (isset($data[$value->Lists->type])) {
                            if (count($value->Lists->users->pluck('id')) > 0) {
                                $data[$value->Lists->type] = array_merge($data[$value->Lists->type], $value->Lists->users->pluck('id'));
                            }
                            if (count($value->Lists->newsletter_contacts->pluck('id')) > 0) {
                                $data[$value->Lists->type] = collect($data[$value->Lists->type])->merge($value->Lists->newsletter_contacts->pluck('id'));
//                        $data[$value->Lists->type] = array_merge($data[$value->Lists->type], $value->Lists->newsletter_contacts->pluck('id')->toArray());
                            }
                        } else {
                            $data[$value->Lists->type] = $value->Lists->newsletter_contacts->pluck('id');
                        }
                    }
                }
                return $data;
            } catch (\Exception $e) {
                
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
    }