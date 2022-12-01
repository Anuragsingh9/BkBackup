<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Hash;
use DB,Auth;
use App\Organisation;
class OrganisationController extends Controller
{
    private $core;
    public function __construct()
    {
        $this->core=app(\App\Http\Controllers\CoreController::class);
    }
    
    public function updateOrgSetting(Request $request)
    {
        $folder = 'uploads';
        if($request->hasFile('logo_img')){            
            $filename=$this->core->fileUploadByS3($request->file('logo_img'),$folder,'public');
            $request->merge(['logo'=>$filename]);
        }
        if($request->hasFile('icon_img')){            
            $filename=$this->core->fileUploadByS3($request->file('icon_img'),$folder,'public');
            $request->merge(['icon'=>$filename]);
        }
        if($request->hasFile('bashlinelogo_img')){           
            $filename=$this->core->fileUploadByS3($request->file('bashlinelogo_img'),$folder,'public');
            $request->merge(['bashlinelogo'=>$filename]);
        }

        if(Auth::user()->role=='M1'){
            $newRec = Organisation::where('id',1)->update($request->except(['logo_img','icon_img','bashlinelogo_img']));
            return response()->json($newRec);
        }else{
            $newRec = Organisation::where('email',Auth::user()->email)->update($request->except(['logo_img','icon_img','bashlinelogo_img']));
            return response()->json($newRec);
        }
    }

    public function getOrgSetting(request $request)
    {
        $res = Organisation::first();

        if($res->logo!='')
                $res->logo=$this->core->getS3Parameter($res->logo,2);
        if($res->icon!='')
                $res->icon=$this->core->getS3Parameter($res->icon,2);
        if($res->bashlinelogo!='')
                $res->bashlinelogo=$this->core->getS3Parameter($res->bashlinelogo,2);
           
            
        return response(($res));
    }
   
}
