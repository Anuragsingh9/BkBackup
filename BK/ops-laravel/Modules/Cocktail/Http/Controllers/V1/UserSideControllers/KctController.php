<?php

namespace Modules\Cocktail\Http\Controllers\V1\UserSideControllers;

use App\Organisation;
use App\Services\SettingService;
use App\Services\UserService;
use App\Signup;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use DB;
use Excel;
use App\Exceptions\CustomValidationException;
use Modules\Cocktail\Events\EventManuallyOpenedEvent;
use Modules\Cocktail\Events\EventManuallyOpenedEventNow;
use Modules\Cocktail\Exceptions\NotExistsException;
use Exception;
use Modules\Cocktail\Http\Requests\V1\OtpVerifyRequest;
use Modules\Cocktail\Http\Requests\V1\PasswordForgetRequest;
use Modules\Cocktail\Http\Requests\V1\PasswordResetRequest;
use Modules\Cocktail\Http\Requests\V1\UserLoginRequest;
use Modules\Cocktail\Http\Requests\V1\UserRegisterRequest;
use Modules\Cocktail\Services\AuthorizationService;
use Modules\Cocktail\Services\Contracts\EmailFactory;
use Modules\Cocktail\Services\EventSpaceService;
use Modules\Cocktail\Services\KctEventService;
use Modules\Cocktail\Services\KctService;
use Modules\Cocktail\Services\V2Services\KctCoreService;
use Modules\Cocktail\Transformers\EventResourcePublic;
use Modules\Cocktail\Transformers\UserAccessTokenResource;
use Modules\Events\Entities\Event;
use Modules\Events\Service\EmailService;

class KctController extends Controller {
    protected $service;
    /**
     * @var EmailFactory
     */
    private $emailFactory;
    
    public function __construct(EmailFactory $emailFactory) {
        $this->emailFactory = $emailFactory;
        $this->service = KctService::getInstance();
    }
    
