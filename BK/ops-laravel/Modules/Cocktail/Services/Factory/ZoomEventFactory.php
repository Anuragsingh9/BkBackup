<?php


namespace Modules\Cocktail\Services\Factory;


use App\Setting;
use App\User;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Modules\Cocktail\Exceptions\CustomValidationException;
use Modules\Cocktail\Services\Contracts\ApiExecuteFactory;
use Modules\Cocktail\Services\Contracts\ExternalEventFactory;
use Modules\Events\Exceptions\CustomException;

class ZoomEventFactory implements ExternalEventFactory {
    /**
     * @var ApiExecuteFactory Api
     */
    private $execute;
    
    /**
     * @var string
     */
    private $baseUrl;
    
    /**
     * @var string To store the access token generated for hitting api
     */
    private $accessToken;
    /**
     * @var string
     */
    private $userEmail;
    /**
     * @var string // to hold the zoom app key
     */
    private $zoomKey;
    /**
     * @var string // to hold the zoom client secret;
     */
    private $zoomSecret;
    
    
    public function __construct() {
        $this->baseUrl = config('kct_const.zoom.base_url');
        $this->setCredentials();
    }
    
    /**
     * To create the zoom event
     *
     * @param array $parameter
     * @return mixed
     * @throws CustomValidationException
     */
    public function create($parameter) {
        $z_user = $this->createORReadZoomUser();
        
        if ($z_user->status == 'pending') {
            return array('status' => 'pending', 'message' => 'Please accept already sent Zoom Invitation');
        }
        
        $this->generateAccessToken(); // get and set access token and user id
        $data = $this->addHeader($parameter); // add authorization in header
        
        $url = $this->getUrl('create_webinar', ['userId' => $this->userEmail]); // prepare the url to hit
        $alternative_hosts_verification_result = $this->verifyAlternativeHosts($parameter);
        
        if ($alternative_hosts_verification_result) {
            return $alternative_hosts_verification_result;
        }
        
        $res = $this->getExecute()->executePOST($url, $data, ApiExecuteFactory::RAW_JSON);
        $res = (object)$this->validateResponse($res, ['id']);
        
        if (property_exists($res, 'message')) {
            return array('status' => 'error', 'message' => $res->message);
        }
        
        if (property_exists((object)$parameter, 'panelists')) {
            $this->addMember($res->id, $parameter);
        }
        
        return array(
            "id"                   => $res->id,
            "webinarId"            => $res->id,
            "linkToJoinAttendees"  => $res->join_url,
            "linkToJoinPresentors" => $res->join_url,
            "linkToJoinModerators" => $res->join_url
        );
    }
    
    /**
     * Convert the request parameter to desired output for zoom event create
     *
     * @param Request $request
     * @return array
     */
    public function prepareCreateParamFromRequest($request) {
        $startCarbon = Carbon::make("{$request->input('date')} {$request->input('start_time')}");
        $endCarbon = Carbon::make("{$request->input('date')} {$request->input('end_time')}");
        
        $settings = [
            'host_video'                     => $request->input('video_hosts_activated') ? 'true' : 'false',
            'panelists_video'                => $request->input('video_panelist_activated') ? 'true' : 'false',
            'practice_session'               => $request->input('enable_practise_session') ? 'true' : 'false',
            'auto_recording'                 => $request->input('auto_recording') ? 'true' : 'false',
            'registrants_email_notification' => 'false',
        ];
        
        if (property_exists((object)$settings, 'alternative_hosts')) {
            $settings["alternative_hosts"] = join(',', $settings['alternative_hosts']);
        }
        
        return [
            "topic"      => $request->input('title'),
            "type"       => 5,
            "start_time" => $startCarbon->format(DATE_ATOM),
            "duration"   => $endCarbon->diffInMinutes($startCarbon),
            "timezone"   => Carbon::now()->timezone->getName(),
            "password"   => null,
            "agenda"     => $request->input('description'),
            "recurrence" => [
                "type"            => 1,
                "repeat_interval" => 1,
                "end_date_time"   => '',
            ],
            "settings"   => $settings,
            "panelists"  => $request->input('panelists', '')
        ];
    }
    
