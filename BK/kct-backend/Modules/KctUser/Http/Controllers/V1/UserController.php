<?php

namespace Modules\KctUser\Http\Controllers\V1;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Modules\KctUser\Http\Requests\V1\LoginUSRequest;
use Modules\KctUser\Http\Requests\V1\UserRegisterRequest;
use Modules\KctUser\Transformers\V1\UserAccessTokenResource;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class manages all user related logics and functionalities.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class UserController
 * @package Modules\KctUser\Http\Controllers\V1;
 */
class UserController extends BaseController {

    /**
     * @OA\Post(
     *  path="/api/v1/p/login",
     *  operationId="us-login_user",
     *  tags={"USAPI1- Authorization"},
     *  summary="Login in platform",
     *  description="Login user with email and password to get the access token",
     *  @OA\RequestBody(
     *      required=true,
     *      description="Request body for login user",
     *      @OA\JsonContent(ref="#/components/schemas/LoginUSRequest")
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="Response for user login",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="object",
     *     description="Data Result",ref="#/components/schemas/UserAccessTokenResource",),
     *      ),
     *   ),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource"))
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To allow user to login into the account and according to user email verification status and event
     * status redirect user to different pages accordingly.
     * @info CASE:
     * 1- EMAIL IS VERIFIED
     *      a. If user's email is verified and request has event uuid then redirect to Quick User Info page
     *      b. Else redirect user to Event list page
     * 2- EMAIL IS NOT VERIFIED
     *      a. Send OTP code and OTP page url to user's email address
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param LoginUSRequest $request
     * @return JsonResponse|UserAccessTokenResource
     */
    public function login(LoginUSRequest $request) {
        try {
            $msg = '';
            $redirect = null;
            $credentials = ['email' => $request->input('email'), 'password' => $request->input('password')];
            $eventUuid = $request->input('event_uuid');
            $isParticipant = 0;
            if (Auth::attempt($credentials)) { // checking credentials
                // if login success set language which is selected
                $this->services->kctService->updateUserLanguage($request->input('lang'));
                if (Auth::user()->email_verified_at) { // checking if email is verified
                    if ($request->has('event_uuid') && $this->services->validationService->isEventSpaceOpenOrFuture($request->input('event_uuid'))) {
                        if ($isParticipant = $this->repo->eventRepository->findParticipant($eventUuid, Auth::user()->id)) { // check event member
                            if ($isParticipant->is_joined_after_reg) { // check is first time login
                                return (new UserAccessTokenResource(Auth::user()))->additional([
                                    'status' => true,
                                    'data'   => ['is_participant' => 1]
                                ]);
                            } else { // user is logging first time after register to this event
                                $redirect = $this->services->kctService->getRedirectUrl(
                                    $request,
                                    'quick_user_info',
                                    ['EVENT_UUID' => $request->input('event_uuid')]
                                );
                            }
                        } else { // user is not member of event
                            $this->services->adminService->addUserToEvent(
                                $request->input('event_uuid'),
                                Auth::user()->id
                            );
                            $redirect = $this->services->kctService->getRedirectUrl(
                                $request,
                                'quick_user_info',
                                ['EVENT_UUID' => $request->input('event_uuid')]
                            );
                            $isParticipant = $request->has('access_code') ? 1 : 0;
                        }
                    } else {
                        // login without event or in past event
                        $redirect = $this->services->kctService->getRedirectUrl($request, 'event-list');
                    }
                } else { // user email not verified
                    $this->services->emailService->sendOtp(Auth::user(), null);
                    $events = $this->repo->userRepository->getUserEvents(Auth::user()->id);
                    $redirect = $this->services->kctService->getRedirectUrl(
                        $request,
                        'event-register',
                        ['EVENT_UUID' => $events[0]->event_uuid]
                    );
                }
            } else { // invalid credentials
                $msg = __('kctuser::message.auth_failed');
            }
            if ($redirect) {
                return (new UserAccessTokenResource(Auth::user()))->additional([
                    'status'       => true,
                    'redirect_url' => $redirect,
                    'data'         => ['is_participant' => $isParticipant ? 1 : 0],
                    'event_uuid'   => $events[0]->event_uuid ?? null,
                ]);
            }
            $data = ['status' => false, 'msg' => $msg,];
            return response()->json($data, 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'msg'    => $e->getMessage(),
                'trace'  => $e->getTrace()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *  path="/api/v1/p/register",
     *  operationId="us-registerAUser",
     *  tags={"USAPI1- Authorization"},
     *  summary="Register in platform within a event",
     *  description="Create a user account and return user data with access token to login.",
     *  @OA\RequestBody(
     *      required=true,
     *      description="Request body",
     *      @OA\JsonContent(ref="#/components/schemas/UserRegisterRequest")
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="Response for user register",
     *      @OA\JsonContent (
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="object",
     *     description="Data Result",ref="#/components/schemas/UserAccessTokenResource",),
     *      ),
     *   ),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource")),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource"))
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To register an user on the platform and send OTP link on the user's registered email to verify the
     * email address.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param UserRegisterRequest $request
     * @return JsonResponse|UserAccessTokenResource
     */
    public function register(UserRegisterRequest $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $lang = $request->input('lang', config('kctuser.default_lang'));
            $data = [
                'fname'       => $request->input('fname'),
                'lname'       => $request->input('lname'),
                'email'       => $request->input('email'),
                'password'    => Hash::make($request->input('password')),
                'login_count' => 1,
                'setting'     => ['lang' => $lang], // default language = English(en)
            ];
            $eventGroup = $this->services->adminService->findEventGroup($request->event_uuid);
            $user = $this->services->userManagementService->createUser($data, $eventGroup->id);
            $this->services->adminService->addUserToEvent($request->event_uuid, $user->id);
            // authentication to access user via Auth
            Auth::loginUsingId($user->id);
            // setting up locale for sending email with current lang
            App::setLocale($lang);
            $this->services->emailService->sendOtp($user, $request->event_uuid);
            DB::connection('tenant')->commit();
            return (new UserAccessTokenResource($user))->additional(['status' => true]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            $result = response()->json([
                'status' => false,
                'msg'    => $e->getMessage(),
                'trace'  => $e->getTrace()
            ], 500);
        }
        return $result;
    }

