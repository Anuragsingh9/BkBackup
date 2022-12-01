<?php


namespace App\Services\BlueJeans\Abstracts;

use App\Exceptions\CustomException;
use App\Services\BlueJeans\BlueJeansService;
use App\Services\BlueJeans\Model\BlueJeansUser;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use phpseclib\Net\SFTP\Stream;

class BlueJeansUserModel {
    protected static $url = [
        'createUser'         => ['method' => 'POST', 'uri' => 'https://api.bluejeans.com/v1/enterprise/{enterpriseId}/users'],
        'searchUser'         => ['method' => 'GET', 'uri' => 'https://api.bluejeans.com/v1/enterprise/{enterpriseId}/users'],
        'userMeetingSetting' => ['method' => 'GET', 'uri' => 'https://api.bluejeans.com/v1/user/{user_id}/room'],
        'getUser'            => ['method' => 'GET', 'uri' => 'https://api.bluejeans.com/v1/user/{user_id}'],
    ];

//    public static function create($param, $enterpriseId = NULL) {
//        $validator = Validator::make($param, [
//            'firstName'           => 'required|string|max:100',
//            'lastName'            => 'required|string|max:100',
//            'password'            => 'required|string|max:50',
//            'emailId'             => 'required|email',
//            'company'             => 'required|string|max:255',
//            'title'               => 'required|string|max:255',
//            'username'            => 'required|string|max:100',
////
//            'billingCategory'     => 'nullable|string',
//            'forcePasswordChange' => 'nullable|boolean',
//            'isAdmin'             => 'nullable|boolean',
//        ]);
//        if ($validator->fails()) {
//            return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 400);
//        }
//        $url = self::$url['createUser'];
//        $options = [];
//        try {
//            $response = (new Client())->request($url['method'], $url['uri'], $options);
//        } catch (GuzzleException $e) {
//            return response()->json(['status' => FALSE, 'msg' => 'Invalid Credentials'], 500);
//        } catch (\Exception $e) {
//            return response()->json(['status' => FALSE, 'msg' => 'Some Internal Error'], 500);
//        }
//        return json_decode($response->getBody()->getContents());
//    }

    /**
     * @param $param
     * @param bool $allDetails
     * @param bool $includeMeetingSetting
     * @return Collection|null
     * @throws GuzzleException
     */
    public static function where($param, $allDetails = TRUE, $includeMeetingSetting = TRUE) {
        $validator = Validator::make($param, [
            'enterpriseId' => 'nullable|integer',
            'order'        => 'nullable|string|in:asc,desc',
            'sortBy'       => 'nullable|string|in:username,firstName,middleName,lastName,enterpriseJoinDate,email',
            'textSearch'   => 'nullable|string',
            'emailId'      => 'nullable|email',
            'pageSize'     => 'nullable|integer|min:0',
            'pageNumber'   => 'nullable|integer|min:1',
            'fields'       => 'nullable|array',
            'fields.*'     => 'in:username,firstName,middleName,lastName,isEnterpriseAdmin,enterpriseJoinDate,email',
        ]);
        if ($validator->fails()) {
            return NULL;
//            return response()->json(['status' => FALSE, 'msg' => implode(',', $validator->errors()->all())], 400);
        }
        try {
            $url = self::$url['searchUser'];
            $requestData = BlueJeansService::prepareAPIData();
            if($requestData instanceof  JsonResponse) {
                if(isset($requestData->getData()->msg)) {
                    throw new CustomException($requestData->getData()->msg);
                } else {
                    throw new CustomException($requestData->getData());
                }
            }
            $url['uri'] = str_replace('{enterpriseId}', $requestData['enterpriseId'], $url['uri']);
            $response = (new Client())->request($url['method'], $url['uri'], $requestData['request']);
            $decode = json_decode($response->getBody()->getContents());
            $users = new Collection();
            foreach ($decode->users as $row) {
                $user = new \stdClass();
                if ($allDetails) {
                    $userResponse = (new Client())->request('GET', 'https://api.bluejeans.com' . $row->uri, $requestData['request']);
                    $user = json_decode($userResponse->getBody()->getContents());
                }
                if ($includeMeetingSetting) {
                    $userResponse = (new Client())->request('GET', 'https://api.bluejeans.com/v1/user/' . $row->id . '/room', $requestData['request']);
                    $decode = json_decode($userResponse->getBody()->getContents());
                    $user->moderatorPasscode = ((isset($decode->moderatorPasscode)) ? $decode->moderatorPasscode : NULL);
                }
                $users->push(new BlueJeansUser($user));
            }
            return $users;
//        } catch (GuzzleException $e) {
//            return response()->json(['status' => FALSE, 'msg' => 'Invalid Credentials'], 500);
        } catch (\Exception $e) {
            return NULL;
//            return response()->json(['status' => FALSE, 'msg' => 'Some Internal Error'], 500);
        }
    }
    
    /**
     * @param bool $allDetails
     *
     * @return Collection|null
     * @throws GuzzleException
     */
    public static function all($allDetails = TRUE) {
        return self::where([], $allDetails);
    }

    public static function first() {
        $users = self::where(['pageSize' => 1], TRUE, TRUE);
        if (!$users) {
            return NULL;
        }
        return $users->first();
    }

    public static function find($userId, $includeRoomDetails = FALSE) {
        try {
            $url = self::$url['getUser'];
            $url['uri'] = str_replace('{user_id}', $userId, $url['uri']);
            $requestData = BlueJeansService::prepareAPIData();
            $response = (new Client())->request($url['method'], $url['uri'], $requestData['request']);
            $decode = json_decode($response->getBody()->getContents());
            if ($includeRoomDetails) {
                $url = self::$url['userMeetingSetting'];
                $url['uri'] = str_replace('{user_id}', $userId, $url['uri']);
                $response = (new Client())->request($url['method'], $url['uri'], $requestData['request']);
                $decode->moderatorPasscode = json_decode($response->getBody()->getContents())->moderatorPasscode;
            }
            return new BlueJeansUser($decode);
        } catch (\Exception $e) {
            return NULL;
        }
    }
}