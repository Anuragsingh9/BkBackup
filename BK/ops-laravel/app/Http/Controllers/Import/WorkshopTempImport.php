<?php

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use App\Model\WorkshopMetaTemp;
use Illuminate\Http\Request;
use App\Workshop;
use App\WorkshopMeta;

class WorkshopTempImport extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $core, $tenancy, $import;
    public function __construct()
    {
        $this->core = app(\App\Http\Controllers\CoreController::class);
        $this->import = app(\App\Http\Controllers\Import\ImportController::class);
        $this->tenancy = app(\Hyn\Tenancy\Environment::class);
    }
    public function index($id=0)
    {
       
        try {
            if(isset($id) && $id==0)
                $data = WorkshopMetaTemp::with("workshop", "user:id,fname,lname,email")->get();
            else
                $data = WorkshopMetaTemp::where('workshop_id',$id)->with("workshop", "user:id,fname,lname,email")->get();
            return response()->json(['data' => $data, 'status' => true]);
        } catch (Exception $e) {
            return response()->json(['msg' => $e->getMessage(), 'status' => false]);
        }

    }
    public function sendEmailToTempUser(Request $request)
    {
        try {
            $ids = explode(',', $request->ids);
            $error = [];
            $succes = [];
            $successIdUpdate = [];
            $res = 0;
            $insertMeta=null;
            $users = WorkshopMetaTemp::with('workshop',"user:id,email")->whereIn('id',$ids)->get();
            foreach ( $users  as $key => $val) {
            
                $insertMeta=WorkshopMeta::create(['workshop_id' => $val->workshop_id,'user_id'=>$val->user_id,'role'=>0]);
               
               
                if($insertMeta){
                    $succes[]=$val->id;
//                    $dataMail = $this->import->getMailData($val->workshop, 'commission_new_user');
//                    $subject = $dataMail['subject'];
//                    $mailData['mail'] = ['subject' => ($subject), 'email' => $val->user->email, 'workshop_data' => $val->workshop, 'url' => $dataMail['route_members']];
//
//                    $this->core->SendEmail($mailData, 'new_commission_user');
                    WorkshopMetaTemp::where('id',$val->id)->delete();
                }else{
                    $error[]=$val->id;
                }
            
            }
           
            if (!empty($succes)) {
                return response()->json(['msg' => 'User email scuessfull', 'erros' => $error, 'scuess' => $succes, 'status' => true], 200);
            } else {
                return response()->json(['msg' => 'User email error', 'erros' => $error, 'scuess' => [], 'status' => false], 200);
            }

        } catch (Exception $e) {
            return response()->json(['msg' => $e->getMessage(), 'status' => false]);
        }
    }
    public function delImportUser(Request $request)
    {
        $ids = explode(',', $request->ids);
        $succes = WorkshopMetaTemp::whereIn('id', $ids)->delete();
        if ($succes) {
            return response()->json(['msg' => 'User delete scuessfull', 'status' => 200], 200);
        } else {
            return response()->json(['msg' => 'User delete error', 'status' => 200], 200);
        }
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