    /**
     * @param $eventId
     * @param array $parameter
     * @return mixed|void
     * @throws CustomValidationException
     */
    public function update($eventId, $parameter) {
        $this->generateAccessToken();
        $data = $this->addHeader($parameter);
        $url = $this->getUrl('get_update_delete_webinar', ['webinarId' => $eventId]);
        $this->getExecute()->executePATCH($url, $data, ApiExecuteFactory::RAW_JSON); // prepare the url to hit
    }
    
    /**
     * To prepare the data to send from the request variable
     *
     * @param Request $request
     * @return array
     */
    public function prepareUpdateParamFromRequest($request) {
        $startCarbon = Carbon::make("{$request->input('date')} {$request->input('start_time')}");
        $endCarbon = Carbon::make("{$request->input('date')} {$request->input('end_time')}");
        
        $settings = [
            'host_video'                     => $request->input('video_hosts_activated') ? 'true' : 'false',
            'panelists_video'                => $request->input('video_panelist_activated') ? 'true' : 'false',
            'practice_session'               => $request->input('enable_practise_session') ? 'true' : 'false',
            'auto_recording'                 => $request->input('auto_recording') ? 'true' : 'false',
            'registrants_email_notification' => 'false',
        ];
        
        
        return
            [
                "topic"      => $request->input('title'),
                "type"       => 5,
                "start_time" => $startCarbon->format(DATE_ATOM),
                "duration"   => $endCarbon->diffInMinutes($startCarbon),
                "timezone"   => Carbon::now()->timezone->getName(),
                "password"   => '',
                "agenda"     => $request->input('description'),
                "recurrence" => [
                    "type"            => 1,
                    "repeat_interval" => 1,
                    "end_date_time"   => ''
                ],
                "settings"   => $settings
            ];
    }
    
    /**
     * @param string $eventId
     * @return mixed|void
     * @throws CustomValidationException
     */
    public function delete($eventId) {
        $this->getAccessToken();
        $data = $this->addHeader([]);
        $url = $this->getUrl('get_update_delete_webinar', ['webinarId' => $eventId]); // prepare the url to hit
        $this->getExecute()->executeDELETE($url, $data, ApiExecuteFactory::FORM_DATA_PARAMETER);
    }
    
    /**
     * To add the zoom member which is actually adding email
     *
     * @param $eventId
     * @param $data
     * @return bool|void
     * @throws CustomValidationException
     * @throws CustomException
     */
    public function addMember($eventId, $data) {
        $this->generateAccessToken();
        if (isset($data['moderator']) && $data['moderator']) {
            $this->addModerator($data['email'], $eventId);
        }
        if (isset($data['presenter']) && isset($data['user']) && $data['presenter']) {
            $this->addPresenter($data['user'], $eventId);
        }
//
//        if (property_exists($data, 'registrant')) {
//            $parameter = $data->registrant;
//            $url = $this->getUrl('add_registrant', ['webinarId' => $eventId]); // prepare the url to hit
//        }
    
    }
    
    /**
     * @param $eventId
     * @param $data
     * @return mixed|void
     * @throws CustomException
     * @throws CustomValidationException
     */
    public function removeMember($eventId, $data) {
        $this->generateAccessToken();
        if (isset($data['moderator']) && $data['moderator']) {
            $this->removeModerator($data['email'], $eventId);
        }
        if (isset($data['presenter']) && isset($data['user']) && $data['presenter']) {
            $this->removePresenter($data['email'], $eventId);
        }
    }
    
