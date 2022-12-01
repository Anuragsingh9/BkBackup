<?php


namespace Modules\KctUser\Services\BusinessServices\factory;


use Aws\Chime\ChimeClient;
use Aws\Result;
use Modules\KctUser\Services\BusinessServices\IVideoChatService;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This will manage the event related functionality
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Interface IEventRepository
 * @package Modules\KctAdmin\Repositories
 */
class VideoChatService implements IVideoChatService {

    public $client;

    public function __construct() {
        $this->client = new ChimeClient([
            'version'     => 'latest',
            'region'      => env("CHIME_AWS_REGION"),
            // commenting this out so the aws credentials can be fetched directly from either env or ec2 env
//            'credentials' => [
//                'key'    => env("CHIME_AWS_KEY"),
//                'secret' => env("CHIME_AWS_SECRET"),
//            'region'      => env("CHIME_AWS_REGION"),
//            ]
        ]);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To create meeting on zoom
     * -----------------------------------------------------------------------------------------------------------------
     *
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

    /**
     * ----------------------------------------------------------------------------------------------------------------
     * @descripiton This method use for remove the attendees from the conversation
     * ----------------------------------------------------------------------------------------------------------------
     *
     * @param $userIds
     */
    public function removeAttendees($userIds) {
        foreach ($userIds as $id) {

        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To delete the zoom meeting
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $meetingUuid
     * @return bool
     */
    public function deleteMeeting($meetingUuid) {
        $this->client->deleteMeeting(['MeetingId' => $meetingUuid]);
        return true;
    }
}
