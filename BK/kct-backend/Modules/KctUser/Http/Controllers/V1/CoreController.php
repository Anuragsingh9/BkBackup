<?php

namespace Modules\KctUser\Http\Controllers\V1;

use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Modules\KctAdmin\Rules\EventRule;
use Modules\KctAdmin\Transformers\V1\LabelResource;
use Modules\KctAdmin\Transformers\V1\VirtualEventResource;
use Modules\KctUser\Entities\OtpCode;
use Modules\KctUser\Exceptions\CustomValidationException;
use Modules\KctUser\Http\Requests\ChangePasswordRequest;
use Modules\KctUser\Http\Requests\V1\InviteUserRequest;
use Modules\KctUser\Http\Requests\V1\OtpVerifyRequest;
use Modules\KctUser\Http\Requests\V1\PasswordForgetRequest;
use Modules\KctUser\Http\Requests\V1\PasswordResetRequest;
use Modules\KctUser\Rules\V1\EventAndSpaceOpenOrNotStarted;
use Modules\KctUser\Traits\KctHelper;
use Modules\KctUser\Traits\Services;
use Modules\KctUser\Transformers\V1\BadgeUSResource;
use Modules\KctUser\Transformers\V1\EventGroupSettingResource;
use Modules\KctUser\Transformers\V1\EventWithCurrentSpaceResource;
use Modules\KctUser\Transformers\V1\InvitedUserResourceCollection;
use Modules\UserManagement\Traits\ServicesAndRepo;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class prepares and handles data for core features of app.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class CoreController
 * @package Modules\KctUser\Http\Controllers\V1;
 */
class CoreController extends BaseController {
    use KctHelper;
    use Services;
    use ServicesAndRepo;

