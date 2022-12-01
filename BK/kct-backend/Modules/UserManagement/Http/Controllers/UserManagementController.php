<?php

namespace Modules\UserManagement\Http\Controllers;

use App\Models\User;
use http\Env\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Modules\KctAdmin\Transformers\V1\GroupResource;
use Modules\KctAdmin\Transformers\V1\LabelResource;
use Modules\KctAdmin\Transformers\V1\UserFullResource;
use Modules\SuperAdmin\Entities\SuperAdminUser;
use Modules\UserManagement\Http\Requests\ChangeDefaultPwdRequest;
use Modules\UserManagement\Http\Requests\ForgotPasswordRequest;
use Modules\UserManagement\Http\Requests\LoginRequest;
use Modules\UserManagement\Http\Requests\ResetPasswordRequest;
use Modules\UserManagement\Http\Requests\SendResetPasswordRequest;
use Modules\UserManagement\Http\Requests\UserLoginRequest;
use Modules\UserManagement\Services\UserHelper;
use Modules\UserManagement\Traits\ServicesAndRepo;
use Modules\UserManagement\Traits\UmHelper;
use Modules\UserManagement\Transformers\UserTokenResource;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will manage all user authentication related logics and functionalities
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class UserManagementController
 * @package Modules\UserManagement\Http\Controllers;
 */
class UserManagementController extends BaseController {

    use UmHelper;
    use ServicesAndRepo;