    /**
     * @param $eventId
     * @param bool $validateResponse
     * @return array
     * @throws CustomValidationException
     */
    public function getEvent($eventId, $validateResponse = false) {
        $this->generateAccessToken();
        $url = $this->getUrl('get_update_delete_webinar', ['webinarId' => $eventId]);
        $data = $this->addHeader([]);
        $res = $this->getExecute()->executeGET($url, $data);
        if ($validateResponse) {
            return $this->validateResponse($res, ['id']);
        }
        return json_decode($res, JSON_OBJECT_AS_ARRAY);
    }
    
    public function getJoinLink($conferenceId, $type) {
        $event = $this->getEvent($conferenceId);
        // as zoom have same link for both presenter and moderator
        if ($event && in_array($type, ['presenter', 'moderator'])) {
            if (isset($event['join_url'])) {
                return $event['join_url'];
            }
        }
        return '';
    }
    
    /**
     * @param $conferenceId
     * @return string|array
     */
    public function prepareEmbeddedLnk($conferenceId) {
        $role = 0;
        $time = time() * 1000 - 30000;
        $data = base64_encode("{$this->zoomKey}{$conferenceId}$time$role");
        $hash = hash_hmac('sha256', $data, $this->zoomSecret, true);
        $_sig = "$this->zoomKey.$conferenceId.$time.$role." . base64_encode($hash);
        $signature = rtrim(strtr(base64_encode($_sig), '+/', '-_'), '=');
        $user = $this->createORReadZoomUser();
        return [
            'embedded_url' => $signature,
            'conf_user_name'    => $user && isset($user->first_name) ? "$user->first_name $user->last_name" : null,
            'conf_meeting_id'   => $conferenceId,
            'conf_user_email'   => $this->userEmail,
            'conf_api_key'      => $this->zoomKey,
        ];
    }
    
    private function verifyAlternativeHosts($parameter) {
        $result = array();
        $alternative_hosts = null;
        if (property_exists((object)$parameter, 'settings') && property_exists((object)$parameter['settings'], 'alternative_hosts')) {
            $alternative_hosts = $parameter['settings']['alternative_hosts'];
            
            if (strpos($alternative_hosts, ',')) {
                $alternative_hosts = explode(",", $alternative_hosts);
            } else {
                $alternative_hosts = [$alternative_hosts];
            }
        }
        
        if ($alternative_hosts) {
            foreach ($alternative_hosts as $alternative_host) {
                $z_user = $this->createORReadZoomUser($alternative_host);
                
                if (property_exists($z_user, 'status') && $z_user->status == 'pending') {
                    $result = array('status' => 'pending', 'message' => 'Alternative host ' . $alternative_host . ' is not exists');
                } else if (property_exists($z_user, 'email') && !property_exists($z_user, 'status')) {
                    $result = array('status' => 'pending', 'message' => 'Alternative host ' . $alternative_host . ' is not exists');
                }
            }
        }
        
        return $result;
    }
    
    private function createORReadZoomUser($userId = null) {
        $get_user_url = $this->getUrl('get_update_delete_user', ['userId' => $userId == null ? $this->userEmail : $userId]);
        $data = $this->addHeader([]);
        $z_user = json_decode($this->getExecute()->executeGET($get_user_url, $data));
        if (property_exists($z_user, 'code') && $z_user->code == 1001) {
            $create_user_url = $this->getUrl('create_user');
            $data = $this->addHeader($this->mapToZoomUser($userId));
            $z_user = json_decode($this->getExecute()->executePOST($create_user_url, $data, ApiExecuteFactory::RAW_JSON));
        }
        return $z_user;
    }
    
    private function mapToZoomUser($userId = null) {
        $mappedUserInfoToZoom = [];
        
        if ($userId) {
            $mappedUserInfoToZoom = [
                "email"      => $userId,
                "type"       => 1,
                "first_name" => 'Test',
                "last_name"  => 'LastName'
            ];
        } else {
            $mappedUserInfoToZoom = [
                "email"      => $this->userEmail,
                "type"       => 1,
                "first_name" => 'loggedInUser',
                "last_name"  => 'LastName'
            ];
        }
        
        return
            [
                "action"    => "create",
                "user_info" => $mappedUserInfoToZoom
            ];
    }
    
