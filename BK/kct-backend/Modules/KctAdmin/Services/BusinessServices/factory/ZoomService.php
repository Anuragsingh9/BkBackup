<?php

namespace Modules\KctAdmin\Services\BusinessServices\factory;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Arr;
use Modules\KctAdmin\Entities\GroupSetting;
use Modules\KctAdmin\Entities\Moment;
use Modules\KctAdmin\Exceptions\ZoomAccountExpiredException;
use Modules\KctAdmin\Exceptions\ZoomGrantException;
use Modules\KctAdmin\Services\BusinessServices\IZoomService;
use Modules\KctAdmin\Traits\KctHelper;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\SuperAdmin\Entities\Setting;

class ZoomService implements IZoomService {
    use ServicesAndRepo;
    use KctHelper;

    private int $environment = 1; // 1 for client Zoom account, 2 for default Zoom account

    public static int $zoomRole_basic = 1;
    public static int $zoomRole_licensed = 2;
    public static string $zoomUserStatus_pending = 'pending';
    public static string $userStatusAction_activate = 'activate';


    protected array $url = [
        'refresh_token'        => 'https://zoom.us/oauth/token',
//        'refresh_token'  => 'http://kct.local/api/test',
        'token_generate'       => 'https://zoom.us/oauth/token',
//        'token_generate' => 'http://kct.local/api/test',
        'self_user'            => 'https://zoom.us/v2/users/me',
        'list_users'           => 'https://zoom.us/v2/users',
        'user_settings'        => 'https://zoom.us/v2/users/:userId/settings?custom_query_fields=feature',
        'user_settings_update' => 'https://zoom.us/v2/users/:userId/settings',
        'user_update'          => 'https://zoom.us/v2/users/:userId',
        'user_create'          => 'https://zoom.us/v2/users',
        'user_status_update'   => 'https://zoom.us/v2/users/:userId/status',
//        'webinar_create'  => 'http://kct.local/api/test?action=webinar_create',
        'webinar_create'       => 'https://zoom.us/v2/users/:userId/webinars',
        'meeting_create'       => 'https://zoom.us/v2/users/:userId/meetings',
        'get_meeting'          => 'https://api.zoom.us/v2/meetings/:meetingId',
    ];

    public function setEnvironment($e) {
        $this->environment = $e;
    }

    public function setEnvironmentByKey($key) {
        if ($key == 'custom_zoom_settings') {
            $this->environment = 1;
        } else {
            $this->environment = 2;
        }
    }