    /**
     * @OA\POST(
     *  path="kct/register",
     *  operationId="registerAUser",
     *  tags={"KCT - V1 - Public API"},
     *  summary="To create a account in ops for the user to register in event",
     *  description="Create a user account and return user data with access token to login",
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(ref="#/components/schemas/UserRegisterRequest")
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="User Account Created",
     *      @OA\JsonContent (
     *          @OA\Property(
     *              property="status",
     *              type="boolean",
     *              description="To indicate server processed request properly",
     *              example="true"
     *          ),
     *          @OA\Property(
     *              property="data",
     *              type="object",
     *              description="To indicate server processed request properly",
     *              ref="#/components/schemas/UserAccessTokenResource",
     *          ),
     *      ),
     *   ),
     *  @OA\Response(
     *      response=422,
     *      description="Data is not valid",
     *      @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),
     *  ),
     *  @OA\Response(
     *      response=500,
     *      description="Some Internal Server Issue Occuerred",
     *      @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  )
     * )
     *
     * @param UserRegisterRequest $request
     * @return JsonResponse|UserAccessTokenResource
     */
    public function register(UserRegisterRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $newCode = UserService::getInstance()->prepareHashCode();
            $data = [
                'email'       => $request->email,
                'fname'       => $request->fname,
                'lname'       => $request->lname,
                'password'    => bcrypt($request->password),
                'on_off'      => 0, // as user is not verified yet,
                'role'        => 'M2',
                'login_count' => 1, // setting 1 so next time user on login no first time password setup pop up as we are setting password during reg
                'hash_code'   => $newCode['hashCode'],
                'login_code'  => $newCode['userCode'],
            ];
            $user = UserService::getInstance()->register($request, false, $data);
            if ($user instanceof JsonResponse) {
                return $user;
            }
            Auth::loginUsingId($user->id);
            $user = KctCoreService::getInstance()->updateUserLanguage($request->input('lang'));
            $this->emailFactory->sendOtp($user, $request, $request->input('event_uuid'));
            
            KctEventService::getInstance()->addCurrentUserToEvent($request);
            KctCoreService::getInstance()->markUserAsFirstLogin($request->input('event_uuid'), $user->id);
            DB::connection('tenant')->commit();
            return (new UserAccessTokenResource($user));
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            DB::connection('tenant')->rollback();
            $result = response()->json(['status' => false, 'msg' => "Can't send email right now !", 'trace' => $e->getTrace()], 500);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            $result = response()->json(['status' => false, 'msg' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }
        return $result;
    }
    
    /**
     * @OA\GET(
     *  path="kct/event/{eventUuid}",
     *  operationId="getSpecificEvent",
     *  tags={"KCT - V1 - Public API"},
     *  summary="Get Event Details",
     *  description="To provide the particular event detail so registering user can see the basic details before register to event",
     *  @OA\Parameter(
     *      name="eventUuid",
     *      description="UUID of Event to fetch details",
     *      required=true,
     *      in="path",
     *      @OA\Schema(
     *          type="uuid"
     *      )
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="User Account Created",
     *      @OA\JsonContent(
     *          @OA\Property(
     *              property="status",
     *              type="boolean",
     *              description="To indicate server processed request properly",
     *              example="true"
     *          ),
     *          @OA\Property(
     *              property="data",
     *              type="array",
     *              description="To indicate server processed request properly",
     *              @OA\Items(
     *                  ref="#/components/schemas/EventResourcePublic",
     *              ),
     *          ),
     *      ),
     *   ),
     *  @OA\Response(
     *      response=422,
     *      description="Data is not valid",
     *      @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),
     *  ),
     *  @OA\Response(
     *      response=500,
     *      description="Some Internal Server Issue Occuerred",
     *      @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  )
     * )
     *
     * @param $eventUuid
     * @return JsonResponse|EventResourcePublic
     */
    public function getEventBeforeRegister($eventUuid) {
        $event = KctService::getInstance()->getEventBeforeRegistration($eventUuid);
        try {
            if (!$event) {
                throw new NotExistsException('Event Not exists');
            }
            return (new EventResourcePublic($event))->additional(['status' => true]);
        } catch (NotExistsException $e) {
            return response()->json(['status' => false, 'msg' => 'Event Does not exists'], 422);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => 'Internal Server Error'], 500);
        }
    }
    
    /**
     * @OA\GET(
     *  path="kct/events/list",
     *  operationId="getEventsList",
     *  tags={"KCT - V1 - Public API"},
     *  summary="To fetch some future events list",
     *  description="To get the future virtual events list from which user can select to register/login into the event",
     *  @OA\Response(
     *      response=200,
     *      description="User Account Created",
     *      @OA\JsonContent(
     *          @OA\Property(
     *              property="status",
     *              type="boolean",
     *              description="To indicate server processed request properly",
     *              example="true"
     *          ),
     *          @OA\Property(
     *              property="data",
     *              type="array",
     *              description="To indicate server processed request properly",
     *              @OA\Items(
     *                  ref="#/components/schemas/EventResourcePublic",
     *              ),
     *          ),
     *      ),
     *  ),
     *  @OA\Response(
     *      response=422,
     *      description="Data is not valid",
     *      @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),
     *  ),
     *  @OA\Response(
     *      response=500,
     *      description="Some Internal Server Issue Occuerred",
     *      @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  )
     * )
     *
     * @param Request $request
     * @return ResourceCollection
     */
    public function getEventsList(Request $request) {
        $carbon = Carbon::now();
        $event = Event::where('type', 'virtual')
            ->where(function ($q) use ($carbon) {
                $q->where('date', '>', $carbon->toDateString());
                $q->orWhere(function ($q) use ($carbon) {
                    $q->where('date', '=', $carbon->toDateString());
                    $q->where('end_time', '>', $carbon->toTimeString());
                });
            })
            ->orderBy('date', 'asc')->orderBy('start_time')
            ->limit(5)->get();
        return EventResourcePublic::collection($event)->additional(['status' => true]);
    }
    
    /**
     * @OA\Post(
     *  path="kct/verify/email",
     *  operationId="otpVerify",
     *  tags={"KCT - V1 - Public API"},
     *  summary="To verify user email address by OTP",
     *  description="To check if OTP sent to email address is valid or not and mark the user as verified if otp is correct",
     *  @OA\RequestBody(
     *       required=true,
     *       @OA\JsonContent(ref="#/components/schemas/OtpVerifyRequest")
     *  ),
     *  @OA\Parameter(
     *      name="Authorization",
     *      in="header",
     *      required=true,
     *      description="Bearer {access-token}",
     *      @OA\Schema(
     *           type="string"
     *      ),
     *   ),
     *  @OA\Response(
     *      response=200,
     *      description="OTP Verification Done",
     *      @OA\JsonContent(
     *          @OA\Property(
     *              property="status",
     *              type="boolean",
     *              description="To indicate server processed request properly",
     *              example="true"
     *          ),
     *          @OA\Property(
     *              property="data",
     *              type="boolean",
     *              description="To indicate server processed request properly",
     *              example="true",
     *          ),
     *      ),
     *   ),
     *   @OA\Response(
     *      response=403,
     *      description="User Is Unauthorized",
     *      @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),
     *  ),
     *  @OA\Response(
     *      response=422,
     *      description="Data is not valid",
     *      @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),
     *  ),
     *  @OA\Response(
     *      response=500,
     *      description="Some Internal Server Issue Occuerred",
     *      @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  )
     * )
     *
     * @param OtpVerifyRequest $request
     * @return JsonResponse
     */
    public function otpVerify(OtpVerifyRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            UserService::getInstance()->otpVerify($request->otp);
            
            if ($request->has('event_uuid')) {
                $isMember = AuthorizationService::getInstance()->isUserEventMember($request->event_uuid);
                $data = [
                    'is_participant' => $isMember,
                    'verified'       => true,
                ];
                $event = Event::where('event_uuid', $request->input('event_uuid'))->first();
                if ($event && $isMember) {
                    $tags = KctService::getInstance()->prepareEmailTags($event, Auth::user()->id);
                    $root = $request->input('link', KctService::getInstance()->getDefaultHost($request));
                    $emailData = ['tags' => $tags, 'root' => $root];
                    $this->emailFactory->sendVirtualRegistration($event, Auth::user()->id, $emailData);
                }
            } else {
                $data = true;
            }
            DB::connection('tenant')->commit();
            return response()->json(['status' => true, 'data' => $data], 200);
        } catch (CustomValidationException $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => 'Invalid OTP'], 422);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }
    }
    
    /**
     * @OA\Post(
     *  path="kct/resend/verify/email",
     *  operationId="resendOTP",
     *  tags={"KCT - V1 - Public API"},
     *  summary="To resend the OTP to user email address",
     *  description="When user require the OTP via email , this will resend the email to user.",
     *  @OA\Parameter(
     *      name="Authorization",
     *      in="header",
     *      required=true,
     *      description="Bearer {access-token}",
     *      @OA\Schema(
     *           type="string"
     *      ),
     *   ),
     *  @OA\Response(
     *      response=200,
     *      description="OTP Verification Done",
     *      @OA\JsonContent(
     *          @OA\Property(
     *              property="status",
     *              type="boolean",
     *              description="To indicate server processed request properly",
     *              example="true"
     *          ),
     *          @OA\Property(
     *              property="msg",
     *              type="boolean",
     *              description="To indicate server processed request properly",
     *              example="true",
     *          ),
     *      ),
     *   ),
     *   @OA\Response(
     *      response=403,
     *      description="User Is Unauthorized",
     *      @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),
     *  ),
     *  @OA\Response(
     *      response=422,
     *      description="Data is not valid",
     *      @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),
     *  ),
     *  @OA\Response(
     *      response=500,
     *      description="Some Internal Server Issue Occuerred",
     *      @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  )
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resendVerificationLink(Request $request) {
        try {
            $this->emailFactory->sendOtp(Auth::user(), $request, $request->input('event_uuid'));
            return response()->json(['status' => true, 'msg' => true], 200);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }
    }
    
    /**
     *
     * @OA\POST(
     *  path="kct/login",
     *  operationId="loginUser",
     *  tags={"KCT - V1 - Public API"},
     *  summary="Login a user by email and password",
     *  description="Allow a user to authorize and generate a access token for getting authenticated in protected api's",
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(ref="#/components/schemas/UserLoginRequest")
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="User Account Created",
     *      @OA\JsonContent(
     *         @OA\Property(
     *             property="status",
     *             type="boolean",
     *             description="To indicate server processed request properly",
     *             example="true"
     *         ),
     *         @OA\Property(
     *             property="data",
     *             type="object",
     *             description="User object response after successful login",
     *             ref="#/components/schemas/UserAccessTokenResource"
     *         ),
     *     ),
     *   ),
     *  @OA\Response(
     *      response=422,
     *      description="Data is not valid",
     *      @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),
     *  ),
     *  @OA\Response(
     *      response=500,
     *      description="Some Internal Server Issue Occuerred",
     *      @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  )
     * )
     *
     * @param UserLoginRequest $request
     * @return JsonResponse|UserAccessTokenResource
     */
    public function login(UserLoginRequest $request) {
        try {
            $credentials = ['email' => $request->email, 'password' => $request->password];
            if (Auth::attempt($credentials)) {
                if ($request->has('event_uuid')) {
                    $additional = $this->service->getUserEventRelation($request->event_uuid);
                    return (new UserAccessTokenResource(Auth::user()))->additional(['data' => $additional]);
                }
                if (!Auth::user()->on_off) {
                    $this->emailFactory->sendOtp(Auth::user(), $request);
                }
                return new UserAccessTokenResource(Auth::user());
            } else {
                $result = response()->json(['status' => false, 'msg' => __('cocktail::message.auth_failed')], 422);
            }
        } catch (\Exception $e) {
            $result = response()->json(['status' => false, 'msg' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }
        return $result;
    }
    
    /**
     *
     * @OA\POST(
     *  path="kct/logout",
     *  operationId="logout",
     *  tags={"KCT - V1 - Public API"},
     *  summary="Logout a user from application and destroy the Token validity",
     *  description="This will destroy the user access token validity. So after logout this token will not be able identify user.",
     *  @OA\Response(
     *      response=200,
     *      description="User Account Created",
     *      @OA\JsonContent(
     *          @OA\Property(
     *              property="status",
     *              type="boolean",
     *              description="To indicate server processed request properly",
     *              example="true"
     *          ),
     *          @OA\Property(
     *              property="data",
     *              type="boolean",
     *              description="To indicate logout successfully",
     *              example="true"
     *          ),
     *      ),
     *  ),
     *  @OA\Response(
     *      response=403,
     *      description="User Is Unauthorized",
     *      @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),
     *  ),
     *  @OA\Response(
     *      response=500,
     *      description="Some Internal Server Issue Occuerred",
     *      @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  ),
     * ),
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request) {
        Auth::user()->token()->revoke();
        return response()->json(['status' => true, 'data' => true], 200);
    }
    
    /**
     *
     * @OA\POST(
     *  path="kct/users/password/forget",
     *  operationId="forgotPassword",
     *  tags={"KCT - V1 - Public API"},
     *  summary="To send a password reset link to respective user email address",
     *  description="This will start user password reset process and send an email to user email address contains instruction and link to reset the password",
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(ref="#/components/schemas/PasswordForgetRequest")
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="Reset Link Sent",
     *      @OA\JsonContent(
     *          @OA\Property(
     *              property="status",
     *              type="boolean",
     *              description="To indicate server processed request properly",
     *              example="true"
     *          ),
     *          @OA\Property(
     *              property="sucess",
     *              type="string",
     *              description="Contains the message to display after successfull reset link send",
     *              example="Message Sent !"
     *          ),
     *      ),
     *  ),
     *  @OA\Response(
     *      response=422,
     *      description="Data is not valid",
     *      @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),
     *  ),
     *  @OA\Response(
     *      response=500,
     *      description="Some Internal Server Issue Occuerred",
     *      @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  )
     * )
     *
     * @param PasswordForgetRequest $request
     * @return JsonResponse
     */
    public function forgetPassword(PasswordForgetRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $rootLink = $request->has('link') ? $request->get('link') : $this->service->getDefaultHost($request);
            $this->emailFactory->sendForgetPassword($request->input('email'), $rootLink);
            DB::connection('tenant')->commit();
            return response()->json(['status' => true, 'success' => __('message.FLASH_RESET_PASS_LINK_SEND')]);
        } catch (\Modules\Cocktail\Exceptions\CustomValidationException $e) {
            return $e->render();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }
    }
    
    /**
     * @OA\POST(
     *  path="kct/users/password/reset",
     *  operationId="resetPassword",
     *  tags={"KCT - V1 - Public API"},
     *  summary="To reset the password and set the new password user entered",
     *  description="This will check if password link is valid and reset the password of user account to the new entered password",
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(ref="#/components/schemas/PasswordResetRequest")
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="Password reset",
     *      @OA\JsonContent(
     *          @OA\Property(
     *              property="status",
     *              type="boolean",
     *              description="To indicate server processed request properly",
     *              example="true"
     *          ),
     *          @OA\Property(
     *              property="sucess",
     *              type="string",
     *              description="Contains the message to display after successfull reset link send",
     *              example="Password reset"
     *          ),
     *      ),
     *  ),
     *  @OA\Response(
     *      response=422,
     *      description="Data is not valid",
     *      @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),
     *  ),
     *  @OA\Response(
     *      response=500,
     *      description="Some Internal Server Issue Occuerred",
     *      @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  ),
     * ),
     *
     * @param PasswordResetRequest $request
     * @return JsonResponse
     */
    public function resetPassword(PasswordResetRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $this->service->resetPassword($request);
            DB::connection('tenant')->commit();
            return response()->json(['status' => true, 'success' => __('message.FLASH_RESET_PASS_SUCCESS')]);
        } catch (\Modules\Cocktail\Exceptions\CustomValidationException $e) {
            return $e->render();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'msg' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }
    }
    
    /**
     * @OA\Get(
     *  path="kct/init/data",
     *  operationId="initData",
     *  tags={"KCT - V1 - Public API"},
     *  summary="To get the dynamic data for loading application",
     *  description="This will return neccessary data required to load the application properly",
     *  @OA\Parameter(
     *      name="Authorization",
     *      in="header",
     *      required=false,
     *      description="Bearer {access-token}",
     *      @OA\Schema(
     *          type="string"
     *      ),
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="",
     *      @OA\JsonContent(
     *          @OA\Property(
     *              property="status",
     *              type="boolean",
     *              description="To indicate server processed request properly",
     *              example="true"
     *          ),
     *          @OA\Property(
     *              property="data",
     *              type="object",
     *              description="User Badge Resource after updating",
     *              @OA\Property(
     *                  property="organisation_name",
     *                  type="stirng",
     *                  description="Current Organisation Long Name",
     *                  example="Organisation Long Name",
     *              ),
     *              @OA\Property(
     *                  property="main_color",
     *                  type="object",
     *                  description="Oganisation main color",
     *                  @OA\Property(
     *                      property="color1",
     *                      type="object",
     *                      description="Oganisation main color1 value",
     *                      ref="#/components/schemas/DocColorObject",
     *                  ),
     *                  @OA\Property(
     *                      property="color2",
     *                      type="object",
     *                      description="Oganisation main color2 Value",
     *                      ref="#/components/schemas/DocColorObject",
     *                  ),
     *                  @OA\Property(
     *                      property="head_bg",
     *                      type="object",
     *                      description="Oganisation header color1",
     *                      ref="#/components/schemas/DocColorObject",
     *                  ),
     *                  @OA\Property(
     *                      property="head_tc",
     *                      type="object",
     *                      description="Oganisation header color2",
     *                      ref="#/components/schemas/DocColorObject",
     *                  ),
     *              ),
     *              @OA\Property(
     *                  property="lang",
     *                  type="object",
     *                  description="User language setting",
     *                  @OA\Property(
     *                      property="current",
     *                      type="string",
     *                      description="User current language selected",
     *                  ),
     *                  @OA\Property(
     *                      property="enabled_languages",
     *                      type="array",
     *                      description="Languages enabled from super admin",
     *                      @OA\Items(type="string")
     *                  ),
     *              ),
     *              @OA\Property(
     *                  property="auth",
     *                  type="object",
     *                  description="User Badge Resource after updating",
     *                  @OA\Property(
     *                      property="fname",
     *                      type="string",
     *                      description="First Name of User",
     *                      example="Someone",
     *                  ),
     *                  @OA\Property(
     *                      property="lname",
     *                      type="string",
     *                      description="Last Name of User",
     *                      example="User",
     *                  ),
     *              ),
     *              @OA\Property(
     *                  property="kct_enabled",
     *                  type="integer",
     *                  description="To indicate if module is enabled or not for current account",
     *                  example="1",
     *              ),
     *              @OA\Property(
     *                  property="active_event",
     *                  type="uuid",
     *                  description="UUID of currently active event",
     *                  example="123e4567-e89b-12d3-a456-426614174000",
     *             ),
     *          ),
     *      ),
     *  ),
     *  @OA\Response(
     *      response=500,
     *      description="Some Internal Server Issue Occuerred",
     *      @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  ),
     * ),
     *
     * @param Request $request
     * @return array
     */
    public function initData(Request $request) {
        return $this->service->getInitData($request);
    }
}