    /**
     * @OA\Get(
     *  path="/api/v1/p/init/data",
     *  operationId="us-initData",
     *  tags={"USAPI1"},
     *  summary="To get the data for init api",
     *  description="",
     *  security={{"api_key": {}}},
     *  @OA\Response(
     *      response=200,
     *      description="",
     *      @OA\JsonContent(
     *          @OA\Property(property="organisation_name",type="string",description="Name of the organisation or account"),
     *          @OA\Property(property="main_color",type="string",description="Organisation Color values",
     *              @OA\Property(property="color1",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="color2",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="head_bg",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="head_tc",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *          ),
     *          @OA\Property(property="lang",type="string",description="Current Language of application",
     *              @OA\Property(property="current_lang",type="string",description="Current Language of application",),
     *              @OA\Property(property="enabled_languages",type="string",description="Available Languages")
     *          ),
     *          @OA\Property(property="auth",type="object",description="Data Result",ref="#/components/schemas/BadgeUSResource"),
     *          @OA\Property(property="kct_enabled",type="string",description="To indicate if application is on"),
     *          @OA\Property(property="graphics_data",type="object",description="Application Graphics data",
     *              @OA\Property(property="main_color_1",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="main_color_2",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="header_bg_color_1",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="header_separation_line_color",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="header_text_color",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="customized_join_button_bg",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="customized_join_button_text",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="sh_background",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="conv_background",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="badge_background",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="space_background",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="user_grid_background",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="user_grid_pagination_color",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="event_tag_color",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="professional_tag_color",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="personal_tag_color",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="tags_text_color",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="content_background",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="apply_customisation",type="integer",example="1",enum={0,1}),
     *              @OA\Property(property="header_footer_customized",type="integer",example="1",enum={0,1}),
     *              @OA\Property(property="button_customized",type="integer",example="1",enum={0,1}),
     *              @OA\Property(property="texture_customized",type="integer",example="1",enum={0,1}),
     *              @OA\Property(property="texture_square_corners",type="integer",example="1",enum={0,1}),
     *              @OA\Property(property="texture_remove_frame",type="integer",example="1",enum={0,1}),
     *              @OA\Property(property="texture_remove_shadows",type="integer",example="1",enum={0,1}),
     *              @OA\Property(property="sh_customized",type="integer",example="1",enum={0,1}),
     *              @OA\Property(property="sh_hide_on_off",type="integer",example="1",enum={0,1}),
     *              @OA\Property(property="conv_customization",type="integer",example="1",enum={0,1}),
     *              @OA\Property(property="badge_customization",type="integer",example="1",enum={0,1}),
     *              @OA\Property(property="space_customization",type="integer",example="1",enum={0,1}),
     *              @OA\Property(property="unselected_spaces_square",type="integer",example="1",enum={0,1}),
     *              @OA\Property(property="selected_spaces_square",type="integer",example="1",enum={0,1}),
     *              @OA\Property(property="extends_color_user_guide",type="integer",example="1",enum={0,1}),
     *              @OA\Property(property="user_grid_customization",type="integer",example="1",enum={0,1}),
     *              @OA\Property(property="tags_customization",type="integer",example="1",enum={0,1}),
     *              @OA\Property(property="content_customized",type="integer",example="1",enum={0,1}),
     *              @OA\Property(property="label_customized",type="integer",example="1",enum={0,1}),
     *              @OA\Property(property="general_setting",type="integer",example="1",enum={0,1}),
     *              @OA\Property(property="invite_attendee",type="integer",example="1",enum={0,1}),
     *              @OA\Property(property="video_explainer",type="integer",example="1",enum={0,1}),
     *
     *              @OA\Property(property="header_line_1",type="string",example="Header Line 1"),
     *              @OA\Property(property="header_line_2",type="string",example="Header Line 2"),
     *              @OA\Property(property="qss_video_url",type="string",example="https://youtube.com"),
     *          ),
     *          @OA\Property(property="explainer",type="object",description="The grid video explainer related values"),
     *          @OA\Property(property="labels",type="array",description="Labels with locale value",
     *               @OA\Items(ref="#/components/schemas/LabelResource")
     *          ),
     *      ),
     *  ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To fetch the user data for first login into a specific event.
     * Fetching the user current space, badge of user, event information and user is space host or not.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function initData(Request $request) {
        try {
            $organisation = $this->tenant->hostname();
            if ($organisation) {
                $graphicsData = [];
                $keys = $this->getGraphicKeys();
                // as the account settings are inserted with the null group id so fetching them separately
                $accountSetting = $this->repo->settingRepository->getSettingsByKey(
                    ['account_settings'],
                    null
                );
                // getting the settings for the group with id 1 for the init data
                $settings = $this->repo->settingRepository->getSettingsByKey(['account_settings', ...$keys,]);

                $accountSetting = $accountSetting->where('setting_key', 'account_settings')->first();

                $generalSettings = $this->services->superAdminService->getGeneralSettings();

                // if th settings are not proper on database then syncing with default value
                if ($settings->count() - 1 != count($this->getGraphicKeys())) {
                    $this->services->adminService->syncGroupSettings(1);
                    $settings = $this->repo->settingRepository->getSettingsByKey(['account_settings', ...$keys,]);
                }

                // this method is used to map the data of settings by there type, e.g. for color it will convert to rgba
                $findValue = function ($key) use ($settings) {
                    $section = $this->findSettingSection($key);
                    if ($section == 'colors') {
                        return $this->getColorFromSetting($settings, $key);
                    } else if ($section == 'checkboxes') {
                        return $this->getCheckFromSetting($settings, $key);
                    } else {
                        $data = $settings->where('setting_key', $key)->first();
                        if ($data && isset($data->setting_value[$key])) {
                            $data = $data->setting_value[$key];
                            if ($section == 'images') {
                                if ($key == 'event_image' && $data == config('kctadmin.constants.event_default_image_path')) {
                                    return $this->services->fileService->getFileUrl($data, false);
                                }
                                return $this->services->fileService->getFileUrl($data);
                            }
                            return $data;
                        }
                    }
                    return null;
                };
                // as in previous application some keys were used as other name
                // so here the previous keys will be used and value will be fetched from new source
                foreach (config('kctadmin.hct_oit_graphic_aliases') as $key => $alias) {
                    $graphicsData[$key] = $findValue($alias);
                }
                // some keys are not aliased because they introduced newly so adding them as it is.
                $newKeys = array_diff(
                    $keys,
                    array_values(config('kctadmin.hct_oit_graphic_aliases'))
                );


                foreach ($newKeys as $newKey) {
                    $graphicsData[$newKey] = $findValue($newKey);
                }
                if (App::getLocale() == "en") {
                    $videoUrl = $generalSettings->setting_value['public_video_en'] ?? null;
                } else {
                    $videoUrl = $generalSettings->setting_value['public_video_fr'] ?? null;
                }
                // fetching all the labels
                $labels = $this->services->adminService->getLabels(1);
                return [
                    'organisation_name' => $organisation->fqdn,
                    'main_color'        => [
                        'color1'  => $this->getColorFromSetting($settings, 'main_color_1'),
                        'color2'  => $this->getColorFromSetting($settings, 'main_color_2'),
                        'head_bg' => $this->getColorFromSetting($settings, 'header_bg_color_1'),
                        'head_tc' => $this->getColorFromSetting($settings, 'header_text_color'),
                    ],
                    'lang'              => [
                        'current_lang'      => App::getLocale(),
                        'enabled_languages' => array_keys(config('kctuser.moduleLanguages')),
                    ],
                    'auth'              => Auth::check()
                        ? new BadgeUSResource($this->services->userService->getUserBadge($request->user('api')->id))
                        : null,
                    'kct_enabled'       => $accountSetting->setting_value['kct_enabled'] ?? 0,
                    // Merging new keys with alias key
                    'graphics_data'     => array_merge($graphicsData, [
                        'bottom_bg_is_colored'    => 0,
                        "video_explainer_enabled" => $generalSettings->setting_value['video_explainer_enabled'] ?? 0,
                        "display_on_reg"          => $generalSettings->setting_value['display_on_reg'] ?? 0,
                        "display_on_live"         => $generalSettings->setting_value['display_on_live'] ?? 0,
                        "video_url"               => $videoUrl,
                    ]),
                    'explainer'         => array_merge([
                        'type' => $generalSettings->setting_value['image_path'] ? "img" : "video",
                        'url'  => $this->services->fileService->getFileUrl(
                                $generalSettings->setting_value['image_path']) ?? $videoUrl,
                    ]),
                    'labels'            => LabelResource::collection($labels),
                    'test_audio'        => $this->services->fileService->getFileUrl(env('AUDIO_TEST_S3_PATH'), false),
                ];
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid Account',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'msg'    => $e->getMessage(),
                'trace'  => $e->getTrace()
            ], 500);
        }
    }


    /**
     * @OA\Get(
     *  path="/api/v1/p/login-join-data",
     *  operationId="us-getDataForFirstLogin",
     *  tags={"USAPI1- Authorization"},
     *  summary="To get the data for showing on quick-signup",
     *  description="",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="event_uuid",in="query",description="Event Uuid",required=true),
     *  @OA\Response(
     *      response=200,
     *      description="",
     *      @OA\JsonContent(
     *          @OA\Property(property="data",type="object",description="",ref="#/components/schemas/ChimeUSResource",),
     *      ),
     *  ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To fetch the user data for first login into a specific event.
     * Fetching the user current space, badge of user, event information and user is space host or not.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getDataForFirstLogin(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'event_uuid' => ['required', new EventRule, new EventAndSpaceOpenOrNotStarted],
        ]);
        if ($validator->fails()) {
            return $this->send422(
                implode(',', $validator->errors()->all()), $validator->errors()
            );
        }
        try {
            $event = $this->repo->eventRepository->findByEventUuid($request->input('event_uuid'));
            $event->load('currentSpace');
            if (!$event->currentSpace) {
                // user is not member of event so no current space
                // adding user to event to get default space as current space
                $this->services->adminService->addUserToEvent(
                    $request->input('event_uuid'),
                    Auth::user()->id
                );
                $event->currentSpace = $event->spaces->first();
            }
            // checking if user is host or not for current space
            $isHost = $event->currentSpace && $event->currentSpace->hosts()->where('host_id', Auth::user()->id)->first()
                ? 1 : 0;
            $invites = $this->services->kctService->getUserEventInvites($request->input('event_uuid'));
            $data = [
                'user_badge'     => new BadgeUSResource(
                    $this->services->userService->getUserBadge(Auth::user()->id, $event->event_uuid)),
                'event_resource' => (new EventWithCurrentSpaceResource($event)),
                'invites'        => InvitedUserResourceCollection::collection($invites),
                'is_space_host'  => $isHost ? 1 : 0,
            ];
            return response()->json([
                'status' => true,
                'data'   => $data,
            ]);
        } catch (Exception $e) {
            return $this->handleIse($e);
        }
    }

    /**
     * @OA\Post(
     *  path="/api/v1/p/send/invite",
     *  operationId="us-sendInvitationEmail",
     *  tags={"USAPI1- Event API"},
     *  summary="To invite other users in event",
     *  description="",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(
     *      required=true,
     *      description="Request body",
     *      @OA\JsonContent(ref="#/components/schemas/InviteUserRequest")
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="Users invited successfully",
     *      @OA\JsonContent(
     *          @OA\Property(property="data",type="object",description="",
     *     ref="#/components/schemas/InvitedUserResourceCollection",),
     *      ),
     *  ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To send invitation email to requested users. If user's email already exist in the account then
     * sending the link to login into the event else sending the link to register into the event.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param InviteUserRequest $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function sendInvitationEmail(InviteUserRequest $request) {
        try {
            $data = $this->services->dataService->prepareInviteUsers($request);
            $emails = $this->services->kctService->separateExistingEmailForInvite($data);
            $event = $this->repo->eventRepository->findByEventUuid($request->input('event_uuid'));
            // existing users handling for sending invitation link for event
            foreach ($emails['existingUsers'] as $k => $user) {
                $this->services->emailService->sendInviteToExistingUser($event, $user);
                // as in current index there is user object added so to insert the data user object will be
                // replaced by data to insert
                $emails['existingUsers'][$k] = $this->services->dataService->prepareUserForInvite(
                    $user,
                    1,
                    $request->input('event_uuid')
                );
            }
            // new users handling for sending invitation link for event
            foreach ($emails['newUsers'] as $user) {
                $inviteUser = new User();
                $inviteUser->fname = $user['first_name'];
                $inviteUser->lname = $user['last_name'];
                $inviteUser->email = $user['email'];
                $this->services->emailService->sendInvitationEmail($event, $inviteUser);
            }
            $dataToInsert = array_merge($emails['existingUsers'], $emails['newUsers']);
            $this->repo->userRepository->insertInvite($dataToInsert);
            $data = $this->services->kctService->getUserEventInvites($request->input('event_uuid'));
            return InvitedUserResourceCollection::collection($data);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'msg'    => $e->getMessage(),
                'trace'  => $e->getTrace(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *  path="/api/v1/graphics/customization",
     *  operationId="us-getCustomGraphics",
     *  tags={"USAPI1- Miscellaneous"},
     *  summary="To get the application graphic settings",
     *  description="",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="eventUuid",in="query",description="Event Uuid",required=false),
     *  @OA\Response(
     *      response=200,
     *      description="",
     *      @OA\JsonContent(
     *          @OA\Property(property="data",type="object",description="Graphics data for the application",
     *              @OA\Property(property="main_color_1",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="main_color_2",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="header_bg_color_1",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="header_separation_line_color",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="header_text_color",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="customized_join_button_bg",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="customized_join_button_text",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="sh_background",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="conv_background",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="badge_background",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="space_background",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="user_grid_background",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="user_grid_pagination_color",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="event_tag_color",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="professional_tag_color",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="personal_tag_color",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="tags_text_color",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *              @OA\Property(property="content_background",type="string",example="{""r"":6,""g"":4,""b"":1,""a"":0.5}"),
     *          ),
     *      ),
     *  ),
     *  @OA\Response(response=403,description="User Is Unauthorized",@OA\JsonContent(ref="#/components/schemas/Doc403Resource"),),
     *  @OA\Response(response=422,description="Data is not valid",@OA\JsonContent(ref="#/components/schemas/Doc422Resource"),),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",@OA\JsonContent(ref="#/components/schemas/Doc500Resource"),),
     * ),
     * ----------------------------------------------------------------------------------------------------------------
     * @descripiton Get the custom graphics of the group
     * This method get the label settings and graphics setting
     * ----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function getCustomGraphics(Request $request): JsonResponse {

        $defaultGroup = $this->services->adminService->getDefaultGroup();
        $group = null;
        if ($request->has('event_uuid')) {
            $validator = Validator::make($request->all(), [
                'event_uuid' => ['nullable', 'exists:tenant.events,event_uuid'],
            ]);
            if ($validator->fails()) {
                return $this->send422(implode(
                    ',',
                    $validator->errors()->all()),
                    $validator->errors()
                );
            }
            $event = $this->services->adminService->findEvent($request->event_uuid)->load('group');
            $group = $event->group;
        } else {
            //find the group id for graphics without event uuid
            $group = $defaultGroup;
        }
        $keys = $this->getGraphicKeys();
        if ($group->id != $defaultGroup->id) {
            $group->load(['setting' => function ($q) {
                $q->where('setting_key', 'group_has_own_customization');
            }]);
            if (!$group->setting->setting_value['group_has_own_customization']) {
                // this group does not follow group customization
                $group = $defaultGroup;
            }
        }
        $groupId = $group->id;
        //Get the settings of group
        $settings = $this->repo->settingRepository->getSettingsByKey(
            ['account_settings', ...$keys,],
            $group->id);
        // Find the colors and checkBox values of settings
        $findValue = function ($key) use ($settings) {
            $section = $this->findSettingSection($key);
            if ($section == 'colors') {
                return $this->getColorFromSetting($settings, $key);
            } else if ($section == 'checkboxes') {
                return $this->getCheckFromSetting($settings, $key);
            } else {
                $data = $settings->where('setting_key', $key)->first();
                if ($data && isset($data->setting_value[$key])) {
                    $data = $data->setting_value[$key];
                    if ($section == 'images') {
                        return $this->services->fileService->getFileUrl($data);
                    }
                    return $data;
                }
            }
            return null;
        };
        // as in previous application some keys were used as other name
        // so here the previous keys will be used and value will be fetched from new source
        foreach (config('kctadmin.hct_oit_graphic_aliases') as $key => $alias) {
            $graphicsData[$key] = $findValue($alias);
        }
        // some keys are not aliased because they introduced newly so adding them as it is.
        $newKeys = array_diff(
            $keys,
            array_values(config('kctadmin.hct_oit_graphic_aliases'))
        );
        $generalSettings = $this->services->superAdminService->getGeneralSettings();
        if (App::getLocale() == "en") {
            $videoUrl = $generalSettings->setting_value['public_video_en'] ?? null;
        } else {
            $videoUrl = $generalSettings->setting_value['public_video_fr'] ?? null;
        }
        foreach ($newKeys as $newKey) {
            $graphicsData[$newKey] = $findValue($newKey);
        }
        $graphicsData['video_url'] = $videoUrl;
        $data = [
            'label_setting'        => LabelResource::collection($this->services->adminService->getLabels($groupId)),
            'graphic_data'         => $graphicsData,
            'current_scenery_data' => $this->services->adminService->fetchEventSceneryData($request->event_uuid, true),

        ];
        return response()->json(['status' => true, 'data' => $data]);
    }

    /**
     * @OA\Post(
     *  path="/api/v1/p/verify/email",
     *  operationId="us-otpVerify",
     *  tags={"USAPI1- Authorization"},
     *  summary="To verify user email address by OTP",
     *  description="To check if OTP sent to email address is valid or not and mark the user as verified
     *  if otp is correct",
     *  security={{"api_key": {}}},
     *  @OA\RequestBody(
     *       required=true,
     *       @OA\JsonContent(ref="#/components/schemas/OtpVerifyRequest")
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="OTP Verification Done",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="boolean",
     *     description="To indicate server processed request properly",example="true",),
     *      ),
     *   ),
     *   @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),),
     *   @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),),
     *   @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),)
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To verify the entered OTP by the user in the process of user account creation and on successful
     * creation sending welcome email to the user.
     *
     * @note The OTP expires in 15 minutes from generation time.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param OtpVerifyRequest $request
     * @return JsonResponse
     */
    public function otpVerify(OtpVerifyRequest $request): JsonResponse {
        try {
            DB::connection('tenant')->beginTransaction();
            $data = true;
            $user = $request->has('email')
                ? $this->services->userManagementService->findByEmail($request->input('email'))
                : $request->user('api');
            $otp = $this->repo->userRepository->getOtp($user->id); // fetching the OTP for the user
            $lastOTPTime = $otp->updated_at;
            $currentTime = Carbon::now();
            $validTime = $lastOTPTime->addMinute(OtpCode::$OTP_validity_minutes);

            if($currentTime > $validTime){ // Checking the link expiry
                return $this->send422('OTP Expired !');
            }
            if ($otp && $request->input('otp') == $otp->code) {
                $user = $this->services->userManagementService->findById($user->id);
                $user->email_verified_at = Carbon::now(); // marking email as verified
                $user->update();
                // Check the user is participant then verify the user
                if ($request->has('event_uuid')) {
                    $participant = $this->repo->eventRepository->findParticipant(
                        $request->input('event_uuid'),
                        $user->id
                    );
                    if ($participant) {
                        $data = [
                            'is_participant' => 1,
                            'verified'       => true,
                        ];
                    }
                }

                // add user to in event and mark as registered
                $this->repo->eventRepository->addUserAndMarkRegistered($request, $user);

                $mailData = [];
                $email = $user->email;
                $to_name = $email;

                //Sending email for successful user account creation
                Mail::send('kctuser::email-templates.account_creation_success',
                    $mailData,
                    function ($message) use ($email, $to_name) {
                        $message->to($email, $to_name)->subject('Welcome to HumannConnect!');
                        $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                    }
                );
            } else {
                return $this->send422('Invalid OTP');
            }
            DB::connection('tenant')->commit();
            return response()->json(['status' => true, 'data' => $data], 200);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json([
                'status' => false,
                'msg'    => $e->getMessage(),
                'trace'  => $e->getTrace()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *  path="/api/v1/p/verify/emailByLink",
     *  operationId="us-verifyUserByMagicLink",
     *  tags={"USAPI1- Authorization"},
     *  summary="To verify user email address by magic link",
     *  description="To verify user email address by magic link and verify the user by magic link",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="event_uuid",in="query",description="Event Uuid",required=true),
     *  @OA\Parameter(name="email",in="query",description="use encrypted email",required=true),
     *   @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),),
     *   @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),),
     *   @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),)
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description To verify the user by magic link for account creation. This link has user's name,token and
     * event_uuid. The link will automatically make the user login to platform.
     *
     * @note This link expires in 15 minutes from time of generation. If user clicks on the links after expiry then
     * the user will be redirected to Link expired page.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function verifyUserByMagicLink(Request $request) {
        try {
            DB::connection('tenant')->beginTransaction();
            $user = $request->has('email')
                ? $this->services->userManagementService->findByEmail($this->decryptData($request->input('email')))
                : $request->user('api');

            $userOtpData = $user->load('otp');
            $lastOTPTime = $userOtpData->otp->updated_at;
            $currentTime = Carbon::now();
            $validTime = $lastOTPTime->addMinute(OtpCode::$OTP_validity_minutes);
            $account = request()->getHost();

            if($currentTime > $validTime){ // Checking the link expiry
                $url = $this->umServices()->kctService->prepareUrl('HE-page-expired',['account' => $account, 'event_uuid' => $request->event_uuid]);
            }else{
                $token = $user->createToken('check')->accessToken;
                $user = $this->services->userManagementService->findById($user->id);
                $user->email_verified_at = Carbon::now(); // marking email as verified
                $user->update();
                // add user to in event and mark as registered
                $this->repo->eventRepository->addUserAndMarkRegistered($request, $user);
                $magicUrl = $this->umServices()->kctService->prepareUrl('magic-link',['account' => $account, 'event' => $request->event_uuid]);
                $url = $magicUrl . "?name=" . $user->fname . ' ' . $user->lname . "&token=" . $token . "&event_uuid=" . $request->event_uuid;
                $mailData = [];
                $email = $user->email;
                $to_name = $email;

                //Sending email for successful user account creation
                Mail::send('kctuser::email-templates.account_creation_success',
                    $mailData,
                    function ($message) use ($email, $to_name) {
                        $message->to($email, $to_name)->subject('Welcome to HumannConnect!');
                        $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                    }
                );
            }
            DB::connection('tenant')->commit();
            return redirect()->to($url);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json([
                'status' => false,
                'msg'    => $e->getMessage(),
                'trace'  => $e->getTrace()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *  path="/api/v1/p/resend/verify/email",
     *  operationId="us-resendOTP",
     *  tags={"USAPI1- Authorization"},
     *  summary="To resend the OTP to user email address",
     *  description="When user require the OTP via email , this will resend the email to user.",
     *  @OA\Parameter(name="event_uuid",in="query",description="Event's uuid",required=true),
     *  @OA\Response(
     *      response=200,
     *      description="OTP Verification Done",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="msg",type="boolean",
     *     description="To indicate server processed request properly",example="true",),
     *      ),
     *   ),
     *   @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),),
     * @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),),
     * @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),)
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To send OTP email containing OTP code and OTP page url on user's email.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description To resend the otp to user email address
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resendVerificationLink(Request $request): JsonResponse {
        try {
            $this->services->emailService->sendOtp(
                Auth::user(),
                $request->input('event_uuid')
            );
            return response()->json(['status' => true, 'msg' => true], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'msg'    => $e->getMessage(),
                'trace'  => $e->getTrace()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *  path="/api/v1/p/logout",
     *  operationId="us-logout",
     *  tags={"USAPI1- Authorization"},
     *  summary="Logout a user from application and destroy the Token validity",
     *  description="This will destroy the user access token validity. So after logout this token will not be
     *  able identify user.",
     *  security={{"api_key": {}}},
     *  @OA\Response(
     *      response=200,
     *      description="User Account Created",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",
     *     description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="boolean",
     *     description="To indicate logout successfully",example="true"),
     *      ),
     *  ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To logout user from the platform
     * -----------------------------------------------------------------------------------------------------------------
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method used for logout the user from application
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse {
        Auth::user()->token()->revoke();
        return response()->json(['status' => true, 'data' => true], 200);
    }

    /**
     * @OA\Post(
     *  path="/api/v1/p/users/password/forget",
     *  operationId="us-forgotPassword",
     *  tags={"USAPI1- Authorization"},
     *  summary="To send a password reset link to respective user email address",
     *  description="This will start user password reset process and send an email to user email address
     *     contains instruction and link to reset the password",
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(ref="#/components/schemas/PasswordForgetRequest")
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="Reset Link Sent",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",description="To indicate server processed request properly",
     *              example="true"
     *          ),
     *          @OA\Property(property="msg",type="string",
     *              description="Contains the message to display after successfull reset link send",example="Message Sent !"
     *          ),
     *      ),
     *  ),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),
     *  ),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  )
     * )
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for sending the reset password link to the user email
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param PasswordForgetRequest $request
     * @return JsonResponse
     */
    public function forgetPassword(PasswordForgetRequest $request): JsonResponse {
        try {
            DB::connection('tenant')->beginTransaction();
            $rootLink = $request->input('link') ?: $this->getHostFromRequest();
            $this->services->emailService->sendForgetPassword($request->input('email'), $rootLink);
            DB::connection('tenant')->commit();
            return response()->json(['status' => true, 'msg' => __('message.FLASH_RESET_PASS_LINK_SEND')]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json([
                'status' => false,
                'msg'    => $e->getMessage(),
                'trace'  => $e->getTrace()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *  path="/api/v1/p/users/password/reset",
     *  operationId="us-resetPassword",
     *  tags={"USAPI1- Authorization"},
     *  summary="To reset the password and set the new password user entered",
     *  description="This will check if password link is valid and reset the password of user account
     *     to the new entered password",
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(ref="#/components/schemas/PasswordResetRequest")
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="Password reset",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",description="To indicate server processed request properly",
     *              example="true"
     *          ),
     *          @OA\Property(property="sucess",type="string",
     *              description="Contains the message to display after successfull reset link send",example="Password reset"
     *          ),
     *      ),
     *  ),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource")
     *  ),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource")
     *  )
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method used for reset the password of user
     * This method take the new password and identifier
     * Verify the identifier and set the new password
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param PasswordResetRequest $request
     * @return JsonResponse
     */
    public function resetPassword(PasswordResetRequest $request): JsonResponse {
        try {
            DB::connection('tenant')->beginTransaction();
            $user = $this->services->userManagementService->findByEmail($request->input('email'));
            if ($user->identifier == $request->input('identifier')) {
                $user->password = Hash::make($request->input('password'));
                $user->identifier = null;
                $user->update();
            } else {
                throw new CustomValidationException('invalid_email', null, 'message');
            }
            DB::connection('tenant')->commit();
            return response()->json(['status' => true, 'success' => __('message.FLASH_RESET_PASS_SUCCESS')]);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json([
                'status' => false,
                'msg'    => $e->getMessage(),
                'trace'  => $e->getTrace()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *  path="/api/v1/p/change/password",
     *  operationId="us-changePassword",
     *  tags={"USAPI1- Authorization"},
     *  summary="To change the password and set the new password user entered",
     *  description="This will change the user password and set the new password",
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(ref="#/components/schemas/ChangePasswordRequest")
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="Change password",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",description="To indicate server processed request properly",
     *              example="true"
     *          ),
     *          @OA\Property(property="sucess",type="string",
     *              description="Contains the message to display after successfull reset link send",example="Password reset"
     *          ),
     *      ),
     *  ),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),
     *  ),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  )
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for changing the user password and set the new password
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param ChangePasswordRequest $request
     * @return JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse {
        try {
            DB::connection('tenant')->beginTransaction();
            $user = $this->services->userManagementService->findById(Auth::user()->id);
            $this->services->userManagementService->updateUserById(
                $user->id,
                ['password' => Hash::make($request->input('password')),]
            );
            // re-fetching user as data is updated for user
            $user->refresh();
            DB::connection('tenant')->commit();
            return response()->json(['status' => true, 'data' => 'true'], 200);
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            return response()->json([
                'status' => false,
                'msg'    => $e->getMessage(),
                'trace'  => $e->getTrace()

            ], 500);
        }
    }

    /**
     * @OA\Get(
     *  path="/api/v1/p/otp/page/data",
     *  operationId="us-getOtpPageData",
     *  tags={"USAPI1- Authorization"},
     *  summary="To get the data for otp page processing",
     *  description="",
     *  security={{"api_key": {}}},
     *  @OA\Parameter(name="key",in="query",description="Encrypted Key to get email and event uuid",required=true),
     *  @OA\Response(
     *      response=200,
     *      description="",
     *      @OA\JsonContent(
     *          @OA\Property(property="data",type="object",description="",ref="#/components/schemas/ChimeUSResource",),
     *      ),
     *  ),
     *  @OA\Response(response=403,description="User Is Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/Doc403Resource"),
     *  ),
     *  @OA\Response(response=422,description="Data is not valid",
     *     @OA\JsonContent(ref="#/components/schemas/Doc422Resource"),
     *  ),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",
     *     @OA\JsonContent(ref="#/components/schemas/Doc500Resource"),
     *  ),
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to get the data on the OTP page as the user will have encrypted key on the OTP
     * page and from there this api will convert the encrypted key into user email and event uuid
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse
     * @example Encrypted key == 2$alksdjfseioj2kjskljj3lkjldsdfjkklj
     * will be converted into
     * email = example@exampe.com
     * event_uuid = 123e4567-e89b-12d3-a456-426614174000
     */
    public function getOtpPageData(Request $request): JsonResponse {
        try {
            $key = $request->key;
            $email = $request->user('api') ? $request->user('api')->email : null;
            $event = $this->repo->eventRepository->findByEventUuid($request->input('key'));
            if (!$event) {
                $data = $this->decryptData($key);
                $param = explode(',', $data);
                $email = $param[0]; // extracting email from param
                $eventUuid = $param[1]; // extracting event uuid from param
                $event = $this->services->adminService->findEvent($eventUuid);
            }
            $email = $this->services->userManagementService->findByEmail($email);
            if (!$email) {
                throw new CustomValidationException('invalid_email', '', 'message');
            }
            if (!$event) {
                throw new CustomValidationException('invalid_event', '', 'message');
            }
            return response()->json([
                'status'     => true,
                'email'      => $email->email,
                'event_data' => new VirtualEventResource($event)
            ], 200);
        } catch (Exception $exception) {
            return $this->handleIse($exception);
        }

    }

    /**
     * @OA\Post(
     *  path="/api/v1/logs",
     *  operationId="createLogs",
     *  tags={"User"},
     *  summary="To create logs of page visited and device update",
     *  description="To create logs of page visited and device update",
     *  @OA\Parameter(name="log_type",in="query",description="Log type of the logging 1. page visited, 2. device update",required=true),
     *  @OA\Parameter(name="event_uuid",in="query",description="Event uuid for the event",required=false),
     *  @OA\Parameter(name="conversation_uuid",in="query",description="conversation uuid for the event",required=false),
     *     @OA\Response(
     *      response=200,
     *      description="Log created",
     *      @OA\JsonContent(
     *          @OA\Property(property="status",type="boolean",description="To indicate server processed request properly",example="true"),
     *          @OA\Property(property="data",type="boolean",description="To indicate server processed request properly",example="true"),
     *      ),
     *  ),
     *  @OA\Response(response=422,description="Data is not valid",@OA\JsonContent(ref="#/components/schemas/Doc422Resource"),),
     *  @OA\Response(response=500,description="Some Internal Server Issue Occuerred",@OA\JsonContent(ref="#/components/schemas/Doc500Resource"),)
     * ),
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton This method is used for creating the log.This method handle the log type for page visited and
     * device update
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createLogs(Request $request): JsonResponse {
        try {
            DB::connection('tenant')->beginTransaction();
            $validator = Validator::make($request->all(), [
                'log_type'          => 'required|integer',
                'event_uuid'        => ['nullable', 'string', new EventRule],
                'conversation_uuid' => 'nullable|string|exists:tenant.kct_conversations,uuid"'

            ]);
            if ($validator->fails()) {
                return $this->send422(
                    implode(',', $validator->errors()->all()), $validator->errors()
                );
            }
            $this->repo->userRepository->storeLogs($request);
            DB::connection('tenant')->commit();
            return response()->json(['status' => true, 'data' => true], 200);

        } catch (Exception $exception) {
            return $this->handleIse($exception);
        }


    }
}