    /**
     * @OA\Get(
     *  path="/api/v1/p/users/settings",
     *  operationId="getUserLevelData",
     *  tags={"USAPI1- User Profile"},
     *  summary="To get the user level settings",
     *  description="",
     *  security={{"api_key": {}}},
     *  @OA\Response(
     *      response=200,
     *      description="User data fetched",
     *      @OA\JsonContent(
     *          @OA\Property(property="active_event_uuid",type="UUID",description="Event Uuid",example="01493146-d018-11ea-9d2a-b82a72a009b4"),
     *          @OA\Property(property="lang",type="string",description="Current lang",example="en"),
     *          @OA\Property(property="auth",type="object",description="User Badge Auth",
     *              @OA\Property(property="user_lname",type="string",description="Last name of user",example="hello"),
     *              @OA\Property(property="user_fname",type="string",description="First name of user",example="hello"),
     *          ),
     *      ),
     *  ),
     *  @OA\Response(
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
     *  ),
     * ),
     *
     * ----------------------------------------------------------------------------------------------------------------
     * @descripiton Get the data according to user
     * Get the active event of the user
     * Get the user language
     * Get the user authentication details
     * ----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserLevelData(Request $request): JsonResponse {
        $activeEvent = $this->services->eventService->getUserActiveEventUuid($request);
        $lang = $this->services->kctService->getUserLang($request);
        // sending the current user data
        $auth = $this->services->kctService->getUserDetails($request);
        return response()->json([
            'active_event_uuid' => $activeEvent ?: null,
            'lang'              => $lang,
            'auth'              => $auth,
        ]);
    }

    /**
     * @OA\Put(
     *  path="/api/v1/p/users/lang",
     *  operationId="updateLang",
     *  tags={"USAPI1- User Profile"},
     *  summary="To update the user level settings",
     *  description="",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(
     *    required=true,
     *    description="",
     *    @OA\JsonContent(
     *       required={"lang"},
     *       @OA\Property(property="lang", type="string", format="text", example="en")
     *    ),
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="User data fetched",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",description="To indicate server processed request properly",),
     *          @OA\Property(property="data",type="object",description="To indicate server processed request properly",
     *              @OA\Property(property="lang",type="string",description="Current lang",example="en"),
     *          ),
     *      ),
     *  ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *      @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),
     *  ),
     *  @OA\Response(response=422,description="Data is not valid",
     *      @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),
     *  ),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *      @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  ),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for the updating the user application language according to the value of lang
     * key provided in request and returns the current language in the response.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateLang(Request $request): JsonResponse {
        try {
            $request->merge(['lang' => strtolower($request->lang)]);
            $validator = Validator::make($request->all(), [
                'lang' => 'required|in:' . 'fr,en',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'msg'    => implode($validator->errors()->all()),
                    'lang'   => $request->lang,
                ]);
            }
            DB::connection('tenant')->beginTransaction();
            $user = Auth::user();
            $setting = $user->setting;
            $setting['lang'] = $request->lang;
            $this->repo->userRepository->getUserById($user->id)->update(['setting' => $setting]);
            DB::connection('tenant')->commit();
            return response()->json(['status' => true, 'data' => ['lang' => $request->lang]]);
        } catch (Exception $exception) {
            DB::connection('tenant')->rollback();
            return response()->json(['status' => false, 'error' => $exception->getMessage()], 422);
        }
    }
}
