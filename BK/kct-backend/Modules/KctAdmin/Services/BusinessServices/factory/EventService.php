<?php


namespace Modules\KctAdmin\Services\BusinessServices\factory;

use App\Models\User;
use Carbon\Carbon;
use Exception;
use Modules\KctAdmin\Entities\Event;
use Modules\KctAdmin\Entities\EventMeta;
use Modules\KctAdmin\Entities\Moment;
use Modules\KctAdmin\Exceptions\DefaultGroupNotFoundException;
use Modules\KctAdmin\Services\BusinessServices\IEventService;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Ramsey\Uuid\Uuid;

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This class will contain all logic related to the event service methods
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * Class EventService
 * @package Modules\KctAdmin\Services\BusinessServices\factory
 */
class EventService implements IEventService {
    use ServicesAndRepo;

    /**
     * @throws DefaultGroupNotFoundException|Exception
     */
    public function createWaterFountainEvent() {
        $event = $this->adminRepo()->eventRepository->findByType(Event::$eventType_all_day);
        if (!$event) {
            $defaultGroup = $this->adminRepo()->groupRepository->getDefaultGroup();
            $firstUser = User::first();
            $isDummyEvent = 1;
            $event = $this->adminRepo()->eventRepository->create([
                'title'              => 'Water Fountain',
                'start_time'         => Carbon::now()->setTime(0, 0),
                'join_code'          => 'water-fountain',
                'end_time'           => Carbon::now()->addDays(100)->setTime(23, 59, 59),
                'type'               => Event::$type_networking, // 1. Networking, 2 Content
                'created_by_user_id' => $firstUser->id,
                'event_settings'     => [
                    'is_dummy_event'           => $isDummyEvent,
                    'is_self_header'           => 0,
                    'manual_access_code'       => Uuid::uuid4(),
                    'is_auto_key_moment_event' => 1,
                    'event_conv_limit'         => 4,
                    'event_scenery'            => [
                        'asset_id'          => 0,
                        'category_type_id'  => 0,
                        'top_bg_color'      => "#ffffffff",
                        'component_opacity' => 92,
                    ]
                ],
                'is_mono_type'       => 0,
                'event_type'         => Event::$eventType_all_day,
                'group_id'           => $defaultGroup->id,
            ]);

            $space = $this->adminRepo()->kctSpaceRepository->create([
                'space_name'       => __('kctadmin::messages.space_default_name'),
                'space_short_name' => '',
                'space_mood'       => __('kctadmin::messages.space_default_name'),
                'max_capacity'     => config('kctadmin.modelConstants.spaces.defaults.default_capacity'),
                'is_vip_space'     => 0,
                'is_duo_space'     => 0,
                'hosts'            => $firstUser->id,
                'order_id'         => config('kctadmin.modelConstants.spaces.defaults.start_order'),
                'event_uuid'       => $event->event_uuid,
            ]);

            // adding the selected user as space host
            $this->adminRepo()->eventRepository->addUserToEvent(
                $event->event_uuid,
                $firstUser->id,
                $space->space_uuid,
                ['is_organiser' => 1, 'is_host' => 1],
            );

            if ($isDummyEvent) {
                $this->adminServices()->dataFactory->prepareDummyUsers($space);
            }

            $this->adminRepo()->eventRepository->makeEventAsDraft([
                'event_uuid'     => $event->event_uuid,
                'reg_start_time' => $event->start_time,
                'reg_end_time'   => $event->end_time,
                'event_status'   => EventMeta::$eventStatus_live, // 1 for live. 2 for draft
                'share_agenda'   => 0,
                'is_reg_open'    => EventMeta::$event_regIsOpen, // 0 for close , 1 for open
            ]);


            $event->moments()->create([
                'moment_name'        => $event->title,
                'moment_description' => $event->title,
                'moment_type'        => Moment::$momentType_networking,
                'start_time'         => $event->start_time,
                'end_time'           => $event->end_time,
            ]);
        }else{
            $settings = $event->event_settings;
            $settings['event_scenery'] = [
                'asset_id' => $settings['event_scenery']['asset_id'] ?? 0,
                'category_type_id' => $settings['event_scenery']['category_type_id'] ?? 0,
                'top_bg_color' => $settings['event_scenery']['top_bg_color'] ?? "#ffffffff",
                'component_opacity' => $settings['event_scenery']['component_opacity'] ?? 92,
            ];
            $event->event_settings = $settings;
            $event->update();
        }

        $event->load(['spaces', 'moments']);
        return $event;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To get the water fountain event, if the check is passed (default true) it will check if
     * water fountain event is enabled or not
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param bool $enableCheck
     * @return mixed|null
     */
    public function getWaterFountainEvent($enableCheck = true) {
        if ($enableCheck) {
            $accountSetting = $this->adminRepo()->settingRepository->getAccountSetting();
            $fetchCheck = (bool)($accountSetting['all_day_event_enabled'] ?? null);
        } else {
            $fetchCheck = true;
        }
        if ($fetchCheck) {
            return $this->adminRepo()->eventRepository->findByType(Event::$eventType_all_day);
        }
        return null;
    }
}
