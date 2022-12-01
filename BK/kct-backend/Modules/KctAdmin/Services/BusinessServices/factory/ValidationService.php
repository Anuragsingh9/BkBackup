<?php

namespace Modules\KctAdmin\Services\BusinessServices\factory;

use Carbon\Carbon;
use Modules\KctAdmin\Entities\Event;
use Modules\KctAdmin\Entities\GroupUser;
use Modules\KctAdmin\Exceptions\CustomValidationException;
use Modules\KctAdmin\Services\BusinessServices\IValidationService;
use Modules\KctAdmin\Traits\KctHelper;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\KctUser\Repositories\BaseRepo;
use Modules\SuperAdmin\Entities\DemoLiveAsset;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will manage the validation related services
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class ValidationService
 * @package Modules\KctAdmin\Services\BusinessServices\factory
 */
class ValidationService extends BaseRepo implements IValidationService {
    use ServicesAndRepo;
    use KctHelper;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description to find the event by event object or by event id or uuid
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $event
     * @return mixed|Event
     */
    public function resolveEvent($event) {
        if ($event instanceof Event) {
            return $event;
        } else if (is_string($event)) {
            $event = $this->adminRepo()->eventRepository->findByEventUuid($event);
        }
        return $event;
    }

    /**
     * @inheritDoc
     */
    public function validateSpaceHostUpdate($hosts, $space) {
        $allHosts = $this->kctCoreService->getAllHostsId($space->event->event_uuid, [$space->space_uuid]);
        $commonHosts = array_intersect($allHosts, $hosts);
        if ($commonHosts) {
            throw new \Exception('user_already_host', '', 'message');
        }
    }

    /**
     * @inheritDoc
     */
    public function isSpaceFuture($spaceUuid): bool {
        $space = $this->adminRepo()->kctSpaceRepository->findSpaceByUuid($spaceUuid);
        if (!$space) {
            throw new CustomValidationException('exists', 'space_uuid', 'validation');
        }
        $space->load('event');
        $eventStart = $this->getCarbonByDateTime($space->event->start_time)->timestamp;
        $currentTime = Carbon::now()->timestamp;
        return ($currentTime < $eventStart);
    }

    /**
     * @inheritDoc
     */
    public function eventTimeCheck($event, int $pastCheck = 0, int $liveCheck = 0, int $futureCheck = 0): ?bool {
        $event = $this->resolveEvent($event);
        if (!$event) {
            return null;
        }
        $start = $this->getCarbonByDateTime($event->start_time);
        $end = $this->getCarbonByDateTime($event->end_time);
        $current = Carbon::now();
        return (
            // if past check is 0 return true else it will check event must be in past
            (!$pastCheck || $end <= $current)
            // if live check is 0 return true, else check if current time must between event start and end
            && (!$liveCheck || ($start <= $current && $current < $end))
            // if future check is return true, else check if current time is behind event start time
            && (!$futureCheck || $current < $start)
        );
    }

    /**
     * @inheritDoc
     */
    public function getEventState($eventUuid): array {
        $event = $this->adminRepo()->eventRepository->findByEventUuid($eventUuid);
        return [
            'event_state' => [
                'is_past'   => $this->isPastEvent($event) ? 1 : 0,
                'is_live'   => $this->isLiveEvent($event) ? 1 : 0,
                'is_future' => $this->isFutureEvent($event) ? 1 : 0,
            ]
        ];
    }


    /**
     * @inheritDoc
     * @throws CustomValidationException
     */
    public function validateMoments($moments) {
        foreach ($moments as &$moment) {
            $moment['moment_start'] = Carbon::createFromFormat('H:i:s', $moment['moment_start']);
            $moment['moment_end'] = Carbon::createFromFormat('H:i:s', $moment['moment_end']);
        }
        foreach ($moments as $i => $m) {
            foreach ($moments as $j => $c) {
                if ($i != $j // to avoid self compare
                    && (
                        // either both network
                        ($m['moment_type'] == 1 && $c['moment_type'] == 1) // if both are networking
                        // or both are not network
                        || ($m['moment_type'] != 1 && $c['moment_type'] != 1)
                    )
                ) {// networking check
                    if (
                        // A start ----- B start --- A end ---- B end
                        // A start ----- B start --- B end ---- A end
                    ($m['moment_start'] < $c['moment_start'] && $c['moment_start'] < $m['moment_end'])
                    ) {
                        throw new CustomValidationException('validation');
                    }
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function isEventMonoType($eventUuid): array {
        $event = $this->adminRepo()->eventRepository->findByEventUuid($eventUuid);
        return ['is_mono_event' => $event->is_mono_type ? $event->is_mono_type : 0];

    }

    /**
     * @inheritDoc
     */
    public function isGroupPilot($userId): bool {
        return (bool)GroupUser::whereUserId($userId)->whereRole(GroupUser::$role_Organiser)->count();
    }

    /**
     * @inheritDoc
     */
    public function checkIsDefaultAsset($event, $type): bool {
        $typeValue = $type == 'image' ? [3] : [1, 2]; // 1. YouTube 2. Vimeo 3. Image
        $assets = DemoLiveAsset::whereIn('asset_type', $typeValue)->get();
        $eventImg = $event->event_settings['event_images'] ?? [];
        $eventVid = $event->event_settings['event_video_links'] ?? [];
        $eventImgCount = count($eventImg);
        $eventVidCount = count($eventVid);
        $assetCount = count($assets);
        foreach ($assets as $asset) {
            if ($asset->asset_type == 3) {
                // extracting the filename from the image url
                $assetInfo = pathinfo($asset->asset_path);
                $filename[] = $assetInfo['basename'];
            } else {
                $filename[] = $asset->asset_path;
            }
        }
        return $type == 'image' ?
            $this->checkIsDefaultImages($eventImg, $eventImgCount, $assetCount, $filename) :
            $this->checkIsDefaultVideos($eventVid, $eventVidCount, $assetCount, $filename);
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To check if current live tab videos are same as demo live tab videos
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventVid
     * @param $eventVidCount
     * @param $assetCount
     * @param $filename
     * @return bool
     */
    public function checkIsDefaultVideos($eventVid, $eventVidCount, $assetCount, $filename): bool {
        $return = false;
        foreach ($eventVid as $video) {
            if ($eventVidCount == $assetCount && in_array($video['value'], $filename)) {
                $return = true;
            } else {
                $return = false;
            }
        }
        return $return;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @descripiton To check if current live tab images are same as demo live tab images
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param $eventImg
     * @param $eventImgCount
     * @param $assetCount
     * @param $filename
     * @return bool
     */
    public function checkIsDefaultImages($eventImg, $eventImgCount, $assetCount, $filename): bool {
        $return = false;
        foreach ($eventImg as $image) {
            $assetInfo = pathinfo($image['path']);
            if ($eventImgCount == $assetCount && in_array($assetInfo['basename'], $filename)) {
                $return = true;
            } else {
                $return = false;
            }
        }
        return $return;
    }
}