    /**
     * @OA\Post(
     *  path="/api/login",
     *  operationId="loginUser",
     *  tags={"User Management"},
     *  summary="To Allow User to login in platform",
     *  description="To Allow User to login in platform",
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(ref="#/components/schemas/LoginRequest"),
     *      ),
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="",
     *      @OA\JsonContent(
     *              ref="#/components/schemas/UserTokenResource"
     *      ),
     *  ),
     *
     * )
     *
     * @param LoginRequest $request
     * @return JsonResponse|UserTokenResource
     */
    public function login(LoginRequest $request) {
        try {
            $credentials = ['email' => $request->input('email'), 'password' => $request->input('password')];
            if (Auth::attempt($credentials)) { // login successful
                // if login success set language which is selected
                $this->repo->userRepository->updateUserLanguage($request->input('lang'));
                App::setLocale(strtolower($request->input('lang')));
                return new UserTokenResource(Auth::user());
            } else { // invalid credentials
                $msg = __('usermanagement::messages.login_failed');
            }
            return UserHelper::send422($msg);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is responsible for user login.
     * 1. Validating the user if fails send error.
     * 2. If user passed the authentication generate and return the access token.
     * -----------------------------------------------------------------------------------------------------------------
     * @param UserLoginRequest $request
     * @return JsonResponse|RedirectResponse
     */
    public function userLogin(UserLoginRequest $request) {
        try {
            $user = $this->repo->userRepository->findByEmail($request->input('email'));
            if ($user != NULL && Hash::check($request->input('password'), $user->password)) {
                $isOrganiser = $user->group->organiser->where('user_id', $user->id)->first();
                // If user have normal user then send the error
                if (!$isOrganiser) {
                    return back()->withErrors(['email' => __('usermanagement::messages.only_organiser_can_access')]);
                }
                Auth::loginUsingId($user->id);
                // Create access token for user
                $accessToken = $user->createToken('Laravel Password Grant Client')->accessToken;
                // Prepare url for redirect to user
                $link = $this->services->kctService->prepareUrl('access', []);
                return redirect()->to($link);
            } else {
                return back()->withErrors(['email' => __('usermanagement::messages.login_failed')]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => $e->getMessage(),
                'trace' => $e->getTrace()
            ], 500);
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method use for user forget the password
     * This method will send the reset link to user email
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param ForgotPasswordRequest $request
     * @return string
     */
    public function forgotPassword(ForgotPasswordRequest $request): string {
        try {
//            $link = $this->services->kctService->getDefaultHost($request);
            $this->services->emailService->resetPassword($request);
            return redirect()->back()->with(['messages' => [
                __('usermanagement::messages.sent_pwd_reset_link'),
            ]]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method use for set the reset password view
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $email
     * @param $key
     * @return View
     */
    public function resetView($email, $key): View {
        return view('usermanagement::auth.reset_password', compact('email', 'key'));
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method use for reset the password of the user
     * Check all the condition of the reset password then redirect to signin page
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param ResetPasswordRequest $request
     * @return View/r
     */
    public function resetPassword(ResetPasswordRequest $request): string {
        try {
            $user = $this->repo->userRepository->findByEmail($request->email);
            if ($user->identifier != null && $user->identifier == $request->identifier) {
                $user->update(['password' => Hash::make($request->password), 'identifier' => null,'login_count' => 1]);
                return redirect()->route('um-signin')->with('success', __('usermanagement::messages.pwd_reset_success'));
            }
            return redirect()->back()->withErrors([
                'user' => 'Invaid Identifired',
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function gotAccess($token) {
        dd($token);
    }

    /**
     * @OA\Post(
     *  path="/api/v1/admin/login",
     *  operationId="loginUser",
     *  tags={"User"},
     *  summary="To Allow User to login in platform",
     *  description="To Allow User to login in platform",
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(ref="#/components/schemas/UserLoginRequest"),
     *      ),
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="string",
     *     description="Weather login passed or failed",example="true"),
     *          @OA\Property(property="data",type="string",
     *     description="Link for accessing account with user token",
     *     example="http://first.kct.local/access?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."),
     *      ),
     *  ),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To allow user to login on admin side(OIT side).
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param UserLoginRequest $request
     * @return JsonResponse|UserTokenResource
     */
    public function adminLogin(UserLoginRequest $request): JsonResponse {
        try {
            $user = $this->repo->userRepository->findByEmail($request->input('email'));
            if (Hash::check($request->input('password'), $user->password)) {
                Auth::loginUsingId($user->id);
                // checking if user should skip set password page on first login
                $this->services->kctService->skipSetPasswordForAuth($user->email);
                $user->refresh();
                // checking if user has previously logged in
                $preLoggedInUser = $user->login_count ? $user->login_count : 0;
                // if user has previously logged in and had set his/her password then dashboard url will be send
                // else set password page url will be send
                $type = $preLoggedInUser ? 'dashboard' : 'set-password';
                $accessToken = $user->createToken('token')->accessToken;
                $user->load('group', 'group.organiser');
                $labels = $this->services->adminService->getLabels(1);
                $currentGroup = $this->umServices()->adminService->getUserCurrentGroup($user->id);
                $link = $this->services->kctService->prepareUrl($type, ['groupKey' => $currentGroup->group_key]);
                return response()->json([
                    'status' => true,
                    'data'   => [
                        'redirect_url'  => $link,
                        'access_token'  => $accessToken,
                        'user_data'     => new UserFullResource($user),
                        'current_group' => new GroupResource($currentGroup),
                    ],
                    'meta'   => ['labels' => LabelResource::collection($labels),],
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }
    }

    /**
     * @OA\Post(
     *  path="/api/v1/admin/default/password",
     *  operationId="defaultPassword",
     *  tags={"User"},
     *  summary="To Allow User to change default password",
     *  description="To Allow User to change default password",
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(ref="#/components/schemas/ChangeDefaultPwdRequest"),
     *      ),
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="string",
     *     description="Weather password change process passed or failed",example="true"),
     *          @OA\Property(property="data",type="string",
     *     description="Password changed successfully",example="Password changed successfully"),
     *      ),
     *  ),
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To allow user to change the default password on the first login on admin side(OIT side)
     * @warning  Default password and new password cannot be same.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param ChangeDefaultPwdRequest $request
     * @return JsonResponse|UserTokenResource
     */
    public function changeDefaultPassword(ChangeDefaultPwdRequest $request): JsonResponse {
        try {
            $user = $this->repo->userRepository->findByEmail($request->email);
            if ($user) {
                $isPasswordUpdated = $user->update([
                    'password'    => Hash::make($request->password),
                    // updating login count value to 1. It will allow user to skip set password page on next login
                    'login_count' => 1
                ]);
                if ($isPasswordUpdated) {
                    return response()->json([
                        'status' => true, 'data' => __('usermanagement::messages.pwd_reset_success')
                    ], 200);
                }
            }
        } catch (\Exception $exception) {
            return $this->handleIse($exception);
        }
    }


    /**
     * @Oa\Post(
     *     path="/api/v1/admin/forgot-password",
     *     tags={"Authenticate"},
     *     summary="Forgot password",
     *     description="Send the reset password link on the user mail",
     *     security={{"api_key":{}}},
     *     @OA\RequestBody(required=true,@OA\JsonContent(ref="#/components/schemas/SendResetPasswordRequest")),
     *     @OA\Response(
     *          response=200,
     *          description="Forgot Password",
     *          @OA\JsonContent(
     *              @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *              @OA\Property(property="data",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          ),
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Reset link not send",
     *          @OA\JsonContent(
     *              @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="false"),
     *              @OA\Property(property="data",type="boolean",
     *     description="To indicate server processed request properly",example="Reset link not send"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @OA\JsonContent(
     *              @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="false"),
     *              @OA\Property(property="data",type="boolean",
     *     description="To indicate server processed request properly",example="Internal server Error"),
     *          ),
     *      ),
     *)
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is responsible for sending a password reset link to user's email
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param SendResetPasswordRequest $request
     * @return JsonResponse
     */
    public function sendResetPassword(SendResetPasswordRequest $request): JsonResponse {
        $this->services->emailService->resetPassword($request);
        return response()->json([
            'status' => true,
            'data'   => true,
        ]);
    }

    /**
     * @Oa\Post(
     *     path="/api/v1/admin/reset-password",
     *     tags={"Authenticate"},
     *     summary="Reset Password",
     *     description="Send the reset password link on the user mail",
     *     security={{"api_key":{}}},
     *     @OA\RequestBody(required=true,@OA\JsonContent(ref="#/components/schemas/ResetPasswordRequest")),
     *     @OA\Response(
     *          response=200,
     *          description="Reset Password",
     *          @OA\JsonContent(
     *              @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *              @OA\Property(property="data",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          ),
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Password not updated",
     *          @OA\JsonContent(
     *              @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="false"),
     *              @OA\Property(property="data",type="boolean",
     *     description="To indicate server processed request properly",example="Password not updated"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @OA\JsonContent(
     *              @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="false"),
     *              @OA\Property(property="data",type="boolean",
     *     description="To indicate server processed request properly",example="Internal server Error"),
     *          ),
     *      ),
     *)
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is responsible for updating the user's password.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function resetPasswordApi(ResetPasswordRequest $request): JsonResponse {
        try {
            $user = $this->repo->userRepository->findByEmail($request->email);
            if ($user->identifier != null && $user->identifier == $request->identifier) {
                $user->update(['password' => Hash::make($request->password), 'identifier' => null,
                               'login_count' => 1]);
                return response()->json([
                    'status' => true,
                    'data'   => true,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error'  => $e->getMessage(),
            ],
                500
            );
        }

    }

}
