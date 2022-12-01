<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use DB;
use App\StartCategory;
use App\Meeting;
use App\User;
use Auth;
use App\Presence;
use App\Wiki;
use App\AccountSettings;
use Hyn\Tenancy\Models\Website;
use Hyn\Tenancy\Contracts\Repositories\WebsiteRepository;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Contracts\Repositories\HostnameRepository;
use Hyn\Tenancy\Environment;

class StartController extends Controller
{
    private $core,$tenancy;
    public function __construct()
    {
         $this->tenancy=app(\Hyn\Tenancy\Environment::class);
        $this->core=app(\App\Http\Controllers\CoreController::class);
    }
   
    public function GetStartList()
    {
            //updating user login count first time.
            $user=User::find(Auth::user()->id);
            if($user->login_count==1){
                User::where('id', Auth::user()->id)->increment('login_count');
            }
        
        $hostname = $this->getHostData();
        //$acc_id = 1;
        $acc_id = $hostname->id;
        $res = DB::connection('mysql')->table('account_settings')->where('account_id', $acc_id)->get();
       
        if($res && $res[0]->user_group_enable==0){
            $data=StartCategory::with(['start'=>function($query){
                $query->where('id','!=',1);
            }])->orderBy('sort_order','ASC')->get();
        } else {
            $data=StartCategory::with('start')->orderBy('sort_order','ASC')->get();
        }
       
        return response()->json($data);
    }

    public function GetPageId(Request $request)
    {
        if($request->pagename=='agenda'){
            return Meeting::where('id',1)->count();
        }
        else if($request->pagename=='inscription'){
            return Presence::where('meeting_id',1)->count();
        }
        else if($request->pagename=='repd'){
            return Meeting::where([[DB::raw('concat(date," ",start_time)'),'<=',DB::raw('NOW()')],['id',1],['validated_prepd',1]])->count();
        }
        else if($request->pagename=='invite-editor'){
            return Wiki::where('id',1)->count();
        }
    }

     function getHostData(){
        $this->tenancy->website();
        $hostdata = $this->tenancy->hostname();
        return $this->tenancy->hostname(); 
    } 
  
}
