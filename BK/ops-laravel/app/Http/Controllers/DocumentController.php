<?php

namespace App\Http\Controllers;

use App\DocumentType;
use App\Meeting;
use App\MeetingDocument;
use App\Model\TaskDocument;
use App\Organisation;
use App\RegularDocument;
use App\TopicDocuments;
use App\Workshop;
use App\WorkshopMeta;
use Auth;
use Carbon\Carbon;
use DB;
use File;
use Hash;
use Illuminate\Http\Request;

class DocumentController extends Controller
{

    private $core;

    public function __construct()
    {
        $this->core = app(\App\Http\Controllers\CoreController::class);
    }

    //code updating quotation for bntec
    public function getDocs(){
        $docs=RegularDocument::where('workshop_id',12)->whereNotIn('id',[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15])/*->orderBy('id','desc')*/->get();
        $done=0;
        foreach ($docs as $k=>$doc) {
            //code for update Quotation from title
//            $oldCote=explode('_',$doc->document_title);
//            if(isset($oldCote[1])){
//
//                if($doc->update(['increment_number'=>(int)substr($oldCote[1],1)]))
//                    $done++;
//            }
            $ext = pathinfo($doc->document_file, PATHINFO_EXTENSION);
            $ex='.'.$ext;
            $postion=strpos($doc->document_title,$ex);

            if($doc->increment_number <10){
                $replacement=' P75E1900'.$doc->increment_number;
            }elseif($doc->increment_number <100){
                $replacement=' P75E190'.$doc->increment_number;
            }else{
                $replacement=' P75E19'.$doc->increment_number;
            }
            if($doc->update(['document_title'=>substr_replace($doc->document_title, $replacement, $postion, 0)]))
                    $done++;
            //dd();

       }
       dd($done);
    }

    public function addDocument(Request $request)
    {
        $domain = strtok($_SERVER['SERVER_NAME'], '.');
        if ($request->workshop_id != '') {
            $workshop = workshop::where('id', $request->workshop_id)->first();
        }
        $workshop_name = $this->core->Unaccent(str_replace(' ', '-', $workshop->workshop_name));

        if ($request->hasFile('doc_file')) {
            $folder = $domain . '/' . $workshop_name . '/' . getDocType($request->document_type_id);
            $filename = $this->core->fileUploadByS3($request->file('doc_file'), $folder, 'public');
            $request->merge(['document_file' => $filename]);
        }
        $request->merge(['increment_number' => getIncrementNumber($request->workshop_id)]);
        // return response($request->workshop_id);
        // return response(getIncrementNumber($request->workshop_id));
        RegularDocument::insert($request->except(['doc_file']));
    }

    public function addTopicFiles(Request $request)
    {

        if ($request->hasFile('doc_file')) {
            $folder = 'uploads' . getDocType($request->document_type_id);
            $filename = $this->core->fileUploadByS3($request->file('doc_file'), $folder, 'public');
            $request->merge(['document_file' => $filename]);
        }
        $request->merge(['increment_number' => 0, 'user_id' => Auth::user()->id, 'created_by_user_id' => Auth::user()->id]);
        $docCreate = RegularDocument::create($request->except(['doc_file']));
        $cote = 0;

        $org_detail = Organisation::where('email', Auth::user()->email)->first();
        //var_dump(Auth::user()->email);exit;
        if ($org_detail != null) {
            $check_type = DocumentType::where('document_name', $org_detail->name_org)->where('document_code', $org_detail->acronym)->pluck('id')->first();
            if ($check_type == null) {
                $check_type = DocumentType::insertGetId(['document_name' => $org_detail->name_org, 'document_code' => $org_detail->acronym]);
            }
            if (RegularDocument::where('id', $docCreate->id)->update(['increment_number' => getIncrementNumber($request->workshop_id), 'uncote' => 0, 'document_type_id' => $check_type])) {
                // $data = RegularDocument::with('workshop')->where('id', $docCreate->id)->first();
                $cote = 1;
            }
        } else {
            //this working due to orgadmin dont have entry in orgnization table
            $org_detail = Organisation::first();
            $check_type = DocumentType::where('document_name', $org_detail->name_org)->where('document_code', $org_detail->acronym)->pluck('id')->first();
            if ($check_type == null) {
                $check_type = DocumentType::insertGetId(['document_name' => $org_detail->name_org, 'document_code' => $org_detail->acronym]);
            }

            if (RegularDocument::where('id', $docCreate->id)->update(['increment_number' => getIncrementNumber($docCreate->workshop_id), 'uncote' => 0, 'document_type_id' => $check_type])) {
                // return response()->json(['status' => 1, 'recored' => $data]);
                $cote = 1;
            }
        }
        $cote = TopicDocuments::insert(array('document_id' => $docCreate->id, 'topic_id' => $request->topic_id, 'created_by_user_id' => Auth::user()->id));

        return response()->json(['status' => $cote]);

    }