    public function executePost($url, $postData = [], $headers = [], $isJson = false) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_POST           => 1,
            CURLOPT_POSTFIELDS     => $isJson ? $postData : http_build_query($postData),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $headers,
        ]);
        $output = curl_exec($ch);
        return json_decode($output, 1);
    }

    public function executePostWithRawJson($url, $postData = [], $headers = []) {
        return $this->executePost($url, json_encode($postData), $headers, true);
    }

    public function executeGet($url, $queryParam = [], $header = [], $assoc = true) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => count($queryParam) ? "$url?" . http_build_query($queryParam) : $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $header
        ]);
        $output = curl_exec($ch);
        return json_decode($output, (int)$assoc);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the access token for the zoom by the current environment(zoom setting key) set now
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param null $token
     * @return mixed|null
     * @throws ZoomGrantException|Exception
     */
    public function getAccessToken($token = null) {
        if ($token) {
            return $token;
        }
        $zoomSettings = $this->adminRepo()->settingRepository->getSettingsByKey(1, $this->getZoomKeys());
        foreach ($zoomSettings as $setting) {
            if ($setting->setting_value['enabled'] && $setting->setting_value['is_assigned']) {
                $this->setEnvironmentByKey($setting->setting_key);
            }
        }
        if ($this->environment == 1) {
            // fetching token for client account
            $setting = $this->adminRepo()->settingRepository->getSettingByKey('custom_zoom_settings')->setting_value ?? null;
            $validTill = Carbon::createFromTimestamp($setting['token_data']['valid_till']);
            if ($validTill->timestamp < Carbon::now()->timestamp) {
                return $this->refreshToken($setting, $setting['token_data']['refresh_token']);
            }
            return $setting['token_data']['access_token'] ?? null;
        } else {
            $setting = $this->adminRepo()->settingRepository->getSettingByKey('default_zoom_settings') ?? null;
            if (!$setting || !isset($setting->setting_value['account_id'])) return null;
            $accId = $setting->setting_value['account_id'];
            $setting = $this->adminServices()->superAdminService->firstOrCreateSetting('zoom_access_tokens');
            if (!$setting || !isset($setting->setting_value[$accId])) return null;
            $validTill = Carbon::createFromTimestamp($setting->setting_value[$accId]['valid_till']);
            if ($validTill->timestamp < Carbon::now()->timestamp) {
                return $this->refreshToken($setting, $setting->setting_value[$accId]['refresh_token']);
            }

            return $setting->setting_value[$accId]['access_token'] ?? null;
        }
//        if (Carbon::now()->timestamp > $tokenSetting['valid_till']) {
//            // token expired refresh the token
//            return $this->refreshToken()['access_token'] ?? null;
//        }
//        return $tokenSetting['access_token'] ?? null;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To refresh the token by re-fetching access token using refresh token and store it in db
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $setting
     * @param $refreshToken
     * @return mixed
     * @throws ZoomGrantException|Exception
     */
    public function refreshToken($setting, $refreshToken) {
        $type = ($this->environment == 1 ? 'custom_zoom_settings' : 'default_zoom_settings');
        $token = $this->getTokenFromCode($refreshToken,
            $type,
            'default',
            'refresh_token'
        );
//        $token = json_decode('{"access_token":"eyJhbGciOiJIUzUxMiIsInYiOiIyLjAiLCJraWQiOiJkNzg5NTI2Ny1lOTgwLTRjMDctYTRmYS1iMzc3YTFjNTJmMjYifQ.eyJ2ZXIiOjcsImF1aWQiOiI5MGIxMTU4ZDlhMDc1NDU5MmRlMjMwYWE4NmVlYmY5NiIsImNvZGUiOiJjOGE3N1NpcnhYX0tpUEQ1YnV1Uk1HR2dtM0NLNW5malEiLCJpc3MiOiJ6bTpjaWQ6aEkwc2NZSjhTVTZ2R0xPSGtkRkNhZyIsImdubyI6MCwidHlwZSI6MCwidGlkIjoxLCJhdWQiOiJodHRwczovL29hdXRoLnpvb20udXMiLCJ1aWQiOiJLaVBENWJ1dVJNR0dnbTNDSzVuZmpRIiwibmJmIjoxNjM5MTExMjYwLCJleHAiOjE2MzkxMTQ4NjAsImlhdCI6MTYzOTExMTI2MCwiYWlkIjoibTJTV2FXeUdSMkN0VF9CZ25CYUZPQSIsImp0aSI6IjU2MzVhMmU0LTYyMDEtNDBjNC1iM2EyLWI0MDMwMmY4N2NmMiJ9.xZktpZToM4aO50JKqw5Krhohh0VpCI168MOnsLhEWczSLFMd1MEvsHYqY-zxcLZYq2ZGnQ01t7_ofq9KAPIV2A","token_type":"bearer","refresh_token":"eyJhbGciOiJIUzUxMiIsInYiOiIyLjAiLCJraWQiOiJlZTMzNmIxOC0wZWFhLTQyNWYtYTVkYy04NjA0M2EzZmFmNDgifQ.eyJ2ZXIiOjcsImF1aWQiOiI5MGIxMTU4ZDlhMDc1NDU5MmRlMjMwYWE4NmVlYmY5NiIsImNvZGUiOiJjOGE3N1NpcnhYX0tpUEQ1YnV1Uk1HR2dtM0NLNW5malEiLCJpc3MiOiJ6bTpjaWQ6aEkwc2NZSjhTVTZ2R0xPSGtkRkNhZyIsImdubyI6MCwidHlwZSI6MSwidGlkIjoxLCJhdWQiOiJodHRwczovL29hdXRoLnpvb20udXMiLCJ1aWQiOiJLaVBENWJ1dVJNR0dnbTNDSzVuZmpRIiwibmJmIjoxNjM5MTExMjYwLCJleHAiOjIxMTIxNTEyNjAsImlhdCI6MTYzOTExMTI2MCwiYWlkIjoibTJTV2FXeUdSMkN0VF9CZ25CYUZPQSIsImp0aSI6Ijk3MmE0YTViLWE5MWMtNGFkOS1iMmZjLWY4OTk5ZmNiYmMxZCJ9.iIWrXW4yuMXGtX5ew5j7TIWwXXSW5sa3csyVTQ1XJZ72yhbsSzDMIAV4jppRV_J9vuPpunaJG10cR9trdFIMEw","expires_in":3599,"scope":"account:read:admin meeting:read:admin meeting:write:admin user:read:admin user:write:admin webinar:read:admin webinar:write:admin"}', 1);
        $zoomUser = $this->getUserByToken($token['access_token']);
        if (!isset($zoomUser['account_number'])) {
            throw new Exception('Invalid Code');
        }

        // storing token to respective key of db
        $setting = $this->storeToken($token, $type, $zoomUser['account_number']);

        // plan details for hosts count
        $plan = $this->getPlan($zoomUser['account_number']);

        // adding webinar data
        $hosts = $this->getWebinarHosts();

        // syncing users of zoom with current system
        $users = $this->syncUser($hosts);
        $webinarData = [
            'available_license' => $plan['plan_webinar'][0]['hosts'] ?? 0,
            'hosts'             => $users,
        ];

        // adding meeting data
        $hosts = $this->getMeetingHosts();
        $users = $this->syncUser($hosts);
        $meetingData = [
            // as meeting host = total hosts - webinar hosts
            'available_license' => ($plan['plan_base']['hosts'] ?? 0) - ($plan['plan_webinar'][0]['hosts'] ?? 0),
            'hosts'             => $users,
        ];
        $previous = $setting->setting_value;
        $previous['webinar_data'] = $webinarData;
        $previous['meeting_data'] = $meetingData;
        $previous['enabled'] = 1;
        $previous['is_assigned'] = 1;
        $setting->setting_value = $previous;
        $setting->update();

        // if current enable disable other
        // only one at a time can be enabled;
        $this->toggleSettings($setting->setting_key);
        return $token['access_token'];
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to get the access token using the code provided by zoom on api
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $code
     * @param $type
     * @param string $groupKey
     * @param string $grantType
     * @return mixed
     * @throws ZoomGrantException
     */
    public function getTokenFromCode($code, $type, $groupKey = 'default', $grantType = 'authorization_code') {
        $url = $this->url['token_generate'];
        $basic = base64_encode(env("ZM_DEFAULT_CLIENT_ID") . ":" . env("ZM_DEFAULT_CLIENT_SECRET"));
        if ($grantType == 'authorization_code') {
            $reqParam = [
                'redirect_uri' => route('zoomHandler', ['type' => $type, 'groupKey' => $groupKey]),
//            'redirect_uri' => 'https://humann.seque.in/test',
                'grant_type'   => 'authorization_code',
                'code'         => $code,
            ];
        } else {
            $reqParam = [
                'redirect_uri'  => route('zoomHandler', ['type' => $type, 'groupKey' => $groupKey]),
//            'redirect_uri' => 'https://humann.seque.in/test',
                'grant_type'    => $grantType,
                'refresh_token' => $code,
            ];
        }
        $output = $this->executePost($url, $reqParam, [
            "Authorization: Basic $basic",
            'Content-Type: application/x-www-form-urlencoded',
        ]);
//        dd(json_encode($output));
//        $output = '{"access_token":"eyJhbGciOiJIUzUxMiIsInYiOiIyLjAiLCJraWQiOiI5YjcxNjc1NC01NDJmLTQ1NzItODJhMS02OWI3ZjIzYjBjM2YifQ.eyJ2ZXIiOjcsImF1aWQiOiI5MGIxMTU4ZDlhMDc1NDU5MmRlMjMwYWE4NmVlYmY5NiIsImNvZGUiOiJPUXRMRjJNNWxGX0tpUEQ1YnV1Uk1HR2dtM0NLNW5malEiLCJpc3MiOiJ6bTpjaWQ6aEkwc2NZSjhTVTZ2R0xPSGtkRkNhZyIsImdubyI6MCwidHlwZSI6MCwidGlkIjowLCJhdWQiOiJodHRwczovL29hdXRoLnpvb20udXMiLCJ1aWQiOiJLaVBENWJ1dVJNR0dnbTNDSzVuZmpRIiwibmJmIjoxNjM3MDI1ODY5LCJleHAiOjE2MzcwMjk0NjksImlhdCI6MTYzNzAyNTg2OSwiYWlkIjoibTJTV2FXeUdSMkN0VF9CZ25CYUZPQSIsImp0aSI6Ijc0ZGVlZTVhLWU4NWYtNDlkZS05ZmNiLWI4ZjczZjQxOGE4NiJ9.D_F7CXFzeMHJdGRnA_p1tuKHdd9A4lOSEeZhUchXKOgTcH2TQ7TK5Yk3YU-wpKOUX20yZ-uj3bCSBVLBGfi2Eg","token_type":"bearer","refresh_token":"eyJhbGciOiJIUzUxMiIsInYiOiIyLjAiLCJraWQiOiI1MWViMDQ1ZS0wMmZkLTQ0MzEtYTAwYS0yMTlhMTllODdkNDQifQ.eyJ2ZXIiOjcsImF1aWQiOiI5MGIxMTU4ZDlhMDc1NDU5MmRlMjMwYWE4NmVlYmY5NiIsImNvZGUiOiJPUXRMRjJNNWxGX0tpUEQ1YnV1Uk1HR2dtM0NLNW5malEiLCJpc3MiOiJ6bTpjaWQ6aEkwc2NZSjhTVTZ2R0xPSGtkRkNhZyIsImdubyI6MCwidHlwZSI6MSwidGlkIjowLCJhdWQiOiJodHRwczovL29hdXRoLnpvb20udXMiLCJ1aWQiOiJLaVBENWJ1dVJNR0dnbTNDSzVuZmpRIiwibmJmIjoxNjM3MDI1ODY5LCJleHAiOjIxMTAwNjU4NjksImlhdCI6MTYzNzAyNTg2OSwiYWlkIjoibTJTV2FXeUdSMkN0VF9CZ25CYUZPQSIsImp0aSI6ImJmY2JhYmY1LTRkN2UtNDEwMS04ZDVmLTBmNzBhNWRiYzZjMyJ9.9KnW5tF28-WxgdAHuWBHTUriYWrmNPOq0DynKRZ-Z1vXEBe_YK6Zq654ZcKjlJu5XV4bmiWgAN6fJYsWsWp7jQ","expires_in":3599,"scope":"account:read:admin meeting:read:admin meeting:write:admin user:read:admin user:write:admin webinar:read:admin webinar:write:admin"}';
//        $output = json_decode($output, 1);
        if (isset($output['access_token'])) {
            return $output;
        } else if (($output['error'] ?? null) == 'invalid_grant') {
            throw new ZoomAccountExpiredException();
        }
        throw new Exception('Invalid code for zoom');
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To store the access token
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $token
     * @param string $type
     * @param string $zoomAccId
     * @return GroupSetting|Setting
     */
    public function storeToken($token, $type, $zoomAccId) {
        $token['valid_till'] = Carbon::now()->addSeconds($token['expires_in'])->timestamp;
        if ($type == 'default_zoom_settings') {
            $setting = $this->adminServices()->superAdminService->firstOrCreateSetting('zoom_access_tokens');
            $previous = $setting->setting_value;
            $previous[$zoomAccId] = $token;
            $setting->setting_value = $previous;
            $setting->update();
            $setting = $this->adminRepo()->settingRepository->getSettingByKey($type);
            $previous = $setting->setting_value;
            $previous['account_id'] = $zoomAccId;
            $previous['enabled'] = 1;
            $otherKey = 'custom_zoom_settings';
        } else {
            $setting = $this->adminRepo()->settingRepository->getSettingByKey($type);
            $previous = $setting->setting_value;
            $previous['token_data'] = $token;
            $previous['enabled'] = 1;
            $otherKey = 'default_zoom_settings';
        }

        $otherSetting = $this->adminRepo()->settingRepository->getSettingByKey($otherKey);
        $otherPrevious = $otherSetting->setting_value;
        $otherPrevious['enabled'] = 0;
        $otherSetting->setting_value = $otherPrevious;
        $otherSetting->update();

        $setting->setting_value = $previous;
        $setting->update();
        return $setting;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the user by token
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return mixed
     */
    public function getUserByToken(string $token) {
        $url = $this->url['self_user'];
        return $this->executeGet($url, [], [
            "Authorization: Bearer $token",
            'Content-Type: application/x-www-form-urlencoded',
        ]);
    }

    public function getUserSettings(?string $zoomUserId = 'me') {
        $token = $this->getAccessToken();
        $url = str_replace(':userId', $zoomUserId, $this->url['user_settings']);
        return $this->executeGet($url, [], [
            "Authorization: Bearer $token",
        ]);
    }

    public function getAllUsers($status = 'active') {
        $url = $this->url['list_users'];
        $token = $this->getAccessToken();
        return $this->executeGet($url, ['status' => $status], [
            "Authorization: Bearer $token",
            'Content-Type: application/x-www-form-urlencoded',
        ]);
    }

    public function fetchHost($technicalSetting, $settingKey) {
        // hosts from setting value, this will contain id and max participant
        $hosts = $technicalSetting->setting_value[$settingKey]['hosts'];

        // fetching ids
        $hostsId = Arr::pluck($hosts, 'id');

        // fetching users from db
        $users = $this->adminServices()->userService->getUsersById($hostsId);

        // mapping fetched users to add the max participant data to users data
        $users->map(function ($user) use ($hosts) {
            $search = Arr::where($hosts, function ($host) use ($user) {
                return $host['id'] == $user->id;
            });
            $search = array_values($search);
            $user->max_participant = $search[0]['max_participant'] ?? 0;
        });

        // attaching users to hosts key
        $previous = $technicalSetting->setting_value;
        $previous[$settingKey]['hosts'] = $users;
        $technicalSetting->setting_value = $previous;
        return $technicalSetting;
    }

    public function syncUser($hosts): array {
        $emails = Arr::pluck($hosts, 'email');
        $users = $this->adminServices()->userService->getUsersByEmail($emails, true);
        $result = [];
        foreach ($hosts as &$host) {
            if ($users->where('email', $host['email'])->count()) {
                $u = $users->where('email', $host['email'])->first();
                if($u->trashed()) {
                    $u->restore();
                }
            } else {
                $u = $this->adminServices()->userService->createUser([
                    'fname'             => $host['first_name'],
                    'lname'             => $host['last_name'],
                    'email'             => $host['email'],
                    'email_verified_at' => Carbon::now()->toDateTimeString(),
                ]);
            }
            $result[] = [
                'id'              => $u->id,
                'max_participant' => $host['settings']['max_participant'] ?? 0,
            ];
        }
        return $result;
    }

    public function getPlan($accountId = null) {
        $token = $this->getAccessToken();
        if (!$token || !$accountId) {
            return null;
        }
        $url = 'https://zoom.us/v2/accounts/me/plans/usage';
        return $this->executeGet($url, [], [
            "Authorization: Bearer $token",
            'Content-Type: application/x-www-form-urlencoded',
        ]);
    }

    public function getWebinarHosts(): array {
        $allUsers = $this->getAllUsers();
        $licensedUsers = [];
        foreach ($allUsers['users'] as $zoomUser) {
            if ($zoomUser['type'] == self::$zoomRole_licensed) {
                $zoomUserSettings = $this->getUserSettings($zoomUser['id']);
                $d[] = $zoomUserSettings;
                if ($zoomUserSettings['feature']['webinar']) {
                    $zoomUser['settings'] = $zoomUserSettings;
                    $zoomUser['settings']['max_participant'] = $zoomUserSettings['feature']['webinar_capacity'] ?? 0;
                    $licensedUsers[] = $zoomUser;
                }
            }
        }
        return $licensedUsers;
    }

    public function getMeetingHosts(): array {
        $allUsers = $this->getAllUsers();
        $licensedUsers = [];
        foreach ($allUsers['users'] as $zoomUser) {
            if ($zoomUser['type'] == self::$zoomRole_licensed) {
                // as user licensed user is meeting licensed
                $zoomUserSettings = $this->getUserSettings($zoomUser['id']);
                $d[] = $zoomUserSettings;
                if (isset($zoomUserSettings['feature']['webinar']) && $zoomUserSettings['feature']['webinar']) {
                    // user is webinar licensed also so skip that user from here
                    continue;
                }
                $zoomUser['settings'] = $zoomUserSettings;
                $zoomUser['settings']['max_participant'] = $zoomUserSettings['feature']['meeting_capacity'] ?? 0;
                $licensedUsers[] = $zoomUser;
            }
        }
        return $licensedUsers;
    }

    public function getOAuthLoginUrl(?string $type = 'custom_zoom_settings'): string {
        return "https://zoom.us/oauth/authorize?response_type=code&client_id="
            . env("ZM_DEFAULT_CLIENT_ID")
            . "&redirect_uri="
            . route('zoomHandler', ['groupKey' => 'default']) . "?type=$type";
    }

    public function toggleSettings(?string $currentKey) {
        // disabling other settings
        $setting = $this->adminRepo()->settingRepository->getSettingByKey(
            $currentKey == 'default_zoom_settings'
                ? 'custom_zoom_settings'
                : 'default_zoom_settings');
        $p = $setting->setting_value;
        $p['enabled'] = 0;
        $setting->setting_value = $p;
        $setting->update();
    }

    public function getUserByEmail($email, $allUsers = null) {
        $allUsers = $allUsers ?: $this->getAllUsers();
        if (isset($allUsers['users'])) {
            foreach ($allUsers['users'] as $zoomUser) {
                if ($zoomUser['email'] == $email) {
                    return $zoomUser;
                }
            }
        }
        return null;
    }

    public function createWebinar($param): array {
        $url = $this->url['webinar_create'];
        $start = $this->getCarbonByDateTime($param['start_time']);
        $end = $this->getCarbonByDateTime($param['end_time']);
        //as moderator coming  in event user object so fatching the userId
        $user = $this->adminServices()->userService->findById($param['moderator']->user_id);
        $zoomUser = $this->getUserByEmail($user->email);
        if (!isset($zoomUser['id'])) {
            throw new Exception('Zoom Connection Expired');
        }
        $url = str_replace(':userId', $zoomUser['id'], $url);
        $token = $this->getAccessToken();
        $webinar = $this->executePostWithRawJson($url,
            [
                "topic"      => $param['moment_name'],
                "type"       => 5,
                "start_time" => $start->toAtomString(),
                "duration"   => $start->diffInMinutes($end),
                "timezone"   => Carbon::now()->timezone->getName(),
                "recurrence" => [
                    "type"            => 1,
                    "repeat_interval" => 1,
                    "end_date_time"   => '',
                ],
                "agenda"     => $param['moment_description'],
                "settings"   => [
                    "host_video"                     => true,
                    "panelists_video"                => true,
                    "practice_session"               => false,
                    "hd_video"                       => true,
                    "hd_video_for_attendees"         => true,
                    "send_1080p_video_to_attendees"  => true,
                    "registrants_confirmation_email" => false,
                    "registrants_email_notification" => false,
                ]
            ], [
                "Authorization: Bearer $token",
                'Content-Type: application/json',
            ]);
        if (!isset($webinar['uuid'])) {
            throw new Exception('Error in zoom webinar create');
        }
        return [
            'moment_id'     => $webinar['id'],
            'host_email'    => $webinar['host_email'],
            'host_id'       => $webinar['host_id'],
            'moderator_url' => $webinar['start_url'],
            'join_url'      => $webinar['join_url'],
        ];
    }

    private int $meetingType_scheduledMeeting = 2;
    private int $meetingType_recurringFixedTime = 8;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to get the Zoom meeting type by the event modal
     * it will check if event is recurring type then the Zoom meeting type and end time will be sent
     * else it will send the type for non-recurring only
     *
     * @note currently this method is only supported for daily meeting recurring only
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return array|int[]
     */
    private function getMeetingTypeByEvent($event): array {
        if ($event->eventRecurrenceData) {
            $endDate = $this->getEventDateTime($event, 'recurring_end_date');
            $endTime = $this->getEventDateTime($event, 'end_time');
            $endDateTime = $this->getCarbonByDateTime($endDate, $endTime);
            return [
                'reoccurEndTime'  => $endDateTime->toIso8601ZuluString(),
                'zoomMeetingType' => $this->meetingType_recurringFixedTime,
            ];
        }
        return [
            'zoomMeetingType' => $this->meetingType_scheduledMeeting,
        ];
    }

    public function createMeeting($param): array {
        $url = $this->url['meeting_create'];
        $start = $this->getCarbonByDateTime($param['start_time']);
        $end = $this->getCarbonByDateTime($param['end_time']);
        $user = $this->adminServices()->userService->findById($param['moderator']->user_id);
        $zoomUser = $this->getUserByEmail($user->email);
        if (!$zoomUser['id']) {
            throw new ZoomAccountExpiredException();
        }
        $url = str_replace(':userId', $zoomUser['id'], $url);
        $token = $this->getAccessToken();
        $type = $this->getMeetingTypeByEvent($param->event);
        $data = [
            "topic"      => $param['moment_name'],
            "type"       => $type['zoomMeetingType'],
            "start_time" => $start->toAtomString(),
            "duration"   => $start->diffInMinutes($end),
            "timezone"   => Carbon::now()->timezone->getName(),
            "agenda"     => $param['moment_description'],
            "recurrence" => [
                "type"            => 1,
                "repeat_interval" => 1,
                "end_date_time"   => $type['reoccurEndTime'] ?? '',
            ],
            "settings"   => [
                "host_video"        => true,
                "participant_video" => true,
                "join_before_host"  => true,
            ]
        ];

        $meeting = $this->executePostWithRawJson($url, $data, [
            "Authorization: Bearer $token",
            'Content-Type: application/json',
        ]);
        if (!isset($meeting['id'])) {
            throw new Exception('Error in zoom webinar create');
        }
        return [
            'moment_id'        => $meeting['id'],
            'host_email'       => $meeting['host_email'] ?? null,
            'host_id'          => $meeting['host_id'] ?? null,
            'moderator_url'    => $meeting['start_url'] ?? null,
            'join_url'         => $meeting['join_url'],
            'start_url_expire' => Carbon::now()->addHours(2)->timestamp,
        ];
    }

    public function getEmbeddedUrl(?Moment $moment): string {
        return $moment->is_live ? $this->getSignature($moment->moment_id) : '';
    }

    /**
     * @deprecated
     *
     * -----------------------------------------------------------------------------------------------------------------
     * @description To prepare the zoom sdk signature with JWT Token for 2.6 or below SDK versions
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $meeting_number
     * @return string
     */
    function getSignatureOld($meeting_number): string {
        $api_key = env('ZM_DEFAULT_APP_KEY');
        $api_secret = env('ZM_DEFAULT_APP_SECRET');
        $role = 0;
        $time = time() * 1000 - 30000;//time in milliseconds (or close enough)
        $data = base64_encode($api_key . $meeting_number . $time . $role);
        $hash = hash_hmac('sha256', $data, $api_secret, true);
        $_sig = $api_key . "." . $meeting_number . "." . $time . "." . $role . "." . base64_encode($hash);
        //return signature, url safe base64 encoded
        return rtrim(strtr(base64_encode($_sig), '+/', '-_'), '=');
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To encode the string into base 64 and if required it will remove the appending character also
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $target
     * @param Boolean $rtrim
     * @return string
     */
    private function base64Encode($target, bool $rtrim = false): string {
        $encoded = base64_encode($target);
        return $rtrim ? rtrim(strtr($encoded, '+/', '-_'), '=') : $encoded;
    }

    function getSignature($meeting_number): string {
        $api_key = env('ZM_DEFAULT_APP_KEY');
        $api_secret = env('ZM_DEFAULT_APP_SECRET');
        $time = time() - 30;//time in seconds (or close enough)
        $exp = $time + (60 * 60 * 2);
        $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
        $oPayload = json_encode([
            "sdkKey"   => $api_key,
            "mn"       => $meeting_number,
            "role"     => 0,
            "iat"      => $time,
            "exp"      => $exp,
            "appKey"   => $api_key,
            "tokenExp" => $exp,
        ]);
        $headerBase64Encoded = $this->base64Encode($header);
        $payloadBase64Encoded = $this->base64Encode($oPayload, true);
        $data = hash_hmac('sha256', "$headerBase64Encoded.$payloadBase64Encoded", $api_secret, true);
        $signature = $this->base64Encode($data, true);
        return "$headerBase64Encoded.$payloadBase64Encoded.$signature";
    }

    /**
     * @param string|null $meetingId
     * @return array
     * @throws ZoomGrantException|Exception
     */
    public function getMeeting(?string $meetingId): array {
        $url = $this->url['get_meeting'];
        $url = str_replace(':meetingId', $meetingId, $url);
        $token = $this->getAccessToken();
        return $this->executeGet($url, [], [
            "Authorization: Bearer $token",
            'Content-Type: application/json',
        ]);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the zoom settings
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @return mixed|null
     */
    public function getZoomSettings() {
        $zoomSettings = $this->adminRepo()->settingRepository->getSettingsByKey(1, $this->getZoomKeys());
        $enabledSetting = null;
        foreach ($zoomSettings as $setting) {
            // checking in each setting if its enabled or not
            if ($setting->setting_value['enabled'] && $setting->setting_value['is_assigned']) {
                $this->adminServices()->zoomService->setEnvironmentByKey($setting->setting_key);
                $enabledSetting = $setting;
                break;
            }
        }
        return $enabledSetting;
    }
}
