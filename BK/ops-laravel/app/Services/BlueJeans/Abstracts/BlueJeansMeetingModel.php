<?php


namespace App\Services\BlueJeans\Abstracts;


use App\Services\BlueJeans\BlueJeansService;
use App\Services\BlueJeans\Model\BlueJeansMeeting;
use App\Services\BlueJeans\Model\BlueJeansUser;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class BlueJeansMeetingModel {

    protected static $url = [
        'listMeeting'   => ['method' => 'GET', 'uri' => 'https://api.bluejeans.com/v1/user/{user_id}/scheduled_meeting'],
        'createMeeting' => ['method' => 'POST', 'uri' => 'https://api.bluejeans.com/v1/user/{user_id}/scheduled_meeting'],
        'updateMeeting' => ['method' => 'PUT', 'uri' => 'https://api.bluejeans.com/v1/user/{user_id}/scheduled_meeting/{meeting_id}'],
        'deleteMeeting' => ['method' => 'DELETE', 'uri' => 'https://api.bluejeans.com/v1/user/{user_id}/scheduled_meeting/{meeting_id}'],
        'getMeeting'    => ['method' => 'GET', 'uri' => 'https://api.bluejeans.com/v1/user/{user_id}/scheduled_meeting/{meeting_id}'],
    ];

    public static function create($param) {
        $validator = Validator::make($param, [
            'title'            => 'required|string|max:250',
            'description'      => 'nullable|string|max:500',
            'start'            => 'required|date_format:U',
            'end'              => 'required|date_format:U',
            'timezone'         => 'required|timezone',
            'endPointType'     => 'nullable|string',
            'endPointVersion'  => 'nullable|numeric',
            'attendees'        => 'nullable|array',
            'isLargeMeeting'   => 'nullable|boolean',
            'personal_meeting' => 'nullable|boolean',
            'sendEmail'        => 'nullable|boolean',
            'userId'           => 'nullable|integer',
        ]);
        if ($validator->fails()) {
            return 0;
        }
        $attendees = [];
        if (isset($param['attendees'])) {
            $attendees = array_map(function ($a) {
                return ['email' => $a];
            }, array_column($param['attendees'], 'email'));
        }
        $createBody = [
            'title'           => $param['title'],
            'description'     => ((isset($param['description'])) ? $param['description'] : ''),
            'start'           => BlueJeansService::secondToMillisecond($param['start']),
            'end'             => BlueJeansService::secondToMillisecond($param['end']),
            'timezone'        => $param['timezone'],
            'endPointType'    => (isset($param['endPointType']) ? $param['endPointType'] : 'WEB_APP'),
            'endPointVersion' => (isset($param['endPointVersion']) ? $param['endPointVersion'] : '2.10'),
            'attendees'       => $attendees,
            'isLargeMeeting'  => (isset($param['isLargeMeeting']) ? $param['isLargeMeeting'] : FALSE),
        ];
        $createQuery = [
            'personal_meeting' => (isset($param['personal_meeting']) ? $param['personal_meeting'] : TRUE),
            'email'            => ((isset($param['sendEmail']) ? $param['sendEmail'] : FALSE)),
        ];

        if (isset($param['userId'])) {
            $userId = $param['userId'];
        } else {
            $user = BlueJeansUser::first();
            if ($user) {
                $userId = $user->id;
            } else {
                return NULL;
            }
        }
        $url = self::$url['createMeeting'];
        $url['uri'] = str_replace('{user_id}', $userId, $url['uri']);
        try {
            $requestData = BlueJeansService::prepareAPIData(NULL, NULL, $createBody, $createQuery);
            // creating meeting
            $response = (new Client())->request($url['method'], $url['uri'], $requestData['request']);
            $decode = json_decode($response->getBody()->getContents());
            // updating meeting
            $createBody['meetingId'] = $decode->id;
            $createBody['userId'] = $userId;
            return self::update($createBody);
//        } catch (GuzzleException $e) {
//            return response()->json(['status' => FALSE, 'msg' => 'Can\t Create Meeting Now' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return NULL;
//            return response()->json(['status' => FALSE, 'msg' => 'Some Internal Error in creating meeting ' . $e->getMessage()], 500);
        }
    }

    public static function update($param) {
        $validator = Validator::make($param, [
            'meetingId'        => 'required|integer',
            'userId'           => 'nullable|integer',
            'title'            => 'required|string|max:250',
            'description'      => 'nullable|string|max:500',
            'start'            => 'required|date_format:U',
            'end'              => 'required|date_format:U',
            'timezone'         => 'required|timezone',
            'endPointType'     => 'nullable|string',
            'endPointVersion'  => 'nullable|numeric',
            'attendees'        => 'nullable|array',
            'isLargeMeeting'   => 'nullable|boolean',
            'personal_meeting' => 'nullable|boolean',
            'email'            => 'nullable|boolean',
            'moderatorLess'    => 'nullable|boolean',
            'moderatorId'      => 'nullable|integer',
        ]);
       
        if ($validator->fails()) {
            return NULL;
//            return response()->json(['status' => FALSE, 'msg' => $validator->errors()->all()], 422);
        }
        $attendees = [];
        if (isset($param['attendees'])) {
            $attendees = array_map(function ($a) {
                return ['email' => $a];
            }, array_column($param['attendees'], 'email'));
        }

        $updateBody = [
            'title'                  => $param['title'],
            'description'            => ((isset($param['description'])) ? $param['description'] : ''),
            'start'                  => BlueJeansService::secondToMillisecond($param['start']),
            'end'                    => BlueJeansService::secondToMillisecond($param['end']),
            'timezone'               => $param['timezone'],
            'endPointType'           => (isset($param['endPointType']) ? $param['endPointType'] : 'WEB_APP'),
            'endPointVersion'        => (isset($param['endPointVersion']) ? $param['endPointVersion'] : '2.10'),
            'attendees'              => $attendees,
            'isLargeMeeting'         => (isset($param['isLargeMeeting']) ? $param['isLargeMeeting'] : FALSE),
            'advancedMeetingOptions' => [
                'moderatorLess' => (isset($param['moderatorLess']) ? $param['moderatorLess'] : FALSE),
            ]
        ];
        if (isset($param['moderatorId'])) {
            $updateBody['moderator'] = [
                'id' => $param['moderatorId']
            ];
        }

        if (isset($param['userId'])) {
            $userId = $param['userId'];
        } else {
            $user = BlueJeansUser::first();
            if (!$user) {
                return NULL;
            }
            $userId = $user->id;
        }
        $url = self::$url['updateMeeting'];
        $url['uri'] = str_replace('{user_id}', $userId, $url['uri']);
        $url['uri'] = str_replace('{meeting_id}', $param['meetingId'], $url['uri']);

        try {
            $requestData = BlueJeansService::prepareAPIData(NULL, NULL, $updateBody);
            $response = (new Client())->request($url['method'], $url['uri'], $requestData['request']);
            $decode = json_decode($response->getBody()->getContents());
            return new BlueJeansMeeting($decode);
//        } catch (GuzzleException $e) {
//            return response()->json(['status' => FALSE, 'msg' => 'Can\'t Update Meeting Now' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return NULL;
//            return response()->json(['status' => FALSE, 'msg' => 'Some Internal Error in updating meeting ' . $e->getMessage()], 500);
        }
    }

    public static function all($param = [], $get = []) {
        $validator = Validator::make($param, [
            'meetingId' => 'nullable|integer',
            'userId'    => 'nullable|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => FALSE, 'msg' => $validator->errors()->all()], 422);
        }
        try {
            if (isset($param['userId'])) {
                $userId = $param['userId'];
            } else {
                $user = BlueJeansUser::first();
                if (!$user) {
                    return NULL;
                } else {
                    $userId = $user->id;
                }
            }
            $url = self::$url['listMeeting'];
            $url['uri'] = str_replace('{user_id}', $userId, $url['uri']);
            $requestData = BlueJeansService::prepareAPIData();
            $response = (new Client())->request($url['method'], $url['uri'], $requestData['request']);
            $decode = json_decode($response->getBody()->getContents());
            $meetings = new Collection();
            foreach ($decode as $meeting) {
                $meetings->push(new BlueJeansMeeting($meeting));
            }
            return $meetings;
//        } catch (GuzzleException $e) {
//            return response()->json(['status' => FALSE, 'msg' => 'Can\t Create Meeting Now' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return NULL;
//            return response()->json(['status' => FALSE, 'msg' => 'Some Internal Error ' . $e->getMessage()], 500);
        }
    }

    public static function delete($param) {
        $validator = Validator::make($param, [
            'meetingId'           => 'required|integer',
            'userId'              => 'nullable|integer',
            'email'               => 'nullable|boolean',
            'cancellationMessage' => 'required_if:email,true|nullable|string',
        ]);
        if ($validator->fails()) {
            return 0;
//            return response()->json(['status' => FALSE, 'msg' => $validator->errors()->all()], 422);
        }

        if (isset($param['userId'])) {
            $userId = $param['userId'];
        } else {
            $user = BlueJeansUser::first();
            if (!$user) {
                return NULL;
            }
            $userId = $user->id;
        }

        $url = self::$url['deleteMeeting'];
        $url['uri'] = str_replace('{user_id}', $userId, $url['uri']);
        $url['uri'] = str_replace('{meeting_id}', $param['meetingId'], $url['uri']);

        $queryString = [
            'email'               => (isset($param['email']) ? $param['email'] : FALSE),
            'cancellationMessage' => (isset($param['cancellationMessage']) && param['email'] ? $param['cancellationMessage'] : ''),
        ];
        try {
            $requestData = BlueJeansService::prepareAPIData(NULL, NULL, NULL, $queryString);
            $response = (new Client())->request($url['method'], $url['uri'], $requestData['request']);
            $decode = json_decode($response->getBody()->getContents());
            if (isset($decode->message)) {
//                return response()->json(['status' => FALSE, 'msg' => $decode->message], 500);
                return 0;
            } else {
                return 1;
//                                return response()->json(['status' => TRUE, 'msg' => 'Deleted'], 200);
            }
//        } catch (GuzzleException $e) {
//            return response()->json(['status' => FALSE, 'msg' => 'Can\'t Update Meeting Now' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return NULL;
//            return response()->json(['status' => FALSE, 'msg' => 'Some Internal Error in updating meeting ' . $e->getMessage()], 500);
        }
    }

    public static function find($param) {
        $validator = Validator::make($param, [
            'meetingId' => 'required|integer',
            'userId'    => 'nullable|integer',
        ]);
        if ($validator->fails()) {
            return 0;
//            return response()->json(['status' => FALSE, 'msg' => $validator->errors()->all()], 422);
        }
        $user = BlueJeansUser::first();
        if (!$user) {
            return NULL;
        }
        $url = self::$url['getMeeting'];
        $url['uri'] = str_replace(['{user_id}', '{meeting_id}'], [$user->id, $param['meetingId']], $url['uri']);
        try {
            $requestData = BlueJeansService::prepareAPIData();
            $response = (new Client())->request($url['method'], $url['uri'], $requestData['request']);
            $decode = json_decode($response->getBody()->getContents());
            return new BlueJeansMeeting($decode);
//        } catch (GuzzleException $e) {
//            return response()->json(['status' => FALSE, 'msg' => 'Can\'t Update Meeting Now' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return null;
//            return response()->json(['status' => FALSE, 'msg' => 'Some Internal Error in updating meeting ' . $e->getMessage()], 500);
        }
    }
}