    public function addFiles(Request $request)
    {

        if ($request->hasFile('doc_file')) {
            $folder = 'uploads' . getDocType($request->document_type_id);
            $filename = $this->core->fileUploadByS3($request->file('doc_file'), $folder, 'public');
            $request->merge(['document_file' => $filename]);
        }

        $request->merge(['increment_number' => 0, 'user_id' => Auth::user()->id, 'created_by_user_id' => Auth::user()->id]);
        $document = RegularDocument::create($request->except(['doc_file']));
        if ($request->has('addFileType') && $request->addFileType == 'taskcomment') {
            $data = TaskDocument::create(['task_id' => $request->task_id, 'document_id' => $document->id]);
        }
        //RegularDocument::insert($request->except(['doc_file']));
        return response()->json($document);
    }

    public function downloadOfflineSoftware(Request $request)
    {

        if (strpos($_SERVER['HTTP_USER_AGENT'], 'Windows')) {
            $url = "https://s3.eu-west-2.amazonaws.com/opsimplify.com/offline_app/OpsimplifyInstaller.exe";
            return redirect($url);
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh')) {
            $url = "https://s3.eu-west-2.amazonaws.com/opsimplify.com/offline_app/OPSimplify.dmg";
            return redirect($url);
        } else {
            return view('errors.not_found', ['error' => $error]);
        }

        // $download_url = $this->core->getS3Parameter($url, 1);


    }

    public function downloadDocument(Request $request)
    {
        $rdData = RegularDocument::whereId($request->docid)->first();

        if (empty($rdData)) {
            $error = 'File doesn`t exist !';
            return view('errors.not_found', ['error' => $error]);
        } else {
            $file_name = str_replace(' ', '-', trim($rdData->document_title));
            $ext = pathinfo($rdData->document_file, PATHINFO_EXTENSION);
            $download_url = $this->core->getS3Parameter($request->url, 1, $file_name . '.' . $ext);
            if ($download_url != null) {

                if (!$request->downloadCount) {
                    RegularDocument::whereId($request->docid)->increment('download_count');
                    return redirect($download_url);
                }
            } else {
                if (!$request->downloadCount) {
                    $error = 'File doesn`t exist !';
                    return view('errors.not_found', ['error' => $error]);
                }
            }
            return response()->json(RegularDocument::whereId($request->docid)->first()->download_count);
        }
        /* if ($rdData->event_id > 0) {
             if (File::exists($rdData->document_file)) {
                 header("Cache-Control: public");
                 header("Content-Description: File Transfer");
                 header("Content-Disposition: attachment; filename=" . $file_name . '.' . $ext);
                 header("Content-Type: application/zip");
                 header("Content-Transfer-Encoding: binary");
                 // header('Content-type: '.$result['ContentType']);
                 // header('Content-length:' . $result['ContentLength']);
                 readfile($rdData->document_file);
             }
              RegularDocument::whereId($request->docid)->increment('download_count');
         } else {

             $download_url = $this->core->getS3Parameter($request->url, 1, $file_name . '.' . $ext);
             if ($download_url != null) {
                 RegularDocument::whereId($request->docid)->increment('download_count');
                 return redirect($download_url);
             }
         }*/
    }

    public function downloadResource(Request $request)
    {
        $download_url = $this->core->getS3Parameter($request->url, 1);
        if ($download_url != null) {
            return redirect($download_url);
        }
        $error = 'File doesn`t exist !';
        return view('errors.not_found', ['error' => $error]);
    }