    /**
     * This method will validate the response by checking the keys passed are present or not after json decode
     * and return the json decoded value
     *
     * @param $res
     * @param $keys
     * @return array
     * @throws CustomValidationException
     */
    private function validateResponse($res, $keys) {
        $result = json_decode($res, JSON_OBJECT_AS_ARRAY);
        if (isset($result['status']) && $result['status'] == "401") {
            throw new CustomValidationException('invalid_conference_credentials', null, 'message');
        }
        foreach ($keys as $key) {
            if (!isset($result[$key])) {
                throw new CustomValidationException('some_issue_in_conference', null, 'message');
            }
        }
        return $result;
    }
    
    /**
     * @param $parameter
     * @return array
     * @throws CustomValidationException
     */
    private function addHeader($parameter) {
        return [
            'headers' => [
                'Accept:application/json',
                "Content-Type:application/json",
                "Authorization: Bearer {$this->getAccessToken()}",
            ],
            'data'    => $parameter,
        ];
    }
    
    /**
     * To get the access token required to hit any api
     *
     * @return mixed
     * @throws CustomValidationException
     */
    private function getAccessToken() {
        if (!$this->accessToken) {
            $this->generateAccessToken();
        }
        return $this->accessToken;
    }
    
    /**
     * @return ApiExecuteFactory
     */
    private function getExecute() {
        if (!$this->execute) {
            $this->execute = app(CurlExecuteFactory::class);
        }
        return $this->execute;
    }
    
    /**
     * This method will prepare the url from config and replace if anything needs to
     *
     * @param $key
     * @param null $replace
     * @return string
     */
    private function getUrl($key, $replace = null) {
        if ($replace) {
            return
                $this->baseUrl
                . str_replace(
                    array_keys($replace), array_values($replace), config("kct_const.zoom.$key")
                );
        }
        return $this->baseUrl . config("zoom.$key");
    }
    
    
    /**
     * This method will fetch the access token for specific user email and set them to class variable.
     * this will also return that value in case not needed through class variable.
     *
     * @return array
     * @throws CustomValidationException
     */
    private function generateAccessToken() {
        $payload = array(
            "iss" => $this->zoomKey,
            "exp" => time() + 36000,
        );
        $this->accessToken = JWT::encode($payload, $this->zoomSecret, "HS256");
    }
    
    /**
     * @param Request $request
     * @return array|mixed
     */
    public function prepareConferenceOptions($request) {
        return [
            'conference_type'     => config('kct_const.conference_type.zoom'),
            'conference_settings' => [
                'video_hosts_activated'    => checkValSet($request->input('video_hosts_activated', 0)),
                'video_panelist_activated' => checkValSet($request->input('video_panelist_activated', 0)),
                'q_a'                      => checkValSet($request->input('q_a', 0)),
                'enable_practise_session'  => checkValSet($request->input('enable_practise_session', 0)),
                'auto_recording'           => checkValSet($request->input('auto_recording', 0)),
            ],
        ];
    }
    
