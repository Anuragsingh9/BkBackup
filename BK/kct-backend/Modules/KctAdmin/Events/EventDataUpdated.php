<?php

namespace Modules\KctAdmin\Events;

use Hyn\Tenancy\Environment;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Modules\KctAdmin\Entities\Event;
use Modules\KctAdmin\Traits\ServicesAndRepo;
use Modules\KctUser\Traits\Services;
use Modules\KctUser\Transformers\V1\SpaceUSResource;

class EventDataUpdated implements ShouldBroadcastNow {
    use SerializesModels;
    use Services;
    use ServicesAndRepo;

    private ?Event $event;
    public array $dataToSend = [];

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($event, $tempDummyRelations) {
        $this->event = $event;

        $tenant = app(Environment::class);
        $this->dataToSend = [
            'namespace'            => $this->userServices()->kctService->getNamespaceFromHost($tenant->hostname()),
            'event_uuid'           => $event->event_uuid,
            'is_dummy_event'       => $event->event_settings['is_dummy_event'],
            'spaces'               => SpaceUSResource::collection($event->spaces),
            'dummy_users'          => $tempDummyRelations['deleted']->pluck('dummy_user_id'),
            'default_space_id'     => $event->spaces->first()->space_uuid,
            'event_conv_limit'     => $event->event_settings['event_conv_limit'] ?? 4,
            'current_scenery_data' => $this->adminServices()->dataFactory->fetchEventSceneryData($event->event_uuid, true),
            'event_title'          => $event->title,
            'event_link'           => $event->join_code,
            'event_grid_rows'      => $event->event_settings['event_grid_rows'] ?? 4,
        ];
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return PrivateChannel
     */
    public function broadcastOn(): PrivateChannel {
        return new PrivateChannel(config('kctuser.events_name.eventDataUpdated'));
    }
}
