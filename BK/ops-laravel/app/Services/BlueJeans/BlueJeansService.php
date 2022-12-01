<?php


namespace App\Services\BlueJeans;


use App\Services\BlueJeans\Model\BlueJeansUser;
use App\Setting;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class BlueJeansService {

    protected static $url = [
        'accessToken' => ['method' => 'POST', 'uri' => 'https://api.bluejeans.com/oauth2/token#Application'],
    ];

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public static function getAccessToken() {
        $response = NULL;
        try {
          
            // check already session have access_token
//            if (session()->has('bluejeans_access_token')) {
//                if (time() - session('bluejeans_access_token_life') < 3000) {
//                    return json_decode(session('bluejeans_access_token'));
//                }
//            }
            $url = self::$url['accessToken'];
            $setting = Setting::where('setting_key', 'video_meeting_api_setting')->first();
            if (!$setting) {
                return NULL;
            }
            $decode = json_decode($setting->setting_value);
            $clientId = (isset($decode->client_id) ? $decode->client_id : NULL);
            $clientSecret = (isset($decode->client_secret) ? $decode->client_secret : NULL);
            if (!$clientId || !$clientSecret) {
                return NULL;
            }
         
            $options = [
                'headers' => [
                    'accept'       => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json'    => [
                    'grant_type'    => 'client_credentials',
//                    'client_id'     => 'ops-vid-dev-001',
//                    'client_secret' => 'd13e84b2368f4ee2bd4a4e3d02c7cf0d',
                    'client_id'     => $clientId,
                    'client_secret' => $clientSecret,
                ]
            ];
          
            $response = (new Client())->request($url['method'], $url['uri'], $options);
           
            //session(['bluejeans_access_token' => $response->getBody()->getContents()]);
          //  session(['bluejeans_access_token_life' => time()]);
            return json_decode($response->getBody()->getContents());
        } catch (GuzzleException $e) {
            return response()->json(['status' => FALSE, 'msg' => 'Invalid Credentials'], 500);
        } catch (\Exception $e) {
            return response()->json(['status' => FALSE, 'msg' => 'Some Internal Error'], 500);
        }
    }

    /**
     * @param null $row
     * @param null $formData
     * @param null $json
     * @param null $query
     * @return array|\Illuminate\Http\JsonResponse|mixed
     */
    public static function prepareAPIData($row = NULL, $formData = NULL, $json = NULL, $query = NULL) {
        $data = [];
        $token = self::getAccessToken();
        if (!isset($token->access_token)) {
            return $token;
        }
        $data['access_token'] = $token->access_token;
        $data['enterpriseId'] = $token->scope->enterprise;
        $data['request'] = [
            'header' => [
                'accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'query'  => [
                'access_token' => $token->access_token,
            ]
        ];
        if ($row) {
            $data['request']['body'] = $row;
        }
        if ($formData) {
            $data['request']['form-data'] = $formData;
        }
        if ($json) {
            $data['request']['json'] = $json;
        }
        if ($query) {
            $query['access_token'] = $token->access_token;
            $data['request']['query'] = $query;
        }
        return $data;
    }

    public static function secondToMillisecond($value) {
        return (int) ($value * (10 ** (strlen(time()) + 3 - strlen($value))));
    }
}