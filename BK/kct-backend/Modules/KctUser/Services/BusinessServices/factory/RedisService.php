<?php


namespace Modules\KctUser\Services\BusinessServices\factory;

use Modules\KctUser\Entities\KctConference;
use Modules\KctUser\Events\MomentStatusUpdated;
use Modules\KctUser\Services\BusinessServices\IRtcService;
use Modules\KctUser\Services\ExternalAgentServices\RTCFactory;
use Modules\KctUser\Services\KctCoreService;
use Modules\Events\Entities\Event;

class RedisService implements IRtcService {
    /**
     * @param Event $event
     * @param KctConference $conference
     */
    public function updateConferenceStatus($event, $conference) {
        $conferenceType = KctCoreService::getInstance()->findEventConferenceTimeType($event);
        $currentConference = KctConference::where('event_uuid', $event->event_uuid)
            ->where('conference_time_block', $conferenceType['time_block'])
            ->first();
        if ($currentConference->conference_id == $conference->conference_id) {
            $embeddedUrl = KctCoreService::getInstance()->getEmbeddedUrl($event);
            $result = [
                'eventUuid' => $event->event_uuid,
                'namespace' => KctCoreService::getInstance()->getNamespaceFromHost(),
            ];
            if ($embeddedUrl) {
                $result['dataToSend'] = array_merge($embeddedUrl, ['status' => $currentConference->is_active]);
            } else {
                $result['dataToSend'] = ['status' => $currentConference->is_active];
            }
            event(new MomentStatusUpdated($result));
        }
    }
}