    private function getCredentials() {
        $result = [
            'client_key'    => null,
            'client_secret' => null,
            'client_email'  => null,
        ];
        $setting = Setting::where('setting_key', 'event_settings')->first();
        if ($setting && $setting->setting_value) {
            $decode = json_decode($setting->setting_value, JSON_OBJECT_AS_ARRAY);
            if ($decode && isset($decode['event_zoom_setting'])) {
                $zoom = $decode['event_zoom_setting'];
                $result['client_key'] = isset($zoom['event_zoom_key']) ? $zoom['event_zoom_key'] : null;
                $result['client_secret'] = isset($zoom['event_zoom_secret']) ? $zoom['event_zoom_secret'] : null;
                $result['client_email'] = isset($zoom['event_zoom_email']) ? $zoom['event_zoom_email'] : null;
            }
        }
        return $result;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To set the credentials for the further processing
     * -----------------------------------------------------------------------------------------------------------------
     */
    private function setCredentials() {
        $credentials = $this->getCredentials();
        $this->zoomKey = $credentials['client_key'];
        $this->zoomSecret = $credentials['client_secret'];
        $this->userEmail = $credentials['client_email'];
    }
    
    /**
     * @param User $user
     * @param $eventId
     * @return array
     * @throws CustomValidationException
     */
    private function addPresenter($user, $eventId) {
        $parameter['panelists'] = [
            [
                'name'  => "$user->fname $user->lname",
                'email' => $user->email,
            ]
        ];
        $url = $this->getUrl('create_update_get_remove_webinar_panelists', ['webinarId' => $eventId]); // prepare the url to hit
        $data = $this->addHeader($parameter);
        $res = $this->getExecute()->executePOST($url, $data, ApiExecuteFactory::RAW_JSON);
        return $this->validateResponse($res, ['id']);
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To add a user as moderator;
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $email
     * @param $eventId
     * @return string
     * @throws CustomValidationException|CustomException
     */
    private function addModerator($email, $eventId) {
        $url = $this->getUrl('get_update_delete_webinar', ['webinarId' => $eventId]); // prepare the url to hit
        $data = $this->addHeader([]);
        $event = json_decode($this->getExecute()->executeGET($url, $data));
        if ($event->id) {
            $this->validateModeratorExistence($email);
            $parameter['settings']['alternative_hosts'] =
                ($event->settings->alternative_hosts ? "{$event->settings->alternative_hosts}," : "")
                . $email;
            $data = $this->addHeader($parameter);
            return $this->getExecute()->executePATCH($url, $data, ApiExecuteFactory::RAW_JSON);
        } else {
            throw new CustomException('Invalid License For Conference Please Contact Support Team');
        }
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To validate if the user has account on zoom licensed or not to become a moderator.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $email
     * @throws CustomValidationException
     */
    public function validateModeratorExistence($email) {
        $z_user = $this->createORReadZoomUser($email);
        if (!$z_user || $z_user->status == 'pending') {
            throw new CustomValidationException('moderator_does_not_exists', null, 'message');
        }
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To remove the moderator from a conference
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $email
     * @param $conferenceId
     * @return mixed
     * @throws CustomException
     * @throws CustomValidationException
     */
    private function removeModerator($email, $conferenceId) {
        $url = $this->getUrl('get_update_delete_webinar', ['webinarId' => $conferenceId]); // prepare the url to hit
        $data = $this->addHeader([]);
        $event = json_decode($this->getExecute()->executeGET($url, $data));
        if ($event && $event->id) {
            $existingHosts = $event->settings->alternative_hosts;
            $existingHosts = explode(',', $existingHosts);
            $newHosts = [];
            foreach ($existingHosts as $host) {
                if ($host != $email) {
                    $newHosts[] = $host;
                }
            }
            $newHosts = implode(',', $newHosts);
            $parameter['settings']['alternative_hosts'] = $newHosts;
            $data = $this->addHeader($parameter);
            return $this->getExecute()->executePATCH($url, $data, ApiExecuteFactory::RAW_JSON);
        } else {
            throw new CustomException('Invalid License For Conference Please Contact Support Team');
        }
    }
    
    private function removePresenter($email, $conferenceId) {
        $url = $this->getUrl('remove_webinar_panelist',
            ['webinarId' => $conferenceId, 'panelistId' => $email]); // prepare the url to hit
        $data = $this->addHeader([]);
        return $this->getExecute()->executeDELETE($url, $data, ApiExecuteFactory::RAW_JSON);
    }
    
}