    public function updateDocTitle(Request $request)
    {
        return response()->json(RegularDocument::where('id', $request->id)->update(['document_title' => $request->document_title]));
    }

    public function deleteDocument(Request $request)
    {
        return response()->json(RegularDocument::where('id', $request->id)->update(['is_active' => 0]));
    }

    public function downloadPrepdStaticPdf(Request $request)
    {

        $meeting = Meeting::where('id', $request->mid)->first(['is_import']);
        if ($meeting->is_import == 1) {
            $data = ['wid' => $request->wid, 'mid' => $request->mid];
            $pdf = $this->core->repdPdf($data);
            return redirect('public/pdf/' . $pdf['pdf_name']);
        } else {
            $rdData = RegularDocument::where(['workshop_id' => $request->wid, 'event_id' => $request->mid, 'document_type_id' => $request->type])->orderBy('id', 'desc')->first();
            if (empty($rdData)) {
                $error = 'File doesn`t exist !';
                return view('errors.not_found', ['error' => $error]);
            } else {
                $file_name = str_replace(' ', '-', trim($rdData->document_title));
                $ext = pathinfo($rdData->document_file, PATHINFO_EXTENSION);
                //working due to double extension
                $extCheck = pathinfo($rdData->document_title, PATHINFO_EXTENSION);

                if ($extCheck == $ext)
                    $fullName = $file_name;
                else
                    $fullName = $file_name . '.' . $ext;
                $download_url = $this->core->getS3Parameter($rdData->document_file, 1, $file_name . '.' . $ext);
                // $download_url =Storage::temporaryUrl(
                //     'file1.jpg', now()->addMinutes(5)
                // );
                if ($download_url != null) {
                    return redirect($download_url);
                } else {
                    if (!$request->downloadCount) {
                        $error = 'File doesn`t exist !';
                        return view('errors.not_found', ['error' => $error]);
                    }
                }
            }
        }
    }

    public function coteDocument(Request $request)
    {
        if (Auth::user()->role == 'M1') {
            $org_detail = Organisation::where('email', Auth::user()->email)->first();
            //var_dump(Auth::user()->email);exit;
            if ($org_detail != null) {
                $check_type = DocumentType::where('document_name', $org_detail->name_org)->where('document_code', $org_detail->acronym)->pluck('id')->first();
                if ($check_type == null) {
                    $check_type = DocumentType::insertGetId(['document_name' => $org_detail->name_org, 'document_code' => $org_detail->acronym]);
                }

                if (RegularDocument::where('id', $request->id)->update(['increment_number' => getIncrementNumber($request->workshop_id), 'uncote' => 0, 'document_type_id' => $check_type])) {
                    $data = RegularDocument::with('workshop')->where('id', $request->id)->first();
                    return response()->json(['status' => 1, 'recored' => $data]);
                }
            } else {
                //this working due to orgadmin dont have entry in orgnization table
                $org_detail = Organisation::first();
                $check_type = DocumentType::where('document_name', $org_detail->name_org)->where('document_code', $org_detail->acronym)->pluck('id')->first();
                if ($check_type == null) {
                    $check_type = DocumentType::insertGetId(['document_name' => $org_detail->name_org, 'document_code' => $org_detail->acronym]);
                }

                if (RegularDocument::where('id', $request->id)->update(['increment_number' => getIncrementNumber($request->workshop_id), 'uncote' => 0, 'document_type_id' => $check_type])) {
                    $data = RegularDocument::with('workshop')->where('id', $request->id)->first();
                    return response()->json(['status' => 1, 'recored' => $data]);
                }
            }
        }
        return response()->json(['status' => 0]);
    }

