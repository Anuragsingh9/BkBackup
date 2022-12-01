<?php

namespace Modules\Cocktail\Services;

use App\Services\Service;
use Aws\Acm\AcmClient;
use Aws\Chime\ChimeClient;
use Aws\Result;


class AwsChimeService extends Service {
    public $client;
    
    public function __construct() {
        $this->client = new ChimeClient([
            'version'     => 'latest',
            'region'      => env("AWS_DEFAULT_REGION"),
            'credentials' => [
                'key'    => env('CHIME_AWS_KEY'),
                'secret' => env('CHIME_AWS_SECRET'),
            ],
        ]);
    }
    
    /**
     * @param $clientRequestToken // unique token for each meeting
     * @param $spaceUuid
     * @return Result
     */
    public function createMeeting($clientRequestToken, $spaceUuid = null) {
        $param = [
            'ClientRequestToken' => $clientRequestToken, // REQUIRED
            'MediaRegion'        => env('AWS_DEFAULT_REGION'),
            'Tags'               => [
                ['Key'   => 'space_uuid', // REQUIRED
                 'Value' => $spaceUuid, // REQUIRED
                ],
            ],
        ];
        return $this->client->createMeeting($param);
    }
    
    public function deleteMeeting($meetingUuid) {
        $this->client->deleteMeeting(['MeetingId' => $meetingUuid]);
        return true;
    }
    
    public function joinAttendee($meetingId, $userId, $conversationUuid) {
        return $this->client->createAttendee([
            'ExternalUserId' => $userId, // REQUIRED
            'MeetingId'      => $meetingId, // REQUIRED
            'Tags'           => [
                [
                    'Key'   => 'conversation_uuid', // REQUIRED
                    'Value' => $conversationUuid, // REQUIRED
                ],
            ],
        ]);
    }
    
    public function removeAttendees($userIds) {
        foreach ($userIds as $id) {
        
        }
    }
    
    public function getMeeting($meetingId) {
        return $this->client->getMeeting([
            'MeetingId' => $meetingId, // REQUIRED
        ]);
    }
}