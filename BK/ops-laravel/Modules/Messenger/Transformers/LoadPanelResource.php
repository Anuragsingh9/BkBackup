<?php

namespace Modules\Messenger\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class LoadPanelResource extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
//        return ['status' => TRUE, 'data' => ['workshops' => $this['workshops'], 'channels' => $this['channels'], 'users' => $this['users'],]];
        $result = [
            'status' => TRUE,
            'data'   => [
                'last_opened'  => $this['lastChatChannel'] ? $this['lastChatChannel']->channel_uuid : ($this['selfChannel'] ? $this['selfChannel']->uuid : NULL),
                'self_channel' => $this['selfChannel'] ? $this['selfChannel']->uuid : NULL,
                'channels'     => LoadPanelChannelResource::collection($this['channels']),
                'users'        => LoadPanelUserResource::collection($this['users']),
                'workshops'    => LoadPanelWorkshopResource::collection($this['workshops']),
            ]
        ];
        if ($this['eventsWorkshop'] !== NULL) {
            $result['data']['event_workshops'] = LoadPanelWorkshopResource::collection($this['eventsWorkshop']);
        }
        return $result;
    }
}
