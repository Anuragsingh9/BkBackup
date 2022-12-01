<?php
    
    namespace App\Http\Controllers;
    
    use App\Model\Skill;
    use App\Services\MeetingService;
    use Illuminate\Http\Request;
    use Carbon\Carbon;
    use Hash;
    use DB, Auth;
    use App\Meeting;
    use App\Presence;
    use App\User;
    use App\Setting;
    use App\Workshop, App\WorkshopMeta;
    use App\Topic, App\TopicDocuments, App\TopicNote;
    use App\Task, App\TaskUser;
    use App\MeetingDocument;
    use App\RegularDocument;
    use Illuminate\Support\Facades\App;
    use Illuminate\Support\Facades\Storage;
    use App\Model\LabelCustomization;
    use Modules\Qualification\Entities\ReferrerField;
    use Modules\Qualification\Entities\Step;
    
    class PdfController extends Controller
    {
        private $core, $impromentMeeitng, $tenancy;
        
        public function __construct()
        {
            $this->tenancy = app(\Hyn\Tenancy\Environment::class);
            $this->core = app(\App\Http\Controllers\CoreController::class);
            $this->impromentMeeitng = app(\App\Http\Controllers\ImprovementMeetingController::class);
        }
        
        public function prepdPdfView(Request $request)
        {
            
            $data = ['wid' => $request->wid, 'mid' => $request->mid];
            $pdf = $this->core->prepdPdf($data);
            return redirect('public/pdf/' . $pdf['pdf_name']);
        }
        
        public function genPrepdPdf(Request $request)
        {
            $mid = $request->mid;
            $wid = $request->wid;
            $prepdData = [];
            $result['workshop_data'] = Workshop::with('meta')->withoutGlobalScopes()->find($wid);
            $result['meeting_data'] = Meeting::find($mid);
            $topics = Topic::where('meeting_id', $mid)->with('docs')->orderBy('list_order', 'ASC')->get();
            $result['topic'] = $this->restructureRecursive($topics);
            $discussionData = [];
            foreach ($topics as $val) {
                $prepdData[$val->id] = ['id' => $val->id, 'discussion' => $val->discussion, 'decision' => $val->decision, 'flag_dis' => 1, 'flag_dec' => 1];
            }
            $result['prepd_data'] = $prepdData;
            $result['settings_data'] = $this->getSettingsAarray($wid);
            $result['url'] = url('/') . '/prepd-pdf-view/' . $mid . '/' . $wid;
            $result['lang'] = isset($request->lang)?$request->lang:App::getLocale();
//        dd($result['settings_data']);
//       dd(view('pdf.prepd_pdf', $result));
            return view('pdf.prepd_pdf', $result);
        }
        
        private function restructureRecursive($array, $parentId = 0)
        {
            $branch = [];
            foreach ($array as $element) {
                if ($element->parent_id == $parentId) {
                    $children = $this->restructureRecursive($array, $element->id);
                    if ($children) {
                        $element->children = $children;
                    }
                    $branch[] = $element;
                }
            }
            return $branch;
        }
        
        function getSettingsAarray($wid)
        {
            $last_rec = Workshop::where('id', $wid)->withoutGlobalScopes()->first(['setting', 'id', 'workshop_name']);
            
            $updatedData = ['header_logo' => '', 'footer_line1' => '', 'footer_line2' => '', 'color1' => '', 'color2' => ''];
            if ((isset($last_rec->setting['custom_graphics_enable']) && $last_rec->setting['custom_graphics_enable'] == 1) || isset($last_rec->setting['pdf'])) {
                $pdfSeting = $last_rec->setting['pdf'];
                $settings_data = json_decode(json_encode($pdfSeting));
            } else {
                $settings_data = getSettingData('pdf_graphic');
            }
            
            if ($settings_data) {
                if ($settings_data->header_logo != '') {
                    $settings_data->header_logo = $this->core->getS3Parameter($settings_data->header_logo, 2);
                    $updatedData['header_logo'] = $settings_data->header_logo;
                }
                $color1 = ($settings_data->color1);
                $color2 = ($settings_data->color2);
                $updatedData['footer_line1'] = @$settings_data->footer_line1;
                $updatedData['footer_line2'] = @$settings_data->footer_line2;
                $updatedData['color1'] = 'background:rgba(' . $color1->r . ', ' . $color1->g . ', ' . $color1->b . ',' . $color1->a . ')';
                $updatedData['color2'] = 'background:rgba(' . $color2->r . ', ' . $color2->g . ', ' . $color2->b . ',' . $color2->a . ')';
            }
            return $updatedData;
        }
        
        public function repdPdfView(Request $request)
        {
            $data = ['wid' => $request->wid, 'mid' => $request->mid];
            $pdf = $this->core->repdPdf($data);
            return redirect('public/pdf/' . $pdf['pdf_name']);
        }
        
        public function genRepdPdf(Request $request)
        {
            $mid = $request->mid;
            $wid = $request->wid;
            $repdData = [];
//        $result['presence_data'] = Presence::with('presence_user')->where('workshop_id',$wid)->where('meeting_id',$mid)->whereIn('presence_status',['P','AE'])->get();
            $result['presence_data'] = Presence::where('workshop_id', $wid)->where('meeting_id', $mid)->with('presence_user')->groupBy('user_id')->get()->toArray();
            usort($result['presence_data'], function ($a, $b) {
                return strcmp($a['presence_user']['lname'], $b['presence_user']['lname']);
            });
            
            $result['workshop_data'] = Workshop::with('meta')->withoutGlobalScopes()->find($wid);
            $result['meeting_data'] = Meeting::find($mid);
            // $result['next3Meetings'] = Meeting::where('id', '!=', $result['meeting_data']->id)->where('workshop_id', $wid)->where('created_at', '>=', $result['meeting_data']->created_at)->get()->take(3);
            
            $result['next3Meetings'] = MeetingService::getInstance()->getUpcomingMeetingForRepd($result['meeting_data']->id, $wid);
            $order_by = "CAST(list_order AS UNSIGNED) ASC";
            $topics = Topic::where('meeting_id', $mid)->with('docs')->orderByRaw($order_by)->get();
            
            $result['topic'] = $this->restructureRecursive($topics);
            
            $discussionData = [];
            foreach ($topics as $val) {
                $repdData[$val->id] = ['id' => $val->id, 'discussion' => $val->discussion, 'decision' => $val->decision, 'flag_dis' => 1, 'flag_dec' => 1];
            }
            $result['repd_data'] = $repdData;
            $result['settings_data'] = $this->getSettingsAarray($wid);
            
            $result['settings_data']['pdfcolor1'] = explode(':', $result['settings_data']['color1'])[1];
            $result['settings_data']['pdfcolor2'] = explode(':', $result['settings_data']['color2'])[1];
            //we are getting doc of workshop with meeitn and uncote==1
            $result['document_data'] = RegularDocument::where(['workshop_id' => $wid, 'event_id' => $mid, 'uncote' => 1])->get(['id', 'document_file', 'document_title', 'workshop_id']);//['id','document_file','document_title','workshop_id']
            $result['wid'] = $wid;
            //Get label from label customization table
            //check label custmization enable
            
            $hostname = $this->tenancy->hostname();
            $acc_id = $hostname->id;
            // $acc_id = 1;
            $checkLabel = DB::connection('mysql')->table('account_settings')->where('account_id', $acc_id)->select('custom_profile_enable')->first();
            $label = LabelCustomization::whereIn('name', ['company_name', 'membership'])->get(['name', 'default_en', 'default_fr', 'custom_en', 'custom_fr']);
            $companyLabel = $label->where('name', 'company_name')->first();
            $membership = $label->where('name', 'membership')->first();
            $result['companyLabel'] = ['en_name' => $companyLabel->default_en, 'fr_name' => $companyLabel->default_fr];
            $result['membershipLabel'] = ['en_name' => $membership->default_en, 'fr_name' => $membership->default_fr];
            if (isset($checkLabel->custom_profile_enable) && $checkLabel->custom_profile_enable) {
                $result['companyLabel'] = ['en_name' => $companyLabel->custom_en, 'fr_name' => $companyLabel->custom_fr];
                $result['membershipLabel'] = ['en_name' => $membership->custom_en, 'fr_name' => $membership->custom_fr];
            }
            $result['lang'] = isset($request->lang)?$request->lang:App::getLocale();
            return view('pdf.repd_pdf', $result);
        }
        
        public function zipData(Request $request)
        {
            $wid = $request->wid;
            $document_data = RegularDocument::where(['workshop_id' => $wid])->get(['id']);
            if (count($document_data) > 0) {
                $request->merge(['links' => $document_data->pluck('id'), 'typeDownload' => 'repd']);
                $archiveFile = $this->impromentMeeitng->zipTopicsDocument($request);
                return response()->download(public_path('public/' . $archiveFile));
            }
            return redirect()->back();
        }
        
        public function genInscriptionPdf(Request $request)
        {
            $mid = $request->mid;
            $wid = $request->wid;
            $repdData = [];
            $result['presence_data'] = Presence::select('presences.*', 'workshop_metas.role', 'workshop_metas.meeting_id as mid')->where('presences.meeting_id', $request->mid)->where(['workshop_metas.workshop_id' => $request->wid])
                ->leftjoin('workshop_metas', 'presences.user_id', '=', 'workshop_metas.user_id')->groupBy('user_id')->orderBy('role', 'desc')->get();
            $result['workshop_data'] = Workshop::with('meta')->withoutGlobalScopes()->find($wid);
            $result['meeting_data'] = Meeting::find($mid);
            $result['settings_data'] = $this->getSettingsAarray($request->mid);
            $result['url'] = url('/') . '/inscription-pdf-view/' . $mid . '/' . $wid;
            $result['presence_data'] = $result['presence_data']->load('user')->sortBy('user.lname');
            $result['lang'] = isset($request->lang)?$request->lang:App::getLocale();
            return view('pdf.inscription_pdf', $result);
        }
        
        public function downloadInscriptionPdf(Request $request)
        {
            $data = ['wid' => $request->wid, 'mid' => $request->mid];
            $pdf = $this->core->incriptionPdf($data);
            return redirect('public/pdf/' . $pdf['pdf_name']);
        }
        
        /*public function uploadInscriptionPdf(Request $request)
        {
            $domain = strtok($_SERVER['SERVER_NAME'], '.');
    
            if ($request->workshop_id != '') {
                $workshop = workshop::where('id', $request->workshop_id)->first();
            }
            $workshop_name = $this->core->Unaccent(str_replace(' ', '-', $workshop->workshop_name));
            $folder = $domain . '/' . $workshop_name . '/attendeelist';
            $domain = strtok($_SERVER['SERVER_NAME'], '.');
            if ($request->hasFile('upload_doc')) {
                $filename = $this->core->fileUploadByS3($request->file('upload_doc'), $folder, 'public');
    
                $request['doc_name'] = $filename;
                $insertCheck = MeetingDocument::insert($request->except(['upload_doc']));
                if ($insertCheck == true) {
                    return response()->json('1', 200);
                } else {
                    return response()->json('0', 200);
                }
            } else {
                return response()->json('0', 200);
            }
        }*/
        public function uploadInscriptionPdf(Request $request)
        {
            $domain = strtok($_SERVER['SERVER_NAME'], '.');
            if ($request->workshop_id != '') {
                $workshop = workshop::where('id', $request->workshop_id)->withoutGlobalScopes()->first();
            }
            $workshop_name = $this->core->Unaccent(str_replace(' ', '-', $workshop->workshop_name));
            $folder = $domain . '/' . $workshop_name . '/attendeelist';
            $domain = strtok($_SERVER['SERVER_NAME'], '.');
            if ($request->hasFile('upload_doc')) {
                $meeting_doc = MeetingDocument::where('meeting_id', $request->meeting_id)->first();
                
                if ($meeting_doc != NULL) {
                    $res = Storage::disk('s3')->delete($meeting_doc->doc_name);
                    if ($res) {
                        $del = MeetingDocument::where('meeting_id', $request->meeting_id)->where('workshop_id', $request->workshop_id)->delete();
                        
                    }
                }
                $filename = $this->core->fileUploadByS3($request->file('upload_doc'), $folder, 'public');
                $request['doc_name'] = $filename;
                $insertCheck = MeetingDocument::insert($request->except(['upload_doc']));
                if ($insertCheck == TRUE) {
                    return response(1);
                } else {
                    return response(0);
                }
            } else {
                return response(0);
            }
        }
        
        public function genReferrerPdf($referrerId, $refFieldId)
        {
            $host = app(\Hyn\Tenancy\Environment::class)->hostname();
            $referrerData = ReferrerField::with('referrer', 'candidate.workshop', 'candidate.userSkillSiret', 'candidate.userSkillCompany', 'step')->where('id', $refFieldId)->first();
            
            if (isset($referrerData->referrer) && !empty($referrerData->referrer)) {
                $fields = Skill::with(['skillImages', 'skillSelect', 'skillCheckBox', 'skillMeta', 'skillCheckBoxAcceptance', 'userSkill' => function ($b) use ($referrerData) {
                    $b->where(['field_id' => $referrerData->referrer->id, 'type' => 'referrer'])->select(DB::raw('address_text_input as original_address_text_input'),'user_skills.*');
                }])->whereHas('skillTab', function ($a) {
                    $a->where('tab_type', 6);
                })->where('is_valid', 1)->orderByRaw('CAST(sort_order AS UNSIGNED) ASC')->get();
                $domain = Step::where('is_final_step', 1)->with('domainCheckbox.userSkill')/*->whereHas('domainCheckbox.userSkill', function ($a) use ($referrerData) {
                $a->where(['field_id' => $referrerData->candidate_id, 'type' => 'candidate']);
            })*/
                ->get();
                
                $result['basic'] = $referrerData->toArray();
                $result['custom'] = !empty($fields) ? $fields->toArray() : [];
                $result['domain'] = !empty($referrerData->step) ? $referrerData->step->toArray() : [];
                //  return ($result);
              
                if (isset($host->fqdn) && in_array($host->fqdn,['qualifelec.ooionline.com'])) {
                    return view('qualification::attest-qualiflec', $result);
                } else {
                    return view('qualification::attest', $result);
                }
            }
        }
        
        public function genReferrerPdfSinge($referrerId, $refFieldId, $stepId)
        {
            $host = app(\Hyn\Tenancy\Environment::class)->hostname();
            
            $step = Step::whereId($stepId)->get();
            $result['domain'] = (count($step) > 0) ? $step->toArray() : [];
            $result['basic']['candidate'] = User::with('workshop', 'userSkillSiret', 'userSkillCompany')->where('id', $referrerId)->first()->toArray();
            if (isset($host->id) && in_array($host->id,[2])) {
                return view('qualification::attest-single_qualiflec', $result);
            } else {
                return view('qualification::attest-single', $result);
            }
            
            
        }
    }

