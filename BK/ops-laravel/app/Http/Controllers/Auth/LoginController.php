<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\CustomValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApiLoginRequest;
use App\Http\Requests\ApiRegisterRequest;
use App\Http\Resources\UserAccessTokenResource;
use App\Services\UserService;
use App\User;
use Hyn\Tenancy\Models\Hostname;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    
    use AuthenticatesUsers;
    
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        Auth::logout();
        $this->middleware('guest')->except('logout');
    }
    
    public function apiRegister(ApiRegisterRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $user = UserService::getInstance()
                ->register($request); // created service method so in module we can use with some extra functionality
            UserService::getInstance()->sendVerificationMail($user, 0);
            DB::connection('tenant')->commit();
            return (new UserAccessTokenResource($user));
        } catch (CustomValidationException $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => 'Internal Server Error'], 500);
        }
    }
    
    public function apiLogin(Request $request) {
        try {
            $credentials = ['email' => $request->email, 'password' => $request->password];
            if (Auth::validate($credentials)) { // let laravel check that user credentials are correct
                $user = User::where('email', $request->email)->first(); // get that first user with the email provided
                if (Hash::check($request->password, $user->password)) { // still checking again the password if in any case two user have same email then we may give another user access
                    return new UserAccessTokenResource($user);
                } else // if we have fault and mistakenly 2 user exists with same email
                    return response()->json(['status' => false, 'msg' => __('message.contact_support', [':above'=> 'login'])], 422);
            }
            return response()->json(['status' => false, 'msg' => __('auth.failed')], 422);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }
    }
}
