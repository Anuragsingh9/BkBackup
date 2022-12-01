<?php

namespace App\Http\Controllers;

use App\Organisation;
use App\User;
use Illuminate\Http\Request;
use Hash;
use DB;
use Auth;
use App\SuperadminSetting;
use Response;
class StyleController extends Controller
{
    private $core;
    public function __construct()
    {
        $this->core=app(\App\Http\Controllers\CoreController::class);
    }
    
    public function getSetting(Request $request){
        $newData=[];
        $data = SuperadminSetting::where('setting_key',$request->setting_key)->first();
        if($request->setting_key=='graphic_config'){
             $json_decode = json_decode($data->setting_value);
             if($json_decode->header_logo!=''){
                    $json_decode->header_logo=$this->core->getS3Parameter($json_decode->header_logo,2);
                    $newData['header_logo']=$json_decode->header_logo;
             }
             if($json_decode->right_header_icon!=''){
                    $json_decode->right_header_icon=$this->core->getS3Parameter($json_decode->right_header_icon,2);
                    $newData['right_header_icon']=$json_decode->right_header_icon;
             }
             $newData['color1']=$json_decode->color1;
             $newData['color2']=$json_decode->color2;
             $newData['transprancy1']=$json_decode->transprancy1;
             $newData['transprancy2']=$json_decode->transprancy2;

        }

        if(!empty($newData))
            $data->setting_value=json_encode($newData); 
        return view('style');
        return response()->json($data);

    }


    public function style(){
        $css_content = view('style');
        return Response::make($css_content, 200)
        ->header('Content-Type', 'text/css');
    }

    public function newsletterStyle(){
        $css_content = view('newsletter-style');
        return Response::make($css_content, 200)
        ->header('Content-Type', 'text/css');
    }
    
    public function crmStyle(){
        $css_content = view('crm-style');
        return Response::make($css_content, 200)
        ->header('Content-Type', 'text/css');
    }

    public function qualificationStyle(){
        $css_content = view('qualification-style');
        return Response::make($css_content, 200)
            ->header('Content-Type', 'text/css');
    }

    public function eventStyle(){
        $css_content = view('event-style');
        return Response::make($css_content, 200)
            ->header('Content-Type', 'text/css');
    }
   
    public function surveyStyle(){
        $css_content = view('survey-style');
        return Response::make($css_content, 200)
            ->header('Content-Type', 'text/css');
    }

    public function needHelp(Request $request){

        $org=Organisation::first(['email']);
        $user=User::find($request->user_id);
        if($user->role=='M1'){
            $role='Org Admin';
        }elseif ($user->role=='M0'){
            $role='Super Admin';
        }elseif ($user->role=='M2'){
            $role='Member';
        }
        $data['mail']['subject']=$request->subject;
        $data['mail']['msg']=$request->message;
        $data['mail']['name']=$user->fname.' '.$user->fname;
        $data['mail']['role']=$role;
        $data['mail']['email']=$org->email;

        return response()->json($this->core->SendEmail($data,'mailMsg'));
    }

    public function newFeature(Request $request){

        $org=Organisation::first(['email']);
        $user=User::find($request->user_id);
        if($user->role=='M1'){
            $role='Org Admin';
        }elseif ($user->role=='M0'){
            $role='Super Admin';
        }elseif ($user->role=='M2'){
            $role='Member';
        }
        $data['mail']['subject']=$request->subject;
        $data['mail']['msg']=$request->message;
        $data['mail']['name']=$user->fname.' '.$user->fname;
        $data['mail']['role']=$role;
        $data['mail']['email']=$org->email;

        return response()->json($this->core->SendEmail($data,'mailMsg'));
    }

    public function sendCode(Request $request){

        $user=User::find($request->user_id);
        if($user->role=='M1'){
            $role='Org Admin';
        }elseif ($user->role=='M0'){
            $role='Super Admin';
        }elseif ($user->role=='M2'){
            $role='Member';
        }
        $data['mail']['subject']='Request For Mobile Code';
        $data['mail']['msg']='Here Is your Mobile Code :'.$user->hash_code;
        $data['mail']['name']=$user->fname.' '.$user->fname;
        $data['mail']['role']=$role;
        $data['mail']['email']=$user->email;

        return response()->json($this->core->SendEmail($data,'mailMsg'));
    }
}