    public function searchDocument(Request $request)
    {
        $query = RegularDocument::query();
        //|| Auth::user()->role != 'M0'
        if (Auth::user()->role != 'M1') {
            $user = WorkshopMeta::where('user_id', Auth::user()->id)->pluck('workshop_id');
            $workshop = Workshop::where('is_private', 0)->pluck('id');
            $finalmerge = array_merge($user->toArray(), $workshop->toArray());
        }
        if ($request->workshop_id != '') {
            $query->where('workshop_id', $request->workshop_id);
        }
        if ($request->document_type_id != '') {
            $query->where('document_type_id', $request->document_type_id);
        }
        if ($request->issuer_id != '') {
            $query->where('issuer_id', $request->issuer_id);
        }
        if ($request->message_category_id != '') {
            $query->where('message_category_id', $request->message_category_id);
        }
        if ($request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }
        if ($request->uncote == 1) {
            $query->where('uncote', $request->uncote);

        }
        if ($request->uncote == 0) {
            $query->where('uncote', $request->uncote);
        }
        if ($request->start_date != '' && $request->end_date != '') {
            //date('Y-m-d', strtotime($request->start_date)date('Y-m-d', strtotime($request->end_date)
            $query->whereBetween(DB::raw('date(created_at)'), array(Carbon::parse(str_replace('/', '-', $request->start_date))->format('Y-m-d'), Carbon::parse(str_replace('/', '-', $request->end_date))->format('Y-m-d')));
        } else {
            if ($request->start_date != '') {
                $query->where(DB::raw('date(created_at)'), '>=', Carbon::parse(str_replace('/', '-', $request->start_date))->format('Y-m-d'));
            }
            if ($request->end_date != '') {
                $query->where(DB::raw('date(created_at)'), '<=', Carbon::parse(str_replace('/', '-', $request->start_date))->format('Y-m-d'));
            }
        }
        if (trim($request->cote) != '') {
            $inc_number = filter_var(preg_replace('/\s+/', '', $request->cote), FILTER_SANITIZE_NUMBER_INT);
            $year = '20' . substr($inc_number, 0, 2);
            //$year = '20' . substr(substr(trim($request->cote),-5), 0, 2);

            $code = substr(preg_replace('/\s+/', '', $request->cote), 0, 3);

            if ($code != '' && strlen(trim($request->cote)) > 5) {
                $wid = Workshop::where('code1', trim($code))->first(['id']);
                ($wid != null) ? $query->where('workshop_id', $wid->id) : '';

            }
            $lStr = ltrim(substr($inc_number, 2, 4), '0');
            //substr($inc_number,-1)var_dump( $lStr,substr($inc_number,-1));exit;
            $query->where('increment_number', $lStr)->whereYear('created_at', $year);
        }
        if (trim($request->document_title) != '') {
            $query->where('document_title', 'like', '%' . trim($request->document_title) . '%');
        }
        if (isset($user) && count($user)) {
            $serachRes = $query->with('workshop', 'issuer', 'documentType', 'messageCategory', 'user')->where('is_active', 1)->whereIn('workshop_id', $finalmerge)
                ->orderBy('created_at', 'desc')->get();
        } else {
            $serachRes = $query->with('workshop', 'issuer', 'documentType', 'messageCategory', 'user')->where('is_active', 1)
                ->orderBy('created_at', 'desc')->get();
            //  var_dump($serachRes);exit;
        }

        return response()->json($serachRes);
    }

    public function getDownloadCount(Request $request)
    {
        $download = RegularDocument::whereId($request->docid)->first()->download_count;
        return response()->json($download);
    }

    public function checkSignaturePdf(Request $request)
    {
        $doc = MeetingDocument::where(['meeting_id' => $request->mid, 'workshop_id' => $request->wid])->first();
        $flag = (!empty($doc)) ? 1 : 0;
        return response($flag);


    }

    public function genSignaturePdf(Request $request)
    {
        $doc = MeetingDocument::where(['meeting_id' => $request->mid, 'workshop_id' => $request->wid])->first();
        if (!empty($doc)) {
            $workName = Workshop::find($request->wid);
            $meetName = Meeting::find($request->mid);
            $fileNMame = $this->core->Unaccent(str_replace(' ', '-', $workName->workshop_name . ' Liste-de-presence ' . Carbon::parse($meetName->date)->format('d-m-y') . substr($doc->doc_name, -4)));
            $download_url = $this->core->getS3Parameter($doc->doc_name, 1, $fileNMame);
            return redirect($download_url);
        }
        $error = 'File doesn`t exist !';
        return view('errors.not_found', ['error' => $error]);
    }


}
