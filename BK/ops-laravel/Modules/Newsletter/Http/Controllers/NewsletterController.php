<?php
    
    namespace Modules\Newsletter\Http\Controllers;
    
    use Illuminate\Support\Facades\DB;
    use Modules\Newsletter\Rules\Alphanumeric;
    use Illuminate\Http\Request;
    use Illuminate\Routing\Controller;
    use Modules\Newsletter\Entities\IContact;
    use Modules\Newsletter\Entities\Sender;
    use Modules\Newsletter\Entities\Template;
    use Modules\Newsletter\Services\IContactSingleton;
    use Modules\Newsletter\Services\NewsletterSingleton;
    use Modules\Newsletter\Entities\Newsletter;
    use Modules\Newsletter\Entities\IcontactMeta;
    use Modules\Newsletter\Entities\ContactStatus;
    use Carbon\Carbon;
    
    
    use Validator;
    use App\Exports\MessageStats;
    use Maatwebsite\Excel\Facades\Excel;
    
    
    class NewsletterController extends Controller
    {
        /**
         * NewsletterController constructor.
         * @param NewsletterSingleton $newsletter
         */
        
        public function __construct(NewsletterSingleton $newsletter, IContactSingleton $IContact)
        {
            $this->newsletter = $newsletter;
            $this->IContact = $IContact;
            $this->list = app(\App\Http\Controllers\Universal\ListsController::class);
            $this->core = app(\App\Http\Controllers\CoreController::class);
            $this->middleware('IcontactCheck', ['only' => [
                'store', 'destroy', 'update', 'newsletterScheduleSave', 'newsletterScheduleCancel'
                , 'spamTest', 'newsletterReport' // Could add bunch of more methods too
            ]]);
        }
        
        /**
         * Display a listing of the resource.
         * Fetch future newsletter list
         * @return \Illuminate\Http\Response
         */
        public function index(Request $request)
        {
            try {
                $size = ($request->has('size') && !empty($request->size)) ? $request->size : NULL;
                $orderBy = ($request->has('order') && !empty($request->order)) ? $request->order : 'desc';
                // $orderBy =($request->has('orderBy')? 'asc'?'desc':'asc');
                $field = ($request->has('field') && !empty($request->field)) ? $request->field : 'id';
                
                $newsletter = $this->newsletter->getFutureNewsletterList($size, $field, $orderBy);
                
                return response()->json(['status' => TRUE, 'data' => $newsletter]);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        /**
         * Display a listing of the past records
         * Fetch past newsletter list
         * @return \Illuminate\Http\Response
         */
        public function getPastNewsletter(Request $request)
        {
            
            try {
                
                $size = ($request->has('size') && !empty($request->size)) ? $request->size : NULL;
                $orderBy = ($request->has('order') && !empty($request->order)) ? $request->order : 'asc';
                
                // $orderBy = ($request->has('orderBy') && !empty($request->orderBy) && ($request->orderBy) !== 'desc' ? 'asc' : 'asc');
                $field = ($request->has('field') && !empty($request->field)) ? $request->field : 'id';
                
                $newsletter = $this->newsletter->getPastNewsletterList($size, $field, $orderBy);
            
                
                return response()->json(['status' => TRUE, 'data' => $newsletter]);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        public function getPastNewsletterList()
        {
            try {
                $newsletter = $this->newsletter->getPastNewsletterListData();
                
                
                return response()->json(['status' => TRUE, 'data' => $newsletter]);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        /**
         * Add newsletter in storage and iContact
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function store(Request $request)
        {
            try {
                //validation
                $validator = validator::make($request->all(), [
                    
                    'sender_id'  => 'required',
                    'name'       => ['required', 'min:2', 'max:120',],
                    'short_name' => ['required', 'min:2', 'max:12'],
                
                ]);
                
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 400);
                }
                
                $senderId = $request->sender_id;
                $name = $request->name;
                $shortName = $request->short_name;
                $description = $request->description;
                
                // Add newsletter to database
                $newsletter = $this->newsletter->addNewsletter($senderId, $name, $shortName, $description);
                
                
                // Add newsletter data in iContact using API
                // Fetch iContact sender id stored in database
                
                // $icontactSenderId = IContact::where('column_id',$newsletter->sender_id)->first(['icontact_id']);
                //  dd($newsletter);
                
                // $instance = IContactSingleton::getIContactInstance();
                // $iNewsletter = $instance->addMessage($newsletter->short_name, $icontactSenderId->icontact_id, $newsletter->html_code, '', $newsletter->name, '', 'normal');
                
                // if(isset($iNewsletter->messageId)){
                //     $icontactNewsletterId = $iNewsletter->messageId;
                //     $data = ['column_id'=>$newsletter->id,'icontact_id'=>$icontactNewsletterId,'type'=>3];
                //     IContact::create($data);
                // }
                
                if ($newsletter['status'] == 500) {
                    
                    return response()->json(['status' => FALSE, 'msg' => $newsletter['msg']], 500);
                }
                
                return response()->json(['status' => TRUE, 'msg' => 'Newsletter Added Successfully', 'newsletter' => $newsletter], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        public function show($id)
        {
            try {
    
                // Fetch data of newsletter when showing edit screen
                $newsletter = Newsletter::with([
                    'sender:id,from_name',
                    'blocks' => function ($q) {
                        $q->orderByRaw('CAST(short_order AS UNSIGNED) ASC');
                    },
                    'template.blocks',
                    'scheduleTime'
                ])->find($id);
                if (!$newsletter) {
                    return response()->json(['status' => FALSE, 'msg' => 'not found'], 200);
                }
                // $newsletter->url='weburl/'.base64_encode($id.'-id');
                $newsletter->url = url('/newsletter/weburl/' . base64_encode($id . '-id'));
                if($newsletter->scheduleTime == null) {
                    $tense = 'f';
                } else {
                    $value = $newsletter->scheduleTime->schedule_time;
                    $date_fr = ['janvier','février','mars','avril','mai','juin','juillet','aout','septembre','octobre','novembre','décembre'];
                    $dateEn = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November','December'];
                    $value = str_replace($date_fr, $dateEn , $value);
    
                    $newsletterScheduleDate = Carbon::createFromFormat('F d, Y H:m', $value)->format('Y-m-d H:i:s');
                    $nowDate = Carbon::now('Europe/Paris')->format('Y-m-d H:i:s');
                    if ($newsletterScheduleDate >= $nowDate) {
                        $tense = 'f';
                    } else {
                        $tense = 'p';
                    }
                }
                if ($tense == 'f') {
                    $newsletterList = Newsletter::
                    with(['scheduleTime'])
                        ->whereDoesntHave('scheduleTime', function ($q) {
                            $q->where('schedule_time', '<=', Carbon::now('Europe/Paris')->format('Y-m-d H:i:s'));
                        })
                        ->whereNull('deleted_at')
                        ->where('id', '!=', $newsletter->id)
                        ->orderBy('id', 'desc')
                        ->take(3)
                        ->get();
                } else {
                    $newsletterList = Newsletter::
                    with(['scheduleTime'])
                        ->whereHas('scheduleTime', function ($q) {
                            $q->where('schedule_time', '<=', Carbon::now('Europe/Paris')->format('Y-m-d H:i:s'));
                        })
                        ->whereNull('deleted_at')
                        ->where('id', '!=', $newsletter->id)
                        ->orderBy('id', 'desc')
                        ->take(3)
                        ->get();
                }
//                $newsletterList = Newsletter::orderBy('id', 'desc')->take(3)->get();
                $templateList = Template::get(['id', 'name', 'header_html_code', 'footer_html_code']);
                return response()->json(['status' => TRUE, 'newsletter' => $newsletter, 'newsletterlist' => $newsletterList, 'templateList' => $templateList], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage(),'path'=>$e->getTraceAsString(), 'newsletterlist' => [], 'templateList' => []], 500);
            }
        }
        
        /**
         * Update newsletter in storage and iContact
         * @param Request $request
         * @param $id
         * @return \Illuminate\Http\JsonResponse
         */
        
        public function update(Request $request, $id)
        {
            try {
                //validation
                $validator = validator::make($request->all(), [
                    
                    'sender_id'  => 'required',
                    'name'       => ['required', 'min:2', 'max:120',],
                    'short_name' => ['required', 'min:2', 'max:12'],
                
                ]);
                
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 400);
                }
                
                $senderId = $request->sender_id;
                $name = $request->name;
                $shortName = $request->short_name;
                $description = $request->description;
                $subject = $request->subject;
                // Update newsletter in database
                $newsletter = $this->newsletter->updateNewsletter($id, $senderId, $name, $shortName, $description, $subject);
                $data = Newsletter::find($id);
                if ($newsletter) {
                    return response()->json(['status' => TRUE, 'data' => $data], 200);
                } else {
                    return response()->json(['status' => FALSE, 'msg' => 'not updated', 'data' => $data], 200);
                }
                // Update newsletter data in iContact
                $instance = IContactSingleton::getInstance();
                $iNewsletter = $instance->updateNewsletterInIcontact($newsletter, $id);
                
                return response()->json(['status' => TRUE, 'msg' => 'updated successfully', 'newsletter' => $newsletter], 200);
            } catch (\Exception $e) {
                
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        public function destroy($id)
        {
            try {
                
                $newsletter_block = Newsletter::find($id);
                $newsletter_block->delete();
                return response()->json(['status' => TRUE, 'msg' => 'Newsletter Deleted Successfully'], 200);
            } catch (\Exception $e) {
                
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        /**
         * Display a detail of the newsletter record from newsletter id
         * Fetch newsletter sending
         * @param integer $id
         * @return \Illuminate\Http\Response
         */
        public function fetechNewsLetterSending($id)
        {
            
            try {
                
                $newsletter = $this->newsletter->fetechNewsLetterSending($id);
                
                return response()->json(['status' => TRUE, 'data' => $newsletter]);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        public function newsletterScheduleSave(Request $request)
        {
            
            try {
                //validation
                $validator = validator::make($request->all(), [
                    
                    'newsletter_id' => 'required',
                    'sender_id'     => 'required',
                    'schedule_time' => 'required',
                    'list_id'       => 'required',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 400);
                }
                $news = Newsletter::with('template')->find($request->newsletter_id);
                if ($news != NULL && empty($news->subject)) {
                    return response()->json(['status' => FALSE, 'msg' => 'Please add Subject'], 400);
                }
                if ($news != NULL && (!isset($news->template->id))) {
                    return response()->json(['status' => FALSE, 'msg' => 'Please Choose Template First'], 400);
                }
                $dataSave = [
                    'newsletter_id' => $request->newsletter_id,
                    'sender_id'     => $request->sender_id,
                    'schedule_time' => $request->schedule_time,
                    'list_id'       => json_decode($request->list_id),
                ];
                $newsletter = $this->newsletter->newsletterScheduleSave($dataSave);
                
                return response()->json(['status' => TRUE, 'data' => $newsletter]);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        public function getNewsletterPreview($id)
        {
            try {
                $newsletter = Newsletter::with(['blocks' => function ($q) {
                    $q->orderByRaw('CAST(short_order AS UNSIGNED) ASC');
                }, 'template:id,footer_html_code,header_html_code'])->find($id);
                return response()->json(['status' => TRUE, 'newsletter' => $newsletter], 200);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        public function spamTest(Request $request)
        {
            $validator = validator::make($request->all(), [
                'newsletter_id' => 'required',
                'sender_id'     => 'required',
                // 'schedule_time' => 'required',
                // 'list_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 400);
            }
            $dataSave = [
                'newsletter_id' => $request->newsletter_id,
                'sender_id'     => $request->sender_id,
                // 'schedule_time' => $request->schedule_time,
                // 'list_id' => json_decode($request->list_id)
            ];
            $postData = $this->newsletter->addnewsletterToicontact($dataSave);
            $data = $this->IContact->addTempletes($postData);
            if(isset($data->warnings) && $data->warnings && !$data->messages) {
                return response()->json(['status' => false, 'msg' => 'Newsletter data is not full field'], 422);
            }
            // var_dump($data);die;
            $Listids = $this->list->createDefaultList();
            // var_dump($Listids);die;
            $list = [];
            foreach ($Listids as $listdata) {
                $list[] = $listdata['icontact_id'];
            } 
            $postDataSpam = [];
            foreach ($data->messages as $msg) {
                $postDataSpam = [[
                    'messageId'      => $msg->messageId,
                    'includeListIds' => implode(',', $list),
                ]];
            }
            
            // dd($postDataSpam);
            $spamReport = $this->IContact->spamTest($postDataSpam);
            return response()->json($spamReport);
        }
        
        public function newsletterScheduleCancel(Request $request)
        {
            try {
                $postData = $this->newsletter->newsletterScheduleCancel($request->newsletterId);
                if ($postData) {
                    return response()->json(['status' => TRUE, 'data' => $postData], 200);
                } else {
                    return response()->json(['status' => fasle, 'data' => $postData], 200);
                }
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        public function sendTestEmail(Request $request)
        {
            try {
                $validator = validator::make($request->all(), [
                    'newsletterId' => 'required',
                    'assignedList' => 'required',
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 400);
                }
                $news = Newsletter::find($request->newsletterId);
                if ($news != NULL && empty($news->subject)) {
                    return response()->json(['status' => FALSE, 'msg' => 'Please add Subject'], 400);
                }
                $emails = [];
                $assignList = json_decode($request->assignedList);
                
                foreach ($assignList as $item) {
                    $emails[] = $item->email;
                }
                $emails = array_unique($emails);
                $newsletter = Newsletter::with('blocks', 'template:id,footer_html_code,header_html_code,text_for_browser_view')->find($request->newsletterId);
                
                $html = $this->newsletter->renderHtml($newsletter);
                $mailData['mail'] = ['subject' => 'Test Email -' . ucfirst($newsletter->subject), 'emails' => $emails, 'msg' => $html];
                // var_dump($mailData['mail']);die;
                $postData = $this->core->SendMassEmail($mailData);
                
                if ($postData == TRUE) {
                    return response()->json(['status' => TRUE, 'msg' => "Email sent"], 200);
                } else {
                    return response()->json(['status' => FALSE, 'msg' => "Email not sent"], 200);
                }
            } catch (\Exception $e) {
                // var_dump($e);die;
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        public function webUrl($newsletterId)
        {
            try {
                $newsletterId = base64_decode($newsletterId . '-id');
                $newsletter = Newsletter::with('blocks', 'template:id,footer_html_code,header_html_code')->find($newsletterId);
                $html = '';
                if ($newsletter->template != NULL) {
                    $html = $this->newsletter->renderHtml($newsletter);
                } else {
                    $html = 'No template selected';
                }
                return view('newsletter.newsletter', ['html' => $html]);
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        public function addListInNewsletter(Request $request)
        {
            try {
                $data = ['listId' => $request->listId, 'newsletterId' => $request->newsletterId];
                $list = $this->newsletter->addListInNewsletter($data);
                $counts = $this->newsletter->countTotalDupilcate($request->newsletterId);
                if ($list) {
                    return response()->json(['status' => TRUE, 'data' => $counts], 200);
                } else {
                    return response()->json(['status' => FALSE, 'data' => $counts], 200);
                }
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        public function removeListInNewsletter(Request $request)
        {
            try {
                $data = ['listId' => $request->listId, 'newsletterId' => $request->newsletterId];
                $list = $this->newsletter->removeListInNewsletter($data);
                $counts = $this->newsletter->countTotalDupilcate($request->newsletterId);
                if ($list) {
                    return response()->json(['status' => TRUE, 'data' => $counts], 200);
                } else {
                    return response()->json(['status' => FALSE, 'msg' => 'List cant deleted'], 200);
                }
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        public function newsletterReport($id)
        {
            try {
                $icontactMessageId = IcontactMeta::where(['type' => 3])->where('column_id', $id)->orderBy('id','desc')->first();
               if(!empty($icontactMessageId)){
                $res = $this->IContact->getStatistics($icontactMessageId->icontact_id);
                $messageData = $this->getMessageData($icontactMessageId, $id);
                $res->statistics->bounces = count($messageData['bounces']);
                if (is_object($res)) {
                    return response()->json(['status' => TRUE, 'data' => ['stats' => $res->statistics, 'data' => $messageData]], 200);
                } else {
                    return response()->json(['status' => FALSE, 'msg' => 'No data found'], 200);
                }
               } else {
                   return response()->json(['status' => FALSE, 'msg' => 'No data found'], 200);
               }
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        public function getMessageData($icontactMessageId, $nId)
        {
            try {
                $data = [];
                $getBounces = $this->IContact->getBounces($icontactMessageId->icontact_id);
                
                // if(is_object($getBounces)){
                // 	if(is_array($getBounces->bounces) && count($getBounces->bounces)){
                // 		$bounceContact=collect($getBounces->bounces);
                // 		//get Bounce conatct id
                // 		$IContactContactId=$bounceContact->pluck('contactId')->toArray();
                
                // 	}
                // }
             
                $getClicks = $this->IContact->getClicks($icontactMessageId->icontact_id);
                $getOpens = $this->IContact->getOpens($icontactMessageId->icontact_id);
                $getUnsubscribes = $this->IContact->getUnsubscribes($icontactMessageId->icontact_id);
                $data['bounces'] = (is_object($getBounces)) ? $getBounces->bounces : [];
                $data['clicks'] = (is_object($getClicks)) ? $getClicks->clicks : [];
                $data['opens'] = (is_object($getOpens)) ? $getOpens->opens : [];
                $data['unsubscribes'] = (is_object($getUnsubscribes)) ? $getUnsubscribes->unsubscribes : [];
                $this->checkBounceOrSubscribe($data['bounces'], 'bounce');
                $this->checkBounceOrSubscribe($data['unsubscribes'], 'unsubscribes');
                $ids = [];
                $openIds = [];
                $array = array_filter($data['opens'], function ($el) use (&$openIds) {
                    if (in_array($el->contactId, $openIds)) {
                        return FALSE;
                    } else {
                        $openIds[] = $el->contactId;
                        return TRUE;
                    }
                });
                $data['opens'] = array_values($array);
                $ids = array_merge($ids, array_column($data['bounces'], 'contactId'));
                $ids = array_merge($ids, array_column($data['clicks'], 'contactId'));
                $ids = array_merge($ids, array_column($data['opens'], 'contactId'));
                $ids = array_merge($ids, array_column($data['unsubscribes'], 'contactId'));
                $ids = array_unique($ids);
                
                $allBounceUnsubscribeIds = $this->getBounceOrSubscribe();
                // dd($allBounceUnsubscribeIds);
                $allIds = $this->getInfo($nId);
                
                // $allIds=array_diff($allIds,$allBounceUnsubscribeIds);
            
                $icotact = IcontactMeta::with('users', 'newsletter_contacts')->whereIn('type', [1, 6])->whereIn('icontact_id', $allIds)->get();
                $blockData = [];
                //get System block contacts data
                if ($icotact && $icotact->count()) {
                    $checkBlockIds = ContactStatus::whereIn('icontact_id', $icotact->pluck('icontact_id')->toArray())->pluck('icontact_id')->toArray();
                    
                    $icotact->map(function ($val) use (&$blockData, $checkBlockIds) {
                        
                        if (in_array($val->icontact_id, $checkBlockIds)) {
                            $blockData[] = $val;
                        }
                    });
                }
                $data['bounces'] = $blockData;
                // dd($ids,$allIds);
                $icotactDatas = [];
                $noinfo = [];
                $unique = [];
                foreach ($icotact as $k => $val) {
                    $icotactDatas[$val->icontact_id] = $val;
                    if (!in_array($val->icontact_id, $ids)) {
                        if (!in_array($val->icontact_id, $unique)) {
                            $unique[] = $val->icontact_id;
                            if (!in_array($val->icontact_id, $allBounceUnsubscribeIds)) {
                                $noinfo[] = ['contactId' => $val->icontact_id, 'openTime' => NULL];
                            }
                        }
                    }
                }
                $data['data'] = $icotactDatas;
                $data['noinfo'] = $noinfo;
                return $data;
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
        }
        
        public function checkBounceOrSubscribe($data, $type)
        {
           
            $ids = [];
            $noIds = [];
            $ids = ContactStatus::where('status', ($type == "bounce") ? 1 : 2)->get()->pluck('icontact_id')->toArray();
            array_filter($data, function ($el) use (&$ids, &$noIds, &$type) {
                if (!in_array($el->contactId, $ids)) {
                    $noIds[] = ['icontact_id' => $el->contactId, 'status' => ($type == "bounce") ? 1 : 2];
                }
            });
         
            (ContactStatus::insert($noIds));
        }
        
        public function getBounceOrSubscribe()
        {
            return ContactStatus::all()->pluck('icontact_id')->toArray();
        }
        
        public function getInfo($id)
        {
         
            $list = $this->newsletter->getListContact($id);
            $icotactExternal = [];
            $icotactInternal = [];
          
            foreach ($list as $k => $value) {
               
                if ($k == 0) {
                    $icotactInternal = IcontactMeta::whereIn('type', [6])->whereIn('column_id', $value)->get()->pluck('icontact_id')->toArray();
                } else {
                    $icotactExternal = IcontactMeta::whereIn('type', [1])->whereIn('column_id', $value)->get()->pluck('icontact_id')->toArray();
                }
            }
            // dd($icotactInternal,$icotactExternal);
            $data = array_merge(array_unique($icotactInternal), array_unique($icotactExternal));
            return $data;
            dd($list, $data);
        }
        
        public function export(Request $request)
        {
            return Excel::download(new MessageStats($request->newsletterId, $this->IContact), 'Stats.xls');
        }
        
        public function FetchPastNewsletter($id)
        {
        
        }
        
        public function sendToList($id)
        {
            try {
                $list = $this->newsletter->sendToList($id);
                if ($list) {
                    return response()->json(['status' => TRUE, 'data' => $list], 200);
                } else {
                    return response()->json(['status' => FALSE, 'msg' => 'No data found'], 200);
                }
            } catch (\Exception $e) {
                return response()->json(['status' => FALSE, 'msg' => $e->getMessage()], 500);
            }
            
            
        }
        public function searchNewsLetter(Request $request, $tense)
        {
            $data = null;
            if($request->has('key') && strlen($request->key)) {
                $key = $request->key;
                $data = Newsletter::with(['scheduleTime', 'sender'])
                    ->where(function ($q) use($key) {
                        $q->orWhere('newsletters.short_name', 'like', "%$key%");
                        $q->orWhereHas('sender', function($q) use($key) {
                            $q->where('from_name', 'like', "%$key%");
                        });
                    })
                    ->where(function($q) use($tense) {
                        if($tense == 'past') {
                            $q->whereHas('scheduleTime', function ($q) {
                                $q->where('schedule_time', '<=', Carbon::now('Europe/Paris')->format('Y-m-d H:i:s'));
                            });
                        }else {
                            $q->whereDoesntHave('scheduleTime', function ($q) {
                                $q->where('schedule_time', '<=', Carbon::now('Europe/Paris')->format('Y-m-d H:i:s'));
                            });
                        }
                    })
                    ->join('newsletter_senders', 'newsletters.sender_id', '=', 'newsletter_senders.id')
                    ->select('newsletters.*', 'newsletter_senders.from_name as fn')
                    ->orderBy(DB::raw(
                        "CASE " .
                        "WHEN newsletters.short_name LIKE '" . addslashes("$key%") . "' COLLATE utf8mb4_unicode_ci  THEN 1 " .
                        "WHEN newsletters.short_name LIKE '" . addslashes("%$key%") . "' COLLATE utf8mb4_unicode_ci  THEN 2 " .
                        "WHEN newsletters.short_name LIKE '" . addslashes("%$key") . "' COLLATE utf8mb4_unicode_ci  THEN 3 " .
                        "WHEN fn LIKE '" . addslashes("$key%") . "' COLLATE utf8mb4_unicode_ci  THEN 4 " .
                        "WHEN fn LIKE '" . addslashes("%$key%") . "' COLLATE utf8mb4_unicode_ci  THEN 5 " .
                        "WHEN fn LIKE '" . addslashes("%$key") . "' COLLATE utf8mb4_unicode_ci  THEN 6 " .
                        "ELSE 7 " .
                        "END"
                    ))
                    ->get()
                ;
            }
            return response()->json(['status' => true, 'data' => $data], 200);
        }
    }
