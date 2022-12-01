<?php


namespace Modules\Cocktail\Services\Factory;


use App\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Cocktail\Exceptions\CustomValidationException;
use Modules\Cocktail\Services\Contracts\ApiExecuteFactory;
use Modules\Cocktail\Services\Contracts\ExternalEventFactory;

class BluejeansEventFactory implements ExternalEventFactory {
    /**
     * @var ApiExecuteFactory
     */
    private $execute;
    
    /**
     * @var string
     */
    private $baseUrl;
    private $accessToken;
    /**
     * @var int
     */
    private $userId;
    
    
    public function __construct() {
        $this->baseUrl = config('cocktail.bluejeans.base_url');
    }
    
    /**
     * To create the bluejeans event
     *
     * @param array $parameter
     * @return mixed
     * @throws CustomValidationException
     */
    public function create($parameter) {
        $this->generateAccessToken(); // get and set access token and user id
        $data = $this->addHeader($parameter); // add authorization in header
        $url = $this->getUrl('create_event', ['userId' => $this->userId]); // prepare the url to hit
        $res = $this->getExecute()->executePOST($url, $data, ApiExecuteFactory::RAW_JSON);
        return $this->validateResponse($res, ['id']);
    }
    
    /**
     * Convert the request parameter to desired output for bluejeans event create
     *
     * @param Request $request
     * @return array
     */
    public function prepareCreateParamFromRequest($request) {
        $startDate = Carbon::parse("{$request->input('date')} {$request->input('start_time')}");
        $endDate = Carbon::parse("{$request->input('date')} {$request->input('end_time')}");
        return [
            "title"                => $request->input('title'),
            "start"                => str_pad($startDate->timestamp, '13', '0'),
            "end"                  => str_pad($endDate->timestamp, '13', '0'),
            "description"          => $request->input('description'),
            "timezone"             => config('cocktail.bluejeans.timezone'),
            "restricted"           => false,
            "requireEmail"         => true,
            "enableRegistration"   => false,
            "enableWaterMark"      => false,
            "enableIntroOutro"     => true,
//            "allowInvitedAttendeesOnly" => true,
            "enableChat"           => (boolean)$request->input('event_chat', false), // 1
            "enableAttendeeRoster" => (boolean)$request->input('attendee_search'), // 2
            "enableQnA"            => (boolean)$request->input('q_a', false), // 3
            "enableQnAAnonymous"   => (boolean)$request->input('allow_anonymous_questions', false), // 4
            "isQnAAutoApprove"     => (boolean)$request->input('auto_approve_questions', false), // 5
            "enableAutoRecord"     => (boolean)$request->input('auto_recording', false), // 6
            "enablePSTN"           => (boolean)$request->input('phone_dial_in'), // 7
            "enableRaiseHand"      => (boolean)$request->input('raise_hand', false), // 8
            "showAttendeeCount"    => (boolean)$request->input('display_attendee_count', false), // 9
            "recurrencePattern"    =>
                [
                    "recurrenceType"  => "NONE",
                    "endDate"         => null,
                    "recurrenceCount" => "6",
                    "frequency"       => "1",
                    "daysOfWeekMask"  => 0,
                    "dayOfMonth"      => 0,
                    "weekOfMonth"     => "NONE"
                ],
            "panelists"            => [],
            "moderators"           => [],
            "attendees"            => [],
            "panelistMessage"      => "",
            "moderatorMessage"     => "",
            "attendeeMessage"      => "",
            "customIntroOutro"     => false,
            "whitelistedDomains"   => "https://sharabh.keepcontact.events",
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
        $url = $this->getUrl('update_event', ['userId' => $this->userId, 'eventId' => $eventId]);
        $this->getExecute()->executePUT($url, $data, ApiExecuteFactory::RAW_JSON); // prepare the url to hit
    }
    
    /**
     * To prepare the data to send from the request variable
     *
     * @param Request $request
     * @return array
     */
    public function prepareUpdateParamFromRequest($request) {
        $startDate = Carbon::parse("{$request->input('date')} {$request->input('start_time')}");
        $endDate = Carbon::parse("{$request->input('date')} {$request->input('end_time')}");
        return [
            "title"                => $request->input('title'),
            "start"                => str_pad($startDate->timestamp, '13', '0'),
            "end"                  => str_pad($endDate->timestamp, '13', '0'),
            "description"          => $request->input('description'),
            "enableChat"           => (boolean)$request->input('event_chat', false), // 1
            "enableAttendeeRoster" => (boolean)$request->input('attendee_search'), // 2
            "enableQnA"            => (boolean)$request->input('q_a', false), // 3
            "enableQnAAnonymous"   => (boolean)$request->input('allow_anonymous_questions', false), // 4
            "isQnAAutoApprove"     => (boolean)$request->input('auto_approve_questions', false), // 5
            "enableAutoRecord"     => (boolean)$request->input('auto_recording', false), // 6
            "enablePSTN"           => (boolean)$request->input('phone_dial_in'), // 7
            "enableRaiseHand"      => (boolean)$request->input('raise_hand', false), // 8
            "showAttendeeCount"    => (boolean)$request->input('display_attendee_count', false), // 9
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
        $url = $this->getUrl('delete_event', ['userId' => $this->userId, 'eventId' => $eventId]); // prepare the url to hit
        $this->getExecute()->executeDELETE($url, $data, ApiExecuteFactory::FORM_DATA_PARAMETER);
    }
    
    /**
     * To add the bluejeans member which is actually adding email
     *
     * @param $eventId
     * @param $data
     * @return bool|void
     * @throws CustomValidationException
     */
    public function addMember($eventId, $data) {
        $this->generateAccessToken();
        $parameter = [
            'attendees' => [$data['email']],
        ];
        if ($data['presenter']) {
            $parameter['panelists'] = [$data['email']];
        }
        if ($data['moderator']) {
            $parameter['moderators'] = [$data['email']];
        }
        $data = $this->addHeader($parameter);
        $url = $this->getUrl('add_member', ['userId' => $this->userId, 'eventId' => $eventId]);// prepare the url to hit
        $this->getExecute()->executePUT($url, $data, ApiExecuteFactory::RAW_JSON);
    }
    
    public function removeMember($eventId, $data) {
        $this->generateAccessToken();
        
    }
    
    /**
     * @param $eventId
     * @param bool $validateResponse
     * @return array
     * @throws CustomValidationException
     */
    public function getEvent($eventId, $validateResponse = false) {
        $this->generateAccessToken();
        $url = $this->getUrl('get_event', ['userId' => $this->userId, 'eventId' => $eventId]);
        $data = $this->addHeader([]);
        $res = $this->getExecute()->executeGET($url, $data);
        if ($validateResponse) {
            return $this->validateResponse($res, ['id', 'panelistUrl', 'attendeeUrl', 'moderatorUrl']);
        }
        return json_decode($res, JSON_OBJECT_AS_ARRAY);
    }
    
    /**
     * @param $conferenceId
     * @param string $type
     * @return mixed|string
     * @throws CustomValidationException
     */
    public function getJoinLink($conferenceId, $type) {
        if ($type == 'presenter') {
            $event = $this->getEvent($conferenceId);
            return isset($event['panelistUrl']) ? $event['panelistUrl'] : '';
        } else if ($type == 'moderator') {
            $event = $this->getEvent($conferenceId);
            return isset($event['moderatorUrl']) ? $event['moderatorUrl'] : '';
        }
        return '';
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
            throw new CustomValidationException('invalid_bluejeans_credentials', '', 'message');
        }
        foreach ($keys as $key) {
            if (!isset($result[$key])) {
                throw new CustomValidationException('some_issue_in_bluejeans', '', 'message');
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
            $this->execute = app(ApiExecuteFactory::class);
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
            return $this->baseUrl . str_replace(array_keys($replace), array_values($replace), config("cocktail.bluejeans.$key"));
        }
        return $this->baseUrl . config("cocktail.bluejeans.$key");
    }
    
    
    /**
     * This method will fetch the access token for specific user email and set them to class variable.
     * this will also return that value in case not needed through class variable.
     *
     * @return array
     * @throws CustomValidationException
     */
    private function generateAccessToken() {
        $clientDetails = $this->getClientDetails();
        $clientId = $clientDetails['client_id'];
        $clientSecret = $clientDetails['client_secret'];
        $clientEmail = $clientDetails['client_email'];
        
        $data = [
            'headers' => ['Accept:application/json', "Content-Type:application/json"],
            'data'    => [
                "grant_type" => "user_app",
                "properties" => [
                    "client_id"     => $clientId,
                    "client_secret" => $clientSecret,
                    "user_email"    => $clientEmail,
                ]
            ]
        ];
        $url = $this->getUrl('access_token');
        $res = $this->getExecute()->executePOST($url, $data, ApiExecuteFactory::RAW_JSON);
        $token = $this->validateResponse($res, ['access_token', 'userId']);
        $this->accessToken = $token['access_token'];
        $this->userId = $token['userId'];
        return [
            'access_token' => $token['access_token'],
            'userId'       => $token['userId']
        ];
    }
    
    /**
     * @return array
     * @throws CustomValidationException
     */
    private function getClientDetails() {
        $setting = Setting::where('setting_key', 'event_settings')->first();
        if (!$setting || empty($setting->setting_value)) {
            throw new CustomValidationException('bluejeans_not_set', '', 'message');
        }
        
        $value = json_decode($setting->setting_value, JSON_OBJECT_AS_ARRAY);
        
        if (!isset($value['event_bluejeans_setting']['bluejeans_event_client_id'])
            || !isset($value['event_bluejeans_setting']['bluejeans_event_client_secret'])) {
            throw new CustomValidationException('bluejeans_not_set', '', 'message');
        }
        $value = $value['event_bluejeans_setting'];
        $clientId = $value['bluejeans_event_client_id'];
        $clientSecret = $value['bluejeans_event_client_secret'];
        $clientEmail = $value['bluejeans_event_client_email'];
        
        if (!$clientId || !$clientSecret || !$clientEmail) {
            throw new CustomValidationException('bluejeans_not_set', '', 'message');
        }
        
        return [
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'client_email'  => $clientEmail,
        ];
    }
    
    public function prepareConferenceOptions($request) {
        return [
            'conference_type'     => config('kct_const.conference_type.bj'),
            'conference_settings' => [
                'event_chat'                => checkValSet($request->input('event_chat')),
                'attendee_search'           => checkValSet($request->input('attendee_search')),
                'q_a'                       => checkValSet($request->input('q_a')),
                'allow_anonymous_questions' => checkValSet($request->input('allow_anonymous_questions')),
                'auto_approve_questions'    => checkValSet($request->input('auto_approve_questions')),
                'auto_recording'            => checkValSet($request->input('auto_recording')),
                'phone_dial_in'             => checkValSet($request->input('phone_dial_in')),
                'raise_hand'                => checkValSet($request->input('raise_hand')),
                'display_attendee_count'    => checkValSet($request->input('display_attendee_count')),
                'allow_embedded_replay'     => checkValSet($request->input('allow_embedded_replay')),
            ],
        ];
    }
    
    public function prepareEmbeddedLnk($conferenceId) {
        $res = $this->getEvent($conferenceId);
        $url = '';
        if (isset($res['attendeeUrl'])) {
            $attendee = $res['attendeeUrl'];
            $code = explode('/', $attendee);
            if (count($code) == 6) {
                $url = str_replace('[[SHARING_ID]]', end($code), config('cocktail.embedded_url_template'));
            }
        }
        return [
            'embedded_url' => $url,
        ];
    }
}