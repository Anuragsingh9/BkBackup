<?php


namespace Modules\Events\Service;


use App\Services\Service;
use App\Setting;
use GuzzleHttp\Client;
use Modules\Cocktail\Exceptions\CustomValidationException;
use Modules\Cocktail\Services\Contracts\ApiExecuteFactory;
use Modules\Cocktail\Services\KctService;
use Modules\Events\Entities\Organiser;
use Modules\Events\Exceptions\CustomException;
use Exception;

class WordPressService extends Service {
    private $WP_URL;
    private $WP_USER_PASS;
    
    /**
     * @var ApiExecuteFactory
     */
    private $apiFactory;
    
    /**
     * WordPressService constructor.
     * @throws CustomValidationException
     */
    public function __construct() {
        $setting = Setting::where('setting_key', 'event_settings')->first();
        $decodeSetting = (($setting) ? json_decode($setting->setting_value, 1) : null);
        if (!isset($decodeSetting['event_wp_setting']['wp_url']))
            throw new CustomValidationException(__('wp_not_set'));
        $this->WP_URL = $decodeSetting['event_wp_setting']['wp_url'];
        $this->WP_USER_PASS = $decodeSetting['event_wp_setting']['wp_pass'];
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To create a Word Press event for the event this method will safely check if wp is enabled from super
     * admin or not and then it will create a wp event and return its wp post id.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $request
     * @param $data
     * @return null
     * @throws CustomException
     */
    public function createEvent($request, $data) {
        if (EventService::getInstance()->isWpOn()) {
            
            $imageUrl = $data['imageUrl'];
            $defaultOrganiserUser = $data['defaultOrganiserUser'];
            
            $wpData = [
                'type'        => $request->type,
                'title'       => $request->title,
                'header_text' => $request->header_text,
                'description' => $request->description,
                'image'       => $imageUrl,
                'date'        => $request->date,
                'start_time'  => $request->start_time,
                'end_time'    => $request->end_time,
                'latitude'    => isset($request->lat) ? $request->lat : '28.0229',
                'longitude'   => isset($request->lng) ? $request->lng : '73.3119',
                'api_type'    => 'add',
                'address'     => $request->address,
                'city'        => $request->city,
                'territory'   => $request->territor_value,
            ];
            
            if ($request->type == 'int' || $request->type == 'virtual') {
                $wpData['internal_organiser_first_name'] = ($defaultOrganiserUser ? $defaultOrganiserUser->fname : '');
                $wpData['internal_organiser_last_name'] = ($defaultOrganiserUser ? $defaultOrganiserUser->lname : '');
                $wpData['internal_organiser_email'] = ($defaultOrganiserUser ? $defaultOrganiserUser->email : '');
                if ($request->type == 'virtual') {
                    $wpData['internal_is_virtual_event'] = true;
                }
            } else if ($request->type === 'ext' && (!empty($request->organiser_id))) { // wp data prepare
                $organiser = Organiser::find($request->organiser_id);
                $wpData['organiser_company'] = $organiser->company;
                $wpData['organiser_first_name'] = $organiser->fname;
                $wpData['organiser_last_name'] = $organiser->lname;
                $wpData['organiser_email'] = $organiser->email;
                $wpData['organiser_site_url'] = $organiser->website;
                $wpData['organiser_phone'] = $organiser->phone;
            }
            $wpPostId = $this->prepareAndSendWPPost($wpData);
            if (!$wpPostId || !is_int($wpPostId)) {
                throw new CustomException($wpPostId, 'Can not create wp now');
            }
            return $wpPostId;
        }
        return null;
    }
    
    public function updateEvent($request, $event, $imageUrl) {
        $wpData = [
            'type'        => $event->type,
            'title'       => $request->title,
            'header_text' => $request->header_text,
            'description' => $request->description,
            'image'       => $imageUrl,
            'date'        => $request->date,
            'start_time'  => $request->start_time,
            'end_time'    => $request->end_time,
            'api_type'    => 'update',
            'post_id'     => $event->wp_post_id,
            'address'     => $request->address,
            'city'        => $request->city,
            'territory'   => (($event->type == 'int' && $request->is_territory) ? $request->territor_value : ''),
        ];
        if ((isset($request->lat) && $request->lat != 0) || (isset($request->lng) && $request->lng != 0)) {
            $wpData['latitude'] = $request->lat;
            $wpData['longitude'] = $request->lng;
        }
        if ($event->type === 'ext' && (!empty($request->organiser_id))) {
            $orgniser = Organiser::find($request->organiser_id);
            $wpData['organiser_company'] = $orgniser->company;
            $wpData['organiser_first_name'] = $orgniser->fname;
            $wpData['organiser_last_name'] = $orgniser->lname;
            $wpData['organiser_email'] = $orgniser->email;
            $wpData['organiser_phone'] = $orgniser->phone;
            $wpData['organiser_site_url'] = $orgniser->website;
        } else if ($event->type === 'int' || $event->type === 'virtual') {
            $defaultOrganiserUser = $event->users->first();
            $wpData['internal_organiser_first_name'] = ($defaultOrganiserUser ? $defaultOrganiserUser->fname : '');
            $wpData['internal_organiser_last_name'] = ($defaultOrganiserUser ? $defaultOrganiserUser->lname : '');
            $wpData['internal_organiser_email'] = ($defaultOrganiserUser ? $defaultOrganiserUser->email : '');
            if ($event->type == 'virtual') {
                $wpData['internal_is_virtual_event'] = true;
                $wpData['virtual_event_registration_link'] = KctService::getInstance()->getRegLink($request, $event);
            }
        }
        return $this->prepareAndSendWPPost($wpData);
    }
    
    /**
     * @param $wpData
     * @return |null
     * @throws \Exception
     */
    public function prepareAndSendWPPost($wpData) {
        $wpPostId = null;
        $eventWPPostType = null;
        $eventWPUpdateId = null;
        $eventData = [];
        
        $eventType = $wpData['type'];
        $eventApiType = $wpData['api_type'];
        
        //set event post type
        if ($eventType === 'int' || $eventType === 'virtual') {
            $eventWPPostType = 'internal_event';
        } else if ($eventType === 'ext') {
            $eventWPPostType = 'external_event';
        }
        
        //set event call url type
        if ($eventApiType === 'update' || $eventApiType === 'delete') {
            $eventWPUpdateId = $wpData['post_id'];
            $eventWPPostType = "$eventWPPostType/$eventWPUpdateId";
        }
        //create event post in wordpress
        $wpAPIClient = new Client([
            'base_uri' => $this->WP_URL,
            'headers'  => ['Content-Type' => 'application/json', "Accept" => "application/json"],
        ]);
        // TO SEND TIME IN H:m FORMAT
        $filteredStartTime = null;
        $filteredEndTime = null;
        if (isset($wpData['start_time'])) {
            $filteredStartTime = date('H:i', strtotime($wpData['start_time']));
        }
        if (isset($wpData['end_time'])) {
            $filteredEndTime = date('H:i', strtotime($wpData['end_time']));
        }
        
        //if eventApi type is not delete
        if ($eventApiType !== 'delete') {
            //set up mapping of wp fields to ops event fields
            if ($eventType === 'int' || $eventType === 'virtual') {
                $eventData = [
                    'title'  => $wpData['title'],
                    'fields' => [
                        'internal_event_title'          => $wpData['title'],
                        'internal_event_header'         => $wpData['header_text'],
                        'internal_event_description'    => $wpData['description'],
                        'internal_event_image'          => $wpData['image'],
                        'internal_event_date'           => $wpData['date'],
                        'internal_event_address'        => $wpData['address'],
                        'internal_event_city'           => $wpData['city'],
                        'internal_event_start_time'     => $filteredStartTime,
                        'internal_event_end_time'       => $filteredEndTime,
                        'internal_territory'            => $wpData['territory'],
                        'internal_organiser_first_name' => $wpData['internal_organiser_first_name'],
                        'internal_organiser_last_name'  => $wpData['internal_organiser_last_name'],
                        'internal_organiser_email'      => $wpData['internal_organiser_email'],
                    ],
                    'status' => 'publish',
                ];
                if (isset($wpData['latitude']) || isset($wpData['longitude'])) {
                    $eventData['fields']['internal_event_latitude'] = $wpData['latitude'];
                    $eventData['fields']['internal_event_longitude'] = $wpData['longitude'];
                }
                if ($eventType == 'virtual') {
                    $eventData['fields']['internal_event_address'] = null;
                    $eventData['fields']['internal_event_city'] = null;
                    $eventData['fields']['internal_is_virtual_event'] = $wpData['internal_is_virtual_event'];
                    $eventData['fields']['virtual_event_registration_link'] = isset($wpData['virtual_event_registration_link']) ? $wpData['virtual_event_registration_link'] : '';
                }
            } else if ($eventType === 'ext') {
                $eventData = [
                    'title'  => $wpData['title'],
                    'fields' => [
                        'external_event_title'       => $wpData['title'],
                        'external_event_header'      => $wpData['header_text'],
                        'external_event_description' => $wpData['description'],
                        'external_event_image'       => $wpData['image'],
                        'external_event_date'        => $wpData['date'],
                        'external_event_start_time'  => $filteredStartTime,
                        'external_event_end_time'    => $filteredEndTime,
                        'organiser_company'          => $wpData['organiser_company'],
                        'organiser_first_name'       => $wpData['organiser_first_name'],
                        'organiser_last_name'        => $wpData['organiser_last_name'],
                        'organiser_email'            => $wpData['organiser_email'],
                        'organiser_phone'            => $wpData['organiser_phone'],
                        'organiser_site_url'         => $wpData['organiser_site_url'],
                    ],
                    'status' => 'publish',
                ];
            }
        }

//            var_export($eventData);
//            var_export($eventWPPostType);
        //  exit;
        $wpEventData = [
            'body' => json_encode($eventData),
            'curl' => [
                CURLOPT_USERPWD => $this->WP_USER_PASS,
            ],
        ];
        //send request to create post in WP
        try {
            //here we checking which url/method we need to use
            if ($eventApiType === 'delete') {
                $wpResponse = $wpAPIClient->delete($eventWPPostType, $wpEventData);
            } else {
                $wpResponse = $wpAPIClient->post($eventWPPostType, $wpEventData);
            }
            
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $responseBody = $e->getResponse()->getBody(true)->getContents();
            throw new CustomException($responseBody, 'Error in wp creation');
        }
        if ($wpResponse->getStatusCode() === 201) {
            $wpPostData = json_decode($wpResponse->getBody()->getContents());
            return $wpPostData->id;
        }
        return null;
    }
    
    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to rollback the wp event as the ops side transaction could not be
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $type
     * @param $wpPostId
     * @throws Exception
     */
    public function rollback($type, $wpPostId) {
        if (!empty($wpPostId) && is_int($wpPostId)) {
            $wpData = [
                'type'     => $type,
                'api_type' => 'delete',
                'post_id'  => $wpPostId,
            ];
            $wpPostId = $this->prepareAndSendWPPost($wpData);
        }
    }
    
    public function deleteEvent($event) {
        $wpData = [
            'type'     => $event->type,
            'api_type' => 'delete',
            'post_id'  => $event->wp_post_id,
        ];
        $wpPostId = $this->prepareAndSendWPPost($wpData);
    }
    
    public function getWpArticles($event) {
        if ($event->wp_post_id) {
            $this->apiFactory = app(ApiExecuteFactory::class);
            $url = "{$this->WP_URL}related-content-posts/$event->wp_post_id";
            $data = [
                'headers' => ['Content-Type:application/json', "Accept:application/json"]
            ];
            $result = $this->apiFactory->executeGET($url, $data);
            $result = json_decode($result, JSON_OBJECT_AS_ARRAY);
            if (isset($result['status']) && $result['status'] && isset($result['data'])) {
                $this->filterPostUrl($result);
                return collect($result['data']);
            }
        }
        return null;
    }
    
    /**
     * To remove the post url if wp is off, so wp content shows as non clickable
     *
     * @param $result
     */
    public function filterPostUrl(&$result) {
        $isWpOn = EventService::getInstance()->isWpOn();
        if (!$isWpOn) {
            $m = count($result['data']);
            for ($i = 0; $i < $m; $i++) {
                $result['data'][$i]['post_url'] = null;
            }
        }
    }
}
