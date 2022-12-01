<?php

namespace App\Http\Controllers;

use App\StaffLogin;
use App\User;
use DB;
use Hash;
use Hyn\Tenancy\Models\Hostname;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Redirect;
use Session;
use Validator;

class StaffAuthController extends Controller
{
    private $tenancy, $core;

    public function __construct()
    {
        $this->tenancy = app(\Hyn\Tenancy\Environment::class);
        $this->core = app(\App\Http\Controllers\CoreController::class);
    }

    public function signin()
    {
        return view('staff.signin');
    }

    public function postSignin(Request $request)
    {

        $rules = array(
            'email' => 'required|email',
            'password' => 'required'
        );
        $this->validate($request, $rules);
        $user = StaffLogin::where(['email' => $request->email])->first();
        if ($user != null && Hash::check($request->password, $user['password'])) {
            session()->put('staff', $user);
            // dd(session()->get());
            return redirect('get-account');
        } else {
            Session::flash('error', "Invalid Credentials , Please try again.");
            return Redirect::back();
        }
    }

    public function accounts()
    {
        if (session()->has('staff')) {
            //$data['accounts'] = HostnameModel::with('organisation')->get();
            return view('staff.staff');
        } else {
            return redirect('support-staff-login');
        }
    }

    public function postAccounts(Request $request)
    {
        $rules = array(
            'text' => 'required',
            'password' => 'required',
            'account_id' => 'required',
            'user_id' => 'required',
        );
        $this->validate($request, $rules);
        if (session()->has('staff')) {
            $this->tenancy->hostname(Hostname::find($request->account_id));
            $hostname = $this->getHostNameData();
            $user = User::find($request->user_id);
            //Auth::loginUsingId($user->id);
            $userTime = md5($user->id . env('APP_KEY'));
            //left shift
            $token = $user->id << 5;

//            $url = config('constants.HOST_TYPE') . $hostname->fqdn . '/#/dashboard';
            $rUrl = env('HOST_TYPE') . $hostname->fqdn . '/' . $userTime . '%20%20' . $token. '%20%20' . session()->get('staff')->id;
            return redirect($rUrl)->with(['data' => session()->get('staff')]);
            echo("<script> var newWin = window.open('" . $rUrl . "');window.parent.location.href='" . $rUrl . "';}</script>");

            /*used for open the new account in new tab
             * echo("<script> var newWin = window.open('" . $rUrl . "', '_blank');if(!newWin || newWin.closed || typeof newWin.closed=='undefined'){alert('Your browser Popup is blocked please allow popup to open this in new tab');window.parent.location.href='" . $rUrl . "';}</script>");*/
            //die(redirect('get-account'));
            //return redirect('get-account');
        } else {
            return redirect('support-staff-login');
        }
    }

    public function search(Request $request)
    {
        if (session()->has('staff')) {
            $hostname = DB::table('hostnames')->where('fqdn', 'LIKE', '%' . $request->q . "%")->get(['id', 'fqdn']);

            return response()->json($hostname);
        } else {
            return response()->json([]);
        }

    }

    public function searchUser(Request $request)
    {
        if (session()->has('staff')) {
            $hostname = DB::table('hostnames')->where('id', $request->a)->first();

            if (isset($hostname->id)) {
                $this->tenancy->hostname(Hostname::find($hostname->id));
                $hostname = $this->getHostNameData();
                $users = User::whereRaw("concat(fname, ' ', lname) like '%$request->q%' ")->orWhere('lname', 'like', '%' . $request->q . '%')->orWhere('email', 'like', '%' . $request->q . '%')->get();

                return response()->json($users);

            }
        } else {
            return response()->json([]);
        }


    }


    function getHostNameData()
    {
        $this->tenancy->website();
        $hostdata = $this->tenancy->hostname();
        $domain = @explode('.' . env('HOST_SUFFIX'), $hostdata->fqdn)[0];
        //$domain = config('constants.HOST_SUFFIX');
        //session('hostdata', ['subdomain' => $domain]);
        return $this->tenancy->hostname();
    }

    public function getTokenLogin(Request $request)
    {

        if (!empty($request->token)) {

            $url = explode(" ", $request->token);
            if (count($url) === 5) {
                $id = $url[2] >> 5;
                $checkId = md5($id . env('APP_KEY'));
                if ($checkId == $url[0]) {
                    $user = User::find($id);
                    (Auth::loginUsingId($user->id));
                    $hostname = $this->getHostNameData();
                    $rUrl = env('HOST_TYPE') . $hostname->fqdn . '/#/dashboard';
                    //adding current staff detail
                    $staffUser = StaffLogin::find($url[4]);
                    session()->put('data', $staffUser);
                    return redirect()->away($rUrl);
                }
            }
        }
    }

    public function logout()
    {
        Auth::logout();
        session()->flush();
        return redirect()->route('signin');
    }

    public function newstafflogin($id = null)
    {
        if ($id > 0) {
            $user['user'] = StaffLogin::find($id);
            return view('super_admin.newstaffadd', $user);
        } else {
            return view('super_admin.newstaffadd');
        }
    }

    public function deltestaff($id)
    {
        if (StaffLogin::where('id', $id)->delete())
            return redirect()->back();
    }

    public function savesuperstaff(Request $request)
    {
        if (isset($request->id) && !empty($request->id)) {
            if (strlen($request->password) > 0) {
                $rules = [
                    'name' => 'required',
                    'phone' => 'required|numeric|digits:10',
                    'mobile' => 'required|numeric|digits:10',
                    'password' => 'min:6|confirmed',
                    'password_confirmation' => 'required|string',
                ];
                $update = ['name' => $request->name,
                    'password' => Hash::make($request->password),
                    'phone' => $request->phone,
                    'mobile' => $request->mobile,
                    'remember_token' => $request->_token];
            } else {

                $rules = array(
                    'name' => 'required',
                    'phone' => 'required|numeric|digits:10',
                    'mobile' => 'required|numeric|digits:10',
                );
                $update = ['name' => $request->name,
                    'phone' => $request->phone,
                    'mobile' => $request->mobile,
                    'remember_token' => $request->_token];
            }
            $errors = $this->validate($request, $rules);
            $data = StaffLogin::where('id', $request->id)->update([
                'name' => $request->name,
                //'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'mobile' => $request->mobile,
                'remember_token' => $request->_token,
            ]);
        } else {
            $rules = array(
                'email' => 'required|email|unique:staff_logins',
                'password' => 'required|string|min:6|confirmed',
                'password_confirmation' => 'required|string',
                'name' => 'required',
                'phone' => 'required|numeric|digits:10',
                'mobile' => 'required|numeric|digits:10',
            );
            $errors = $this->validate($request, $rules);
            $data = StaffLogin::insert([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'mobile' => $request->mobile,
                'remember_token' => $request->_token
            ]);
        }
        return redirect()->route('staffList');
    }

    public function staffList()
    {
        if (session()->has('superadmin')) {
            $data['data'] = StaffLogin::get();
            return view('super_admin.stafflist', $data);
        } else {
            return redirect('support-staff-login');
        }
    }


}
